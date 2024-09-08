<?php
$title = 'Admin Panel';
include __DIR__ . '/../parts/admin/admin_header.php';
?>
    <h1>Configuration Panel</h1>
    <form id="style-config-form" method="POST" action="">
        <?php foreach ($data as $key => $value): ?>
            <label for="<?php echo $key; ?>"><?php echo ucfirst(str_replace('_', ' ', $key)); ?>:</label>
            <?php if (strpos($key, 'color') !== false): ?>
                <!-- Use color input for color values -->
                <input type="color" id="<?php echo $key; ?>" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
            <?php else: ?>
                <!-- Use text input for other values (font sizes, border radius, etc.) -->
                <input type="text" id="<?php echo $key; ?>" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
            <?php endif; ?>
            <br>
        <?php endforeach; ?>

        <button type="submit">Save Changes</button>
    </form>
</body>

</html>