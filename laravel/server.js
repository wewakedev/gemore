const express = require("express");
const nodemailer = require("nodemailer");
const bodyParser = require("body-parser");
const cors = require("cors");
require("dotenv").config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());
app.use(express.static(__dirname));

// Debug route to check email configuration
app.get("/check-email-config", (req, res) => {
  res.json({
    host: process.env.SMTP_HOST,
    port: process.env.SMTP_PORT,
    secure: process.env.SMTP_SECURE,
    user: process.env.SMTP_USER,
    // Don't send the actual password
    hasPassword: !!process.env.SMTP_PASS,
  });
});

// Contact Form Endpoint
app.post("/send-contact", async (req, res) => {
  console.log("Received contact form submission:", req.body);

  const { name, email, phone, subject, message } = req.body;

  // Validate required fields
  if (!name || !email || !message) {
    console.log("Missing required fields:", { name, email, message });
    return res.status(400).json({
      success: false,
      error: "Please fill all required fields.",
    });
  }

  try {
    console.log("Creating email transporter with config:", {
      host: process.env.SMTP_HOST,
      port: process.env.SMTP_PORT,
      secure: process.env.SMTP_SECURE === "true",
      user: process.env.SMTP_USER,
    });

    // Create transporter
    let transporter = nodemailer.createTransport({
      host: "mail.privateemail.com",
      port: 465,
      secure: true, // use SSL
      auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASS,
      },
    });

    // Verify transporter
    console.log("Verifying transporter...");
    await transporter.verify();
    console.log("Transporter verified successfully");

    // Prepare email data
    const mailOptions = {
      from: `"GeMore Nutrients Website" <${process.env.SMTP_USER}>`,
      to: process.env.SMTP_USER,
      subject: `New Contact Form Submission - ${subject || "General Inquiry"}`,
      html: `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
          <h2 style="color: #8B0000; text-align: center;">New Contact Form Submission</h2>
          <hr style="border: none; height: 2px; background: #8B0000;">
          
          <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="color: #333; margin-top: 0;">Customer Details:</h3>
            <p><strong>Name:</strong> ${name}</p>
            <p><strong>Email:</strong> <a href="mailto:${email}" style="color: #8B0000;">${email}</a></p>
            <p><strong>Phone:</strong> ${phone || "N/A"}</p>
            <p><strong>Subject:</strong> ${subject || "N/A"}</p>
          </div>
          
          <div style="background: #fff; padding: 20px; border-left: 4px solid #8B0000; margin: 20px 0;">
            <h3 style="color: #333; margin-top: 0;">Message:</h3>
            <p style="line-height: 1.6;">${message}</p>
          </div>
        </div>
      `,
    };

    // Send email
    console.log("Sending email...");
    const info = await transporter.sendMail(mailOptions);
    console.log("Email sent successfully:", info.messageId);

    // Send auto-reply
    console.log("Sending auto-reply...");
    const autoReplyInfo = await transporter.sendMail({
      from: `"GeMore Nutrients" <${process.env.SMTP_USER}>`,
      to: email,
      subject: "Thank you for contacting GeMore Nutrients",
      html: `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
          <h2 style="color: #8B0000; text-align: center;">Thank You for Contacting Us</h2>
          
          <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <p>Dear ${name},</p>
            <p>Thank you for reaching out to GeMore Nutrients. We have received your message and will get back to you within 24 hours.</p>
            <p>For urgent inquiries, please feel free to call us at: <strong>+91 92117 98913</strong></p>
          </div>
          
          <div style="background: #fff; padding: 20px; border-left: 4px solid #8B0000; margin: 20px 0;">
            <h3 style="color: #333; margin-top: 0;">Your Message Details:</h3>
            <p><strong>Subject:</strong> ${subject || "General Inquiry"}</p>
            <p><strong>Message:</strong></p>
            <p style="line-height: 1.6;">${message}</p>
          </div>
          
          <div style="text-align: center; margin-top: 30px;">
            <p style="color: #666;">Best regards,</p>
            <p style="color: #8B0000; font-weight: bold;">The GeMore Nutrients Team</p>
          </div>
        </div>
      `,
    });
    console.log("Auto-reply sent successfully:", autoReplyInfo.messageId);

    res.json({
      success: true,
      message: "Thank you! We'll get back to you soon.",
    });
  } catch (err) {
    console.error("Detailed error:", {
      name: err.name,
      message: err.message,
      stack: err.stack,
      code: err.code,
      response: err.response,
    });

    res.status(500).json({
      success: false,
      error: "Failed to send message. Please try again later.",
      details: process.env.NODE_ENV === "development" ? err.message : undefined,
    });
  }
});

// Error handling middleware
app.use((err, req, res, next) => {
  console.error("Global error handler:", err);
  res.status(500).json({
    success: false,
    error: "Something went wrong! Please try again later.",
    details: process.env.NODE_ENV === "development" ? err.message : undefined,
  });
});

// Order confirmation endpoint
app.post("/send-order-confirmation", async (req, res) => {
  console.log("Received order:", req.body);

  const {
    orderNumber,
    customerName,
    customerEmail,
    customerPhone,
    shippingAddress,
    items,
    paymentMethod,
    timestamp,
  } = req.body;

  if (!orderNumber || !customerName || !customerEmail || !items) {
    return res.status(400).json({
      success: false,
      error: "Missing required order information.",
    });
  }

  try {
    // Create transporter
    let transporter = nodemailer.createTransport({
      host: "mail.privateemail.com",
      port: 465,
      secure: true, // use SSL
      auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASS,
      },
    });

    // Calculate totals
    const subtotal = items.reduce(
      (total, item) => total + item.price * item.quantity,
      0
    );
    const shipping = subtotal > 1500 ? 0 : 99;
    const total = subtotal + shipping;

    // Create order items HTML
    const orderItemsHTML = items
      .map(
        (item) => `
      <tr>
        <td style="padding: 10px; border-bottom: 1px solid #eee;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <img src="${item.image}" alt="${
          item.name
        }" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
            <div>
              <strong>${item.name}</strong><br>
              ${item.variant ? `<small>${item.variant}</small>` : ""}
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

    // Send confirmation email to customer
    await transporter.sendMail({
      from: `"Ge More Nutralife" <${process.env.SMTP_USER}>`,
      to: customerEmail,
      subject: `Order Confirmed - #${orderNumber}`,
      html: `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
          <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #8B0000; margin: 0;">Order Confirmed!</h1>
            <p style="color: #666; margin: 5px 0;">Thank you for shopping with Ge More Nutralife</p>
          </div>
          
          <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h2 style="color: #28a745; margin-top: 0;">Order Details</h2>
            <p><strong>Order Number:</strong> #${orderNumber}</p>
            <p><strong>Order Date:</strong> ${new Date(
              timestamp
            ).toLocaleDateString()}</p>
            <p><strong>Payment Method:</strong> ${
              paymentMethod === "cod"
                ? "Cash on Delivery"
                : paymentMethod === "upi"
                ? "UPI Payment"
                : "Credit/Debit Card"
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
              <p style="margin: 5px 0;">Subtotal: ₹${subtotal.toLocaleString(
                "en-IN"
              )}</p>
              <p style="margin: 5px 0;">Shipping: ₹${shipping.toLocaleString(
                "en-IN"
              )}</p>
              <hr style="margin: 10px 0;">
              <p style="margin: 5px 0; font-size: 1.2em; font-weight: bold; color: #8B0000;">
                Total: ₹${total.toLocaleString("en-IN")}
              </p>
            </div>
          </div>
          
          <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="color: #333; margin-top: 0;">Shipping Address</h3>
            <p style="margin: 0; line-height: 1.6;">
              ${customerName}<br>
              ${shippingAddress.address}<br>
              ${shippingAddress.city}, ${shippingAddress.state} ${
        shippingAddress.pincode
      }<br>
              Phone: ${customerPhone}
            </p>
          </div>
          
          <div style="background: #8B0000; color: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="margin-top: 0;">What's Next?</h3>
            <ul style="margin: 10px 0; padding-left: 20px;">
              <li>Your order will be processed within 24 hours</li>
              <li>You'll receive a tracking number once shipped</li>
              <li>Expected delivery: 3-5 business days</li>
              <li>Free shipping on orders above ₹1500</li>
            </ul>
          </div>
          
          <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p style="color: #666; margin: 10px 0;">Need help? Contact us:</p>
            <p style="color: #8B0000; margin: 5px 0;">📧 info@gemorenutralife.com</p>
            <p style="color: #8B0000; margin: 5px 0;">📞 +91 92117 98913</p>
          </div>
        </div>
      `,
    });

    // Send notification to admin
    await transporter.sendMail({
      from: `"Ge More Nutralife System" <${process.env.SMTP_USER}>`,
      to: process.env.SMTP_USER,
      subject: `New Order Received - #${orderNumber}`,
      html: `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
          <h2 style="color: #8B0000; text-align: center;">New Order Received</h2>
          <hr style="border: none; height: 2px; background: #8B0000;">
          
          <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="color: #333; margin-top: 0;">Order Information</h3>
            <p><strong>Order Number:</strong> #${orderNumber}</p>
            <p><strong>Date:</strong> ${new Date(
              timestamp
            ).toLocaleString()}</p>
            <p><strong>Payment Method:</strong> ${
              paymentMethod === "cod"
                ? "Cash on Delivery"
                : paymentMethod === "upi"
                ? "UPI Payment"
                : "Credit/Debit Card"
            }</p>
            <p><strong>Total Amount:</strong> ₹${total.toLocaleString(
              "en-IN"
            )}</p>
          </div>
          
          <div style="background: #fff; padding: 20px; border-left: 4px solid #8B0000; margin: 20px 0;">
            <h3 style="color: #333; margin-top: 0;">Customer Details</h3>
            <p><strong>Name:</strong> ${customerName}</p>
            <p><strong>Email:</strong> ${customerEmail}</p>
            <p><strong>Phone:</strong> ${customerPhone}</p>
            <p><strong>Address:</strong><br>
            ${shippingAddress.address}<br>
            ${shippingAddress.city}, ${shippingAddress.state} ${
        shippingAddress.pincode
      }</p>
          </div>
          
          <div style="margin: 20px 0;">
            <h3 style="color: #333;">Order Items</h3>
            <table style="width: 100%; border-collapse: collapse;">
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
              <p style="margin: 5px 0;">Subtotal: ₹${subtotal.toLocaleString(
                "en-IN"
              )}</p>
              <p style="margin: 5px 0;">Shipping: ₹${shipping.toLocaleString(
                "en-IN"
              )}</p>
              <hr style="margin: 10px 0;">
              <p style="margin: 5px 0; font-size: 1.2em; font-weight: bold; color: #8B0000;">
                Total: ₹${total.toLocaleString("en-IN")}
              </p>
            </div>
          </div>
          
          <div style="background: #28a745; color: white; padding: 15px; border-radius: 8px; text-align: center;">
            <h3 style="margin-top: 0;">Action Required</h3>
            <p>Please process this order within 24 hours and update the customer with tracking information.</p>
          </div>
        </div>
      `,
    });

    res.json({
      success: true,
      message: "Order confirmation sent successfully",
    });
  } catch (error) {
    console.error("Error sending order confirmation:", error);
    res.status(500).json({
      success: false,
      error: "Failed to send order confirmation",
      details:
        process.env.NODE_ENV === "development" ? error.message : undefined,
    });
  }
});

// Start server with error handling
const server = app
  .listen(PORT, () => {
    console.log(`Server running on http://localhost:3000`);
    console.log("Email configuration:", {
      host: process.env.SMTP_HOST,
      port: process.env.SMTP_PORT,
      secure: process.env.SMTP_SECURE,
      user: process.env.SMTP_USER,
      hasPassword: !!process.env.SMTP_PASS,
    });
  })
  .on("error", (err) => {
    if (err.code === "EADDRINUSE") {
      console.error(
        `Port ${PORT} is already in use. Please try a different port or kill the process using this port.`
      );
      process.exit(1);
    } else {
      console.error("Failed to start server:", err);
      process.exit(1);
    }
  });
