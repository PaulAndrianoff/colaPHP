<?php
header("Content-type: text/css");

$configFile = __DIR__ . '/../../app/config/style.json';

if (file_exists($configFile)) {
    $config = json_decode(file_get_contents($configFile), true);
} else {
    $config = [
        "color_primary" => "#007BFF",
        "color_secondary" => "#6C757D",
        "color_success" => "#28A745",
        "color_danger" => "#DC3545",
        "color_warning" => "#FFC107",
        "color_info" => "#17A2B8",
        "color_light" => "#F8F9FA",
        "color_dark" => "#343A40",
        "color_white" => "#FFFFFF",
        "color_black" => "#000000",

        "font_size_small" => "0.8rem",
        "font_size_base" => "1rem",
        "font_size_large" => "1.2rem",
        "font_size_xl" => "1.5rem",

        "font_family_sans" => "'Helvetica Neue', Arial, sans-serif",
        "font_family_serif" => "'Times New Roman', serif",
        "font_family_mono" => "'Courier New', monospace",

        "spacing_xs" => "0.25rem",
        "spacing_sm" => "0.5rem",
        "spacing_md" => "1rem",
        "spacing_lg" => "1.5rem",
        "spacing_xl" => "2rem",

        "border_radius_sm" => "0.2rem",
        "border_radius_md" => "0.5rem",
        "border_radius_lg" => "1rem",

        "shadow_sm" => "0px 1px 3px rgba(0, 0, 0, 0.1)",
        "shadow_md" => "0px 4px 6px rgba(0, 0, 0, 0.1)",
        "shadow_lg" => "0px 10px 20px rgba(0, 0, 0, 0.2)"
    ];
}
?>

:root {
    --color-primary: <?php echo $config['color_primary']; ?>;
    --color-secondary: <?php echo $config['color_secondary']; ?>;
    --color-success: <?php echo $config['color_success']; ?>;
    --color-danger: <?php echo $config['color_danger']; ?>;
    --color-warning: <?php echo $config['color_warning']; ?>;
    --color-info: <?php echo $config['color_info']; ?>;
    --color-light: <?php echo $config['color_light']; ?>;
    --color-dark: <?php echo $config['color_dark']; ?>;
    --color-white: <?php echo $config['color_white']; ?>;
    --color-black: <?php echo $config['color_black']; ?>;

    /* Font sizes */
    --font-size-small: <?php echo $config['font_size_small']; ?>;
    --font-size-base: <?php echo $config['font_size_base']; ?>;
    --font-size-large: <?php echo $config['font_size_large']; ?>;
    --font-size-xl: <?php echo $config['font_size_xl']; ?>;

    /* Font family */
    --font-family-sans: <?php echo $config['font_family_sans']; ?>;
    --font-family-serif: <?php echo $config['font_family_serif']; ?>;
    --font-family-mono: <?php echo $config['font_family_mono']; ?>;

    /* Spacing */
    --spacing-xs: <?php echo $config['spacing_xs']; ?>;
    --spacing-sm: <?php echo $config['spacing_sm']; ?>;
    --spacing-md: <?php echo $config['spacing_md']; ?>;
    --spacing-lg: <?php echo $config['spacing_lg']; ?>;
    --spacing-xl: <?php echo $config['spacing_xl']; ?>;

    /* Borders */
    --border-radius-sm: <?php echo $config['border_radius_sm']; ?>;
    --border-radius-md: <?php echo $config['border_radius_md']; ?>;
    --border-radius-lg: <?php echo $config['border_radius_lg']; ?>;

    /* Shadows */
    --shadow-sm: <?php echo $config['shadow_sm']; ?>;
    --shadow-md: <?php echo $config['shadow_md']; ?>;
    --shadow-lg: <?php echo $config['shadow_lg']; ?>;
}
