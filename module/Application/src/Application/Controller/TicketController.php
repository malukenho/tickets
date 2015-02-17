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

use Application\Command\Ticket\CommandBus;
use Application\Command\Ticket\OpenNewTicket;
use Application\Command\Ticket\RemoveTicket;
use Application\Command\Ticket\TicketIdentifier;
use Application\Filter\Ticket as TicketFilter;
use Application\Form\Ticket;
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

    protected $ticketRepository;

    public function __construct(CommandBus $commandHandler, Ticket $ticketForm, $repository)
    {
        $this->commandBus       = $commandHandler;
        $this->ticketForm       = $ticketForm;
        $this->ticketRepository = $repository;
    }

    public function indexAction()
    {
        $tickets = $this->ticketRepository->findAll();

        return new ViewModel(['tickets' => $tickets]);
    }

    public function viewAction()
    {
        return new ViewModel();
    }

    public function openAction()
    {
        $request = $this->getRequest();
        $form    = $this->ticketForm;

        if ($request->isPost()) {

            $ticketFilter = new TicketFilter();
            $form->setInputFilter($ticketFilter->getInputFilter());
            $form->setData($request->getPost()->toArray());

            if ($form->isValid()) {
                return $this->registerNewTicket($form->getData());
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    public function removeTicketAction()
    {
        $id = $this->params('id');
        $this->commandBus->handle(new RemoveTicket($id));

        $this->redirect('ticket');
    }

    protected function registerNewTicket(array $validData)
    {
        $result = $this->commandBus->handle(
            new OpenNewTicket(
                $validData['subject'],
                $validData['description'],
                $validData['importance'],
                1, // @todo probably not needed
                1  // @todo $this->authService->getIdentity()->getId()
            )
        );

        if ($result) {
            return $this->redirect(
                'ticket/view',
                ['ticketId' => $result->getTicketId()]
            );
        }
    }
}
