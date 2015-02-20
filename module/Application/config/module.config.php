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

use Application\Command\Ticket\CommandBus;
use Application\Command\Ticket\OpenNewTicket;
use Application\Command\Ticket\RemoveTicket;
use Application\Command\Ticket\TicketCommandHandler;
use Application\Form\Ticket as FormTicket;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Router\Http\Literal;
use Application\Controller\IndexController;
use Application\Controller\TicketController;
use Zend\Mvc\Router\Http\Segment;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Application\Entity\Ticket as TicketEntity;
use Application\Controller\ConsultantController;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],

            'ticket' => [
                'type'    => Literal::class,
                'may_terminate' => true,
                'options' => [
                    'route'    => '/ticket',
                    'defaults' => [
                        'controller' => TicketController::class,
                        'action'     => 'index',
                    ],
                ],
                'child_routes' => [
                    'view' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/view/:ticketId',
                            'constraints' => [
                                'ticketId' => '[a-zA-Z0-9-]{36}',
                            ],
                            'defaults' => [
                                'controller' => TicketController::class,
                                'action'     => 'view',
                            ],
                        ],
                    ],
                    'form' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/open-ticket-form-page',
                            'defaults' => [
                                'controller' => TicketController::class,
                                'action'     => 'open',
                            ],
                        ],
                    ],
                    'register' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/register',
                            'defaults' => [
                                'controller' => TicketController::class,
                                'action'     => 'register',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/edit/:ticketId',
                            'constraints' => [
                                'ticketId'     => '[a-zA-Z0-9-]{36}',
                            ],
                            'defaults' => [
                                'controller' => TicketController::class,
                                'action'     => 'editTicket',
                            ],
                        ],
                    ],
                    'remove' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/remove/:id',
                            'constraints' => [
                                'id'     => '[a-zA-Z0-9-]{36}',
                            ],
                            'defaults' => [
                                'controller' => TicketController::class,
                                'action'     => 'removeTicket',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'factories' => [
            CommandBus::class => function ($em) {
                $commandTicketCollection = [
                    OpenNewTicket::class => 'handleOpenNewTicket',
                    RemoveTicket::class  => 'handleRemoveTicket',
                ];

                $entityManager = $em->get(EntityManager::class);
                $ticketCommandHandler = new TicketCommandHandler($entityManager);

                return new CommandBus($commandTicketCollection, $ticketCommandHandler);
            },
        ],
    ],

    'controllers' => [
        'invokables' => [
            IndexController::class => IndexController::class,
        ],

        'factories' => [
            TicketController::class => function ($em) {

                $serviceLocator = $em->getServiceLocator();

                $ticketForm = $serviceLocator
                    ->get('FormElementManager')
                    ->get(FormTicket::class);

                $ticketRepository = $serviceLocator
                    ->get(EntityManager::class)
                    ->getRepository(TicketEntity::class);

                $commandBus = $serviceLocator->get(CommandBus::class);

                return new TicketController(
                    $commandBus,
                    $ticketForm,
                    $ticketRepository
                );
            },
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'doctrine' => [
        'driver' => [
            'application_entity' => [
                'class' => AnnotationDriver::class,
                'paths' => realpath(__DIR__ . '/../src/Application/Entity'),
            ],

            'orm_default' => [
                'drivers' => [
                    'Application\Entity' => 'application_entity',
                ],
            ],
        ],
    ],
];
