# 🚀 Elevate Portal - Job Portal Application

A comprehensive job portal application built with PHP, MySQL, and modern web technologies following Object-Oriented Programming (OOP) principles and the Model-View-Controller (MVC) architectural pattern.

> **Status**: ✅ Fully Functional | 🔧 Production Ready | 🚀 XAMPP Compatible

## 📋 Table of Contents

- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Security Features](#security-features)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

### For Job Seekers
- **User Registration & Authentication** - Secure account creation and login
- **Job Search & Filtering** - Advanced search with location, job type, and experience filters
- **Job Applications** - Apply to jobs with cover letters and resume uploads
- **Application Tracking** - Monitor application status and history
- **Personalized Dashboard** - View applied jobs and recommendations

### For Employers
- **Company Profile Management** - Complete company information setup
- **Job Posting** - Create detailed job listings with requirements
- **Application Management** - Review and manage job applications
- **Candidate Communication** - Direct interaction with applicants
- **Analytics Dashboard** - Track job performance and applications

### General Features
- **Responsive Design** - Mobile-friendly interface
- **Security** - CSRF protection, SQL injection prevention, XSS protection
- **File Upload** - Secure resume upload with validation
- **Pagination** - Efficient data loading and navigation
- **Search Functionality** - Real-time job search capabilities

## 🛠 Technology Stack

| Component | Technology | Version |
|-----------|------------|---------|
| **Backend** | PHP | 8.x |
| **Database** | MySQL/MariaDB | 5.7+ |
| **Frontend** | HTML5, CSS3, JavaScript | - |
| **Framework** | Bootstrap | 5.3.0 |
| **Icons** | Font Awesome | 6.4.0 |
| **Server** | Apache (XAMPP/WAMP) | - |

## 📦 Installation

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache web server
- XAMPP/WAMP (recommended for local development)

### Quick Start (XAMPP)

1. **Download/Clone** the project to `C:\xampp\htdocs\Elevate`
2. **Start XAMPP** - Start Apache and MySQL services
3. **Run Installation** - Visit `http://localhost/Elevate/install.php`
4. **Access Application** - Visit `http://localhost/Elevate/public/`

### Manual Installation

#### Step 1: Set Up Web Server
1. Copy the project folder to your web server directory:
   - **XAMPP**: `C:\xampp\htdocs\Elevate`
   - **WAMP**: `C:\wamp64\www\Elevate`
   - **Linux**: `/var/www/html/Elevate`

#### Step 2: Database Setup
1. Start your MySQL server
2. Create database using phpMyAdmin or command line:
   ```sql
   CREATE DATABASE elevate_portal_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

#### Step 3: Run Installation
Visit the installation script in your browser:
```
http://localhost/Elevate/install.php
```

#### Step 4: Access the Application
```
http://localhost/Elevate/public/
```

### Apache Configuration Requirements
- **mod_rewrite** must be enabled
- **AllowOverride All** must be set for the htdocs directory
- **.htaccess** files must be allowed

## ⚙️ Configuration

### Database Configuration
Edit `config/database.php` to match your database settings:

```php
// Database connection constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'elevate_portal_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Application settings
define('APP_NAME', 'Elevate Portal');
define('APP_URL', 'http://localhost/Elevate/public');
```

### File Upload Settings
Adjust upload settings in `config/database.php`:

```php
define('UPLOAD_MAX_SIZE', 5242880); // 5MB
define('ALLOWED_UPLOAD_TYPES', ['pdf', 'doc', 'docx']);
```

## 🚀 Usage

### Demo Accounts
The application comes with pre-configured demo accounts:

**Employer Account:**
- Username: `admin_user`
- Password: `password`

**Job Seeker Account:**
- Username: `john_seeker`
- Password: `password`

### Getting Started

1. **For Job Seekers:**
   - Register a new account or use the demo account
   - Browse available jobs
   - Apply to positions that interest you
   - Track your applications from the dashboard

2. **For Employers:**
   - Register as an employer or use the demo account
   - Complete your company profile
   - Post job openings
   - Review and manage applications

## 📁 Project Structure

```
Elevate/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php      # Authentication & user management
│   │   └── JobController.php       # Job posting & application management
│   ├── Models/
│   │   ├── DatabaseModel.php       # Base database class
│   │   ├── UserModel.php           # User data operations
│   │   ├── JobModel.php            # Job data operations
│   │   ├── CompanyModel.php        # Company profile management
│   │   └── ApplicationModel.php    # Job application handling
│   └── Views/
│       ├── layout/                 # Header, footer, navigation
│       ├── auth/                   # Login, register pages
│       ├── jobs/                   # Job listings, details, forms
│       ├── employer/               # Employer dashboard & tools
│       ├── seeker/                 # Job seeker dashboard
│       ├── pages/                  # Static pages (about, contact)
│       └── errors/                 # Error pages (404, 500)
├── config/
│   └── database.php               # Database configuration
├── database/
│   └── migrations/                # Database schema files
├── public/
│   ├── index.php                  # Front controller (entry point)
│   └── .htaccess                  # Apache URL rewriting rules
├── uploads/
│   └── resumes/                   # User uploaded files
├── install.php                    # Installation wizard
├── SETUP_GUIDE.md                # Detailed setup instructions
├── development-guide.md           # Development documentation
└── README.md                      # This file
```

## 🔒 Security Features

- **Password Hashing** - Bcrypt encryption for secure password storage
- **SQL Injection Prevention** - PDO prepared statements
- **XSS Protection** - Input sanitization and output escaping
- **CSRF Protection** - Token-based form validation
- **Session Security** - Secure session management
- **File Upload Validation** - Type and size restrictions
- **Rate Limiting** - Login attempt restrictions

## 🧪 Testing

### Manual Testing Checklist

**Authentication:**
- [ ] User registration with validation
- [ ] Login with correct credentials
- [ ] Login failure with incorrect credentials
- [ ] Session management and logout

**Job Management:**
- [ ] Job creation by employers
- [ ] Job editing and deletion
- [ ] Job search and filtering
- [ ] Job application process

**Security:**
- [ ] SQL injection attempts
- [ ] XSS attack prevention
- [ ] CSRF token validation
- [ ] File upload restrictions

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 Development Guidelines

- Follow PSR-4 autoloading standards
- Use meaningful variable and function names
- Comment complex logic
- Validate all user inputs
- Use prepared statements for database queries
- Implement proper error handling

## ✅ Recent Updates

- **Fixed URL Routing** - All navigation and forms now work correctly
- **Improved Authentication** - Login/logout functionality fully operational  
- **Enhanced Security** - Fixed SQL parameter binding issues
- **Better Navigation** - User-aware header with proper login/logout states
- **XAMPP Compatibility** - Optimized for XAMPP development environment

## 🐛 Known Issues

- Email notifications not configured (future enhancement)
- File upload progress indicator not implemented
- Advanced search filters could be expanded

## 🔮 Future Enhancements

- [ ] Email notification system
- [ ] Advanced job matching algorithm
- [ ] Mobile application
- [ ] Social media integration
- [ ] Video interview scheduling
- [ ] Salary insights and analytics
- [ ] Multi-language support

## 📞 Support

If you encounter any issues or have questions:

1. Check the [development guide](development-guide.md)
2. Review the troubleshooting section
3. Open an issue on GitHub
4. Contact support at support@elevateportal.com

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Bootstrap team for the excellent CSS framework
- Font Awesome for the comprehensive icon library
- PHP community for continuous improvements
- All contributors and testers

---

**Built with ❤️ in Nepal**

For more detailed technical information, please refer to the [Development Guide](development-guide.md).
