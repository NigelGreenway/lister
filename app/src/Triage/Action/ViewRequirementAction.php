<?php
namespace App\Triage\Action;

use Ramsey\Uuid\Uuid;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Slim\Views\Twig;

final class ViewRequirementAction
{
    private $view;

    public function __construct(
        Twig            $view,
        \PDO            $db,
        Router          $router
    ) {
        $this->view   = $view;
        $this->db     = $db;
        $this->router = $router;
    }

    public function dispatch(Request $request, $response, $args)
    {
        switch ($request->getMethod()) {
            case 'GET':
                return $this->get($response, $args);
                break;
            case 'POST':
                return $this->post($request, $args);
                break;
        }
    }

    private function get(Response $response, array $args = [])
    {
        $q = $this->db->prepare("SELECT * FROM requirements WHERE id = :id");
        $q->execute([
            ':id' => $args['id'],
        ]);
        $requirement = $q->fetch(\PDO::FETCH_ASSOC);
        $q = $this->db->prepare("SELECT * FROM requirement_comments WHERE requirement_id = :id ORDER BY date_recorded DESC");
        $q->execute([
            ':id' => $args['id'],
        ]);
        $comments = $q->fetchAll(\PDO::FETCH_ASSOC);

        $this->view->render(
            $response,
            'triage/view_requirement.html.twig',
            [
                'requirement'  => $requirement,
                'comments' => $comments,
            ]
        );
        return $response;
    }

    private function post(Request $request, array $args = [])
    {
        $comment = $request->getParam('comment');
        $q = $this
            ->db
            ->prepare("INSERT INTO requirement_comments
                    (id, comment, requirement_id, date_recorded)
                VALUES
                    (:id, :comment, :requirement_id, :datetime)
            ");
        $q->execute([
            ':id' => $commentId = Uuid::uuid4(),
            ':comment' => $request->getParam('comment'),
            ':requirement_id' => $args['id'],
            ':datetime' => (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
        ]);

        header('Location: '.$this->router->pathFor('triage.requirement.view', ['id' => $args['id']]));
        exit;
    }
}
