<?php session_start(); ?>
<!DOCTYPE html>
<html>
<?php include_once 'php sections/head.php'; ?>
    <body>

<?php

require 'php sections/phpfunctions.php';
require 'php sections/loginverification.php';

?>
<div class="perfect-center">
    <div id="login">
        <div id="redirect-message">
            <p><?php echo($redirect_message); ?></p>
        </div>
        <span class="header"><p>Login</p></span>
        <form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post">
            <p><input type="text" name="username" placeholder="Charactername"></p>
            <span class="error-message"><p><?php echo($username_error); ?></p></span>
            <p><input type="password" name="password" placeholder="Password"></p>
            <span class="error-message"><p><?php echo($password_error); ?></p></span>
            <p><input type="submit" value="Log in"></p>
        </form>
        <p>Or <a href="createcharacter.php">create a new character</a>.</p>
    </div>
</div>

    </body> 
</html>