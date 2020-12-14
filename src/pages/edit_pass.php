<?php
include_once('../templates/tpl_common.php');
include_once('../database/user_queries.php');
include_once('../templates/tpl_edit_pass.php');
include_once('../database/db_user.php');
include_once("security_functions.php");

session_start();
if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = generate_random_token();
}

if(isset($_SESSION['user']) && checkUser($_SESSION['user']) ) {
    echo '<script src="../js/utils.js" defer></script>';
    echo '<script src="../js/edit_pass.js" defer></script>';
    draw_head($_SESSION['user']." Edit Password");
    draw_header();
    draw_edit_pass();
    echo '</div>';
    draw_footer();
}
else {
    header('Location: ' . '../index.php');
}
?>