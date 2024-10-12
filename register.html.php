<?php 
/*
        Name: Raluca Ciobanu
        User ID: C00289426
        Date:5/10/2024

        This script is used to register a new user. Upon successful registration, 
        the user is redirected to the login page.
*/
include 'db.inc.php';
session_start();
echo '<link rel="stylesheet" href="pass.css" type="text/css">';

// Check if the form has been submitted.
if (isset($_POST['register']))
{
    // Check if all input fields are filled.
    if (isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['emailAddress']) && isset($_POST['password']) && isset($_POST['confirmPassword']))
    {
        // Retrieve the input fields.
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $emailAddress = $_POST['emailAddress'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];

        // Check if the password matches the confirm password.
        if ($password == $confirmPassword)
        {
            // Query to check if the email address already exists in the database.
            $sql = "SELECT * FROM users WHERE loginName = '$emailAddress'";
            if (!mysqli_query($con, $sql))
                echo "Error in Select query." . mysqli_error($con);
            else
            {
                // Check if the email address is already in the database.
                $result = mysqli_query($con, $sql);
                if (mysqli_num_rows($result) > 0)
                    echo "Error: Email address already exists.";
                else
                {
                    // Insert the new user into the database.
                    $sql = "INSERT INTO users (firstName, lastName, loginName, password) VALUES ('$firstName', '$lastName', '$emailAddress', '$password')";
                    if (!mysqli_query($con, $sql))
                        echo "Error in Insert query." . mysqli_error($con);
                    else
                    {
                        // Redirect (after a short delay) to the login screen after successful registration.
                        echo "Registration Successful!";
                        header("Refresh: 1; url=userLogin.php");
                        sleep(1);
                        exit();
                    }
                }
            }
        }
        else
            echo "Error: Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register New Account</title>
    <head>
    <link rel="stylesheet" type="text/css" href="layout.css">	
    <script>
        // This function checks if the passwords match in the password and confirm password fields.
        function checkPassword() 
        {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirmPassword').value;
            if (password != confirmPassword) 
            {
                document.getElementById('confirmPasswordError').innerHTML = 'Passwords do not match';
            } 
            else 
            {
                document.getElementById('confirmPasswordError').innerHTML = '';
            }
        }       
    </script>
</head>
<body>
    <h1>Register</h1>
    <form action="register.html.php" method="post">
        <label for="firstName">First Name:</label>
        <input type="text" name="firstName" id="firstName" required><br><br>
        <label for="lastName">Last Name:</label>
        <input type="text" name="lastName" id="lastName" required><br><br>
        <label for="emailAddress">Email Address:</label>
        <input type="email" name="emailAddress" id="emailAddress" required><br><br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>
        <label for="confirmPassword">Confirm Password:</label>
        <!-- The oninput event occurs when the user types something in the input field, here
             the checkPassword function is called for each keystroke so the confirm password
             is checked character by character.
        -->
        <input type="password" name="confirmPassword" id="confirmPassword" oninput="checkPassword()" required>
        <span id="confirmPasswordError" style="color: red;"></span><br><br>
        <input type="submit" name="register" value="Register">
    </form>
</body>
</html>