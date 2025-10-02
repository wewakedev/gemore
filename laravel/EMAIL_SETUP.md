# Email Configuration Setup Guide

## Updated Email Address
All email functionality has been updated to use: **info@gemorenutralife.com**

## What Changed
- ✅ Contact form submissions now go to `info@gemorenutralife.com`
- ✅ Order confirmations are sent from `info@gemorenutralife.com`
- ✅ Admin order notifications are sent to `info@gemorenutralife.com`
- ✅ Customer support emails reference `info@gemorenutralife.com`
- ✅ All documentation updated with new email address

## Environment Variables Setup

Create a `.env` file in your project root with the following configuration:

```env
# SMTP Configuration for info@gemorenutralife.com
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=false
SMTP_USER=info@gemorenutralife.com
SMTP_PASS=your_app_password_here

# Server Configuration
PORT=3000
```

## Gmail App Password Setup

1. **Enable 2-Step Verification** on the `info@gemorenutralife.com` Gmail account
2. **Generate App Password**:
   - Go to Google Account Settings
   - Security → 2-Step Verification
   - App passwords → Generate new app password
   - Select "Mail" as the app type
   - Copy the 16-character password

3. **Replace `your_app_password_here`** in the `.env` file with the generated app password

## Files Updated

### Backend (`server.js`)
- Contact form endpoint: `/send-contact`
- Order confirmation endpoint: `/send-order-confirmation`
- SMTP transporter configuration
- Email templates for customer and admin notifications

### Frontend (`index.html`)
- Structured data schema
- Contact section email link
- Support information

### Documentation
- `README.md`
- `STORE_README.md`
- All configuration examples

## Email Templates

### Contact Form Emails
- **To**: `info@gemorenutralife.com`
- **From**: `GeMore Nutrients Website <info@gemorenutralife.com>`
- **Subject**: "New Contact Form Submission - GeMore Nutrients"

### Order Confirmation Emails
- **Customer Email**: Sent to customer's email address
- **Admin Email**: Sent to `info@gemorenutralife.com`
- **From**: `Ge More Nutralife <info@gemorenutralife.com>`
- **Subject**: "Order Confirmation - [Order Number]" / "New Order Received - [Order Number]"

## Testing Email Configuration

1. **Test Contact Form**:
   - Fill out the contact form on the website
   - Check if email is received at `info@gemorenutralife.com`

2. **Test Order System**:
   - Place a test order through the store
   - Verify both customer and admin emails are received

3. **Check Email Logs**:
   - Monitor server console for email sending status
   - Check Gmail sent items for confirmation

## Troubleshooting

### Common Issues
- **Authentication failed**: Check app password is correct
- **Less secure app access**: Use App Password instead of regular password
- **SMTP timeout**: Verify SMTP_HOST and SMTP_PORT settings
- **Email not received**: Check spam folder, verify email address

### Debug Steps
1. Verify `.env` file is loaded correctly
2. Check server logs for SMTP errors
3. Test SMTP connection independently
4. Verify Gmail account settings

## Security Notes
- Never commit `.env` file to version control
- Use environment variables for sensitive data
- Keep app passwords secure and regenerate if compromised
- Monitor email sending logs for unusual activity

## Support
If you encounter issues with email configuration:
- 📧 Email: info@gemorenutralife.com
- 📞 Phone: +91 92117 98913 

## 🔧 **Update Your Environment Variables**

On your EC2 instance, run these commands:

### **Step 1: Navigate to your project and update .env file**

```bash
# First, find and navigate to your project directory
find ~ -name "gemore-main" -type d 2>/dev/null
cd /path/to/your/gemore-main  # Replace with the actual path found above

# Edit the .env file
nano .env
```

### **Step 2: Add your email configuration**

In the nano editor, add/update these lines:

```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=false
SMTP_USER=info@gemorenutralife.com
SMTP_PASS=gemoreinfo@123
PORT=3000
```

**To save in nano**: Press `Ctrl + X`, then `Y`, then `Enter`

### **Step 3: Complete deployment script**

```bash
#!/bin/bash
echo "🚀 Deploying with email configuration..."

# Pull latest changes
git pull origin main

# Install dependencies
npm install

# Update .env file with your credentials
cat > .env << 'EOF'
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=false
SMTP_USER=info@gemorenutralife.com
SMTP_PASS=gemoreinfo@123
PORT=3000
EOF

# Restart application
if command -v pm2 &> /dev/null; then
    pm2 restart all
    echo "✅ Restarted with PM2"
else
    sudo pkill node
    nohup node server.js > server.log 2>&1 &
    echo "✅ Started with nohup"
fi

echo ""
echo "🎉 Deployment Complete!"
echo "🌐 Website: http://3.110.50.120:3000"
echo "🛒 Store: http://3.110.50.120:3000/store.html"
echo "📧 Email configured for: info@gemorenutralife.com"
```

## ⚠️ **Important Gmail Security Note**

If you have **2-Factor Authentication** enabled on your Gmail account, you'll need to:

1. **Generate an App Password** instead:
   - Go to Google Account Settings
   - Security → 2-Step Verification
   - App passwords → Generate new app password
   - Use the 16-character password instead of `gemoreinfo@123`

2. **Update the .env file** with the App Password if needed

## 🧪 **Test Your Email Configuration**

After deployment, test your email:

1. **Visit**: http://3.110.50.120:3000
2. **Fill out the contact form**
3. **Check your inbox** at info@gemorenutralife.com
4. **Test the store**: http://3.110.50.120:3000/store.html
5. **Place a test order** to verify order confirmation emails

## 📊 **Check Application Status**

```bash
# Check if your app is running
pm2 status
# or
ps aux | grep node

# Check logs for any errors
tail -f server.log
# or
pm2 logs
```

## 🔍 **Troubleshooting Email Issues**

If emails don't work:

```bash
# Check server logs for email errors
tail -f server.log | grep -i smtp

# Test if the server is receiving form submissions
curl -X POST http://localhost:3000/send-contact \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","message":"Test message"}'
```

**Run the deployment script above and let me know if you encounter any issues!** 