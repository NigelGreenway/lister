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

        $q = $this->db->prepare("
            SELECT
                t.id,
                t.table_link,
                (
                    CASE t.table_link
                        WHEN 'requirement'
                        THEN t.requirement_id
                        ELSE t.bug_id
                    END
                ) AS link_id,
                (
                    CASE t.table_link
                        WHEN 'requirement'
                        THEN (SELECT r.story FROM requirements r WHERE r.id = t.requirement_id AND r.is_archived = 0)
                        ELSE (SELECT b.summary FROM bugs b         WHERE b.id = t.bug_id AND b.is_archived = 0)
                    END
                ) AS summary,
                (
                    CASE t.table_link
                        WHEN 'requirement'
                        THEN (SELECT r.is_archived FROM requirements r WHERE r.id = t.requirement_id AND r.is_archived = 0)
                        ELSE (SELECT b.is_archived FROM bugs b         WHERE b.id = t.bug_id AND b.is_archived = 0)
                    END
                ) AS is_archived

            FROM
                triage t
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
