# Local Government Monitoring and Evaluation System (LGMES) - PHP MVC Application

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

