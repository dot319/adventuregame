<?php session_start(); 

ini_set('display_errors',1);
error_reporting(E_ALL);

/*** THIS! ***/
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

?>
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