const express = require("express");
const nodemailer = require("nodemailer");
const bodyParser = require("body-parser");
const cors = require("cors");
require("dotenv").config();
const app = express();
const PORT = process.env.PORT || 3000;
app.use(cors());
app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());
app.use(express.static(__dirname));
app.post("/send-contact", async (req, res) => {
  const { name, email, phone, message } = req.body;
  if (!name || !email || !message) {
    return;
    res.status(400).json({ error: "Please fill all required fields." });
  }
  try {
    let transporter = nodemailer.createTransport({
      host: process.env.SMTP_HOST || "smtp.gmail.com",
      port: process.env.SMTP_PORT || 587,
      secure: process.env.SMTP_SECURE === "true" || false,
      auth: {
        user: process.env.SMTP_USER || "info@gemorenutralife.com",
        pass: process.env.SMTP_PASS || "your_app_password_here",
      },
    });
    await transporter.sendMail({
      from: `GeMore Nutrients Website <${
        process.env.SMTP_USER || "info@gemorenutralife.com"
      }>`,
      to: "info@gemorenutralife.com",
      subject: "New Contact Form Submission - GeMore Nutrients",
      html: `
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
  <h2 style="color: #456ef7; text-align: center;">New Contact Form Submission</h2>
  <hr style="border: none; height: 2px; background: linear-gradient(90deg, #456ef7, #ff6600);">
  
  <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <h3 style="color: #333; margin-top: 0;">Customer Details:</h3>
    <p><strong>Name:</strong> ${name}</p>
    <p><strong>Email:</strong> <a href="mailto:${email}" style="color: #456ef7;">${email}</a></p>
    <p><strong>Phone:</strong> ${phone || "N/A"}</p>
  </div>
  
  <div style="background: #fff; padding: 20px; border-left: 4px solid #456ef7; margin: 20px 0;">
    <h3 style="color: #333; margin-top: 0;">Message:</h3>
    <p style="line-height: 1.6;">${message}</p>
  </div>
  
  <div style="text-align: center; margin-top: 30px; color: #666; font-size: 12px;">
    <p>This email was sent from the GeMore Nutrients website contact form.</p>
  </div>
</div>
`,
    });
    res.json({ success: true, message: "Message sent successfully!" });
  } catch (err) {
    console.error("Email error:", err);
    res.status(500).json({
      error: "Failed to send message. Please try again later.",
    });
  }
});
// Order confirmation endpoint
app.post("/send-order-confirmation", async (req, res) => {
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
    return res
      .status(400)
      .json({ success: false, error: "Missing required order information." });
  }

  try {
    let transporter = nodemailer.createTransporter({
      host: process.env.SMTP_HOST || "smtp.gmail.com",
      port: process.env.SMTP_PORT || 587,
      secure: process.env.SMTP_SECURE === "true" || false,
      auth: {
        user: process.env.SMTP_USER || "info@gemorenutralife.com",
        pass: process.env.SMTP_PASS || "your_app_password_here",
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
              <small>${item.flavor} - ${item.size}</small>
            </div>
          </div>
        </td>
        <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: center;">₹${
          item.price
        }</td>
        <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: center;">${
          item.quantity
        }</td>
        <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: center;">₹${
          item.price * item.quantity
        }</td>
      </tr>
    `
      )
      .join("");

    // Send confirmation email to customer
    await transporter.sendMail({
      from: `Ge More Nutralife <${
        process.env.SMTP_USER || "info@gemorenutralife.com"
      }>`,
      to: customerEmail,
      subject: `Order Confirmation - ${orderNumber}`,
      html: `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
          <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #456ef7; margin: 0;">Ge More Nutralife</h1>
            <p style="color: #666; margin: 5px 0;">Premium Sports Supplements</p>
          </div>
          
          <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h2 style="color: #28a745; margin-top: 0;">Order Confirmed! 🎉</h2>
            <p>Dear ${customerName},</p>
            <p>Thank you for your order! We're excited to help you on your fitness journey.</p>
            
            <div style="background: white; padding: 15px; border-radius: 5px; margin: 15px 0;">
              <h3 style="color: #333; margin-top: 0;">Order Details</h3>
              <p><strong>Order Number:</strong> ${orderNumber}</p>
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
          </div>
          
          <div style="margin: 20px 0;">
            <h3 style="color: #333;">Order Summary</h3>
            <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
              <thead>
                <tr style="background: #f8f9fa;">
                  <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Product</th>
                  <th style="padding: 10px; text-align: center; border-bottom: 2px solid #ddd;">Price</th>
                  <th style="padding: 10px; text-align: center; border-bottom: 2px solid #ddd;">Qty</th>
                  <th style="padding: 10px; text-align: center; border-bottom: 2px solid #ddd;">Total</th>
                </tr>
              </thead>
              <tbody>
                ${orderItemsHTML}
              </tbody>
            </table>
            
            <div style="text-align: right; margin-top: 20px;">
              <p style="margin: 5px 0;">Subtotal: ₹${subtotal}</p>
              <p style="margin: 5px 0;">Shipping: ₹${shipping}</p>
              <hr style="margin: 10px 0;">
              <p style="margin: 5px 0; font-size: 1.2em; font-weight: bold; color: #456ef7;">Total: ₹${total}</p>
            </div>
          </div>
          
          <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="color: #333; margin-top: 0;">Shipping Address</h3>
            <p style="margin: 0; line-height: 1.6;">
              ${customerName}<br>
              ${shippingAddress.address}<br>
              ${shippingAddress.city}, ${shippingAddress.state} - ${
        shippingAddress.pincode
      }<br>
              ${shippingAddress.country}<br>
              Phone: ${customerPhone}
            </p>
          </div>
          
          <div style="background: #456ef7; color: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
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
            <p style="color: #456ef7; margin: 5px 0;">📧 info@gemorenutralife.com</p>
            <p style="color: #456ef7; margin: 5px 0;">📞 +91 92117 98913</p>
          </div>
          
          <div style="text-align: center; margin-top: 20px;">
            <p style="color: #999; font-size: 0.9em;">© 2024 Ge More Nutralife. All rights reserved.</p>
          </div>
        </div>
      `,
    });

    // Send order notification to admin
    await transporter.sendMail({
      from: `Ge More Nutralife <${
        process.env.SMTP_USER || "info@gemorenutralife.com"
      }>`,
      to: "info@gemorenutralife.com",
      subject: `New Order Received - ${orderNumber}`,
      html: `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
          <h2 style="color: #456ef7; text-align: center;">New Order Received</h2>
          <hr style="border: none; height: 2px; background: linear-gradient(90deg, #456ef7, #ff6600);">
          
          <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="color: #333; margin-top: 0;">Order Information</h3>
            <p><strong>Order Number:</strong> ${orderNumber}</p>
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
            <p><strong>Total Amount:</strong> ₹${total}</p>
          </div>
          
          <div style="background: #fff; padding: 20px; border-left: 4px solid #456ef7; margin: 20px 0;">
            <h3 style="color: #333; margin-top: 0;">Customer Details</h3>
            <p><strong>Name:</strong> ${customerName}</p>
            <p><strong>Email:</strong> ${customerEmail}</p>
            <p><strong>Phone:</strong> ${customerPhone}</p>
            <p><strong>Address:</strong> ${shippingAddress.address}, ${
        shippingAddress.city
      }, ${shippingAddress.state} - ${shippingAddress.pincode}</p>
          </div>
          
          <div style="margin: 20px 0;">
            <h3 style="color: #333;">Order Items</h3>
            <table style="width: 100%; border-collapse: collapse;">
              <thead>
                <tr style="background: #f8f9fa;">
                  <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Product</th>
                  <th style="padding: 10px; text-align: center; border-bottom: 2px solid #ddd;">Price</th>
                  <th style="padding: 10px; text-align: center; border-bottom: 2px solid #ddd;">Qty</th>
                  <th style="padding: 10px; text-align: center; border-bottom: 2px solid #ddd;">Total</th>
                </tr>
              </thead>
              <tbody>
                ${orderItemsHTML}
              </tbody>
            </table>
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
    res
      .status(500)
      .json({ success: false, error: "Failed to send order confirmation" });
  }
});

app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
});
