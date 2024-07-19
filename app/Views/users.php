<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
</head>
<body>
    <h1>Users</h1>
    <ul>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <li>
                    <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?> -
                    <?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>No users found.</li>
        <?php endif; ?>
    </ul>
</body>
</html>
