<?php
$title = 'Admin Login';
include __DIR__ . '/../parts/header.php';
?>
<body>
    <h1>Admin Login</h1>
    <form method="POST" action="<?= $formRoute ?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>
</body>
</html>
