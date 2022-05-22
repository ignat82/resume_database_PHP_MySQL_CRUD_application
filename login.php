<?php
session_start();

require_once "pdo.php";
require_once "bootstrap.php";
/*****************************************************************************
 * credentials are checked based on login (email) and value of password
 * salted hash that's stored in DB.
 * The password itself is not stored anywhere
 ****************************************************************************/
$salt = 'XyZzy12*_';

if ( isset($_POST['email']) && isset($_POST['pass']) )
{
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 )
    {
        $_SESSION['error'] = "User name and password are required";
        header("Location: login.php");
        return;
    }
    if(str_contains($_POST['email'],'@') === false)
    {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: login.php");
        return;
    }
    else
    {
        $check = hash('md5', $salt.$_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name FROM users
        WHERE email = :em AND password = :pw');
        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ( $row !== false )
        {
            $_SESSION['name'] = $row['name'];
            $_SESSION['logged_user_id'] = $row['user_id'];
            header("Location: index.php");
            return;
        }
        else
        {
            error_log("Login fail ".$_POST['email']." $check");
            $_SESSION['error'] = "Incorrect password";
            header("Location: login.php");
            return;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ignat Nushtaev Login Page 2e8c6be6</title>
</head>
<body>
<div class="container">
    <h1>Please log in</h1>
    <?php
    if ( isset($_SESSION['error'])) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
        unset($_SESSION['error']);
    }
    ?>
    <form method="POST">
        <label for="name">User Name</label>
        <input type="text" name="email" id="email"><br/>
        <label for="password">Password</label>
        <input type="text" name="pass" id="password"><br/>
        <input type="submit" onclick="return doValidate();" value="Log In">
        <input type="submit" name="cancel" value="Cancel">
    </form>
    <p>
        For a password hint, view source and find a password hint
        in the HTML comments.
        <!-- Hint: The password is the four character sound a cat
        makes (all lower case) followed by 123. -->
        <!--simple js falidation of credentials input-->
        <script>
            function doValidate() {
                console.log('Validating...');
                try {
                    addr = document.getElementById('email').value;
                    pw = document.getElementById('password').value;
                    console.log("Validating addr="+addr+" pw="+pw);
                    if (addr == null || addr == "" || pw == null || pw == "") {
                        alert("Both fields must be filled out");
                        return false;
                    }
                    if ( addr.indexOf('@') == -1 ) {
                        alert("Invalid email address");
                        return false;
                    }
                    return true;
                } catch(e) {
                    return false;
                }
                return false;
            }
        </script>
    </p>
</div>
</body>
