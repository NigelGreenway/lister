<?php
namespace App\Triage\Action;

use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;

final class EditRequirementAction
{
    private $view;
    private $logger;

    public function __construct(
        Twig            $view,
        LoggerInterface $logger,
        Messages        $flash,
        \PDO            $db
    ) {
        $this->view   = $view;
        $this->logger = $logger;
        $this->flash  = $flash;
        $this->db     = $db;
    }

    public function dispatch(Request $request, $response, $args)
    {
        switch ($request->getMethod()) {
            case 'GET':
                return $this->get($request, $response, $args);
                break;
            case 'POST':
                return $this->post($request, $response, $args);
                break;
        }
    }

    private function get(Request $request, Response $response, array $args = [])
    {
        $this->logger->info("Edit requirement page action dispatched");

        $q = $this->db->prepare("SELECT * FROM requirements WHERE id = :id");
        $q->execute([
            ':id' => $args['id'],
        ]);
        $requirement = $q->fetch(\PDO::FETCH_ASSOC);
        $q = $this->db->prepare("SELECT * FROM requirements WHERE is_a_theme = 1");
        $q->execute();
        $requirements = $q->fetchAll(\PDO::FETCH_ASSOC);

        $this->view->render(
            $response,
            'triage/edit_requirement.html.twig',
            [
                'requirement'  => $requirement,
                'requirements' => $requirements,
            ]
        );
        return $response;
    }

    private function post(
        Request  $request,
        Response $response,
        array    $args = []
    ) {
        $this->logger->info("A requirement has been edited");

        $data = $request->getParam('requirement');
        $q = $this
            ->db
            ->prepare("UPDATE requirements SET
                    story      = :story,
                    priority   = :priority,
                    is_a_theme = :theme,
                    estimate   = :estimate,
                    parent     = :parent
                WHERE
                  id = :id
            ");
        $q->execute([
            ':id' => $data['id'],
            ':story' => $data['story'],
            ':priority' => $data['priority'],
            ':theme' => isset($data['theme']) === true ? 1 : 0,
            ':estimate' => $data['estimate'],
            ':parent' => $data['parent'] == '-' ? null : $data['parent'],
        ]);

        $this->flash->addMessage('success', 'Requirement has been updated');

        header('Location:'.$this->router->pathFor('triage.requirement.edit'));
        exit;
    }
}
