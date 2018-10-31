<?php session_start(); ?>
<!DOCTYPE html>
<html>
<?php include_once 'php sections/head.php'; ?>
    <body>

<?php

require 'php sections/phpfunctions.php';
require 'php sections/newcharactertodatabase.php';

?>
<div class="perfect-center">
    <div id="create-character">
        <span class="header"><p>Create a new character</p></span>
        <span class='error-message'><?php echo($upload_error); ?></span>
        <form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post">
            <p><input type="text" name="username" placeholder="Create a character"></p>
            <span class="error-message"><p><?php echo($username_error); ?></p></span>
            <p><input type="password" name="password" placeholder="Create a password"></p>
            <span class="error-message"><p><?php echo($password_error); ?></p></span>
            <p><input type="password" name="confirm_password" placeholder="Confirm your password"></p>
            <span class="error-message"><p><?php echo($confirm_password_error); ?></p></span>
            <p><input type="submit" value="Create"></p>
        </form>
        <p>Or <a href="login.php">log in</a> with an existing character</p>
    </div>
</div>

    </body> 
</html>