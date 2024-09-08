<?php
$title = 'Admin Panel - Create';
include __DIR__ . '/../parts/admin/admin_header.php';
?>
    <h1>Table: <?= $modelName ?></h1>
    <h2>Create New Record</h2>
    <form method="POST" action="create">
        <?php foreach ($columns as $column => $type): ?>
            <?php if (isset($errors[$column])): ?>
                <p style="color: red;"><?= $errors[$column]; ?></p>
            <?php endif; ?>
            <label for="<?= $column; ?>"><?= ucfirst($column); ?></label>
            <input type="text" id="<?= $column; ?>" name="<?= $column; ?>" required>
            <br>
        <?php endforeach; ?>
        <button type="submit">Create</button>
    </form>
</body>
</html>
