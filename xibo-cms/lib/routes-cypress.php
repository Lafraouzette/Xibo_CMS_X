<?php


defined('XIBO') or die('Sorry, you are not allowed to directly access this page.');


// Cypress endpoints
// these are removed during the docker build process and are not in the final release files.
$app->post('/createCommand', ['\Xibo\Controller\CypressTest','createCommand']);
$app->post('/createCampaign', ['\Xibo\Controller\CypressTest','createCampaign']);
$app->post('/scheduleCampaign', ['\Xibo\Controller\CypressTest','scheduleCampaign']);
$app->post('/displaySetStatus', ['\Xibo\Controller\CypressTest','displaySetStatus']);
$app->get('/displayStatusEquals', ['\Xibo\Controller\CypressTest','displayStatusEquals']);
