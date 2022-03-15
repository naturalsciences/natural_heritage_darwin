<?php

// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it or make something more sophisticated.
$src=$_SERVER['HTTP_X_FORWARDED_FOR'];
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
#uncomment to debug
#$go=true;
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
