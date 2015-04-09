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

namespace Support\Command\Ticket\Handler;

use Support\Command\Command;
use Support\Command\CommandHandlerInterface;
use Support\Command\Ticket\ReopenTicket as ReopenTicketCommand;
use Support\Entity\Ticket;
use Doctrine\Common\Persistence\ObjectManager;

final class ReopenTicket implements CommandHandlerInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function handle(Command $command)
    {
        $ticket = $this
            ->objectManager
            ->getRepository(Ticket::class)
            ->find($command->getTicketIdentifier());

        $ticket->reopen();
        $this->objectManager->flush();
    }

    public function canHandle(Command $command)
    {
        return $command instanceof ReopenTicketCommand;
    }
}
