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