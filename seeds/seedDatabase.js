const mongoose = require("mongoose");
const bcrypt = require("bcryptjs");
require("dotenv").config();

// Import models
const User = require("../models/User");
const Category = require("../models/Category");
const Product = require("../models/Product");
const Coupon = require("../models/Coupon");

// Connect to database
const connectDB = async () => {
  try {
    const mongoURI =
      process.env.MONGODB_URI || "mongodb://localhost:27017/gemore_nutralife";
    await mongoose.connect(mongoURI, {
      useNewUrlParser: true,
      useUnifiedTopology: true,
    });
    console.log("MongoDB Connected");
  } catch (error) {
    console.error("Database connection failed:", error);
    process.exit(1);
  }
};

// Seed data
const seedData = async () => {
  try {
    console.log("Starting database seeding...");

    // Clear existing data
    await User.deleteMany({});
    await Category.deleteMany({});
    await Product.deleteMany({});
    await Coupon.deleteMany({});

    console.log("Cleared existing data");

    // Create admin user
    const admin = new User({
      name: "Admin User",
      email: "admin@gemorenutralife.com",
      password: "admin123", // Will be hashed by the pre-save middleware
      role: "admin",
      isVerified: true,
    });
    await admin.save();
    console.log("Admin user created");

    // Create sample customer
    const customer = new User({
      name: "John Doe",
      email: "customer@example.com",
      password: "customer123", // Will be hashed by the pre-save middleware
      phone: "+919876543210",
      role: "customer",
      isVerified: true,
      addresses: [
        {
          type: "home",
          name: "John Doe",
          address: "123 Main Street, Apartment 4B",
          city: "Mumbai",
          state: "Maharashtra",
          pincode: "400001",
          phone: "+919876543210",
          isDefault: true,
        },
      ],
    });
    await customer.save();
    console.log("Sample customer created");

    // Create categories
    const categories = [
      {
        name: "Whey Protein",
        description:
          "Premium quality whey protein supplements for muscle building and recovery",
        slug: "whey-protein",
        icon: "fas fa-dumbbell",
        isActive: true,
        sortOrder: 1,
      },
      {
        name: "Pre-Workout",
        description:
          "Energy boosting supplements for enhanced workout performance",
        slug: "pre-workout",
        icon: "fas fa-bolt",
        isActive: true,
        sortOrder: 2,
      },
      {
        name: "Creatine",
        description:
          "Pure creatine supplements for strength and power enhancement",
        slug: "creatine",
        icon: "fas fa-fire",
        isActive: true,
        sortOrder: 3,
      },
    ];

    const createdCategories = await Category.insertMany(categories);
    console.log("Categories created");

    // Create products
    const wheyCategory = createdCategories.find(
      (cat) => cat.slug === "whey-protein"
    );
    const preWorkoutCategory = createdCategories.find(
      (cat) => cat.slug === "pre-workout"
    );
    const creatineCategory = createdCategories.find(
      (cat) => cat.slug === "creatine"
    );

    const products = [
      {
        name: "Nutralife Whey Protein",
        description:
          "Premium quality whey protein isolate for muscle building and recovery. Each serving provides 25g of high-quality protein with all essential amino acids.",
        category: wheyCategory._id,
        sku: "NL-WP-001",
        variants: [
          {
            name: "Chocolate Flavor",
            size: "1kg",
            price: 2499,
            originalPrice: 2999,
            stock: 50,
            images: [
              "/images/WHEY PROTEIN CHOCOLATE 1KG 1.png",
              "/images/WHEY PROTEIN CHOCOLATE 1KG 2.png",
              "/images/WHEY PROTEIN CHOCOLATE 1KG 3.png",
            ],
            isActive: true,
          },
          {
            name: "Chocolate Flavor",
            size: "2kg",
            price: 4499,
            originalPrice: 5399,
            stock: 30,
            images: ["/images/WHEY PROTEIN 2 KG CHOCOLATE.jpg"],
            isActive: true,
          },
          {
            name: "Kesar Kulfi Flavor",
            size: "1kg",
            price: 2499,
            originalPrice: null,
            stock: 40,
            images: [
              "/images/WHEY PROTEIN KESAR KULFI 1KG 1.png",
              "/images/WHEY PROTEIN KESAR KULFI 1KG 2.png",
              "/images/WHEY PROTEIN KESAR KULFI 1KG 3.png",
            ],
            isActive: true,
          },
        ],
        images: [
          "/images/WHEY PROTEIN CHOCOLATE 1KG 1.png",
          "/images/WHEY PROTEIN KESAR KULFI 1KG 1.png",
        ],
        tags: ["New", "Best Seller"],
        isActive: true,
        isFeatured: true,
        sortOrder: 1,
        ratings: {
          average: 4.8,
          count: 156,
        },
        seo: {
          slug: "nutralife-whey-protein",
          metaTitle:
            "Nutralife Whey Protein - Premium Quality Protein Supplement",
          metaDescription:
            "Premium quality whey protein isolate for muscle building and recovery. 25g protein per serving with essential amino acids.",
        },
      },
      {
        name: "Nutralife Pre-Workout",
        description:
          "Energy boost supplement for enhanced workout performance. Contains caffeine, beta-alanine, and citrulline malate for maximum energy and endurance.",
        category: preWorkoutCategory._id,
        sku: "NL-PW-001",
        variants: [
          {
            name: "Tangy Orange Flavor",
            size: "300g",
            price: 1499,
            originalPrice: 1799,
            stock: 35,
            images: [
              "/images/PRE WORKOUT TANGY ORANGE 1.png",
              "/images/PRE WORKOUT TANGY ORANGE 2.png",
              "/images/PRE WORKOUT TANGY ORANGE 3.png",
            ],
            isActive: true,
          },
          {
            name: "Fruit Punch Flavor",
            size: "300g",
            price: 1499,
            originalPrice: null,
            stock: 45,
            images: [
              "/images/PRE WORKOUT FRUIT PUNCH 1.png",
              "/images/PRE WORKOUT FRUIT PUNCH 2.png",
              "/images/PRE WORKOUT FRUIT PUNCH 3.png",
            ],
            isActive: true,
          },
        ],
        images: [
          "/images/PREWORKOUT TANGY ORANGE.jpg",
          "/images/PREWORKOUT FRUIT PUNCH.jpg",
        ],
        tags: ["Popular", "Energy"],
        isActive: true,
        isFeatured: true,
        sortOrder: 2,
        ratings: {
          average: 4.6,
          count: 89,
        },
        seo: {
          slug: "nutralife-pre-workout",
          metaTitle: "Nutralife Pre-Workout - Energy Boost Supplement",
          metaDescription:
            "Energy boost supplement for enhanced workout performance with caffeine, beta-alanine, and citrulline malate.",
        },
      },
      {
        name: "Nutralife Creatine",
        description:
          "Premium creatine monohydrate for strength and power enhancement. Increases muscle strength, power output, and workout intensity.",
        category: creatineCategory._id,
        sku: "NL-CR-001",
        variants: [
          {
            name: "Tangy Orange Flavor",
            size: "250g",
            price: 999,
            originalPrice: null,
            stock: 60,
            images: [
              "/images/CREATINE TANGY ORANGE 1.png",
              "/images/CREATINE TANGY ORANGE 2.png",
              "/images/CREATINE TANGY ORANGE 3.png",
            ],
            isActive: true,
          },
          {
            name: "Unflavored",
            size: "250g",
            price: 999,
            originalPrice: null,
            stock: 55,
            images: [
              "/images/CREATINE UNFLAVORED 1.png",
              "/images/CREATINE UNFLAVORED 2.png",
              "/images/CREATINE UNFLAVORED 3.png",
            ],
            isActive: true,
          },
        ],
        images: ["/images/CREATINE TANGY ORANGE.jpg"],
        tags: ["Strength", "Power"],
        isActive: true,
        isFeatured: true,
        sortOrder: 3,
        ratings: {
          average: 4.5,
          count: 67,
        },
        seo: {
          slug: "nutralife-creatine",
          metaTitle: "Nutralife Creatine - Premium Creatine Monohydrate",
          metaDescription:
            "Premium creatine monohydrate for strength and power enhancement. Increases muscle strength and workout intensity.",
        },
      },
      {
        name: "Nutralife Mass Gainer",
        description:
          "High-calorie mass gainer for muscle growth and weight gain. Perfect blend of proteins, carbohydrates, and essential nutrients.",
        category: wheyCategory._id,
        sku: "NL-MG-001",
        variants: [
          {
            name: "Chocolate Flavor",
            size: "1kg",
            price: 1899,
            originalPrice: 2299,
            stock: 25,
            images: ["/images/product_choco.jpeg"],
            isActive: true,
          },
          {
            name: "Vanilla Flavor",
            size: "1kg",
            price: 1899,
            originalPrice: null,
            stock: 30,
            images: ["/images/product_choco.jpeg"],
            isActive: true,
          },
        ],
        images: ["/images/product_choco.jpeg"],
        tags: ["Mass Gainer", "Weight Gain"],
        isActive: true,
        isFeatured: true,
        sortOrder: 4,
        ratings: {
          average: 4.3,
          count: 45,
        },
        seo: {
          slug: "nutralife-mass-gainer",
          metaTitle: "Nutralife Mass Gainer - High Calorie Weight Gainer",
          metaDescription:
            "High-calorie mass gainer for muscle growth and weight gain with proteins and carbohydrates.",
        },
      },
      {
        name: "Nutralife BCAA",
        description:
          "Branched-chain amino acids for muscle recovery and endurance. Essential amino acids for muscle protein synthesis.",
        category: preWorkoutCategory._id,
        sku: "NL-BCAA-001",
        variants: [
          {
            name: "Mixed Berry Flavor",
            size: "250g",
            price: 1299,
            originalPrice: 1599,
            stock: 40,
            images: ["/images/product_fruit.jpeg"],
            isActive: true,
          },
          {
            name: "Orange Flavor",
            size: "250g",
            price: 1299,
            originalPrice: null,
            stock: 35,
            images: ["/images/product_orange.jpeg"],
            isActive: true,
          },
        ],
        images: ["/images/product_fruit.jpeg"],
        tags: ["BCAA", "Recovery"],
        isActive: true,
        isFeatured: true,
        sortOrder: 5,
        ratings: {
          average: 4.4,
          count: 78,
        },
        seo: {
          slug: "nutralife-bcaa",
          metaTitle: "Nutralife BCAA - Branched Chain Amino Acids",
          metaDescription:
            "Branched-chain amino acids for muscle recovery and endurance during workouts.",
        },
      },
      {
        name: "Nutralife Glutamine",
        description:
          "Pure L-Glutamine for muscle recovery and immune system support. Essential for muscle repair and growth.",
        category: creatineCategory._id,
        sku: "NL-GLU-001",
        variants: [
          {
            name: "Unflavored",
            size: "300g",
            price: 899,
            originalPrice: null,
            stock: 50,
            images: ["/images/product_unflavoured.png"],
            isActive: true,
          },
          {
            name: "Lemon Flavor",
            size: "300g",
            price: 999,
            originalPrice: 1199,
            stock: 45,
            images: ["/images/product_kesar.png"],
            isActive: true,
          },
        ],
        images: ["/images/product_unflavoured.png"],
        tags: ["Glutamine", "Recovery"],
        isActive: true,
        isFeatured: true,
        sortOrder: 6,
        ratings: {
          average: 4.2,
          count: 32,
        },
        seo: {
          slug: "nutralife-glutamine",
          metaTitle: "Nutralife Glutamine - Pure L-Glutamine Supplement",
          metaDescription:
            "Pure L-Glutamine for muscle recovery and immune system support during intense training.",
        },
      },
    ];

    const createdProducts = await Product.insertMany(products);
    console.log("Products created");

    // Create sample coupons
    const coupons = [
      {
        code: "WELCOME10",
        name: "Welcome Discount",
        description: "Get 10% off on your first order",
        type: "percentage",
        value: 10,
        minimumOrderAmount: 1000,
        maximumDiscount: 500,
        usageLimit: {
          total: 1000,
          perUser: 1,
        },
        validFrom: new Date(),
        validUntil: new Date(Date.now() + 90 * 24 * 60 * 60 * 1000), // 90 days
        isActive: true,
        isFirstTimeUser: true,
        createdBy: admin._id,
      },
      {
        code: "SAVE100",
        name: "Flat 100 Off",
        description: "Get flat ₹100 off on orders above ₹1500",
        type: "fixed",
        value: 100,
        minimumOrderAmount: 1500,
        usageLimit: {
          total: 500,
          perUser: 3,
        },
        validFrom: new Date(),
        validUntil: new Date(Date.now() + 60 * 24 * 60 * 60 * 1000), // 60 days
        isActive: true,
        createdBy: admin._id,
      },
      {
        code: "BULK20",
        name: "Bulk Order Discount",
        description: "Get 20% off on orders above ₹3000",
        type: "percentage",
        value: 20,
        minimumOrderAmount: 3000,
        maximumDiscount: 1000,
        usageLimit: {
          total: null, // unlimited
          perUser: 5,
        },
        validFrom: new Date(),
        validUntil: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000), // 30 days
        isActive: true,
        createdBy: admin._id,
      },
    ];

    await Coupon.insertMany(coupons);
    console.log("Coupons created");

    console.log("Database seeding completed successfully!");
    console.log("\n=== LOGIN CREDENTIALS ===");
    console.log("Admin Login:");
    console.log("Email: admin@gemorenutralife.com");
    console.log("Password: admin123");
    console.log("\nCustomer Login:");
    console.log("Email: customer@example.com");
    console.log("Password: customer123");
    console.log("========================\n");
  } catch (error) {
    console.error("Seeding error:", error);
  }
};

// Run seeder
const runSeeder = async () => {
  await connectDB();
  await seedData();
  mongoose.connection.close();
  console.log("Database connection closed");
  process.exit(0);
};

// Execute if run directly
if (require.main === module) {
  runSeeder();
}

module.exports = { seedData, connectDB };
