<?php

// ============================================================================
// PHP Setups
// ============================================================================

date_default_timezone_set('Asia/Kuala_Lumpur');

// Is GET request?
function is_get() {
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post() {
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain GET parameter
function get($key, $value = null) {
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain POST parameter
function post($key, $value = null) {
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null) {
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Redirect to URL
function redirect($url = null) {
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}

// Set or get temporary session variable
function temp($key, $value = null) {
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    }
    else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

// ============================================================================
// HTML Helpers
// ============================================================================

// Encode HTML special characters
function encode($value) {
    return htmlentities($value);
}

// Generate <input type='text'>
function html_text($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='radio'> list
function html_radios($key, $items, $br = false) {
    $value = encode($GLOBALS[$key] ?? '');
    echo '<div>';
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'checked' : '';
        echo "<label><input type='radio' id='{$key}_$id' name='$key' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}

// Generate <select>
function html_select($key, $items, $default = '- Select One -', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// Generate menu
function menu($name, $price, $ingredients, $image) {
    echo "<div class='menu'>";
    echo "<img class='menu' src='/images/product/$image' alt='$name'>";
    echo "<h1>$name RM&nbsp;$price</h1>";
    echo "<span>Ingredients</span>";
    echo "<ul>";
    foreach ($ingredients as $ingredient) {
        echo "<li>$ingredient</li>";
    }
    echo "</ul>";
    echo "<button class='cta'>Buy Now</button>";
    echo "</div>";
}

// Generate product
function product($name, $price, $image) {
    echo "<div class='product'>";
    echo "<img class='product' src='/images/product/$image' alt='$name'>";
    echo "<h1>$name RM&nbsp;$price</h1>";
    echo "</div>";
}

// Generate aboutus_container
function aboutus_container($title_class, $title_id, $content_class, $content_id, $title, $content = null) {
    echo "<div class='aboutus_container'>";
    echo "<div class='$title_class' id='$title_id'>$title</div>";
    echo "<div class='$content_class' id='$content_id'>";
    echo $content ?? null;
    echo "</div>";
    echo "</div>";
}

// Genrate staff_container
function staff_container($role, $image, $content) {
    echo "<div class='staff_role'>$role</div>";
    echo "<div class='staff_container'>";
    echo "<img class='aboutus-images' src='/images/aboutus/$image' alt='$image'>";
    echo "<p>$content</p>";
    echo "</div>";
}

// Generate faq_container
function faq_container($title, $content) {
    echo "<div class='faq_container'>";
    echo "<div class='faq_q'>$title</div>";
    echo "<div class='faq_a_container'>" . $content ?? null . "</div>";
    echo "</div>";
}

// Generate contacts_section
function contacts_section($title, $content) {
    echo "<div class='contacts_section'>";
    echo "<div class='contacts_title'>$title</div>";
    echo "<div class='contacts_content'>" . $content ?? null . "</div>";
    echo "</div>";
}

// ============================================================================
// Error Handlings
// ============================================================================

// Global error array
$_err = []; // Array of error messages

// Generate <span class='err'>
function err($key) {
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<span class='err'>$_err[$key]</span>";
    }
    else {
        echo '<span></span>';
    }
}

// ============================================================================
// Global Constants and Variables
// ============================================================================
