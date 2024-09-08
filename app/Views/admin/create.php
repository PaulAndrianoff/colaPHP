<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Create</title>
    <link rel="stylesheet" href="/colaPHP/public/css/styles.css">
</head>
<body>
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
