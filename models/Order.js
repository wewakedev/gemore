const mongoose = require("mongoose");

const orderSchema = new mongoose.Schema(
  {
    orderNumber: {
      type: String,
      unique: true,
      required: true,
    },
    user: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "User",
      required: true,
    },
    items: [
      {
        product: {
          type: mongoose.Schema.Types.ObjectId,
          ref: "Product",
          required: true,
        },
        variant: {
          name: String,
          size: String,
        },
        quantity: {
          type: Number,
          required: true,
          min: 1,
        },
        price: {
          type: Number,
          required: true,
        },
        originalPrice: Number,
        image: String,
        sku: String,
      },
    ],
    shippingAddress: {
      name: {
        type: String,
        required: true,
      },
      address: {
        type: String,
        required: true,
      },
      city: {
        type: String,
        required: true,
      },
      state: {
        type: String,
        required: true,
      },
      pincode: {
        type: String,
        required: true,
      },
      phone: {
        type: String,
        required: true,
      },
    },
    billingAddress: {
      name: String,
      address: String,
      city: String,
      state: String,
      pincode: String,
      phone: String,
      sameAsShipping: {
        type: Boolean,
        default: true,
      },
    },
    payment: {
      method: {
        type: String,
        enum: ["cod", "upi", "card", "netbanking", "wallet"],
        required: true,
      },
      status: {
        type: String,
        enum: ["pending", "processing", "completed", "failed", "refunded"],
        default: "pending",
      },
      transactionId: String,
      gateway: String, // razorpay, stripe, etc.
      gatewayOrderId: String,
      paidAt: Date,
    },
    pricing: {
      subtotal: {
        type: Number,
        required: true,
      },
      discount: {
        type: Number,
        default: 0,
      },
      coupon: {
        code: String,
        discount: Number,
        type: {
          type: String,
          enum: ["percentage", "fixed"],
        },
      },
      shipping: {
        type: Number,
        default: 0,
      },
      tax: {
        type: Number,
        default: 0,
      },
      total: {
        type: Number,
        required: true,
      },
    },
    status: {
      type: String,
      enum: [
        "pending",
        "confirmed",
        "processing",
        "shipped",
        "delivered",
        "cancelled",
        "returned",
      ],
      default: "pending",
    },
    tracking: {
      provider: String,
      trackingNumber: String,
      trackingUrl: String,
      estimatedDelivery: Date,
    },
    statusHistory: [
      {
        status: String,
        timestamp: {
          type: Date,
          default: Date.now,
        },
        note: String,
        updatedBy: {
          type: mongoose.Schema.Types.ObjectId,
          ref: "User",
        },
      },
    ],
    notes: {
      customer: String,
      admin: String,
    },
    cancellation: {
      reason: String,
      requestedAt: Date,
      processedAt: Date,
      refundStatus: {
        type: String,
        enum: ["pending", "processed", "failed"],
      },
    },
  },
  {
    timestamps: true,
  }
);

// Generate order number
orderSchema.pre("save", function (next) {
  if (!this.orderNumber) {
    const timestamp = Date.now().toString();
    this.orderNumber = "GM" + timestamp.slice(-8);
  }
  next();
});

// Add status to history when status changes
orderSchema.pre("save", function (next) {
  if (this.isModified("status")) {
    this.statusHistory.push({
      status: this.status,
      timestamp: new Date(),
    });
  }
  next();
});

// Create indexes for better performance
orderSchema.index({ user: 1, createdAt: -1 });
orderSchema.index({ orderNumber: 1 });
orderSchema.index({ status: 1 });
orderSchema.index({ "payment.status": 1 });

module.exports = mongoose.model("Order", orderSchema);
