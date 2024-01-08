<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it or make something more sophisticated.
//ftheeten 2023 04 07
if(array_key_exists("HTTP_X_FORWARDED_FOR",$_SERVER))
{
	$src=$_SERVER['HTTP_X_FORWARDED_FOR'];
}
else
{
	$src=$_SERVER['REMOTE_ADDR'];
}
if(!filter_var($src, FILTER_VALIDATE_IP))
{
	$src=$_SERVER['REMOTE_ADDR'];
}

$test=file_get_contents ("ips.cfg");


$list_ips=explode("\r",$test);

$go=false;
foreach($list_ips as $url)
{
	if(strpos($src, trim($url)) === 0)
	{
		
		$go=true;
	}	
}
$go=true;
if ($go)
{

	require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

	$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'dev', true);
	sfContext::createInstance($configuration)->dispatch();

}
else
{
	header('HTTP/1.0 403 Forbidden');
}
