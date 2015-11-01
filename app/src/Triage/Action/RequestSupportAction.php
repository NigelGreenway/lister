<?php
namespace App\Triage\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;

final class RequestSupportAction
{
    private $view;
    private $logger;

    public function __construct(Twig $view, LoggerInterface $logger)
    {
        $this->view = $view;
        $this->logger = $logger;
    }

    public function dispatch($request, $response, $args)
    {
        $this->logger->info("Request support page action dispatched");

        $this->view->render($response, 'triage/request_support.html.twig');
        return $response;
    }
}
