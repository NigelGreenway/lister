<?php
// Routes

$app->get('/', 'App\Action\HomeAction:dispatch')
    ->setName('homepage');


////
// Triage
$app->get('/triage', 'App\Triage\Action\TriageHomeAction:dispatch')
    ->setName('triage.home');

////
// Requirement Management
////
$app->map(['GET', 'POST'], '/triage/requirements/record', 'App\Triage\Action\RecordRequirementAction:dispatch')
    ->setName('triage.requirement.record');

$app->map(['GET', 'POST'], '/triage/requirements/edit/{id}', 'App\Triage\Action\EditRequirementAction:dispatch')
    ->setName('triage.requirement.edit');

$app->map(['GET'], '/triage/requirements/archive/{id}', 'App\Triage\Action\ArchiveRequirementAction:dispatch')
    ->setName('triage.requirement.archive');

$app->map(['GET', 'POST'], '/triage/requirements/view/{id}', 'App\Triage\Action\ViewRequirementAction:dispatch')
    ->setName('triage.requirement.view');
////
// Bug management
////
$app->map(['GET', 'POST'], '/triage/bugs/report', 'App\Triage\Action\ReportBugAction:dispatch')
    ->setName('triage.bug.report');
////
// Support Management
////
$app->get('/triage/support/request', 'App\Triage\Action\RequestSupportAction:dispatch')
    ->setName('triage.support.request');
//
/////