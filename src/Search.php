<?php

namespace Sahal\Zendesk;

// Class to create all the functionalities required for the CLI Search.
class Search {
    // All the users and tickets initialization.
    public $users, $tickets;

    /**
     * Constructor to initialize the JSON values and welcome the user
     *
     * 
     */
    public function __construct() {

        // Extract tickets and users from the JSON files.
        $this->users = json_decode(file_get_contents(__DIR__ ."/../data/users.json"), true);
        $this->tickets = json_decode(file_get_contents(__DIR__ ."/../data/tickets.json"), true);

        // Displaying welcome messages to the users.
        echo "Welcome to Zendesk Search \n";
        echo "Type 'quit' at any time, Press 'Enter' to continue \n\n\n";
    }

    /**
     * Get all the users
     *
     * @return Array
     */
    public function getAllUsers() {
        return $this->users;
    }

    /**
     * Get all the tickets
     *
     * @return Array
     */
    public function getAllTickets() {
        return $this->tickets;
    }

    /**
     * Get all the searchable fields
     *
     * @return Array
     */
    public function getSearchableFields($print_values = 1) {
        // Array to store all the user and ticket keys.
        $all_keys = [
            'user' => [],
            'ticket' => []
        ];

        // Find all the keys for users.
        foreach($this->users as $user) {
            $keys = array_keys($user);
            $missing_keys = array_diff($keys, $all_keys['user']);
        
            // Add missing keys to the main array
            if($missing_keys) {
                $all_keys['user'] = array_merge($all_keys['user'], $missing_keys);
            }
        }

        // Find all the keys for tickets.
        foreach($this->tickets as $ticket) {
            $keys = array_keys($ticket);
            $missing_keys = array_diff($keys, $all_keys['ticket']);
        
            // Add missing keys to the main array
            if($missing_keys) {
                $all_keys['ticket'] = array_merge($all_keys['ticket'], $missing_keys);
            }
        }

        // Show values to the user. (CLI)
        if($print_values) {
            echo "\n---------------------------\n";
            echo "Search Users with\n";
            foreach($all_keys['user'] as $user_key) {
                echo $user_key . "\n";
            }
    
            echo "\n---------------------------\n";
            echo "Search Tickets with\n";
            foreach($all_keys['ticket'] as $ticket_key) {
                echo $ticket_key . "\n";
            }
        }

        // Return array of all the keys
        return $all_keys;
    }

    /**
     * Function to get all the user inputs
     *
     * @return Bool
     */
    public function getSearchOption() {
        // Handle user input from CLI
        $handle = fopen("php://stdin","r");

        // Options for the user.
        $search_options = ["1", "2", "quit"];
        $search_option = '';

        // Check for valid input from the user.
        while(!in_array($search_option, $search_options)) {
            // Display options to the user.
            echo "\t Select Search options: \n";
            echo "\t * Press 1 to search Zendesk \n";
            echo "\t * Press 2 to view a list of searchable fields \n";
            echo "\t * Type 'quit' to exit \n\n";

            // Get user input.
            $search_option = str_replace("\n", '', fgets($handle));

            // Search Option.
            if($search_option == "1") {
                $search_type = '';

                // Streamline search option (user or ticket).
                while(!in_array($search_type, ["1", "2"])) {
                    echo "Select 1) Users or 2) Tickets\n";
                    $search_type = str_replace("\n", '', fgets($handle));

                    if($search_type == "1" || $search_type == "2") {
                        // Get search term.
                        echo "\nEnter search term\n";
                        $search_term = str_replace("\n", '', fgets($handle));

                        ($search_type == 1) ? $search_type_value = 'user' : $search_type_value = 'ticket';

                        // Check if the search term is valid.
                        $all_searchable_keys = $this->getSearchableFields(0)[$search_type_value];
                        if(!in_array($search_term, $all_searchable_keys)) {
                            echo "Sorry, the search term is not present.\n";
                            return false;
                        }

                        // Get search value.
                        echo "\nEnter search value\n";
                        $search_value = str_replace("\n", '', fgets($handle));

                        // Call function to search.
                        echo "\nSearching " . $search_type_value . "s for " . $search_term . " with a value of " . $search_value . "\n";
                        $this->zendesk_search($search_type, $search_term, $search_value);
                    } else {
                        echo "You have entered an incorrect value. Please try again. \n";
                    }
                }
            // Show all searchable options.
            } else if($search_option == "2") {
                $this->getSearchableFields();
            // Option to quit.
            } else if($search_option == "quit") {
                echo "Thank you for using this app.\n\n";
                return true;
            } else {
                echo "You have entered an incorrect value. Please try again. \n";
            }
        }
    }

    /**
     * Function to perform the search operation
     *
     * @return Bool
     */
    public function zendesk_search($search_type, $search_term, $search_value) {
        $found = 0;
        // Search for users.
        if($search_type == "1") {
            foreach($this->users as $user) {
                // Validate search term or search for missing values.
                if((isset($user[$search_term]) && $user[$search_term] == $search_value) || (!isset($user[$search_term]) && $search_value == "null")) {
                    $found = 1;
                    $keys = array_keys($user);
                    echo "\n\n";
                    foreach($keys as $key) {
                        // Convert array to string.
                        if(is_array($user[$key])) {
                            $user[$key] = implode(",",$user[$key]);
                        }

                        // Spacing for the key and value.
                        ($key == "created_at" || $key == "assignee_id" || $key == "verified") ? $tabs = "\t\t" : $tabs = "\t\t\t";

                        // Display key and value.
                        echo $key . $tabs . var_export($user[$key], true) . "\n";
                    }

                    // Find assigned tickets.
                    $assigned_ticket = $this->find_values(1, $user["_id"]);
                    echo "tickets" . "\t\t\t[" . implode(",",$assigned_ticket) . "]\n";
                }
            }

            // Display message if nothing was found.
            if(!$found) {
                echo "No Values Found.\n";
            }
        // Search for tickets.
        } else {
            foreach($this->tickets as $ticket) {
                // Validate search term or search for missing values.
                if((isset($ticket[$search_term]) && $ticket[$search_term] == $search_value) || (!isset($ticket[$search_term]) && $search_value == "null")) {
                    $found = 1;
                    $keys = array_keys($ticket);
                    echo "\n\n";
                    foreach($keys as $key) {
                        // Convert array to string.
                        if(is_array($ticket[$key])) {
                            $ticket[$key] = implode(",",$ticket[$key]);
                        }

                        // Spacing for the key and value.
                        ($key == "created_at" || $key == "assignee_id") ? $tabs = "\t\t" : $tabs = "\t\t\t";

                        // Display key and value.
                        echo $key . $tabs . $ticket[$key] . "\n";
                    }

                    // Find assigned user.
                    $assigned_user = (isset($ticket["assignee_id"])) ? $this->find_values(2, $ticket["assignee_id"]) : ["NULL"];
                    echo "assignee_name" . "\t\t" . implode(",",$assigned_user) . "\n";
                }
            }
            // Display message if nothing was found.
            if(!$found) {
                echo "No Values Found.\n";
            }
        }
        return true;
    }

    /**
     * Function to find the values (assigned user/tickets)
     *
     * @return Bool
     */
    public function find_values($type, $id) {
        // Array to store all matches results.
        $matches = [];

        // Find assigned tickets for a user.
        if($type === 1) {
            foreach($this->tickets as $ticket) {
                if(isset($ticket["assignee_id"]) && $ticket["assignee_id"] == $id) {
                    $matches[] = $ticket["subject"];
                }
            }
        // Find the user for the ticket.
        } else {
            foreach($this->users as $user) {
                if(isset($user["_id"]) && $user["_id"] == $id) {
                    $matches[] = $user["name"];
                    break;
                }
            }
        }

        return $matches;
    }
}

// Execute code if called on the same file.
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    $agent = new Search();
    $agent->getSearchOption();
}