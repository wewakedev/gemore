<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank you for contacting Gemore</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .message-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .contact-info {
            margin: 15px 0;
        }
        .contact-info strong {
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">GEMORE</div>
        <p>Premium Fitness Supplements</p>
    </div>

    <div class="content">
        <h2>Thank you for contacting us!</h2>
        
        <p>Dear {{ $contact->name }},</p>
        
        <p>We have received your message and appreciate you taking the time to contact us. Our team will review your inquiry and get back to you as soon as possible.</p>
        
        <div class="message-details">
            <h3>Your Message Details:</h3>
            <div class="contact-info">
                <strong>Name:</strong> {{ $contact->name }}
            </div>
            <div class="contact-info">
                <strong>Email:</strong> {{ $contact->email }}
            </div>
            @if($contact->phone)
            <div class="contact-info">
                <strong>Phone:</strong> {{ $contact->phone }}
            </div>
            @endif
            @if($contact->subject)
            <div class="contact-info">
                <strong>Subject:</strong> {{ $contact->subject }}
            </div>
            @endif
            <div class="contact-info">
                <strong>Message:</strong><br>
                {{ $contact->message }}
            </div>
            <div class="contact-info">
                <strong>Submitted on:</strong> {{ $contact->created_at->format('F j, Y \a\t g:i A') }}
            </div>
        </div>

        <p>We typically respond to inquiries within 24-48 hours during business days. If your inquiry is urgent, please don't hesitate to call us directly.</p>
        
        <p>Thank you for choosing Gemore for your fitness supplement needs!</p>
        
        <p>Best regards,<br>
        The Gemore Team</p>
    </div>

    <div class="footer">
        <p>This is an automated confirmation email. Please do not reply to this email.</p>
        <p>If you need immediate assistance, please contact us directly.</p>
        <p>&copy; {{ date('Y') }} Gemore. All rights reserved.</p>
    </div>
</body>
</html>
