<?php 
/*
        Name: Raluca Ciobanu
        User ID: C00289426
        Date:8/10/2024      
        
        This script is used to enable admins to login to the system, the admin is allowed 3 attempts.
        On successful login, the admin is redirected to adminhome.html.php
*/

include 'db.inc.php';
session_start();
echo '<link rel="stylesheet" href="layout.css" type="text/css">';

// 
if((isset($_POST['loginName'])) && (isset($_POST['password'])))
{
    // Get the number of login attempts 
    $attempts = $_SESSION['attempts'];

    // Query to check if the login name and password combination is correct
    $sql = "SELECT * FROM agents WHERE loginName = '$_POST[loginName]' AND password = '$_POST[password]'";  
    if (!$result = mysqli_query($con, $sql))
        echo "Error in query: ". mysqli_error($con);
    else
    {       
        // Query succeeded, but if no rows are returned, then the login name and password combination is incorrect.
        if (mysqli_affected_rows($con) == 0)
        {
            // Increase the number of login attempts by 1
            $attempts++;
            
            // If less than 3 login attempts have been made
            if ($attempts <=3)
            {
                // Update the number of login attempts
                $_SESSION['attempts'] = $attempts;
                // Rebuild the page
                buildPage($attempts);

                echo "<div class='errorstyle'>No record found with this login name and password combination - Please try again.</div>";
            }
            else // 3 login attempts have been made
            {
                echo "<div class='errorstyle'>Sorry - You have used all 3 attempts<br>
                  Shutting down ...</div>";
            }
        }
        else
        {           
            // Successful login
            $_SESSION['user'] = $_POST['loginName']; // Session variable to keep track of the login name for use with Change Password screen
            $row = mysqli_fetch_assoc($result);
            $_SESSION['supportLevel'] = $row['supportLevel'];
            if ($_SESSION['supportLevel'] == 1)
            {
                header("Location: manageElevatedTickets.html.php");
            }
            else
            {
                header("Location: manageTickets.html.php"); 
            } 
            exit();      
        } 
    }
}
else 
{
    // building page for initial display
    $attempts = 1;  // Screen will be displayed for first attempt
    $_SESSION['attempts'] = $attempts;  // set session variables so that the number of attempts can be counted.    
    buildPage();
}

/*
  This function builds the login page allowing the agent input a login name and password.
*/
function buildPage()
{
    echo " <body>

    <form action = 'agentLogin.php' method = 'post'>
    <h1>Tech Troubleshooters Agent Login</h1>
    <h2>Sign in with your email address</h2>
    <br>
    <br>
    <label for='loginName'>Login Name</label>
    <input type = 'text' name = 'loginName' id = 'loginName' autocomplete = 'off'/><br><br>
    <label for='password'>Password</label>
    <input type = 'password' name = 'password' id = 'password'><br><br>
    <input type='submit' value='Login'>    
    </form></body>";

}

mysqli_close($con);
?>
