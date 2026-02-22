<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'E-MES'; ?></title>
    <link rel="stylesheet" href="<?php echo env('APP_URL'); ?>/public/css/style.css">
</head>

<body>
    <header>
        <nav>
            <div class="container">
                <h1>E-MES</h1>
                <ul>
                    <li><a href="<?php echo env('APP_URL'); ?>">Home</a></li>
                    <li><a href="<?php echo env('APP_URL'); ?>/home/about">About</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">