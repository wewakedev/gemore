const express = require("express");
const multer = require("multer");
const path = require("path");
const { body, validationResult } = require("express-validator");
const { verifyToken, requireAdmin } = require("../middleware/auth");
const Product = require("../models/Product");
const Category = require("../models/Category");
const Order = require("../models/Order");
const User = require("../models/User");
const Coupon = require("../models/Coupon");

const router = express.Router();

// Configure multer for file uploads
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, "./public/uploads/");
  },
  filename: (req, file, cb) => {
    const uniqueSuffix = Date.now() + "-" + Math.round(Math.random() * 1e9);
    cb(
      null,
      file.fieldname + "-" + uniqueSuffix + path.extname(file.originalname)
    );
  },
});

const upload = multer({
  storage: storage,
  limits: { fileSize: 5 * 1024 * 1024 }, // 5MB limit
  fileFilter: (req, file, cb) => {
    const allowedTypes = /jpeg|jpg|png|gif|webp/;
    const extname = allowedTypes.test(
      path.extname(file.originalname).toLowerCase()
    );
    const mimetype = allowedTypes.test(file.mimetype);

    if (mimetype && extname) {
      return cb(null, true);
    } else {
      cb(new Error("Only image files are allowed"));
    }
  },
});

// All admin routes require authentication and admin role
router.use(verifyToken);
router.use(requireAdmin);

// ===== DASHBOARD =====

// Get dashboard statistics
router.get("/dashboard", async (req, res) => {
  try {
    const [
      totalProducts,
      totalOrders,
      totalUsers,
      totalRevenue,
      recentOrders,
      topProducts,
    ] = await Promise.all([
      Product.countDocuments({ isActive: true }),
      Order.countDocuments(),
      User.countDocuments({ role: "customer" }),
      Order.aggregate([
        { $match: { status: { $in: ["delivered", "confirmed"] } } },
        { $group: { _id: null, total: { $sum: "$pricing.total" } } },
      ]),
      Order.find()
        .populate("user", "name email")
        .sort({ createdAt: -1 })
        .limit(5)
        .lean(),
      Product.find({ isActive: true })
        .sort({ "ratings.count": -1 })
        .limit(5)
        .select("name ratings images")
        .lean(),
    ]);

    res.json({
      success: true,
      data: {
        stats: {
          totalProducts,
          totalOrders,
          totalUsers,
          totalRevenue: totalRevenue[0]?.total || 0,
        },
        recentOrders,
        topProducts,
      },
    });
  } catch (error) {
    console.error("Dashboard error:", error);
    res.status(500).json({
      success: false,
      message: "Server error getting dashboard data",
    });
  }
});

// ===== PRODUCT MANAGEMENT =====

// Get all products
router.get("/products", async (req, res) => {
  try {
    const { page = 1, limit = 20, search, category, status } = req.query;

    const filter = {};
    if (search) {
      filter.$or = [
        { name: { $regex: search, $options: "i" } },
        { sku: { $regex: search, $options: "i" } },
      ];
    }
    if (category) filter.category = category;
    if (status) filter.isActive = status === "active";

    const skip = (parseInt(page) - 1) * parseInt(limit);

    const [products, totalProducts] = await Promise.all([
      Product.find(filter)
        .populate("category", "name")
        .sort({ createdAt: -1 })
        .skip(skip)
        .limit(parseInt(limit))
        .lean(),
      Product.countDocuments(filter),
    ]);

    res.json({
      success: true,
      data: {
        products,
        pagination: {
          currentPage: parseInt(page),
          totalPages: Math.ceil(totalProducts / parseInt(limit)),
          totalProducts,
          limit: parseInt(limit),
        },
      },
    });
  } catch (error) {
    console.error("Get products error:", error);
    res.status(500).json({
      success: false,
      message: "Server error getting products",
    });
  }
});

// Create product
router.post(
  "/products",
  upload.array("images", 10),
  [
    body("name").trim().notEmpty().withMessage("Product name is required"),
    body("description")
      .trim()
      .notEmpty()
      .withMessage("Description is required"),
    body("category").isMongoId().withMessage("Valid category is required"),
    body("sku").trim().notEmpty().withMessage("SKU is required"),
  ],
  async (req, res) => {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({
          success: false,
          message: "Validation failed",
          errors: errors.array(),
        });
      }

      const productData = req.body;

      // Handle uploaded images
      if (req.files && req.files.length > 0) {
        productData.images = req.files.map(
          (file) => `/uploads/${file.filename}`
        );
      }

      // Parse variants if they exist
      if (productData.variants) {
        try {
          productData.variants = JSON.parse(productData.variants);
        } catch (e) {
          return res.status(400).json({
            success: false,
            message: "Invalid variants format",
          });
        }
      }

      // Parse tags
      if (productData.tags) {
        productData.tags = Array.isArray(productData.tags)
          ? productData.tags
          : productData.tags.split(",").map((tag) => tag.trim());
      }

      const product = new Product(productData);
      await product.save();

      res.status(201).json({
        success: true,
        message: "Product created successfully",
        data: product,
      });
    } catch (error) {
      console.error("Create product error:", error);
      res.status(500).json({
        success: false,
        message: "Server error creating product",
      });
    }
  }
);

// Update product
router.put("/products/:id", upload.array("newImages", 10), async (req, res) => {
  try {
    const product = await Product.findById(req.params.id);
    if (!product) {
      return res.status(404).json({
        success: false,
        message: "Product not found",
      });
    }

    const updateData = req.body;

    // Handle new uploaded images
    if (req.files && req.files.length > 0) {
      const newImages = req.files.map((file) => `/uploads/${file.filename}`);
      updateData.images = [...(product.images || []), ...newImages];
    }

    // Parse variants if they exist
    if (updateData.variants) {
      try {
        updateData.variants = JSON.parse(updateData.variants);
      } catch (e) {
        return res.status(400).json({
          success: false,
          message: "Invalid variants format",
        });
      }
    }

    // Parse tags
    if (updateData.tags) {
      updateData.tags = Array.isArray(updateData.tags)
        ? updateData.tags
        : updateData.tags.split(",").map((tag) => tag.trim());
    }

    Object.assign(product, updateData);
    await product.save();

    res.json({
      success: true,
      message: "Product updated successfully",
      data: product,
    });
  } catch (error) {
    console.error("Update product error:", error);
    res.status(500).json({
      success: false,
      message: "Server error updating product",
    });
  }
});

// Delete product
router.delete("/products/:id", async (req, res) => {
  try {
    const product = await Product.findById(req.params.id);
    if (!product) {
      return res.status(404).json({
        success: false,
        message: "Product not found",
      });
    }

    // Soft delete - just mark as inactive
    product.isActive = false;
    await product.save();

    res.json({
      success: true,
      message: "Product deleted successfully",
    });
  } catch (error) {
    console.error("Delete product error:", error);
    res.status(500).json({
      success: false,
      message: "Server error deleting product",
    });
  }
});

// ===== CATEGORY MANAGEMENT =====

// Get all categories
router.get("/categories", async (req, res) => {
  try {
    const categories = await Category.find()
      .sort({ sortOrder: 1, name: 1 })
      .lean();

    res.json({
      success: true,
      data: categories,
    });
  } catch (error) {
    console.error("Get categories error:", error);
    res.status(500).json({
      success: false,
      message: "Server error getting categories",
    });
  }
});

// Create category
router.post(
  "/categories",
  upload.single("image"),
  [body("name").trim().notEmpty().withMessage("Category name is required")],
  async (req, res) => {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({
          success: false,
          message: "Validation failed",
          errors: errors.array(),
        });
      }

      const categoryData = req.body;

      // Handle uploaded image
      if (req.file) {
        categoryData.image = `/uploads/${req.file.filename}`;
      }

      // Generate slug if not provided
      if (!categoryData.slug) {
        categoryData.slug = categoryData.name
          .toLowerCase()
          .replace(/[^a-z0-9]/g, "-")
          .replace(/-+/g, "-")
          .replace(/^-|-$/g, "");
      }

      const category = new Category(categoryData);
      await category.save();

      res.status(201).json({
        success: true,
        message: "Category created successfully",
        data: category,
      });
    } catch (error) {
      console.error("Create category error:", error);
      res.status(500).json({
        success: false,
        message: "Server error creating category",
      });
    }
  }
);

// Update category
router.put("/categories/:id", upload.single("image"), async (req, res) => {
  try {
    const category = await Category.findById(req.params.id);
    if (!category) {
      return res.status(404).json({
        success: false,
        message: "Category not found",
      });
    }

    const updateData = req.body;

    // Handle uploaded image
    if (req.file) {
      updateData.image = `/uploads/${req.file.filename}`;
    }

    Object.assign(category, updateData);
    await category.save();

    res.json({
      success: true,
      message: "Category updated successfully",
      data: category,
    });
  } catch (error) {
    console.error("Update category error:", error);
    res.status(500).json({
      success: false,
      message: "Server error updating category",
    });
  }
});

// Delete category
router.delete("/categories/:id", async (req, res) => {
  try {
    const category = await Category.findById(req.params.id);
    if (!category) {
      return res.status(404).json({
        success: false,
        message: "Category not found",
      });
    }

    // Check if category has products
    const productCount = await Product.countDocuments({
      category: category._id,
    });
    if (productCount > 0) {
      return res.status(400).json({
        success: false,
        message:
          "Cannot delete category with products. Move products to another category first.",
      });
    }

    await Category.findByIdAndDelete(req.params.id);

    res.json({
      success: true,
      message: "Category deleted successfully",
    });
  } catch (error) {
    console.error("Delete category error:", error);
    res.status(500).json({
      success: false,
      message: "Server error deleting category",
    });
  }
});

// ===== ORDER MANAGEMENT =====

// Get all orders
router.get("/orders", async (req, res) => {
  try {
    const { page = 1, limit = 20, status, search } = req.query;

    const filter = {};
    if (status && status !== "all") filter.status = status;
    if (search) {
      filter.$or = [
        { orderNumber: { $regex: search, $options: "i" } },
        { "shippingAddress.name": { $regex: search, $options: "i" } },
      ];
    }

    const skip = (parseInt(page) - 1) * parseInt(limit);

    const [orders, totalOrders] = await Promise.all([
      Order.find(filter)
        .populate("user", "name email")
        .populate("items.product", "name")
        .sort({ createdAt: -1 })
        .skip(skip)
        .limit(parseInt(limit))
        .lean(),
      Order.countDocuments(filter),
    ]);

    res.json({
      success: true,
      data: {
        orders,
        pagination: {
          currentPage: parseInt(page),
          totalPages: Math.ceil(totalOrders / parseInt(limit)),
          totalOrders,
          limit: parseInt(limit),
        },
      },
    });
  } catch (error) {
    console.error("Get orders error:", error);
    res.status(500).json({
      success: false,
      message: "Server error getting orders",
    });
  }
});

// Update order status
router.put(
  "/orders/:id/status",
  [
    body("status")
      .isIn([
        "pending",
        "confirmed",
        "processing",
        "shipped",
        "delivered",
        "cancelled",
      ])
      .withMessage("Valid status is required"),
    body("note").optional().trim(),
  ],
  async (req, res) => {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({
          success: false,
          message: "Validation failed",
          errors: errors.array(),
        });
      }

      const order = await Order.findById(req.params.id);
      if (!order) {
        return res.status(404).json({
          success: false,
          message: "Order not found",
        });
      }

      const { status, note, trackingNumber, trackingProvider } = req.body;

      order.status = status;

      // Add to status history
      order.statusHistory.push({
        status,
        timestamp: new Date(),
        note: note || "",
        updatedBy: req.user._id,
      });

      // Update tracking if provided
      if (trackingNumber && trackingProvider) {
        order.tracking = {
          provider: trackingProvider,
          trackingNumber,
          trackingUrl: generateTrackingUrl(trackingProvider, trackingNumber),
        };
      }

      await order.save();

      res.json({
        success: true,
        message: "Order status updated successfully",
        data: order,
      });
    } catch (error) {
      console.error("Update order status error:", error);
      res.status(500).json({
        success: false,
        message: "Server error updating order status",
      });
    }
  }
);

// ===== USER MANAGEMENT =====

// Get all users
router.get("/users", async (req, res) => {
  try {
    const { page = 1, limit = 20, search, role } = req.query;

    const filter = {};
    if (search) {
      filter.$or = [
        { name: { $regex: search, $options: "i" } },
        { email: { $regex: search, $options: "i" } },
      ];
    }
    if (role) filter.role = role;

    const skip = (parseInt(page) - 1) * parseInt(limit);

    const [users, totalUsers] = await Promise.all([
      User.find(filter)
        .select("-password")
        .sort({ createdAt: -1 })
        .skip(skip)
        .limit(parseInt(limit))
        .lean(),
      User.countDocuments(filter),
    ]);

    res.json({
      success: true,
      data: {
        users,
        pagination: {
          currentPage: parseInt(page),
          totalPages: Math.ceil(totalUsers / parseInt(limit)),
          totalUsers,
          limit: parseInt(limit),
        },
      },
    });
  } catch (error) {
    console.error("Get users error:", error);
    res.status(500).json({
      success: false,
      message: "Server error getting users",
    });
  }
});

// ===== COUPON MANAGEMENT =====

// Get all coupons
router.get("/coupons", async (req, res) => {
  try {
    const coupons = await Coupon.find().sort({ createdAt: -1 }).lean();

    res.json({
      success: true,
      data: coupons,
    });
  } catch (error) {
    console.error("Get coupons error:", error);
    res.status(500).json({
      success: false,
      message: "Server error getting coupons",
    });
  }
});

// Create coupon
router.post(
  "/coupons",
  [
    body("code").trim().notEmpty().withMessage("Coupon code is required"),
    body("name").trim().notEmpty().withMessage("Coupon name is required"),
    body("type")
      .isIn(["percentage", "fixed"])
      .withMessage("Valid coupon type is required"),
    body("value").isNumeric().withMessage("Valid coupon value is required"),
    body("validFrom").isISO8601().withMessage("Valid start date is required"),
    body("validUntil").isISO8601().withMessage("Valid end date is required"),
  ],
  async (req, res) => {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({
          success: false,
          message: "Validation failed",
          errors: errors.array(),
        });
      }

      const couponData = req.body;
      couponData.createdBy = req.user._id;

      const coupon = new Coupon(couponData);
      await coupon.save();

      res.status(201).json({
        success: true,
        message: "Coupon created successfully",
        data: coupon,
      });
    } catch (error) {
      console.error("Create coupon error:", error);
      res.status(500).json({
        success: false,
        message: "Server error creating coupon",
      });
    }
  }
);

// Update coupon
router.put("/coupons/:id", async (req, res) => {
  try {
    const coupon = await Coupon.findByIdAndUpdate(req.params.id, req.body, {
      new: true,
      runValidators: true,
    });

    if (!coupon) {
      return res.status(404).json({
        success: false,
        message: "Coupon not found",
      });
    }

    res.json({
      success: true,
      message: "Coupon updated successfully",
      data: coupon,
    });
  } catch (error) {
    console.error("Update coupon error:", error);
    res.status(500).json({
      success: false,
      message: "Server error updating coupon",
    });
  }
});

// Delete coupon
router.delete("/coupons/:id", async (req, res) => {
  try {
    const coupon = await Coupon.findByIdAndDelete(req.params.id);

    if (!coupon) {
      return res.status(404).json({
        success: false,
        message: "Coupon not found",
      });
    }

    res.json({
      success: true,
      message: "Coupon deleted successfully",
    });
  } catch (error) {
    console.error("Delete coupon error:", error);
    res.status(500).json({
      success: false,
      message: "Server error deleting coupon",
    });
  }
});

// Helper function to generate tracking URL
function generateTrackingUrl(provider, trackingNumber) {
  const providers = {
    bluedart: `https://www.bluedart.com/tracking?trackNo=${trackingNumber}`,
    dtdc: `https://www.dtdc.in/tracking/tracking_results.asp?Ttype=awb_no&strTType=${trackingNumber}`,
    ekart: `https://ekart.in/track?track_no=${trackingNumber}`,
    delhivery: `https://www.delhivery.com/track/package/${trackingNumber}`,
    fedex: `https://www.fedex.com/apps/fedextrack/?tracknumbers=${trackingNumber}`,
    indiapost: `https://www.indiapost.gov.in/_layouts/15/dop.portal.tracking/trackconsignment.aspx?consignmentnumber=${trackingNumber}`,
  };

  return providers[provider.toLowerCase()] || `#tracking-${trackingNumber}`;
}

module.exports = router;
