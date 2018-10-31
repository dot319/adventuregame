<?php session_start(); ?>
<!DOCTYPE html>
<html>
<?php include_once 'php sections/head.php'; ?>
    <body>

<?php

if (!isset($_SESSION['UserID'])) {
    header('location: login.php?game=true');
}

require_once 'php sections/game.php';

?>

    </body> 
</html>