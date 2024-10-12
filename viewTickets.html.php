<?php 
    /*
        Name: Raluca Ciobanu
        User ID: C00289426
        Date:6/10/2024

        This script is used to view all support tickets. One ticket is shown at a time.
        The user can move between tickets using next and previous buttons.
        A home button allows the user exit to the userhome.html.php page.
    */
    session_start();
    if (!isset($_SESSION['user'])) 
    {
        header("Location: userLogin.php");
        exit();
    }
    // To keep track of which ticket in the sequence of tickets is being shown to the user.
    //  This is required because the user needs to be able to view the previous and next tickets.
    if (!isset($_SESSION['currentViewTicket'])) 
    {
        $_SESSION['currentViewTicket'] = 0;
    }
 
    include 'db.inc.php';

    $email = $_SESSION['user'];
    // Retrieve any tickets associated with this user, the ticket subject has to be retrieved from
    //  the support category reference table.
    $sql = "SELECT t.ticketId, t.description, t.resolution, t.openStatus, t.resolvedStatus, sc.title AS categoryTitle
        FROM tickets t
        JOIN ticket_support_category tsc ON t.ticketId = tsc.ticketId
        JOIN supportcategory sc ON tsc.supportCategoryId = sc.supportCategoryId
        WHERE t.openedBy = '$email'";
    
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) == 0) 
    {
        echo "No support tickets logged";
    } 
    else 
    {
        // Retrieve the tickets from the database into an array.
        $tickets = array();
        while ($row = mysqli_fetch_assoc($result)) 
        {
            $tickets[] = $row;
        }

        // Retrieve the current ticket number.
        $currentTicket = $_SESSION['currentViewTicket'];

        // Check if the next button has been pressed.
        if (isset($_POST['next'])) 
        {
            // Move to the next ticket (count tells us how many items are in the array)
            if ($currentTicket < count($tickets) - 1) 
            {
                $currentTicket++;
            }
            // Store the current ticket in the session
            $_SESSION['currentViewTicket'] = $currentTicket;
        } 
        // Check if the previous button has been pressed.
        elseif (isset($_POST['previous'])) 
        {
            // Move to the previous ticket
            if ($currentTicket > 0) 
            {
                $currentTicket--;
            }
            $_SESSION['currentViewTicket'] = $currentTicket;
        }

        // For safety, ensure the $currentTicket variable is between 0 
        //  and the (number of tickets - 1)
        if ($currentTicket >= count($tickets)) 
        {           
            $currentTicket = count($tickets) - 1;
        }

        ?>
        <!DOCTYPE html>
        <html>
        <head>
        <title>User Home</title>
        <link rel="stylesheet" type="text/css" href="layout.css">	
        </head>
        <body>
            <table>
                <tr>
                    <th>Sequence Number</th>
                    <th>Ticket ID</th> 
                    <th>Ticket Subject</th>              
                    <th>Ticket Description</th>
                    <th>Resolution</th>
                    <th>Open</th>
                    <th>Resolved</th>
                </tr>
                <tr>
                    <!-- Display the current ticket from the tickets array 
                     using the php variable $currentTicket 
                     -->
                    <td><?php echo ($currentTicket + 1) . " of " . count($tickets); ?></td>
                    <td><?php echo $tickets[$currentTicket]['ticketId']."\t"; ?></td>
                    <td><?php echo $tickets[$currentTicket]['categoryTitle']."\t"; ?></td>                    
                    <td><?php echo $tickets[$currentTicket]['description']; ?></td>
                    <td><?php echo $tickets[$currentTicket]['resolution']; ?></td>                    
                     <!-- Display the status as "Yes" or "No" instead of numeric values of
                      1 or 0
                     -->
                    <td><?php 
                        if ($tickets[$currentTicket]['openStatus'] == true)
                        {
                            echo "Yes";
                        }
                        else
                        {
                            echo "No";
                        }                        
                        ?>
                    </td>
                    <td><?php 
                        if ($tickets[$currentTicket]['resolvedStatus'] == true)
                        {
                            echo "Yes";
                        }
                        else
                        {
                            echo "No";
                        }                        
                        ?>
                    </td>
                </tr>
            </table>          
            <br><br>
            <form action="viewTickets.html.php" method="post">
                <input type="submit" name="previous" value="Previous">
                <input type="submit" name="next" value="Next">                
            </form>
            <br><br>
            <button class="home-button" onclick="window.location.href = 'userhome.html.php'">Home</button>            
        </body>
        </html>
        <?php
    }
?>
