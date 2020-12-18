<?php
include_once('../templates/tpl_common.php');
include_once("../templates/tpl_add_pet.php");
include_once("security_functions.php");
session_start();
if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = generate_random_token();
}
echo '<script src="../js/utils.js" defer></script>';
echo '<script src="../js/add_pet.js" defer></script>';
draw_head("Add Pet");
$location = '<a href="main.php">main </a> > <a href="add_pet.php"> add_pet</a>';
draw_header($location);
if(isset($_SESSION['user'])){
    draw_add_pet();
}
else{
    header('Location: ' . 'login.php');
}
echo '</div>';
draw_footer();
?>
