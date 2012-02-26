<?php

// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it or make something more sophisticated.
// A web request comes through with argc = 0 so we can use this to verify if php is executing on the command line or not

if ($_SERVER['argc'] == 0)
{
  // Redirect to the homepage
  header('Location: http://'.$_SERVER['SERVER_NAME']);
  exit();
}

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

// Actual CRON code below can now access all the Symfony models and functions
ignore_user_abort(TRUE);
set_time_limit(0);
ini_set('memory_limit', '256M');

$env = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'prod';

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', $env, true);
$contextInstance = sfContext::createInstance($configuration);

echo ">> Running reminder email cron job in $env mode >>\r\n";

$request = $contextInstance->getRequest();

$request->setParameter('module', 'cron');
$request->setParameter('action', 'sendReminderEmails');

$contextInstance->dispatch();