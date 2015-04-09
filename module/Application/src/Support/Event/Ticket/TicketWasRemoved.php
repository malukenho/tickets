<?php

namespace Support\Event\Ticket;

class TicketWasRemoved
{
    private $ticketId;

    public function __construct($ticketId)
    {
        $this->ticketId = $ticketId;
    }

    public function getTicketId()
    {
        return $this->ticketId;
    }
}
