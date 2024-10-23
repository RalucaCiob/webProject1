<?php 
    /*
        Name: Raluca Ciobanu
        User ID: C00289426
        Date:8/10/2024

        This script is used to manage elevated support tickets. The agent can search for a ticket
        by using the ticket id and resolve a ticket by providing a written resolution. 
    */
    session_start();
    if (!isset($_SESSION['user']) || !isset($_SESSION['supportLevel'])) 
    {
        header("Location: agentLogin.php");
        exit();
    }
    // To keep track of which ticket in the sequence of tickets is being shown to the user.
    //  This is required because the user needs to be able to view the previous and next tickets.
    if (!isset($_SESSION['currentViewTicket'])) 
    {
        $_SESSION['currentViewTicket'] = 0;
    }
 
    include 'db.inc.php';

    // Retrieve all tickets that are not resolved from the elevated tickets table where
    //  the assigned support level is equal to the support level of the agent.
    $sql = "SELECT * FROM elevated_tickets WHERE openStatus = true AND assignedSupportLevel = " . ($_SESSION['supportLevel']);    
    $result = mysqli_query($con, $sql);

    // Check if there are any tickets
    if (mysqli_num_rows($result) == 0) 
    {
        echo "No elevated tickets logged";
        echo "<br><br>";
        echo "<form action='agentLogin.php' method='post'>";
        echo "<input type='submit' value='Logout'>";
        echo "</form>";
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

        // Handle the "ticket search" form submission
        if (isset($_POST['search'])) 
        {
            $search_ticket_id = $_POST['search_ticket_id'];

            // Search for the ticket ID in the $tickets array
            $found_ticket = null;
            foreach ($tickets as $key => $ticket) 
            {
                if ($ticket['elevatedTicketId'] == $search_ticket_id) 
                {
                    $found_ticket = $key;
                    break;
                }
            }

            // If the ticket is found, set the $currentTicket variable
            if ($found_ticket !== null) 
            {
                $currentTicket = $found_ticket;
                $_SESSION['currentViewTicket'] = $currentTicket;
            } 
            else 
            {
                // Display an error message if the ticket is not found
                echo "Error: Ticket ID not found.";
            }
        }



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

        // To keep track if the ticket has been modified (resolved or elevated)
        $ticketModified = false;

       
        // Handle the resolve button
        if (isset($_POST['resolve'])) 
        {
            $ticketId = $tickets[$currentTicket]['elevatedTicketId'];
            $resolution = $_POST['resolution'];

            // Update the elevated tickets table so that the ticket is marked as resolved,
            //  and the resolution text (for the same ticket) is stored in the tickets table.
            // The ticket status is marked resolved, but left open until closed by the user.
            $sql = "UPDATE elevated_tickets 
                   JOIN tickets ON elevated_tickets.elevatedTicketId = tickets.ticketId
                   SET 
                   elevated_tickets.openStatus = false,                     
                   elevated_tickets.resolvedBy = '".$_SESSION['user']."',                     
                   tickets.resolution = '$resolution', 
                   tickets.resolvedStatus = true
                   WHERE elevated_tickets.elevatedTicketId = '$ticketId'
        ";
            mysqli_query($con, $sql);

            $ticketModified = true;
            // Redirect back to the previous screen
            header("Location: manageElevatedTickets.html.php");
            exit();
        }

       
        // If the ticket has been resolved or elevated, remove it from the array so it doesn't
        //  appear in the list of tickets the agent is browsing.
        if ($ticketModified == true)
        {               
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
        <title>User Home</title>
        <link rel="stylesheet" type="text/css" href="layout.css">	
        </head>
        <body>
        <?php 
            if (count($tickets) > 0) 
            {
                echo count($tickets)
            ?>
            <table>
                <tr>
                    <th>Sequence Number</th>
                    <th>Ticket ID</th>                                   
                    <th>Ticket Description</th> 
                    <th>Elevated By</th>
                    <th>Elevated Date</th>                                                           
                </tr>
                <tr>
                    <!-- Display the current ticket from the tickets array 
                     using the php variable $currentTicket 
                     -->
                    <td><?php echo ($currentTicket + 1) . " of " . count($tickets); ?></td>
                    <td><?php echo $tickets[$currentTicket]['elevatedTicketId']."\t"; ?></td>                                      
                    <td><?php echo $tickets[$currentTicket]['reasonForElevation']; ?></td>
                    <td><?php echo $tickets[$currentTicket]['elevatedBy']; ?></td>
                    <td><?php echo $tickets[$currentTicket]['dateCreated']; ?></td>                    
                </tr>
            </table> 
        <?php
            } 
            else 
            {
                echo "<h2>No elevated tickets to display</h2>";
            }  
        ?>           
            <br><br>
            <form action="" method="post">
                <input type="text" name="search_ticket_id" placeholder="Search by ticket ID">
                <input type="submit" name="search" id= "searchButton" value="Search">
            </form>
            <!-- A form to allow the user to navigate to the next or previous ticket, resolve a ticket or elevate a ticket -->
            <form action="manageElevatedTickets.html.php" method="post">
                <input type="submit" name="previous" id = "previousButton" value="Previous">
                <input type="submit" name="next" id = "nextButton" value="Next">                
                <button type="button" id="resolveButton" onclick="openResolveDialog()">Resolve</button>
            </form>
            <br><br>
            <button class="logout-button" id = "logoutButton" onclick="window.location.href = 'index.html'">Logout</button>            
            <!-- Resolve dialog form, this allows the user enter text and submit the form -->
            <div id="resolve-dialog" style="display: none;">
                <h2>Resolve Ticket</h2>
                <form action="manageElevatedTickets.html.php" method="post">
                    <textarea id="resolve-notes" name="resolution" rows="5" cols="50" required></textarea>
                    <br>
                    <button type="submit" name="resolve" >Submit</button>
                    <button type="button" onclick="cancelResolve()">Cancel</button>
                </form>
            </div>
            <script>
                // This purpose of this script is to disable all form input except for the resolve and elevate buttons
                //   and associated text areas.
                var searchButton = document.getElementById("searchButton");
                var logoutButton = document.getElementById("logoutButton");
                var nextButton = document.getElementById("nextButton");
                var prevButton = document.getElementById("previousButton");
                var resolveButton = document.getElementById("resolveButton");
                var resolveDialog = document.getElementById("resolve-dialog");
              
                // This function toggles the display of the resolve and elevate dialog forms.
                function toggleButtons() {
                    if (resolveDialog.style.display === "none" && elevateDialog.style.display === "none") 
                    {                       
                        nextButton.disabled = false;
                        prevButton.disabled = false;
                        searchButton.disabled = false;
                        logoutButton.disabled = false;
                        resolveButton.disabled = false;
                        // Remove the "disabled" styling from the buttons
                        searchButton.classList.remove("disabled-button");
                        logoutButton.classList.remove("disabled-button");
                        nextButton.classList.remove("disabled-button");
                        prevButton.classList.remove("disabled-button");
                        resolveButton.classList.remove("disabled-button");
                    } 
                    else if (resolveDialog.style.display === "block" || elevateDialog.style.display === "block") 
                    {                                           
                        nextButton.disabled = true;
                        prevButton.disabled = true;
                        searchButton.disabled = true;
                        logoutButton.disabled = true;
                        resolveButton.disabled = true;
                        // Activate the "disabled" styling on the buttons
                        searchButton.classList.add("disabled-button");
                        logoutButton.classList.add("disabled-button");
                        nextButton.classList.add("disabled-button");
                        prevButton.classList.add("disabled-button");
                        resolveButton.classList.add("disabled-button");
                    }
                }

                // On page load, call the toggleButtons function to ensure the correct buttons are disabled.
                window.onload = function() 
                {                                        
                    toggleButtons();
                }


                // Add an event listener to the openResolveDialog function
                function openResolveDialog() 
                {
                    resolveDialog.style.display = "block";
                    toggleButtons();
                }

                // Add an event listener to the cancelResolve function
                function cancelResolve() 
                {
                    resolveDialog.style.display = "none";
                    toggleButtons();
                }              
            
            </script>
        </body>
        </html>
        <?php
    }
?>
