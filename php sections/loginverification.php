<?php 

//Check if user is logged in already
if (isset($_SESSION['UserID'])) {
    header('location: startgame.php');
    exit;
}

//Connect to database
require_once 'php sections/connect.php';

//Set variables
$username = "";
$password = "";
$username_error =  "";
$password_error =  "";
$redirect_message = "";

//Check if user is redirected from registration page
if (isset($_GET['reg'])) {
    $redirect_message = "Registration successful!";
}

if (isset($_GET['game'])) {
    $redirect_message = "You must be logged in to play the game";
}

//Check if something was posted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Check if fields aren't empty
    if (empty(trim($_POST['username']))) {
        $username_error = "Please enter a username";
    }
    if (empty(trim($_POST['password']))) {
        $password_error = "Please enter a password";
    }
    
    //If username field is entered, check if username exists
    if ($username_error ==  "") {
        $stmt = $conn->prepare("SELECT `AdvID`, `Password` FROM Adventurers WHERE `Name`=?");
        $stmt->bind_param("s", $username);
        $username = cleanInput($_POST['username']);
        $myResult = $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
            $username_error = "Username doesn't exist";
        
        //Check is password is correct
        } else {
            $password = cleanInput($_POST['password']);
            $stmt->bind_result($userID, $pw);
            while ($stmt->fetch()) {
                $password_hash = $pw;
            }
            if (password_verify($password, $password_hash) == false) {
                $password_error = "Password incorrect";
            }
        }
    }

    //If all is well, log in
    if ($username_error == "" && $password_error == "") {
        session_start();
        $_SESSION['UserID'] = $userID;
        $_SESSION['Username'] = $username;
        header('location: startgame.php');
    }
}

//Close connection
$conn->close();

?>