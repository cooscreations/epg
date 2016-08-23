	<?php
/* Recursive helper functions */
function addslashes_recursive($value) {
    if (is_array($value)) {
        foreach ($value as $index => $val) {
            $value[$index] = addslashes_recursive($val);
        }
        return $value;
    } else {
        return addslashes($value);
    }
}
function stripslashes_recursive($value) {
    if (is_array($value)) {
        foreach ($value as $index => $val) {
            $value[$index] = stripslashes_recursive($val);
        }
        return $value;
    } else {
        return stripslashes($value);
    }
}

/* Use this if you want addslashes() to be run on all incoming data */
if (!get_magic_quotes_gpc()) {
    // Recursively apply addslashes() to all data
    $_GET = addslashes_recursive($_GET);
    $_POST = addslashes_recursive($_POST);
    $_COOKIE = addslashes_recursive($_COOKIE);
    $_REQUEST = addslashes_recursive($_REQUEST);
}

/* Use this if you do NOT want addslashes() to be run on all incoming data */
if (get_magic_quotes_gpc()) {
    // Recursively apply stripslashes() to all data
    $_GET = stripslashes_recursive($_GET);
    $_POST = stripslashes_recursive($_POST);
    $_COOKIE = stripslashes_recursive($_COOKIE);
    $_REQUEST = stripslashes_recursive($_REQUEST);
}
?>
