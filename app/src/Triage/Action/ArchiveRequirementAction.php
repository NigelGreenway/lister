<?php
namespace App\Triage\Action;

use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;

final class ArchiveRequirementAction
{
    private $view;
    private $logger;

    public function __construct(
        Twig            $view,
        LoggerInterface $logger,
        Messages        $flash,
        \PDO            $db,
        Router          $router
    ) {
        $this->view   = $view;
        $this->logger = $logger;
        $this->flash  = $flash;
        $this->db     = $db;
        $this->router = $router;
    }

    public function dispatch(Request $request, $response, $args)
    {
        switch ($request->getMethod()) {
            case 'GET':
                return $this->get($request, $response, $args);
                break;
        }
    }

    private function get(Request $request, Response $response, array $args = [])
    {
        $q = $this->db->prepare("UPDATE requirements SET is_archived = 1 WHERE id = :id");
        $q->execute([
            ':id' => $args['id'],
        ]);

        $this->logger->info(sprintf('[%s] has been archived', $args['id']));
        $this->flash->addMessage('success', sprintf('[%s] has been archived', $args['id']));

        header('Location: '.$this->router->pathFor('triage.home'));
        exit;
    }
}
