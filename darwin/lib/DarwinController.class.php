<?php

class DarwinController extends sfFrontWebController
{
	public function genUrl($parameters = array(), $absolute = false)
  {
    $url=parent::genUrl($parameters ,$absolute);
	$base=sfConfig::get('dw_root_url_darwin');
	if(substr( $base, 0, 5)=="https")
	{

		$base_no_ssl=str_replace("https", "http", $base);
		
			if(substr( $url, 0, strlen($base_no_ssl) ) === $base_no_ssl)
			{
				$url=str_replace($base_no_ssl, $base,$url);
			}
		
	}

    return $url;
  }
}
