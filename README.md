# E-MES - PHP MVC Application

A lightweight PHP MVC (Model-View-Controller) framework for building web applications.

## Project Structure

```
e-mes/
├── app/
│   ├── controllers/       # Application controllers
│   │   └── HomeController.php
│   ├── models/           # Data models
│   │   └── User.php
│   ├── views/            # View templates
│   │   ├── layouts/      # Layout templates
│   │   │   ├── header.php
│   │   │   └── footer.php
│   │   └── home/         # Home views
│   │       ├── index.php
│   │       └── about.php
│   └── core/             # Core framework files
│       ├── App.php       # Main application router
│       ├── Controller.php # Base controller class
│       └── Database.php  # Database connection handler
├── config/               # Configuration files
│   ├── config.php       # Application configuration
│   ├── database.php     # Database configuration
│   ├── database.sql     # Database schema
│   └── env.php          # Environment loader
├── public/              # Public accessible files
│   ├── css/
│   │   └── style.css    # Main stylesheet
│   ├── js/
│   │   └── main.js      # Main JavaScript
│   ├── .htaccess        # URL rewriting rules
│   └── index.php        # Application entry point
├── .env                 # Environment variables (not in git)
├── .env.example         # Example environment file
├── .gitignore          # Git ignore rules
├── .htaccess           # Root URL rewriting
└── README.md           # This file
```

## Installation

### 1. Clone or Download

Place the project in your XAMPP htdocs folder:

```
c:\xampp\htdocs\e-mes
```

### 2. Configure Environment

Copy `.env.example` to `.env` and update the values:

```bash
cp .env.example .env
```

Edit `.env` file with your database credentials:

```
DB_HOST=localhost
DB_NAME=e_mes_db
DB_USER=root
DB_PASS=
```

### 3. Create Database

Import the database schema using phpMyAdmin or command line:

```bash
mysql -u root -p < config/database.sql
```

Or manually in phpMyAdmin:

1. Create a database named `e_mes_db`
2. Import the SQL file from `config/database.sql`

### 4. Configure Apache

Ensure `mod_rewrite` is enabled in Apache:

1. Open `httpd.conf` (usually in `c:\xampp\apache\conf\`)
2. Uncomment: `LoadModule rewrite_module modules/mod_rewrite.so`
3. Find `AllowOverride None` in the `<Directory>` section
4. Change it to: `AllowOverride All`
5. Restart Apache

### 5. Access Application

Open your browser and navigate to:

```
http://localhost/e-mes
```

## MVC Architecture

### Controllers

Located in `app/controllers/`

Create a new controller:

```php
<?php
class ProductController extends Controller {
    public function index() {
        // Load model
        $productModel = $this->model('Product');

        // Get data
        $products = $productModel->getAllProducts();

        // Load view with data
        $this->view('products/index', ['products' => $products]);
    }
}
```

### Models

Located in `app/models/`

Create a new model:

```php
<?php
class Product {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllProducts() {
        return $this->db->fetchAll("SELECT * FROM products");
    }
}
```

### Views

Located in `app/views/`

Create a new view:

```php
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="content">
    <h2>Products</h2>
    <?php foreach($products as $product): ?>
        <div class="product">
            <h3><?php echo $product['name']; ?></h3>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
```

## URL Routing

The application uses clean URLs:

- `http://localhost/e-mes/` → HomeController::index()
- `http://localhost/e-mes/home/about` → HomeController::about()
- `http://localhost/e-mes/product/view/5` → ProductController::view(5)

Pattern: `/{controller}/{method}/{param1}/{param2}/...`

## Database Usage

The Database class provides simple methods for queries:

```php
$db = new Database();

// Fetch all rows
$users = $db->fetchAll("SELECT * FROM users WHERE status = ?", ['active']);

// Fetch single row
$user = $db->fetch("SELECT * FROM users WHERE id = ?", [1]);

// Execute query
$db->query("UPDATE users SET status = ? WHERE id = ?", ['active', 1]);

// Get last insert ID
$id = $db->lastInsertId();
```

## Security Best Practices

1. **Never commit .env file** - It's already in .gitignore
2. **Use prepared statements** - Database class uses PDO with prepared statements
3. **Sanitize user input** - Use `$this->sanitize()` in controllers
4. **Hash passwords** - Use `password_hash()` and `password_verify()`
5. **Validate and sanitize** - Always validate input on both client and server side

## Git Usage

Initialize repository:

```bash
git init
git add .
git commit -m "Initial commit"
```

Before pushing to GitHub, ensure:

- `.env` file is listed in `.gitignore` ✓
- Only committed `.env.example` ✓
- No sensitive data in files ✓

## Default Login Credentials

If you imported the database.sql file:

- **Username:** admin
- **Email:** admin@e-mes.com
- **Password:** admin123

⚠️ **Important:** Change the default admin password immediately!

## Troubleshooting

### 404 Errors

- Check if `mod_rewrite` is enabled in Apache
- Verify `.htaccess` files exist in root and public folders
- Check `AllowOverride All` in Apache config

### Database Connection Failed

- Verify `.env` file exists and has correct credentials
- Ensure MySQL service is running
- Check if database exists

### Page Not Found

- Verify controller exists in `app/controllers/`
- Check that controller extends `Controller` class
- Ensure method is public

## Development

### Adding New Features

1. Create model in `app/models/`
2. Create controller in `app/controllers/`
3. Create views in `app/views/`
4. Add routes by accessing URL pattern

### Extending Core

Core files are in `app/core/`. Modify carefully as they affect the entire application.

## License

This project is open-source and available for educational and commercial use.

## Support

For issues and questions, please refer to the documentation or create an issue in the repository.

---

**Happy Coding! 🚀**
