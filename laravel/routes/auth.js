const express = require("express");
const { body, validationResult } = require("express-validator");
const User = require("../models/User");
const { generateToken, verifyToken } = require("../middleware/auth");

const router = express.Router();

// Register new user
router.post(
  "/register",
  [
    body("name")
      .trim()
      .isLength({ min: 2 })
      .withMessage("Name must be at least 2 characters"),
    body("email")
      .isEmail()
      .normalizeEmail()
      .withMessage("Please provide a valid email"),
    body("password")
      .isLength({ min: 6 })
      .withMessage("Password must be at least 6 characters"),
    body("phone")
      .optional()
      .isMobilePhone("en-IN")
      .withMessage("Please provide a valid phone number"),
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

      const { name, email, password, phone } = req.body;

      // Check if user already exists
      const existingUser = await User.findOne({ email });
      if (existingUser) {
        return res.status(400).json({
          success: false,
          message: "User already exists with this email",
        });
      }

      // Create new user
      const user = new User({
        name,
        email,
        password, // Will be hashed by the pre-save middleware
        phone,
      });

      await user.save();

      // Generate token
      const token = generateToken(user._id);

      // Set token in session
      req.session.token = token;

      res.status(201).json({
        success: true,
        message: "User registered successfully",
        user: user.toJSON(),
        token,
      });
    } catch (error) {
      console.error("Registration error:", error);
      res.status(500).json({
        success: false,
        message: "Server error during registration",
      });
    }
  }
);

// Login user
router.post(
  "/login",
  [
    body("email")
      .isEmail()
      .normalizeEmail()
      .withMessage("Please provide a valid email"),
    body("password").notEmpty().withMessage("Password is required"),
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

      const { email, password } = req.body;

      // Find user by email
      const user = await User.findOne({ email });
      if (!user) {
        return res.status(401).json({
          success: false,
          message: "Invalid email or password",
        });
      }

      // Check password
      const isPasswordValid = await user.comparePassword(password);
      if (!isPasswordValid) {
        return res.status(401).json({
          success: false,
          message: "Invalid email or password",
        });
      }

      // Generate token
      const token = generateToken(user._id);

      // Set token in session
      req.session.token = token;

      res.json({
        success: true,
        message: "Login successful",
        user: user.toJSON(),
        token,
      });
    } catch (error) {
      console.error("Login error:", error);
      res.status(500).json({
        success: false,
        message: "Server error during login",
      });
    }
  }
);

// Logout user
router.post("/logout", (req, res) => {
  try {
    // Clear session
    req.session.destroy((err) => {
      if (err) {
        return res.status(500).json({
          success: false,
          message: "Error during logout",
        });
      }

      res.json({
        success: true,
        message: "Logout successful",
      });
    });
  } catch (error) {
    console.error("Logout error:", error);
    res.status(500).json({
      success: false,
      message: "Server error during logout",
    });
  }
});

// Get current user
router.get("/me", verifyToken, async (req, res) => {
  try {
    res.json({
      success: true,
      user: req.user,
    });
  } catch (error) {
    console.error("Get user error:", error);
    res.status(500).json({
      success: false,
      message: "Server error getting user info",
    });
  }
});

// Update user profile
router.put(
  "/profile",
  verifyToken,
  [
    body("name")
      .optional()
      .trim()
      .isLength({ min: 2 })
      .withMessage("Name must be at least 2 characters"),
    body("phone")
      .optional()
      .isMobilePhone("en-IN")
      .withMessage("Please provide a valid phone number"),
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

      const { name, phone } = req.body;
      const userId = req.user._id;

      const updateData = {};
      if (name) updateData.name = name;
      if (phone) updateData.phone = phone;

      const user = await User.findByIdAndUpdate(userId, updateData, {
        new: true,
        runValidators: true,
      });

      res.json({
        success: true,
        message: "Profile updated successfully",
        user: user.toJSON(),
      });
    } catch (error) {
      console.error("Profile update error:", error);
      res.status(500).json({
        success: false,
        message: "Server error updating profile",
      });
    }
  }
);

// Add address
router.post(
  "/address",
  verifyToken,
  [
    body("name").trim().notEmpty().withMessage("Name is required"),
    body("address").trim().notEmpty().withMessage("Address is required"),
    body("city").trim().notEmpty().withMessage("City is required"),
    body("state").trim().notEmpty().withMessage("State is required"),
    body("pincode")
      .trim()
      .isLength({ min: 6, max: 6 })
      .withMessage("Pincode must be 6 digits"),
    body("phone")
      .isMobilePhone("en-IN")
      .withMessage("Please provide a valid phone number"),
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

      const userId = req.user._id;
      const addressData = req.body;

      // If this is the first address or marked as default, make it default
      const user = await User.findById(userId);
      if (user.addresses.length === 0 || addressData.isDefault) {
        // Set all other addresses as non-default
        user.addresses.forEach((addr) => (addr.isDefault = false));
        addressData.isDefault = true;
      }

      user.addresses.push(addressData);
      await user.save();

      res.json({
        success: true,
        message: "Address added successfully",
        addresses: user.addresses,
      });
    } catch (error) {
      console.error("Add address error:", error);
      res.status(500).json({
        success: false,
        message: "Server error adding address",
      });
    }
  }
);

// Get user addresses
router.get("/addresses", verifyToken, async (req, res) => {
  try {
    const user = await User.findById(req.user._id);
    res.json({
      success: true,
      addresses: user.addresses,
    });
  } catch (error) {
    console.error("Get addresses error:", error);
    res.status(500).json({
      success: false,
      message: "Server error getting addresses",
    });
  }
});

module.exports = router;
