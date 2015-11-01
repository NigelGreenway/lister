<?php
namespace App\Triage\Action;

use Ramsey\Uuid\Uuid;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;

final class ReportBugAction
{
    private $view;
    private $logger;

    public function __construct(
        Twig $view,
        LoggerInterface $logger,
        Messages $flash,
        \PDO $db,
        Router $router
    ) {
        $this->view = $view;
        $this->logger = $logger;
        $this->flash = $flash;
        $this->db = $db;
        $this->router = $router;
    }

    public function dispatch(Request $request, Response $response, array $args = [])
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
        $this->logger->info("Report bug page action dispatched");

        $this->view->render($response, 'triage/report_bug.html.twig');

        return $response;
    }

    private function post(Request $request, Response $response, array $args = [])
    {
        $this->logger->info("A requirement has been recorded");
        $this->logger->info('Requirement Recorded', ['post' => json_encode($request->getParam('requirement'))]);

        $data = $request->getParam('bug');
        $q = $this
            ->db
            ->prepare("INSERT INTO bugs
                    (id, summary, expected, actual, date_reported)
                VALUES
                    (:id, :summary, :expected, :actual, :date_reported)
            ");
        $q->execute([
            ':id' => $id = Uuid::uuid4(),
            ':summary' => $data['summary'],
            ':expected' => $data['expected'],
            ':actual' => isset($data['actual']) === true ? 1 : 0,
            ':date_reported' => (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
        ]);

        $q = $this->db->prepare("INSERT INTO triage
            (id, table_link, bug_id)
          VALUES
            (:id, :table_link, :bug_id)
        ");
        $q->execute([
            ':id' => Uuid::uuid4(),
            ':table_link' => 'bug',
            ':bug_id'   => (string) $id,
        ]);

        $this->flash->addMessage('success', 'Bug has been reported');

        header('Location:'.$this->router->pathFor('triage.bug.report'));
        exit;
    }
}
