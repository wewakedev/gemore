const mongoose = require("mongoose");

const couponSchema = new mongoose.Schema(
  {
    code: {
      type: String,
      required: true,
      unique: true,
      uppercase: true,
      trim: true,
    },
    name: {
      type: String,
      required: true,
    },
    description: {
      type: String,
      trim: true,
    },
    type: {
      type: String,
      enum: ["percentage", "fixed"],
      required: true,
    },
    value: {
      type: Number,
      required: true,
    },
    minimumOrderAmount: {
      type: Number,
      default: 0,
    },
    maximumDiscount: {
      type: Number, // for percentage coupons
    },
    usageLimit: {
      total: {
        type: Number,
        default: null, // null means unlimited
      },
      perUser: {
        type: Number,
        default: 1,
      },
    },
    usageCount: {
      type: Number,
      default: 0,
    },
    users: [
      {
        user: {
          type: mongoose.Schema.Types.ObjectId,
          ref: "User",
        },
        usedCount: {
          type: Number,
          default: 0,
        },
        lastUsed: Date,
      },
    ],
    validFrom: {
      type: Date,
      required: true,
    },
    validUntil: {
      type: Date,
      required: true,
    },
    applicableProducts: [
      {
        type: mongoose.Schema.Types.ObjectId,
        ref: "Product",
      },
    ],
    applicableCategories: [
      {
        type: mongoose.Schema.Types.ObjectId,
        ref: "Category",
      },
    ],
    excludeProducts: [
      {
        type: mongoose.Schema.Types.ObjectId,
        ref: "Product",
      },
    ],
    excludeCategories: [
      {
        type: mongoose.Schema.Types.ObjectId,
        ref: "Category",
      },
    ],
    isActive: {
      type: Boolean,
      default: true,
    },
    isFirstTimeUser: {
      type: Boolean,
      default: false,
    },
    createdBy: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "User",
    },
  },
  {
    timestamps: true,
  }
);

// Check if coupon is valid
couponSchema.methods.isValid = function (userId = null) {
  const now = new Date();

  // Check if coupon is active
  if (!this.isActive) return false;

  // Check date validity
  if (now < this.validFrom || now > this.validUntil) return false;

  // Check total usage limit
  if (this.usageLimit.total && this.usageCount >= this.usageLimit.total)
    return false;

  // Check per user limit if userId provided
  if (userId && this.usageLimit.perUser) {
    const userUsage = this.users.find(
      (u) => u.user.toString() === userId.toString()
    );
    if (userUsage && userUsage.usedCount >= this.usageLimit.perUser)
      return false;
  }

  return true;
};

// Calculate discount amount
couponSchema.methods.calculateDiscount = function (orderAmount) {
  if (orderAmount < this.minimumOrderAmount) return 0;

  let discount = 0;

  if (this.type === "percentage") {
    discount = (orderAmount * this.value) / 100;
    if (this.maximumDiscount && discount > this.maximumDiscount) {
      discount = this.maximumDiscount;
    }
  } else {
    discount = this.value;
  }

  return Math.min(discount, orderAmount);
};

// Create indexes
couponSchema.index({ code: 1 });
couponSchema.index({ validFrom: 1, validUntil: 1 });
couponSchema.index({ isActive: 1 });

module.exports = mongoose.model("Coupon", couponSchema);
