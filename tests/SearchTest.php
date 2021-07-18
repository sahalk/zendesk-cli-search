<?php 

namespace Sahal\Zendesk\Test;

use PHPUnit\Framework\TestCase;

// Class to test the Zendesk Search.
class SearchTest extends TestCase {
    /**
     * Constructor to initialize the values
     *
     * 
     */
    public function __construct() {
        parent::__construct();

        // All the contents of users and tickets JSON files.
        $this->users = json_decode(file_get_contents(__DIR__ ."/../data/users.json"), true);
        $this->tickets = json_decode(file_get_contents(__DIR__ ."/../data/tickets.json"), true);

        // All the searchable fields for users and tickets.
        $this->user_fields = ['_id', 'name', 'created_at', 'verified'];
        $this->ticket_fields = ['_id', 'created_at', 'type', 'subject', 'assignee_id', 'tags'];
    }

    /**
     * Testing creating the object 
     *
     * @return status;
     */
    public function testCreateAgent() {
        $agent = $this->createAgent();

        // Testing if the object was created correctly with the JSON values loaded.
        $this->assertTrue(is_array($agent->users));
    }

    /**
     * Testing to check if the data is being read correctly 
     *
     * @return status;
     */
    public function testExtractAllData() {
        $agent = $this->createAgent();

        // Checking the users and tickets JSON matches the files.
        $this->assertSame($this->users, $agent->getAllUsers());
        $this->assertSame($this->tickets, $agent->getAllTickets());
    }

    /**
     * Testing if the searchable fields are generated correctly
     *
     * @return status;
     */
    public function testSearchableFields() {
        $agent = $this->createAgent();

        // Check to see if the user and ticket fields match correctly.
        $this->assertSame($this->user_fields, $agent->getSearchableFields(0)['user']);
        $this->assertSame($this->ticket_fields, $agent->getSearchableFields(0)['ticket']);
    }

    /**
     * Testing the searching functionality
     *
     * @return status;
     */
    public function testZendeskSearch() {
        $agent = $this->createAgent();

        // Search for the user with _id 71.
        $this->assertTrue($agent->zendesk_search(1, '_id', 71));

        // Search for the ticket with _id 8ea53283-5b36-4328-9a78-f261ee90f44b.
        $this->assertTrue($agent->zendesk_search(2, '_id', '8ea53283-5b36-4328-9a78-f261ee90f44b'));

        // Search for the ticket with no assigned users.
        $this->assertTrue($agent->zendesk_search(2, 'assignee_id', 'null'));

        // Search for a user with an invalid field and value (Code should not break).
        $this->assertTrue($agent->zendesk_search(1, 'wrong_field', '123'));
    }

    /**
     * Testing the functionality to find assigned user/tickets
     *
     * @return status;
     */
    public function testFindValues() {
        $agent = $this->createAgent();

        // Valid values for ticket and user match.
        $user_tickets_match = ['A Problem in Morocco', 'A Problem in United Kingdom', 'A Drama in Australia'];
        $ticket_assignee_match = ['Prince Hinton'];

        // Search for the tickets assigned to user with _id 7.
        $this->assertSame($user_tickets_match, $agent->find_values(1, 7));

        // Search for user assigned to the ticket with _id 71.
        $this->assertSame($ticket_assignee_match, $agent->find_values(2, 71));

        // Search for tickets assigned to user with _id 4 (User with no tickets). 
        $this->assertSame([], $agent->find_values(1, 4));
    }

    /**
     * Function to create the Object for the Zendesk Search Class
     *
     * @return status;
     */
    private function createAgent() {
        $agent = new \Sahal\Zendesk\Search;
        return $agent;
    }
}

