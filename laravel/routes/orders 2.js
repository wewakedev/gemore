const express = require("express");
const { body, validationResult } = require("express-validator");
const Order = require("../models/Order");
const Product = require("../models/Product");
const User = require("../models/User");
const Coupon = require("../models/Coupon");
const { verifyToken } = require("../middleware/auth");
const nodemailer = require("nodemailer");

const router = express.Router();

// Create new order
router.post(
  "/create",
  verifyToken,
  [
    body("items")
      .isArray({ min: 1 })
      .withMessage("At least one item is required"),
    body("items.*.product")
      .isMongoId()
      .withMessage("Valid product ID is required"),
    body("items.*.quantity")
      .isInt({ min: 1 })
      .withMessage("Quantity must be at least 1"),
    body("shippingAddress.name")
      .trim()
      .notEmpty()
      .withMessage("Shipping name is required"),
    body("shippingAddress.address")
      .trim()
      .notEmpty()
      .withMessage("Shipping address is required"),
    body("shippingAddress.city")
      .trim()
      .notEmpty()
      .withMessage("Shipping city is required"),
    body("shippingAddress.state")
      .trim()
      .notEmpty()
      .withMessage("Shipping state is required"),
    body("shippingAddress.pincode")
      .trim()
      .isLength({ min: 6, max: 6 })
      .withMessage("Valid pincode is required"),
    body("shippingAddress.phone")
      .isMobilePhone("en-IN")
      .withMessage("Valid phone number is required"),
    body("payment.method")
      .isIn(["cod", "upi", "card", "netbanking", "wallet"])
      .withMessage("Valid payment method is required"),
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

      const {
        items,
        shippingAddress,
        billingAddress,
        payment,
        couponCode,
        notes,
      } = req.body;
      const userId = req.user._id;

      // Validate and process items
      const processedItems = [];
      let subtotal = 0;

      for (const item of items) {
        const product = await Product.findById(item.product);
        if (!product || !product.isActive) {
          return res.status(400).json({
            success: false,
            message: `Product not found or inactive: ${item.product}`,
          });
        }

        // Find the variant
        const variant = product.variants.find(
          (v) => v.name === item.variant?.name && v.size === item.variant?.size
        );

        if (!variant) {
          return res.status(400).json({
            success: false,
            message: `Variant not found for product: ${product.name}`,
          });
        }

        // Check stock
        if (variant.stock < item.quantity) {
          return res.status(400).json({
            success: false,
            message: `Insufficient stock for ${product.name} - ${variant.name} ${variant.size}`,
          });
        }

        const itemPrice = variant.price;
        const itemTotal = itemPrice * item.quantity;
        subtotal += itemTotal;

        processedItems.push({
          product: product._id,
          variant: {
            name: variant.name,
            size: variant.size,
          },
          quantity: item.quantity,
          price: itemPrice,
          originalPrice: variant.originalPrice || itemPrice,
          image: variant.images?.[0] || product.images?.[0],
          sku:
            product.sku + "-" + variant.name.replace(/\s+/g, "-").toLowerCase(),
        });
      }

      // Apply coupon if provided
      let discount = 0;
      let couponData = null;

      if (couponCode) {
        const coupon = await Coupon.findOne({
          code: couponCode.toUpperCase(),
          isActive: true,
        });

        if (!coupon || !coupon.isValid(userId)) {
          return res.status(400).json({
            success: false,
            message: "Invalid or expired coupon code",
          });
        }

        discount = coupon.calculateDiscount(subtotal);
        couponData = {
          code: coupon.code,
          discount: discount,
          type: coupon.type,
        };
      }

      // Calculate shipping
      const shipping = subtotal > 1500 ? 0 : 99;
      const total = subtotal - discount + shipping;

      // Create order
      const order = new Order({
        user: userId,
        items: processedItems,
        shippingAddress,
        billingAddress: billingAddress || {
          ...shippingAddress,
          sameAsShipping: true,
        },
        payment: {
          method: payment.method,
          status: payment.method === "cod" ? "pending" : "processing",
        },
        pricing: {
          subtotal,
          discount,
          coupon: couponData,
          shipping,
          total,
        },
        notes: {
          customer: notes?.customer || "",
        },
      });

      await order.save();

      // Update product stock
      for (const item of processedItems) {
        await Product.findOneAndUpdate(
          {
            _id: item.product,
            "variants.name": item.variant.name,
            "variants.size": item.variant.size,
          },
          {
            $inc: { "variants.$.stock": -item.quantity },
          }
        );
      }

      // Update coupon usage
      if (couponData) {
        await Coupon.findOneAndUpdate(
          { code: couponData.code },
          {
            $inc: { usageCount: 1 },
            $push: {
              users: {
                $each: [{ user: userId, usedCount: 1, lastUsed: new Date() }],
                $slice: -1000, // Keep last 1000 usage records
              },
            },
          }
        );
      }

      // Send order confirmation email
      await sendOrderConfirmationEmail(order);

      res.status(201).json({
        success: true,
        message: "Order created successfully",
        data: {
          orderId: order._id,
          orderNumber: order.orderNumber,
          total: order.pricing.total,
        },
      });
    } catch (error) {
      console.error("Create order error:", error);
      res.status(500).json({
        success: false,
        message: "Server error creating order",
      });
    }
  }
);

// Get user orders
router.get("/my-orders", verifyToken, async (req, res) => {
  try {
    const { page = 1, limit = 10, status } = req.query;
    const userId = req.user._id;

    const filter = { user: userId };
    if (status) {
      filter.status = status;
    }

    const skip = (parseInt(page) - 1) * parseInt(limit);

    const [orders, totalOrders] = await Promise.all([
      Order.find(filter)
        .populate("items.product", "name images")
        .sort({ createdAt: -1 })
        .skip(skip)
        .limit(parseInt(limit))
        .lean(),
      Order.countDocuments(filter),
    ]);

    const totalPages = Math.ceil(totalOrders / parseInt(limit));

    res.json({
      success: true,
      data: {
        orders,
        pagination: {
          currentPage: parseInt(page),
          totalPages,
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

// Get single order
router.get("/:orderNumber", verifyToken, async (req, res) => {
  try {
    const order = await Order.findOne({
      orderNumber: req.params.orderNumber,
      user: req.user._id,
    })
      .populate("items.product", "name images seo.slug")
      .lean();

    if (!order) {
      return res.status(404).json({
        success: false,
        message: "Order not found",
      });
    }

    res.json({
      success: true,
      data: order,
    });
  } catch (error) {
    console.error("Get order error:", error);
    res.status(500).json({
      success: false,
      message: "Server error getting order",
    });
  }
});

// Validate coupon
router.post("/validate-coupon", verifyToken, async (req, res) => {
  try {
    const { couponCode, orderAmount } = req.body;

    if (!couponCode || !orderAmount) {
      return res.status(400).json({
        success: false,
        message: "Coupon code and order amount are required",
      });
    }

    const coupon = await Coupon.findOne({
      code: couponCode.toUpperCase(),
      isActive: true,
    });

    if (!coupon) {
      return res.status(404).json({
        success: false,
        message: "Coupon not found",
      });
    }

    if (!coupon.isValid(req.user._id)) {
      return res.status(400).json({
        success: false,
        message: "Coupon is expired or usage limit exceeded",
      });
    }

    const discount = coupon.calculateDiscount(orderAmount);

    if (discount === 0) {
      return res.status(400).json({
        success: false,
        message: `Minimum order amount ₹${coupon.minimumOrderAmount} required for this coupon`,
      });
    }

    res.json({
      success: true,
      data: {
        coupon: {
          code: coupon.code,
          name: coupon.name,
          type: coupon.type,
          value: coupon.value,
          discount,
        },
      },
    });
  } catch (error) {
    console.error("Validate coupon error:", error);
    res.status(500).json({
      success: false,
      message: "Server error validating coupon",
    });
  }
});

// Cancel order
router.post("/:orderNumber/cancel", verifyToken, async (req, res) => {
  try {
    const { reason } = req.body;
    const order = await Order.findOne({
      orderNumber: req.params.orderNumber,
      user: req.user._id,
    });

    if (!order) {
      return res.status(404).json({
        success: false,
        message: "Order not found",
      });
    }

    // Check if order can be cancelled
    if (!["pending", "confirmed"].includes(order.status)) {
      return res.status(400).json({
        success: false,
        message: "Order cannot be cancelled at this stage",
      });
    }

    // Update order status
    order.status = "cancelled";
    order.cancellation = {
      reason: reason || "Cancelled by customer",
      requestedAt: new Date(),
    };

    await order.save();

    // Restore product stock
    for (const item of order.items) {
      await Product.findOneAndUpdate(
        {
          _id: item.product,
          "variants.name": item.variant.name,
          "variants.size": item.variant.size,
        },
        {
          $inc: { "variants.$.stock": item.quantity },
        }
      );
    }

    res.json({
      success: true,
      message: "Order cancelled successfully",
    });
  } catch (error) {
    console.error("Cancel order error:", error);
    res.status(500).json({
      success: false,
      message: "Server error cancelling order",
    });
  }
});

// Function to send order confirmation email
async function sendOrderConfirmationEmail(order) {
  try {
    const user = await User.findById(order.user);
    if (!user) return;

    const transporter = nodemailer.createTransporter({
      host: "mail.privateemail.com",
      port: 465,
      secure: true,
      auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASS,
      },
    });

    // Create order items HTML
    const orderItemsHTML = order.items
      .map(
        (item) => `
        <tr>
          <td style="padding: 10px; border-bottom: 1px solid #eee;">
            <div style="display: flex; align-items: center; gap: 10px;">
              <img src="${item.image}" alt="${
          item.product.name
        }" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
              <div>
                <strong>${item.product.name}</strong><br>
                ${
                  item.variant
                    ? `<small>${item.variant.name} - ${item.variant.size}</small>`
                    : ""
                }
              </div>
            </div>
          </td>
          <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: center;">₹${item.price.toLocaleString(
            "en-IN"
          )}</td>
          <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: center;">${
            item.quantity
          }</td>
          <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">₹${(
            item.price * item.quantity
          ).toLocaleString("en-IN")}</td>
        </tr>
      `
      )
      .join("");

    // Send email to customer
    await transporter.sendMail({
      from: `"Ge More Nutralife" <${process.env.SMTP_USER}>`,
      to: user.email,
      subject: `Order Confirmed - #${order.orderNumber}`,
      html: `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
          <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #8B0000; margin: 0;">Order Confirmed!</h1>
            <p style="color: #666; margin: 5px 0;">Thank you for shopping with Ge More Nutralife</p>
          </div>
          
          <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h2 style="color: #28a745; margin-top: 0;">Order Details</h2>
            <p><strong>Order Number:</strong> #${order.orderNumber}</p>
            <p><strong>Order Date:</strong> ${order.createdAt.toLocaleDateString()}</p>
            <p><strong>Payment Method:</strong> ${
              order.payment.method === "cod"
                ? "Cash on Delivery"
                : order.payment.method.toUpperCase()
            }</p>
          </div>
          
          <div style="margin: 20px 0;">
            <h3 style="color: #333;">Order Summary</h3>
            <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
              <thead>
                <tr style="background: #f8f9fa;">
                  <th style="padding: 10px; text-align: left;">Product</th>
                  <th style="padding: 10px; text-align: center;">Price</th>
                  <th style="padding: 10px; text-align: center;">Qty</th>
                  <th style="padding: 10px; text-align: right;">Total</th>
                </tr>
              </thead>
              <tbody>
                ${orderItemsHTML}
              </tbody>
            </table>
            
            <div style="text-align: right; margin-top: 20px;">
              <p style="margin: 5px 0;">Subtotal: ₹${order.pricing.subtotal.toLocaleString(
                "en-IN"
              )}</p>
              ${
                order.pricing.discount > 0
                  ? `<p style="margin: 5px 0; color: #28a745;">Discount: -₹${order.pricing.discount.toLocaleString(
                      "en-IN"
                    )}</p>`
                  : ""
              }
              <p style="margin: 5px 0;">Shipping: ₹${order.pricing.shipping.toLocaleString(
                "en-IN"
              )}</p>
              <hr style="margin: 10px 0;">
              <p style="margin: 5px 0; font-size: 1.2em; font-weight: bold; color: #8B0000;">
                Total: ₹${order.pricing.total.toLocaleString("en-IN")}
              </p>
            </div>
          </div>
        </div>
      `,
    });
  } catch (error) {
    console.error("Send order email error:", error);
  }
}

module.exports = router;
