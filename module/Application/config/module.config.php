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
use Application\Entity\Comment;
use Application\Form\Comment as FormComment;
use Application\Form\Ticket as FormTicket;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Router\Http\Literal;
use Application\Controller\IndexController;
use Application\Controller\TicketController;
use Zend\Mvc\Router\Http\Segment;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Application\Entity\Ticket as TicketEntity;
use Application\Command\Ticket\Handler;

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
                    'close' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/close/:ticketId',
                            'constraints' => [
                                'ticketId' => '[a-zA-Z0-9-]{36}',
                            ],
                            'defaults' => [
                                'controller' => TicketController::class,
                                'action'     => 'closeTicket',
                            ],
                        ],
                    ],
                    'solve' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/solve/:ticketId',
                            'constraints' => [
                                'ticketId' => '[a-zA-Z0-9-]{36}',
                            ],
                            'defaults' => [
                                'controller' => TicketController::class,
                                'action'     => 'solveTicket',
                            ],
                        ],
                    ],
                    'reopen' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/reopen/:ticketId',
                            'constraints' => [
                                'ticketId' => '[a-zA-Z0-9-]{36}',
                            ],
                            'defaults' => [
                                'controller' => TicketController::class,
                                'action'     => 'reopen',
                            ],
                        ],
                    ],
                    'comment' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/comment/:ticketId',
                            'constraints' => [
                                'ticketId' => '[a-zA-Z0-9-]{36}',
                            ],
                            'defaults' => [
                                'controller' => TicketController::class,
                                'action'     => 'comment',
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
                            'route'    => '/remove/:ticketId',
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
                $entityManager = $em->get(EntityManager::class);

                $commandTicketCollection = [
                    new Handler\OpenNewTicket($entityManager),
                    new Handler\RemoveTicket($entityManager),
                    new Handler\CommentOnTicket($entityManager),
                    new Handler\CloseTicket($entityManager),
                    new Handler\ReopenTicket($entityManager),
                    new Handler\SolveTicket($entityManager),
                ];

                return new CommandBus($commandTicketCollection);
            },
        ],
    ],

    'controllers' => [
        'invokables' => [
            IndexController::class => IndexController::class,
        ],

        'factories' => [
            TicketController::class => function ($em) {

                $serviceLocator     = $em->getServiceLocator();
                $formElementManager = $serviceLocator->get('FormElementManager');

                $ticketForm  = $formElementManager->get(FormTicket::class);
                $commentForm = $formElementManager->get(FormComment::class);

                $entityManager = $serviceLocator->get(EntityManager::class);

                $ticketRepository  = $entityManager->getRepository(TicketEntity::class);
                $commentRepository = $entityManager->getRepository(Comment::class);

                $commandBus = $serviceLocator->get(CommandBus::class);

                return new TicketController(
                    $commandBus,
                    $ticketForm,
                    $commentForm,
                    $ticketRepository,
                    $commentRepository
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
