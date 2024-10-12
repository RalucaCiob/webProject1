<?php 
/*
        Name: Raluca Ciobanu
        User ID: C00289426
        Date:5/10/2024      
        
        This script is used to display the user home page, consisting of a menu of buttons. 
        Each button redirects the user to the corresponding page (e.g. raise ticket redirects to
        raiseTicket.html.php).
*/
    session_start();
    // Check if the user is logged in, if not redirect to login page.
    if (!isset($_SESSION['user'])) 
    {
        header("Location: userLogin.php");
        exit();
    }
    echo "Welcome " . $_SESSION['user'];
    echo "<br><br><br>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Home</title>
    <link rel="stylesheet" type="text/css" href="layout.css">	
</head>
<body>
    <h1>User Home</h1>
    <div>
        <button onclick="window.location.href = 'raiseTicket.html.php'">Raise a support ticket</button>
        <button onclick="window.location.href = 'viewTickets.html.php'">View all tickets</button>
        <button onclick="window.location.href = 'closeTicket.html.php'">Close a ticket</button>
        <button onclick="window.location.href = 'index.html'">Logout</button>
    </div>
</body>
</html>