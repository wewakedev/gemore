# Ge More Nutralife Store - E-commerce Documentation

## Overview
A complete e-commerce store implementation for Ge More Nutralife featuring product catalog, shopping cart, wishlist, and full checkout functionality.

## Features

### 🛍️ Product Catalog
- **6 Premium Products**: Whey Protein, Pre-Workout (2 flavors), Creatine, Premium Kesar, Pure Unflavoured
- **Smart Filtering**: Filter by category (All, Protein, Pre-Workout, Creatine, Premium)
- **Advanced Sorting**: Sort by price, name, popularity
- **Product Details**: High-quality images, descriptions, ratings, reviews, stock status
- **Interactive Cards**: Hover effects, wishlist toggle, product badges

### 🛒 Shopping Cart
- **Persistent Storage**: Cart data saved in localStorage
- **Quantity Management**: Increase/decrease quantities, remove items
- **Real-time Updates**: Live cart count and total calculation
- **Sidebar Display**: Smooth slide-out cart interface
- **Auto-calculations**: Subtotal, shipping (free over ₹1500), total

### ❤️ Wishlist
- **Save for Later**: Add/remove products from wishlist
- **Persistent Storage**: Wishlist data saved in localStorage
- **Quick Add to Cart**: One-click add to cart from wishlist
- **Visual Feedback**: Heart icon toggle states

### 💳 Checkout Process
- **3-Step Checkout**: Shipping → Payment → Confirmation
- **Form Validation**: Comprehensive form validation with error messages
- **Multiple Payment Options**: 
  - Cash on Delivery (COD)
  - UPI Payment
  - Credit/Debit Card
- **Order Summary**: Complete order breakdown with items and totals
- **Email Confirmations**: Automated emails to customer and admin

## Technical Implementation

### Frontend
- **HTML5**: Semantic, accessible markup
- **CSS3**: Modern styling with animations and responsive design
- **JavaScript**: ES6+ with class-based architecture
- **Bootstrap**: Grid system and responsive utilities
- **Font Awesome**: Icons for better UX

### Backend
- **Node.js**: Server-side JavaScript runtime
- **Express.js**: Web application framework
- **Nodemailer**: Email service for order confirmations
- **Environment Variables**: Secure configuration management

### Key Files
```
├── store.html              # Main store page
├── css/
│   └── store.css          # Store-specific styles
├── js/
│   └── store.js           # Store functionality
├── server.js              # Backend server with order endpoints
└── images/                # Product images
```

## Usage Guide

### 1. Product Browsing
- Visit `/store.html` to view the product catalog
- Use filter buttons to browse by category
- Sort products using the dropdown menu
- Click product cards to view details

### 2. Shopping Cart
- Click "Add to Cart" on any product
- Access cart via the cart icon in navigation
- Modify quantities using +/- buttons
- Remove items using the trash icon
- View live total calculations

### 3. Wishlist
- Click the heart icon on any product
- Access wishlist via the heart icon in navigation
- Add wishlist items to cart
- Remove items from wishlist

### 4. Checkout Process
- Click "Checkout" in cart sidebar
- **Step 1**: Fill shipping information
- **Step 2**: Select payment method and review order
- **Step 3**: View order confirmation

### 5. Order Management
- Orders generate unique order numbers (GMN######)
- Email confirmations sent to customer and admin
- Order details include shipping address, items, and payment method

## Product Data Structure
```javascript
{
  id: 1,
  name: 'Nutralife Whey Protein',
  description: 'Premium quality whey protein isolate...',
  price: 2499,
  originalPrice: 2999,
  image: 'images/WHEY PROTEIN 2 KG CHOCOLATE.jpg',
  category: 'protein',
  stock: 50,
  rating: 4.8,
  reviews: 256,
  badge: 'Best Seller',
  flavor: 'Chocolate',
  size: '2kg'
}
```

## Configuration

### Environment Variables
```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=false
SMTP_USER=info@gemorenutralife.com
SMTP_PASS=your_app_password_here
PORT=3000
```

### Email Templates
- **Customer Confirmation**: Professional order confirmation with complete details
- **Admin Notification**: Order details for processing and fulfillment

## Responsive Design
- **Mobile-First**: Optimized for all screen sizes
- **Breakpoints**: 
  - Desktop: 1200px+
  - Tablet: 768px - 1199px
  - Mobile: < 768px
- **Touch-Friendly**: Large buttons and easy navigation

## Security Features
- **Input Validation**: Form validation on both client and server
- **Error Handling**: Comprehensive error messages and fallbacks
- **Data Sanitization**: Safe handling of user inputs
- **Environment Variables**: Secure configuration management

## Performance Optimizations
- **Lazy Loading**: Product images loaded as needed
- **Local Storage**: Cart and wishlist data cached locally
- **Minified Assets**: Optimized CSS and JavaScript
- **Efficient DOM Manipulation**: Minimal reflows and repaints

## Browser Support
- ✅ Chrome 70+
- ✅ Firefox 65+
- ✅ Safari 12+
- ✅ Edge 79+
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## Installation & Setup

1. **Clone the repository**
2. **Install dependencies**:
   ```bash
   npm install
   ```
3. **Configure environment variables**:
   - Create `.env` file with SMTP settings
4. **Start the server**:
   ```bash
   npm start
   ```
5. **Access the store**:
   - Open `http://localhost:3000/store.html`

## API Endpoints

### POST /send-order-confirmation
Sends order confirmation emails to customer and admin.

**Request Body:**
```json
{
  "orderNumber": "GMN123456",
  "customerName": "John Doe",
  "customerEmail": "john@example.com",
  "customerPhone": "9876543210",
  "shippingAddress": {
    "address": "123 Main St",
    "city": "Mumbai",
    "state": "Maharashtra",
    "pincode": "400001",
    "country": "India"
  },
  "items": [
    {
      "id": 1,
      "name": "Nutralife Whey Protein",
      "price": 2499,
      "quantity": 1,
      "flavor": "Chocolate",
      "size": "2kg",
      "image": "images/product.jpg"
    }
  ],
  "paymentMethod": "cod",
  "timestamp": "2024-01-01T00:00:00.000Z"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Order confirmation sent successfully"
}
```

## Future Enhancements
- [ ] User authentication and accounts
- [ ] Order tracking system
- [ ] Product reviews and ratings
- [ ] Inventory management
- [ ] Coupon and discount system
- [ ] Multi-language support
- [ ] Advanced search functionality
- [ ] Product recommendations
- [ ] Social media integration
- [ ] Analytics and reporting

## Support
For technical support or questions:
- 📧 Email: info@gemorenutralife.com
- 📞 Phone: +91 92117 98913

## License
© 2024 Ge More Nutralife. All rights reserved. 