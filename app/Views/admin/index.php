<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="/colaPHP/public/css/styles.css">
</head>
<body>
    <h1>Welcome to the Admin Panel</h1>
    <ul>
        <li><a href="<?= $configuration ?>">Configuration Panel</a></li>
        <li><a href="<?= $logout ?>">Logout</a></li>
    </ul>
    <h2>Models</h2>
    <ul>
        <?php foreach ($models as $model): ?>
            <li>
                <a href="admin/models/<?php echo strtolower($model); ?>"><?php echo ucfirst($model); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
