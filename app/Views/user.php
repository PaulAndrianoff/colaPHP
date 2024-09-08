<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link rel="stylesheet" href="/colaPHP/public/css/styles.css">
</head>
<body>
    <h1>User Details</h1>
    <?php if (!empty($user)): ?>
        <p>ID: <?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?></p>
        <p>Name: <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></p>
        <p>Email: <?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php else: ?>
        <p>User not found.</p>
    <?php endif; ?>
</body>
</html>
