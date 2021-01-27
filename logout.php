<?php
//sessionを終了することでlogoutとする
session_start();
session_destroy();
header('Location: ./homepage.php');
exit;
?>
