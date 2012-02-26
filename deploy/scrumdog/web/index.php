<?php

$domainArrayReversed = array_reverse(split('\.', $_SERVER['SERVER_NAME']));
$subdomain = isset($domainArrayReversed[2]) ? $domainArrayReversed[2] : '';
$second_subdomain = isset($domainArrayReversed[3]) ? $domainArrayReversed[3] : '';

//This is for switching the environment based on the subdomain
$debug = false;
switch($subdomain)
{
    case 'local':
    case 'dev':
        $env = 'dev';
        $debug = true;
		break;
	case 'stage':
		$env = 'stage';
		break;
    case 'www':
	default:
		$env = 'prod';
}

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', $env, $debug);

sfContext::createInstance($configuration)->dispatch();