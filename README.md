# Global News Network - Complete News Website

A modern, responsive news website built with PHP, MySQL, and CSS3 featuring dynamic content management, user authentication, and search functionality.

## 🚀 Features

### Frontend
- **Responsive Design** - Works perfectly on desktop, tablet, and mobile devices
- **Modern UI/UX** - Clean, professional design with smooth animations
- **HTML5 Semantic Elements** - Proper structure with header, main, aside, and footer
- **CSS3 Styling** - Modern gradients, shadows, and hover effects

### Backend
- **Dynamic Content Loading** - Articles loaded from MySQL database
- **Search Functionality** - Real-time search across articles
- **User Authentication** - Registration and login system
- **Admin Panel** - Complete CRUD operations for articles
- **Pagination** - Efficient content loading for large datasets

## 📁 File Structure

```
Uni/
├── index.php          # Main website with dynamic content
├── login.php          # User authentication page
├── admin.php          # Admin panel for content management
├── config.php         # Database configuration
├── auth.php           # Authentication system
├── news.php           # News management and API
├── styles.css         # Main stylesheet
└── README.md          # This file
```

## 🛠️ Setup Instructions

### Prerequisites
- XAMPP, WAMP, or similar local server
- PHP 7.4+ 
- MySQL 5.7+

### Installation

1. **Start your local server** (XAMPP/WAMP)
2. **Place files** in your web server directory (e.g., `htdocs/Uni/`)
3. **Access the website** at `http://localhost/Uni/`

### Database Setup
The database will be automatically created on first visit with:
- Sample articles
- Default categories (Politics, Technology, Sports, etc.)
- Admin user account

### Default Admin Account
- **Username:** admin
- **Password:** admin123

## 🎯 Key Features

### User Authentication
- User registration and login
- Session management
- Role-based access (user/admin)

### Content Management
- Create, edit, and delete articles
- Mark articles as featured or breaking news
- Category management
- View tracking

### Search & Filtering
- Real-time search functionality
- Category-based filtering
- AJAX-powered content loading

### Responsive Design
- Mobile-first approach
- Flexible grid layouts
- Touch-friendly interactions

## 🔧 Customization

### Adding New Categories
Edit the `initializeDatabase()` function in `config.php`:

```php
$categories = [
    ['Your Category', 'your-category', 'fas fa-icon'],
    // Add more categories here
];
```

### Styling
Modify `styles.css` to customize:
- Color scheme
- Typography
- Layout spacing
- Animations

### Database Configuration
Update database settings in `config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database');
```

## 📱 Responsive Breakpoints

- **Desktop:** 1200px+
- **Tablet:** 768px - 1199px
- **Mobile:** < 768px

## 🎨 Design Features

- **Gradient Backgrounds** - Modern purple/blue theme
- **Card-based Layout** - Clean article presentation
- **Hover Effects** - Interactive elements with smooth transitions
- **Typography** - Professional font hierarchy
- **Icons** - Font Awesome integration

## 🔒 Security Features

- **Password Hashing** - Secure password storage
- **SQL Injection Prevention** - Prepared statements
- **XSS Protection** - HTML escaping
- **Session Security** - Proper session management

## 🚀 Performance Optimizations

- **Efficient Queries** - Optimized database queries
- **Lazy Loading** - AJAX content loading
- **CSS Optimization** - Minimal, efficient stylesheets
- **Image Optimization** - Icon-based placeholders

## 📈 Future Enhancements

- Image upload functionality
- Rich text editor for articles
- User comments system
- Social media integration
- Newsletter subscription
- Advanced analytics

## 🤝 Contributing

Feel free to enhance this project by:
- Adding new features
- Improving the design
- Optimizing performance
- Adding more security measures

## 📄 License

This project is open source and available under the MIT License.

---

**Built with ❤️ for modern web development** 