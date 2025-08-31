const mongoose = require("mongoose");

const productSchema = new mongoose.Schema(
  {
    name: {
      type: String,
      required: true,
      trim: true,
    },
    description: {
      type: String,
      required: true,
    },
    category: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "Category",
      required: true,
    },
    subcategory: {
      type: String,
      trim: true,
    },
    brand: {
      type: String,
      default: "Ge More Nutralife",
    },
    sku: {
      type: String,
      unique: true,
      required: true,
    },
    variants: [
      {
        name: String, // e.g., "Chocolate Flavor", "Tangy Orange Flavor"
        size: String, // e.g., "1kg", "2kg", "300g"
        price: {
          type: Number,
          required: true,
        },
        originalPrice: Number,
        stock: {
          type: Number,
          default: 0,
        },
        images: [String], // Array of image URLs
        isActive: {
          type: Boolean,
          default: true,
        },
      },
    ],
    images: [String], // Main product images
    tags: [String], // e.g., ["New", "Best Seller", "Sale"]
    features: [String],
    specifications: {
      servingSize: String,
      servingsPerContainer: String,
      nutritionFacts: [
        {
          nutrient: String,
          amount: String,
          dailyValue: String,
        },
      ],
    },
    ratings: {
      average: {
        type: Number,
        default: 0,
      },
      count: {
        type: Number,
        default: 0,
      },
    },
    reviews: [
      {
        user: {
          type: mongoose.Schema.Types.ObjectId,
          ref: "User",
        },
        rating: {
          type: Number,
          required: true,
          min: 1,
          max: 5,
        },
        comment: String,
        createdAt: {
          type: Date,
          default: Date.now,
        },
        isVerified: {
          type: Boolean,
          default: false,
        },
      },
    ],
    seo: {
      metaTitle: String,
      metaDescription: String,
      slug: {
        type: String,
        unique: true,
      },
    },
    isActive: {
      type: Boolean,
      default: true,
    },
    isFeatured: {
      type: Boolean,
      default: false,
    },
    sortOrder: {
      type: Number,
      default: 0,
    },
  },
  {
    timestamps: true,
  }
);

// Create indexes for better performance
productSchema.index({ category: 1, isActive: 1 });
productSchema.index({ isFeatured: 1, sortOrder: 1 });
productSchema.index({ "seo.slug": 1 });
productSchema.index({ sku: 1 });

// Calculate average rating
productSchema.methods.calculateAverageRating = function () {
  if (this.reviews.length === 0) {
    this.ratings.average = 0;
    this.ratings.count = 0;
  } else {
    const sum = this.reviews.reduce((acc, review) => acc + review.rating, 0);
    this.ratings.average = Math.round((sum / this.reviews.length) * 10) / 10;
    this.ratings.count = this.reviews.length;
  }
};

// Generate slug from name
productSchema.pre("save", function (next) {
  if (this.isModified("name") && (!this.seo || !this.seo.slug)) {
    if (!this.seo) this.seo = {};
    this.seo.slug = this.name
      .toLowerCase()
      .replace(/[^a-z0-9]/g, "-")
      .replace(/-+/g, "-")
      .replace(/^-|-$/g, "");
  }
  next();
});

module.exports = mongoose.model("Product", productSchema);
