<?php
// Shared header
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Creator Engine</title>
    <!-- Combined CSS -->
    <link rel="stylesheet" href="/assets/css/editor.css">
</head>
<body>
    <header class="topbar">
        <div class="logo">Website Creator Engine</div>
        <nav>
            <a href="/public/dashboard.php">Dashboard</a>
            <a href="/public/templates.php">Templates</a>
        </nav>
        <div class="auth-actions">
            <?php if (\App\Core\Auth::isLoggedIn()): ?>
                <a href="/api/auth/logout.php">Logout</a>
            <?php else: ?>
                <a href="/public/auth.php">Login / Register</a>
            <?php endif; ?>
        </div>
    </header>
