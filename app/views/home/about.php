<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="content">
    <h2><?php echo $title; ?></h2>
    <p><?php echo $description; ?></p>

    <div class="about-section">
        <h3>About This Application</h3>
        <p>E-MES is built using a custom PHP MVC framework that provides:</p>
        <ul>
            <li>Clean URL routing</li>
            <li>Secure database connections</li>
            <li>Environment-based configuration</li>
            <li>Reusable components and layouts</li>
        </ul>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>