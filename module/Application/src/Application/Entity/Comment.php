<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rhumsaa\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="comment")
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="id", type="string")
     */
    private $uuid;
    /**
     * @ORM\ManyToOne(targetEntity="Ticket")
     * @ORM\JoinColumn(name="ticket", referencedColumnName="id")
     * @var Ticket
     */
    private $ticket;
    /**
     * @ORM\Column(name="comment", type="string")
     * @var string
     */
    private $comment;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->uuid = Uuid::uuid1();
    }

    /**
     * @param string $comment
     * @param Ticket $ticket
     */
    public function updateCommentInformationFromPost($comment, Ticket $ticket)
    {
        $this->comment = $comment;
        $this->ticket  = $ticket;
    }

    /**
     * @return Uuid
     */
    public function getCommentIdentifier()
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return Ticket $comment
     */
    public function getTicket()
    {
        return $this->ticket;
    }
}
