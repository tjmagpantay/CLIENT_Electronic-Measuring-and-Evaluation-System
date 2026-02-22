<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="hero">
    <h2><?php echo $title; ?></h2>
    <p><?php echo $description; ?></p>
</div>

<div class="content">
    <h3>Getting Started</h3>
    <p>Your PHP MVC application is up and running!</p>

    <div class="features">
        <div class="feature-card">
            <h4>MVC Architecture</h4>
            <p>Clean separation of concerns with Model-View-Controller pattern</p>
        </div>

        <div class="feature-card">
            <h4>Database Ready</h4>
            <p>PDO-based database layer configured and ready to use</p>
        </div>

        <div class="feature-card">
            <h4>Environment Variables</h4>
            <p>Secure configuration management with .env file</p>
        </div>

        <div class="feature-card">
            <h4>Git Ready</h4>
            <p>Pre-configured .gitignore for safe version control</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>