const express = require("express");
const Product = require("../models/Product");
const Category = require("../models/Category");
const { optionalAuth } = require("../middleware/auth");

const router = express.Router();

// Get all products with filtering and pagination
router.get("/", optionalAuth, async (req, res) => {
  try {
    const {
      category,
      search,
      minPrice,
      maxPrice,
      sortBy = "createdAt",
      sortOrder = "desc",
      page = 1,
      limit = 12,
      featured,
    } = req.query;

    // Build filter object
    const filter = { isActive: true };

    // Category filter
    if (category && category !== "all") {
      const categoryDoc = await Category.findOne({ slug: category });
      if (categoryDoc) {
        filter.category = categoryDoc._id;
      }
    }

    // Search filter
    if (search) {
      filter.$or = [
        { name: { $regex: search, $options: "i" } },
        { description: { $regex: search, $options: "i" } },
        { tags: { $in: [new RegExp(search, "i")] } },
      ];
    }

    // Price filter
    if (minPrice || maxPrice) {
      filter["variants.price"] = {};
      if (minPrice) filter["variants.price"].$gte = parseInt(minPrice);
      if (maxPrice) filter["variants.price"].$lte = parseInt(maxPrice);
    }

    // Featured filter
    if (featured === "true") {
      filter.isFeatured = true;
    }

    // Build sort object
    const sort = {};
    sort[sortBy] = sortOrder === "desc" ? -1 : 1;

    // Calculate pagination
    const skip = (parseInt(page) - 1) * parseInt(limit);

    // Execute query
    const [products, totalProducts] = await Promise.all([
      Product.find(filter)
        .populate("category", "name slug")
        .sort(sort)
        .skip(skip)
        .limit(parseInt(limit))
        .lean(),
      Product.countDocuments(filter),
    ]);

    // Calculate pagination info
    const totalPages = Math.ceil(totalProducts / parseInt(limit));
    const hasNextPage = parseInt(page) < totalPages;
    const hasPrevPage = parseInt(page) > 1;

    res.json({
      success: true,
      data: {
        products,
        pagination: {
          currentPage: parseInt(page),
          totalPages,
          totalProducts,
          limit: parseInt(limit),
          hasNextPage,
          hasPrevPage,
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

// Get featured products for homepage
router.get("/featured", async (req, res) => {
  try {
    const limit = parseInt(req.query.limit) || 6;

    const products = await Product.find({
      isActive: true,
      isFeatured: true,
    })
      .populate("category", "name slug")
      .sort({ sortOrder: 1, createdAt: -1 })
      .limit(limit)
      .lean();

    res.json({
      success: true,
      data: products,
    });
  } catch (error) {
    console.error("Get featured products error:", error);
    res.status(500).json({
      success: false,
      message: "Server error getting featured products",
    });
  }
});

// Get single product by slug
router.get("/:slug", optionalAuth, async (req, res) => {
  try {
    const product = await Product.findOne({
      "seo.slug": req.params.slug,
      isActive: true,
    })
      .populate("category", "name slug")
      .populate("reviews.user", "name")
      .lean();

    if (!product) {
      return res.status(404).json({
        success: false,
        message: "Product not found",
      });
    }

    // Get related products from same category
    const relatedProducts = await Product.find({
      category: product.category._id,
      _id: { $ne: product._id },
      isActive: true,
    })
      .populate("category", "name slug")
      .limit(4)
      .lean();

    res.json({
      success: true,
      data: {
        product,
        relatedProducts,
      },
    });
  } catch (error) {
    console.error("Get product error:", error);
    res.status(500).json({
      success: false,
      message: "Server error getting product",
    });
  }
});

// Search products
router.get("/search/query", async (req, res) => {
  try {
    const { q, limit = 10 } = req.query;

    if (!q) {
      return res.json({
        success: true,
        data: [],
      });
    }

    const products = await Product.find({
      isActive: true,
      $or: [
        { name: { $regex: q, $options: "i" } },
        { description: { $regex: q, $options: "i" } },
        { tags: { $in: [new RegExp(q, "i")] } },
      ],
    })
      .populate("category", "name slug")
      .limit(parseInt(limit))
      .select("name seo.slug variants.price variants.originalPrice images")
      .lean();

    res.json({
      success: true,
      data: products,
    });
  } catch (error) {
    console.error("Search products error:", error);
    res.status(500).json({
      success: false,
      message: "Server error searching products",
    });
  }
});

// Add product review
router.post("/:slug/review", optionalAuth, async (req, res) => {
  try {
    if (!req.user) {
      return res.status(401).json({
        success: false,
        message: "Login required to add review",
      });
    }

    const { rating, comment } = req.body;

    if (!rating || rating < 1 || rating > 5) {
      return res.status(400).json({
        success: false,
        message: "Rating must be between 1 and 5",
      });
    }

    const product = await Product.findOne({
      "seo.slug": req.params.slug,
      isActive: true,
    });

    if (!product) {
      return res.status(404).json({
        success: false,
        message: "Product not found",
      });
    }

    // Check if user already reviewed this product
    const existingReview = product.reviews.find(
      (review) => review.user.toString() === req.user._id.toString()
    );

    if (existingReview) {
      return res.status(400).json({
        success: false,
        message: "You have already reviewed this product",
      });
    }

    // Add review
    product.reviews.push({
      user: req.user._id,
      rating,
      comment: comment || "",
      createdAt: new Date(),
    });

    // Recalculate average rating
    product.calculateAverageRating();
    await product.save();

    res.json({
      success: true,
      message: "Review added successfully",
    });
  } catch (error) {
    console.error("Add review error:", error);
    res.status(500).json({
      success: false,
      message: "Server error adding review",
    });
  }
});

// Get product categories
router.get("/categories/all", async (req, res) => {
  try {
    const categories = await Category.find({ isActive: true })
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

module.exports = router;
