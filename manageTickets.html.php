<?php 
    /*
        Name: Raluca Ciobanu
        User ID: C00289426
        Date:8/10/2024

        This script is used to manage support tickets. The agent can search for a ticket
        by using the ticket id, resolve a ticket by providing a written resolution 
        or elevate a ticket and provide a reason for the elevation.
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

    // Retrieve all tickets that are not resolved, the ticket subject has to be retrieved from
    //  the support category reference table. Also, only tickets that have NOT been elevated
    //  already are shown - this is the purpose of joining the elevated_tickets table.
    $sql = "SELECT t.ticketId, t.description, t.resolvedStatus, sc.title AS categoryTitle
    FROM tickets t
    JOIN ticket_support_category tsc ON t.ticketId = tsc.ticketId
    JOIN supportcategory sc ON tsc.supportCategoryId = sc.supportCategoryId
    LEFT JOIN elevated_tickets et ON t.ticketId = et.elevatedTicketId
    WHERE t.resolvedStatus = false AND et.elevatedTicketId IS NULL";
    
    $result = mysqli_query($con, $sql);

    // Check if there are any tickets
    if (mysqli_num_rows($result) == 0) 
    {
        echo "No support tickets logged";
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
                if ($ticket['ticketId'] == $search_ticket_id) 
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
            $ticketId = $tickets[$currentTicket]['ticketId'];
            $resolution = $_POST['resolution'];

            // Update the tickets table
            $sql = "UPDATE tickets SET resolvedStatus = true, resolution = '$resolution', assignedTo = '".$_SESSION['user']."' WHERE ticketId = '$ticketId'";       
            mysqli_query($con, $sql);

            $ticketModified = true;
            // Redirect back to the previous screen
            header("Location: manageTickets.html.php");
            exit();
        }

        // Handle the elevate button
        if (isset($_POST['elevate']))
        {        
            $reasonForElevation = $_POST['elevate-text'];
            $ticketId = $tickets[$currentTicket]['ticketId'];
            // Prepare the SQL statement to insert the new record into the elevated_tickets table
            $sql = "INSERT INTO elevated_tickets  (elevatedTicketId, reasonForElevation, elevatedBy, assignedSupportLevel, dateCreated)
                    VALUES ('$ticketId', '$reasonForElevation', '".$_SESSION['user']."', " . ($_SESSION['supportLevel'] + 1) . ", NOW())";

            // Execute the statement
            $result = mysqli_query($con, $sql);
            
            if ($result) 
            {
                // Record inserted successfully
                echo "Ticket elevated successfully";
                $ticketModified = true;
            }
            else 
            {
                // Error inserting record
                echo "Error elevating ticket: " . mysqli_error($con);
            }
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
            <table>
                <tr>
                    <th>Sequence Number</th>
                    <th>Ticket ID</th> 
                    <th>Ticket Subject</th>              
                    <th>Ticket Description</th>                    
                </tr>
                <tr>
                    <!-- Display the current ticket from the tickets array 
                     using the php variable $currentTicket 
                     -->
                    <td><?php echo ($currentTicket + 1) . " of " . count($tickets); ?></td>
                    <td><?php echo $tickets[$currentTicket]['ticketId']."\t"; ?></td>
                    <td><?php echo $tickets[$currentTicket]['categoryTitle']."\t"; ?></td>                    
                    <td><?php echo $tickets[$currentTicket]['description']; ?></td>
                </tr>
            </table>          
            <br><br>
            <form action="" method="post">
                <input type="text" name="search_ticket_id" placeholder="Search by ticket ID">
                <input type="submit" name="search" id= "searchButton" value="Search">
            </form>
            <!-- A form to allow the user to navigate to the next or previous ticket, resolve a ticket or elevate a ticket -->
            <form action="manageTickets.html.php" method="post">
                <input type="submit" name="previous" id = "previousButton" value="Previous">
                <input type="submit" name="next" id = "nextButton" value="Next">                
                <button type="button" id="resolveButton" onclick="openResolveDialog()">Resolve</button>
                <button type="button" id="elevateButton" onclick="openElevateDialog()">Elevate</button>
            </form>
            <br><br>
            <button class="logout-button" id = "logoutButton" onclick="window.location.href = 'index.html'">Logout</button>            
            <!-- Resolve dialog form, this allows the user enter text and submit the form -->
            <div id="resolve-dialog" style="display: none;">
                <h2>Resolve Ticket</h2>
                <form action="manageTickets.html.php" method="post">
                    <textarea id="resolve-notes" name="resolution" rows="5" cols="50" required></textarea>
                    <br>
                    <button type="submit" name="resolve" >Submit</button>
                    <button type="button" onclick="cancelResolve()">Cancel</button>
                </form>
            </div>
             <!-- Elevate dialog form, this allows the user enter text and submit the form -->
             <div id="elevate-dialog" style="display: none;">
                <h2>Elevate Ticket</h2>
                <form action="manageTickets.html.php" method="post">
                    <textarea id="elevate-notes" name="elevate-text" rows="5" cols="50"></textarea>
                    <br>
                    <button type="submit" name="elevate" >Submit</button>
                    <button type="button" onclick="cancelElevate()">Cancel</button>
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
                var elevateButton = document.getElementById("elevateButton");
                var resolveDialog = document.getElementById("resolve-dialog");
                var elevateDialog = document.getElementById("elevate-dialog");

                // This function toggles the display of the resolve and elevate dialog forms.
                function toggleButtons() {
                    if (resolveDialog.style.display === "none" && elevateDialog.style.display === "none") 
                    {                       
                        nextButton.disabled = false;
                        prevButton.disabled = false;
                        searchButton.disabled = false;
                        logoutButton.disabled = false;
                        resolveButton.disabled = false;
                        elevateButton.disabled = false;
                        // Remove the "disabled" styling from the buttons
                        searchButton.classList.remove("disabled-button");
                        logoutButton.classList.remove("disabled-button");
                        nextButton.classList.remove("disabled-button");
                        prevButton.classList.remove("disabled-button");
                        resolveButton.classList.remove("disabled-button");
                        elevateButton.classList.remove("disabled-button");
                    } 
                    else if (resolveDialog.style.display === "block" || elevateDialog.style.display === "block") 
                    {                                           
                        nextButton.disabled = true;
                        prevButton.disabled = true;
                        searchButton.disabled = true;
                        logoutButton.disabled = true;
                        resolveButton.disabled = true;
                        elevateButton.disabled = true;
                        // Activate the "disabled" styling on the buttons
                        searchButton.classList.add("disabled-button");
                        logoutButton.classList.add("disabled-button");
                        nextButton.classList.add("disabled-button");
                        prevButton.classList.add("disabled-button");
                        resolveButton.classList.add("disabled-button");
                        elevateButton.classList.add("disabled-button");
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
                 // Add an event listener to the openElevateDialog function
                 function openElevateDialog() 
                {
                    elevateDialog.style.display = "block";
                    toggleButtons();
                }

                // Add an event listener to the cancelElevate function
                function cancelElevate() 
                {
                    elevateDialog.style.display = "none";
                    toggleButtons();
                }              
            </script>
        </body>
        </html>
        <?php
    }
?>
