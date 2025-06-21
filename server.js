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
        user: process.env.SMTP_USER || "infoflynutrition@gmail.com",
        pass: process.env.SMTP_PASS || "your_app_password_here",
      },
    });
    await transporter.sendMail({
      from: `GeMore Nutrients Website <${
        process.env.SMTP_USER || "infoflynutrition@gmail.com"
      }>`,
      to: "infoflynutrition@gmail.com",
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
app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
});
