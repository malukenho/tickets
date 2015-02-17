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
 * @ORM\Table(name="ticket")
 */
class Ticket
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="id", type="string")
     */
    private $id;
    /**
     * @ORM\Column(name="subject", type="text", length=255)
     */
    private $subject;
    /**
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    /**
     * @ORM\Column(name="importance", type="integer")
     */
    private $importance;
    /**
     * @ORM\Column(name="opened_by", type="integer")
     */
    private $openedBy;
    /**
     * @ORM\Column(name="active", type="boolean", nullable=true, options={"default"=1})
     */
    private $active;
    /**
     * @ORM\Column(name="responsible", type="integer", length=20, nullable=true)
     */
    private $responsible;

    public function __construct()
    {
        $this->id = Uuid::uuid1();
    }

    /**
     * @return mixed
     */
    public function getResponsible()
    {
        return $this->responsible;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getImportance()
    {
        return $this->importance;
    }

    /**
     * @return mixed
     */
    public function getOpenedBy()
    {
        return $this->openedBy;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    public function updateTicketInformationFromOpenCommand(
        $subject,
        $description,
        $importance,
        $openedBy,
        $active
    ) {
        $this->subject = $subject;
        $this->description = $description;
        $this->openedBy = $openedBy;
        $this->active = $active;
        $this->importance = $importance;
    }
}
