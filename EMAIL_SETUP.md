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