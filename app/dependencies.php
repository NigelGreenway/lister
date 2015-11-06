<?php
// DIC configuration

$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new \Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension(
        $c->get('router'),
        $c->get('request')->getUri()
    ));
    $view->addExtension(new \Custom\ListerTwigExtension(
        $c->get('flash'))
    );
    $view->addExtension(new Twig_Extension_Debug());

    return $view;
};

// Flash messages
$container['flash'] = function ($c) {
    return new \Slim\Flash\Messages;
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings');
    $logger = new \Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['logger']['path'], \Monolog\Logger::DEBUG));
    return $logger;
};

$container['database.read'] = function($c) {
    $settings = $c->get('settings')['database']['read'];

    $read = new \PDO(
        sprintf(
            '%s:host=%s;dbname=%s;charset=%s',
            $settings['connector'],
            $settings['host'],
            $settings['name'],
            $settings['charset']
        ),
        $settings['user'],
        $settings['password']
    );

    $read->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $read;
};

// -----------------------------------------------------------------------------
// Action factories
// -----------------------------------------------------------------------------

$container['App\Action\HomeAction'] = function ($c) {
    return new App\Action\HomeAction($c->get('view'), $c->get('logger'), $c->get('database.read'));
};

// ----------------------------------------------------------------------------
// Triage Action Factory
// ----------------------------------------------------------------------------
$container['App\Triage\Action\TriageHomeAction'] = function ($c) {
    return new App\Triage\Action\TriageHomeAction(
        $c->get('view'),
        $c->get('logger'),
        $c->get('database.read')
    );
};
$container['App\Triage\Action\RecordRequirementAction'] = function ($c) {
    return new App\Triage\Action\RecordRequirementAction(
        $c->get('view'),
        $c->get('logger'),
        $c->get('flash'),
        $c->get('database.read'),
        $c->get('router')
    );
};
$container['App\Triage\Action\ViewRequirementAction'] = function ($c) {
    return new App\Triage\Action\ViewRequirementAction(
        $c->get('view'),
        $c->get('database.read'),
        $c->get('router')
    );
};
$container['App\Triage\Action\EditRequirementAction'] = function ($c) {
    return new App\Triage\Action\EditRequirementAction(
        $c->get('view'),
        $c->get('logger'),
        $c->get('flash'),
        $c->get('database.read')
    );
};
$container['App\Triage\Action\ArchiveRequirementAction'] = function ($c) {
    return new App\Triage\Action\ArchiveRequirementAction(
        $c->get('view'),
        $c->get('logger'),
        $c->get('flash'),
        $c->get('database.read'),
        $c->get('router')
    );
};

$container['App\Triage\Action\ReportBugAction'] = function ($c) {
    return new App\Triage\Action\ReportBugAction(
        $c->get('view'),
        $c->get('logger'),
        $c->get('flash'),
        $c->get('database.read'),
        $c->get('router')
    );
};
$container['App\Triage\Action\RequestSupportAction'] = function ($c) {
    return new App\Triage\Action\RequestSupportAction($c->get('view'), $c->get('logger'));
};