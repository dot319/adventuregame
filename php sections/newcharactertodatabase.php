<?php require_once 'php sections/connect.php';

//Set variables
$username = "";
$password = "";
$confirm_password = "";
$username_error =  "";
$password_error =  "";
$confirm_password_error = "";
$upload_error = "";

//Check if something was posted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Check if fields aren't empty
    if (empty(trim($_POST['username']))) {
        $username_error = "Please enter a username";
    }
    if (empty(trim($_POST['password']))) {
        $password_error = "Please enter a password";
    }
    if (empty(trim($_POST['confirm_password']))) {
        $confirm_password_error = "Please confirm your password";
    }
    
    //If username field is entered, check if username already exists
    if ($username_error ==  "") {
        $stmt = $conn->prepare("SELECT * FROM Adventurers WHERE `Name`=?");
        $stmt->bind_param("s", $username);
        $username = cleanInput($_POST['username']);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $username_error = "Username already exists";
        }
    }

    //If password field is entered, check if password is long enough
    if ($password_error == "") {
        $password = cleanInput($_POST['password']);
        if (strlen($password) < 8) {
            $password_error = "Your password must be at least 8 characters long";
        }
    }

    //If confirmpassword field is entered, check if it corresponds to password
    if ($confirm_password_error == "") {
        $confirm_password = cleanInput($_POST['confirm_password']);
        if ($password != $confirm_password) {
            $confirm_password_error = "You entered two different passwords";
        }
    }

    //If all is well, entere data into database
    if ($username_error ==  "" && $password_error ==  "" && $confirm_password_error == "") {
        $stmt = $conn->prepare("INSERT INTO Adventurers (`Name`, `HP`, `MaxHP`, `Currency`, `Attack`, `Defense`, `Password`)
        VALUES (?, 100, 100, 0, 1, 1, ?)");
        $stmt->bind_param("ss", $username, $password);
        $password = password_hash($password, PASSWORD_DEFAULT);
        if ($stmt->execute()) {
            header('location: login.php?reg=true');
        } else {
            $upload_error = "Something went wrong, try again later.";
        }
    }

}

//Close connection
$conn->close();

?>