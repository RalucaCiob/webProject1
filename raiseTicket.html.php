<?php 
/*
        Name: Raluca Ciobanu
        User ID: C00289426
        Date:5/10/2024

        This script is used to raise a support ticket where the user can type in a description
        of the problem. Upon successful submission, the user is redirected to userhome.html.php
*/
    session_start();
    // Check if the user is logged in, if not redirect to login page.
    if (!isset($_SESSION['user'])) 
    {
        header("Location: userLogin.php");
        exit();
    }
    include 'db.inc.php';

   

    // Check if the form has been submitted.
    if (isset($_POST['submit'])) 
    {
        // Retrieve form data
        $description = $_POST['description'];
        $email = $_SESSION['user'];
        $category = $_POST['category'];
        // Retrieve the current date and time.
        $dateCreated = date("Y-m-d H:i:s");

        // Insert the new ticket into the database.
        $sql = "INSERT INTO tickets (description, openedBy, dateCreated, openStatus, resolvedStatus) VALUES ('$description', '$email', '$dateCreated', true, false)";
        // Check if the SQL query was not successful
        if (!mysqli_query($con, $sql)) 
        {
            echo "Error in Insert query: " . mysqli_error($con);
        }
        // else query was successful, redirect to userhome.html.php  
        else 
        {
            // Get the last inserted ticketId
            $ticketId = mysqli_insert_id($con);

            // Insert the category into the ticket_support_category table
            $sql = "INSERT INTO ticket_support_category (ticketId, supportCategoryId) VALUES ('$ticketId', '$category')";
            mysqli_query($con, $sql);
            echo "Ticket raised successfully!";
            header("Location: userhome.html.php");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Raise a support ticket</title>
    <link rel="stylesheet" type="text/css" href="layout.css">	
</head>
<body>
    <h1>Raise a support ticket</h1>
    <form action="raiseTicket.html.php" method="post">       
        <select id="category" name="category" required>
            <option value="">Select Category</option>
            <?php 
            // Retrieve categories from the supportCategory table
            $sql = "SELECT supportCategoryId, title FROM supportcategory";
            $result = mysqli_query($con, $sql);

            // Populate the category list with titles from the supportCategory table
            while ($row = mysqli_fetch_assoc($result)) 
            {
                echo "<option value='" . $row['supportCategoryId'] . "'>" . $row['title'] . "</option>";
            }
            ?>
        </select>
        <br><br>
        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea><br><br>      
        <input type="submit" name="submit" value="Submit">
        <button onclick="window.location.href = 'userhome.html.php'">Cancel</button>
    </form>
</body>
</html>