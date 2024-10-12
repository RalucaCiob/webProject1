<?php 
    /*
        Name: Raluca Ciobanu
        User ID: C00289426
        Date:6/10/2024

        This script is used to close a support ticket. Only tickets that are currently open
        and that have been resolved can be closed.
    */
    session_start();
    if (!isset($_SESSION['user'])) 
    {
        header("Location: loginScreen.php");
        exit();
    }
    // To keep track of which ticket in the sequence of tickets is being shown to the user.
    //  This is required because the user needs to be able to view the previous and next tickets.
    if (!isset($_SESSION['currentTicket'])) 
    {
        $_SESSION['currentTicket'] = 0;
    }
 
    include 'db.inc.php';

    $email = $_SESSION['user'];
    // Check if the user has any tickets eligible for closure (currently open and resolved).
    $sql = "SELECT ticketId, description, resolution FROM tickets WHERE openedBy = '$email' AND openStatus = true AND resolvedStatus = true";
    $result = mysqli_query($con, $sql);

    // Retrieve the tickets from the database into an array.
    $tickets = array();
    while ($row = mysqli_fetch_assoc($result)) 
    {
        $tickets[] = $row;
    }

    // Retrieve the current ticket number.
    $currentTicket = $_SESSION['currentTicket'];
   
    // Check if the next button has been pressed.
    if (isset($_POST['next'])) 
    {
        // Move to the next ticket (count tells us how many items are in the array)
        if ($currentTicket < count($tickets) - 1) 
        {
            $currentTicket++;
        }
        // Store the current ticket in the session
        $_SESSION['currentTicket'] = $currentTicket;
    } 
    // Check if the previous button has been pressed.
    elseif (isset($_POST['previous'])) 
    {
        // Move to the previous ticket
        if ($currentTicket > 0) 
        {
            $currentTicket--;
        }
        $_SESSION['currentTicket'] = $currentTicket;
    }
    elseif (isset($_POST['close']) && count($tickets) > 0)
    {       
        // Update the 'openStatus' field to false for the currently selected ticket
        $sql = "UPDATE tickets SET openStatus = false WHERE ticketId = '" . $tickets[$currentTicket]['ticketId'] . "'";
        mysqli_query($con, $sql);  
        // Delete the current ticket from the array
        unset($tickets[$currentTicket]); 
        // The array needs to be reindexed after deleting the element.
        $tickets = array_values($tickets);
        // If the array is empty, set the current ticket to 0
        if (count($tickets) == 0)
        {
            $_SESSION['currentTicket'] = 0;
        }
        // Ensure the $currentTicket variable is not out of bounds as an index.
        elseif ($currentTicket >= count($tickets)) 
        {           
            $currentTicket = count($tickets) - 1;
            $_SESSION['currentTicket'] = $currentTicket;     
        } 
    }

        ?>
        <!DOCTYPE html>
        <html>
        <head>
        <title>Close Ticket</title>
        <link rel="stylesheet" type="text/css" href="layout.css">	
        </head>
        <body>
            <h1> Showing all open support tickets that are resolved </h1>
            <?php 
            if (count($tickets) > 0) 
            {
            ?>
                <table>
                    <tr>
                        <th>Sequence Number</th>
                        <th>Ticket ID</th>               
                        <th>Ticket Description</th>  
                        <th>Resolution</th>                                                             
                    </tr>
                    <tr>
                        <!-- Display the current ticket from the tickets array 
                        using the php variable $currentTicket 
                        -->
                        <td><?php echo ($currentTicket + 1) . " of " . count($tickets); ?></td> 
                        <td><?php echo $tickets[$currentTicket]['ticketId']."\t"; ?></td>
                        <td><?php echo $tickets[$currentTicket]['description']; ?></td> 
                        <td><?php echo $tickets[$currentTicket]['resolution']; ?></td>                                          
                    </tr>
                </table>
            <?php
            } 
            else 
            {
                echo "<h2>No open tickets left that have been marked as resolved</h2>";
            }
            ?>
            <br><br>
            <!-- A form to allow the user to navigate to the next or previous ticket, resolve a ticket or close a ticket -->
            <form action="closeTicket.html.php" method="post">
                <input type="submit" name="previous" value="Previous">
                <input type="submit" name="next" value="Next"> 
                <input type="submit" name="close" value="Close">                   
            </form>
            <br><br>
            <button class="home-button" onclick="window.location.href = 'userhome.html.php'">Home</button>            
        </body>
        </html>
        <?php
?>
