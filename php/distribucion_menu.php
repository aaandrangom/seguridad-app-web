<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    if (isset($_POST['menu'])) {
        $menu = $_POST['menu'];
        $_SESSION['menu_completo'] = $menu;
    }
}
?>