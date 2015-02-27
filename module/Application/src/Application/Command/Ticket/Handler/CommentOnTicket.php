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

namespace Application\Command\Ticket\Handler;

use Application\Command\Command;
use Application\Command\CommandHandlerInterface;
use Application\Command\Ticket\CommentOnTicket as CommentOnTicketCommand;
use Application\Entity\Comment;
use Application\Event\Ticket\CommentWasAdded;
use Doctrine\Common\Persistence\ObjectManager;

final class CommentOnTicket implements CommandHandlerInterface
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
        $comment = new Comment();
        $comment->updateCommentInformationFromPost(
            $command->getComment(),
            $command->getTicket()
        );

        $this->objectManager->persist($comment);
        $this->objectManager->flush();

        return new CommentWasAdded(
            $command->getCommentIdentifier(),
            $command->getTicket()->getTicketIdentifier()
        );
    }

    public function canHandle(Command $command)
    {
        return $command instanceof CommentOnTicketCommand;
    }
}
