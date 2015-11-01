<?php
namespace App\Triage\Action;

use Slim\Http\Request;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;

final class TriageHomeAction
{
    private $view;
    private $logger;
    private $db;

    public function __construct(
        Twig            $view,
        LoggerInterface $logger,
        \PDO            $db
    ) {
        $this->view = $view;
        $this->logger = $logger;
        $this->db = $db;
    }

    public function dispatch(Request $request, $response, $args)
    {
        $this->logger->info("Triage home page action dispatched");

        $q = $this->db->prepare("SELECT
                id,
                table_link,
                IF (
                    requirement_id IS NOT NULL,
                    (SELECT r.story FROM requirements r WHERE r.id = requirement_id ),
                    IF (
                        bug_id IS NOT NULL,
                        (SELECT b.summary FROM bugs b WHERE b.id = bug_id ),
                        NULL
                    )
                ) as 'summary'
            FROM
                triage
        ");
        $q->execute();

        $this->view->render(
            $response,
            'triage/triage_home.html.twig',
            [
                'data' => $q->fetchAll(\PDO::FETCH_ASSOC),
            ]
        );
        return $response;
    }
}
