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

namespace Application\Controller;

use Application\Command\Ticket\CloseTicket;
use Application\Command\Ticket\CommandBus;
use Application\Command\Ticket\CommentOnTicket;
use Application\Command\Ticket\OpenNewTicket;
use Application\Command\Ticket\RemoveTicket;
use Application\Command\Ticket\ReopenTicket;
use Application\Command\Ticket\SolveTicket;
use Application\Filter\Ticket as TicketFormFilter;
use Application\Form\Ticket;
use Application\Form\Comment;
use Doctrine\ORM\EntityRepository;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TicketController extends AbstractActionController
{
    /**
     * @var CommandBus
     */
    protected $commandBus;
    /**
     * @var Ticket
     */
    protected $ticketForm;
    /**
     * @var Comment
     */
    protected $commentForm;

    protected $ticketRepository;
    protected $commentRepository;

    public function __construct(
        CommandBus $commandHandler,
        Ticket $ticketForm,
        Comment $commentForm,
        EntityRepository $ticketRepository,
        EntityRepository $commentRepository
    ) {
        $this->commandBus        = $commandHandler;
        $this->ticketForm        = $ticketForm;
        $this->commentForm       = $commentForm;
        $this->ticketRepository  = $ticketRepository;
        $this->commentRepository = $commentRepository;
    }

    public function indexAction()
    {
        $tickets = $this->ticketRepository->findAll();

        return new ViewModel(['tickets' => $tickets]);
    }

    public function viewAction()
    {
        $uuid     = $this->params()->fromRoute('ticketId');
        $result = $this->ticketRepository->find($uuid);
        $comments = $this->commentRepository->findBy(['ticket' => $uuid]);

        return new ViewModel([
            'ticketData'  => $result,
            'commentForm' => $this->commentForm,
            'comments'    => $comments,
        ]);
    }

    public function openAction()
    {
        $request = $this->getRequest();
        $form    = $this->ticketForm;

        if ($request->isPost()) {
            $formFilter = new TicketFormFilter();
            $form->setInputFilter($formFilter->getInputFilter());

            $form->setData($request->getPost());

            if ($form->isValid()) {
                return $this->registerNewTicket($form->getData());
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    public function editTicketAction()
    {
        $form     = $this->ticketForm;
        $ticketId = $this->params('ticketId');

        $ticketInformation = $this->ticketRepository->find($ticketId);

        $form->setData([
            'subject'     => $ticketInformation->getSubject(),
            'description' => $ticketInformation->getDescription(),
            'importance'  => $ticketInformation->getImportance(),
        ]);

        $form->get('submit')->setValue('Update');

        return new ViewModel([
            'form' => $form
        ]);
    }

    public function removeTicketAction()
    {
        $id = $this->params('ticketId');
        $this->commandBus->push(new RemoveTicket($id));

        $this->redirect()->toRoute('ticket');
    }

    public function reopenAction()
    {
        $id = $this->params('ticketId');
        $this->commandBus->push(new ReopenTicket($id));

        return $this->redirect()->toRoute(
            'ticket/view',
            ['ticketId' => $id]
        );
    }

    public function closeTicketAction()
    {
        $id = $this->params('ticketId');
        $this->commandBus->push(new CloseTicket($id));

        return $this->redirect()->toRoute(
            'ticket/view',
            ['ticketId' => $id]
        );
    }

    public function solveTicketAction()
    {
        $id = $this->params('ticketId');
        $this->commandBus->push(new SolveTicket($id));

        return $this->redirect()->toRoute(
            'ticket/view',
            ['ticketId' => $id]
        );
    }

    public function commentAction()
    {
        $id = $this->params('ticketId');
        $request = $this->getRequest();

        if (! $request->isPost()) {
            $this->redirect()->toRoute('ticket/view', ['ticketId' => $id]);
        }

        $id     = $this->params('ticketId');
        $ticket = $this->ticketRepository->find($id);
        $this->commandBus->push(
            new CommentOnTicket($ticket, $request->getPost()->get('comment'))
        );

        return $this->redirect()->toRoute(
            'ticket/view',
            ['ticketId' => $id]
        );
    }

    protected function registerNewTicket(array $validData)
    {
        $newTicket = new OpenNewTicket(
            $validData['subject'],
            $validData['description'],
            $validData['importance'],
            1, // @todo probably not needed
            1  // @todo $this->authService->getIdentity()->getId()
        );

        $this->commandBus->push($newTicket);

        return $this->redirect()->toRoute(
            'ticket/view',
            ['ticketId' => $newTicket->getUuid()]
        );
    }
}
