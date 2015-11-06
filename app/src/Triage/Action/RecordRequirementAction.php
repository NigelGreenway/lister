<?php
namespace App\Triage\Action;

use Ramsey\Uuid\Uuid;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;

final class RecordRequirementAction
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
                return $this->get($response);
                break;
            case 'POST':
                return $this->post($request);
                break;
        }
    }

    private function get(
        Response $response
    ) {
        $this->logger->info("Record requirement page action dispatched");

        $q = $this->db->prepare("SELECT * FROM requirements WHERE is_a_theme = 1");
        $q->execute();

        $this->view->render(
            $response,
            'triage/add_requirement.html.twig',
            [
                'requirements' => $q->fetchAll(\PDO::FETCH_ASSOC),
            ]
        );
        return $response;
    }

    private function post(
        Request  $request
    ) {
        $this->logger->info("A requirement has been recorded");
        $this->logger->info('Requirement Recorded', ['post' => json_encode($request->getParam('requirement'))]);

        $data = $request->getParam('requirement');
        $q = $this
            ->db
            ->prepare("INSERT INTO requirements
                    (id, story, priority, is_a_theme, estimate, parent, date_recorded)
                VALUES
                    (:id, :story, :priority, :theme, :estimate, :parent, :date_recorded)
            ");
        $q->execute([
            ':id' => $requirementId = Uuid::uuid4(),
            ':story' => $data['story'],
            ':priority' => $data['priority'],
            ':theme' => isset($data['theme']) === true ? 1 : 0,
            ':estimate' => $data['estimate'],
            ':parent' => $data['parent'] == '-' ? null : $data['parent'],
            ':date_recorded' => (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
        ]);

        $comment = $request->getParam('requirement_comments');
        $q = $this
            ->db
            ->prepare("INSERT INTO requirement_comments
                    (id, comment, requirement_id)
                VALUES
                    (:id, :comment, :requirement_id)
            ");
        $q->execute([
            ':id' => $commentId = Uuid::uuid4(),
            ':comment' => $comment,
            ':requirement_id' => $requirementId,
        ]);

        $q = $this->db->prepare("INSERT INTO triage
            (id, table_link, requirement_id)
          VALUES
            (:id, :table_link, :requirement_id)
        ");
        $q->execute([
            ':id' => Uuid::uuid4(),
            ':table_link' => 'requirement',
            ':requirement_id'   => (string) $requirementId,
        ]);

        $this->flash->addMessage('success', 'Requirement has been recorded 1');

        header('Location:'.$this->router->pathFor('triage.requirement.record'));
        exit;
    }
}
