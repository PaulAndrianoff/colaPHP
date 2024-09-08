<?php
$title = 'Users';
include __DIR__ . '/parts/header.php';
?>
<body>
    <h1>Users</h1>
    <ul>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <li>
                    <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>No users found.</li>
        <?php endif; ?>
    </ul>
</body>
</html>
