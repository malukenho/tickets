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

namespace Support\Factory;

use Support\Command\Ticket\CommandBus;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Support\Form\Ticket as FormTicket;
use Support\Form\Comment as FormComment;
use Support\Entity\Ticket as TicketEntity;
use Support\Entity\Comment;
use Support\Controller\TicketController as Controller;

class TicketController implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $serviceLocator = $serviceManager->getServiceLocator();
        $formElementManager = $serviceLocator->get('FormElementManager');

        $ticketForm = $formElementManager->get(FormTicket::class);
        $commentForm = $formElementManager->get(FormComment::class);

        $entityManager = $serviceLocator->get(EntityManager::class);

        $ticketRepository = $entityManager->getRepository(TicketEntity::class);
        $commentRepository = $entityManager->getRepository(Comment::class);

        $commandBus = $serviceLocator->get(CommandBus::class);

        return new Controller(
            $commandBus,
            $ticketForm,
            $commentForm,
            $ticketRepository,
            $commentRepository
        );
    }
}
