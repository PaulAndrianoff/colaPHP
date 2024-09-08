<?php
$title = 'User Details';
include __DIR__ . '/parts/header.php';
?>
<body>
    <h1>User Details</h1>
    <?php if (!empty($user)): ?>
        <p>ID: <?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?></p>
        <p>Name: <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php else: ?>
        <p>User not found.</p>
    <?php endif; ?>
</body>
</html>
