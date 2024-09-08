<?php
$title = 'Admin Panel - Create';
include __DIR__ . '/../parts/admin/admin_header.php';
?>
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
