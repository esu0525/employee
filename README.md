# Employee Management System - PHP Version

A beautiful and functional employee management system built with PHP, MySQL, and modern CSS design.

## Features

### 🎨 Modern Aesthetic Design
- Gradient color schemes (indigo, purple, pink, blue)
- Responsive layout with mobile-friendly sidebar
- Beautiful stat cards with icons and animations
- Smooth transitions and hover effects
- Professional table styling

### 👥 Master List
- View all active employees
- Search functionality (name, position, department, ID)
- Add new employees with comprehensive form
- Click employee names to view detailed profiles
- Export functionality
- Statistics cards (Total Active, Departments, Search Results)

### 📋 Employee Details
- Complete personal and work information
- Upload up to 5 PDF documents per employee
- Download and delete documents
- Organized information sections:
  - Work Information (ID, Department, Email, Phone, Date Joined)
  - Personal Information (DOB, Address, Emergency Contacts)
  - Documents Management

### 📚 History Module
Four categorized sub-modules with color-coded tabs:
- **Inactive** (Gray theme) - Inactive employees
- **Resign** (Orange theme) - Resigned employees
- **Retired** (Purple theme) - Retired employees
- **Transfer** (Blue theme) - Transferred employees with locations

### 📝 Request List
- View and manage employee requests
- Filter by status (All, Pending, Approved, Rejected)
- Search functionality
- Statistics dashboard
- Action buttons for pending requests
- Color-coded request types:
  - Leave (Blue)
  - Transfer (Purple)
  - Resignation (Red)
  - Update (Green)

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Or use XAMPP/WAMP/MAMP for local development

### Setup Instructions

1. **Clone or Download the Project**
   ```bash
   # Place the php-employee-system folder in your web server directory
   # For XAMPP: C:/xampp/htdocs/php-employee-system
   # For WAMP: C:/wamp64/www/php-employee-system
   ```

2. **Create Database**
   - Open phpMyAdmin or MySQL command line
   - Create a new database named `employee_management`
   - Import the database schema:
     ```bash
     mysql -u root -p employee_management < database.sql
     ```
   - Or copy and paste the contents of `database.sql` into phpMyAdmin SQL tab

3. **Configure Database Connection**
   - Open `includes/db.php`
   - Update the database credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');  // Your MySQL password
     define('DB_NAME', 'employee_management');
     ```

4. **Set File Permissions**
   ```bash
   # Create uploads directory and set permissions
   mkdir uploads
   chmod 777 uploads  # On Linux/Mac
   # On Windows, right-click folder > Properties > Security > Allow write permissions
   ```

5. **Access the Application**
   - Open your browser and navigate to:
     ```
     http://localhost/php-employee-system/
     ```

## File Structure

```
php-employee-system/
├── assets/
│   └── styles.css              # Main stylesheet with all design
├── includes/
│   ├── db.php                  # Database connection and utilities
│   ├── header.php              # Common header with sidebar
│   └── footer.php              # Common footer with scripts
├── uploads/                    # Directory for uploaded documents
├── database.sql                # Database schema and sample data
├── index.php                   # Master List page
├── add-employee.php            # Handle add employee form submission
├── employee-details.php        # Employee details and document upload
├── history.php                 # Redirect to history pages
├── history-inactive.php        # Inactive employees
├── history-resign.php          # Resigned employees
├── history-retired.php         # Retired employees
├── history-transfer.php        # Transferred employees
├── requests.php                # Request management
└── README.md                   # This file
```

## Database Schema

### Tables

#### employees
- `id` - Employee ID (Primary Key)
- `name` - Full name
- `position` - Job position
- `department` - Department
- `email` - Email address
- `phone` - Phone number
- `date_joined` - Date employee joined
- `status` - active/inactive/resign/retired/transfer
- `status_date` - Date of status change
- `transfer_location` - Location if transferred
- `address` - Home address
- `date_of_birth` - Date of birth
- `emergency_contact` - Emergency contact name
- `emergency_phone` - Emergency contact phone
- `created_at` - Record creation timestamp
- `updated_at` - Record update timestamp

#### employee_documents
- `id` - Document ID (Primary Key, Auto Increment)
- `employee_id` - Foreign key to employees
- `document_name` - Original file name
- `file_path` - Path to uploaded file
- `upload_date` - Upload timestamp

#### requests
- `id` - Request ID (Primary Key)
- `employee_id` - Foreign key to employees
- `employee_name` - Employee name
- `request_type` - leave/transfer/resignation/update
- `request_date` - Date of request
- `status` - pending/approved/rejected
- `description` - Request description
- `created_at` - Record creation timestamp
- `updated_at` - Record update timestamp

## Sample Data

The database comes pre-populated with:
- 10 sample employees with various statuses
- 6 sample requests with different types and statuses

## Features in Detail

### Add Employee
- Comprehensive form with validation
- Required fields: Name, Position, Department, Email, Phone, Date Joined
- Optional fields: Address, Date of Birth, Emergency Contact Info
- Auto-generated employee IDs (EMP001, EMP002, etc.)
- Success/error messages

### Employee Details & Documents
- View complete employee information
- Upload multiple PDF files (max 5 per employee)
- File validation (PDF only)
- Download documents
- Delete documents with confirmation
- Real-time document count
- Alert messages for upload limits

### Search & Filter
- Real-time search across all pages
- Filter requests by status
- Search by multiple fields (name, ID, position, department)
- Instant results

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Safari
- Edge
- Opera

## Security Notes

⚠️ **Important**: This is a demonstration application. For production use, you should:

1. Use prepared statements (already implemented in some queries)
2. Add user authentication and authorization
3. Implement CSRF protection
4. Add input sanitization and validation
5. Use HTTPS for secure communication
6. Implement file upload security (file type verification, size limits)
7. Add rate limiting
8. Implement proper error handling
9. Use environment variables for sensitive data

## Customization

### Colors
Edit `assets/styles.css` to change:
- Gradient colors
- Sidebar colors
- Badge colors
- Stat card colors

### Database
Modify `includes/db.php` for:
- Different database credentials
- Additional utility functions

### Icons
Uses Lucide Icons (loaded via CDN)
- Explore more icons at: https://lucide.dev/icons

## Troubleshooting

### "Connection failed" error
- Check database credentials in `includes/db.php`
- Ensure MySQL service is running
- Verify database exists

### File upload not working
- Check `uploads/` directory exists
- Verify directory has write permissions
- Check PHP upload settings in `php.ini`:
  ```ini
  upload_max_filesize = 10M
  post_max_size = 10M
  ```

### Icons not showing
- Check internet connection (icons load from CDN)
- Verify `<script src="https://unpkg.com/lucide@latest"></script>` is loading

### Styling issues
- Clear browser cache
- Check `assets/styles.css` is loading
- Verify file path is correct

## Support

For issues or questions:
1. Check this README
2. Review database.sql for schema
3. Check browser console for JavaScript errors
4. Check PHP error logs

## License

This is a demonstration project for educational purposes.

## Credits

- Design: Modern gradient aesthetics with Tailwind-inspired styling
- Icons: Lucide Icons (https://lucide.dev)
- Database: MySQL
- Backend: PHP
