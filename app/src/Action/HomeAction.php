<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;

final class HomeAction
{
    private $view;
    private $logger;

    public function __construct(
        Twig $view,
        LoggerInterface $logger,
        \PDO $db
    ) {
        $this->view = $view;
        $this->logger = $logger;
        $this->db = $db;
    }

    public function dispatch($request, $response, $args)
    {
        $this->logger->info("Home page action dispatched");

        $q = $this->db->prepare("SELECT
            (SELECT COUNT(*) FROM requirements) as 'requirements',
            (SELECT COUNT(*) FROM bugs) as 'bugs'
        ");
        $q->execute();

        $this->view->render(
            $response,
            'home.twig',
            [
                'data' => $q->fetch(\PDO::FETCH_ASSOC),
            ]
        );

        return $response;
    }
}
