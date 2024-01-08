<?php

//ftheeten 2018 04 17        
function array_filter_recursive($array, $callback = null) {
    foreach ($array as $key => & $value) {
        if (is_array($value)) {
            $value = array_filter_recursive($value, $callback);
        }
        else {
            if ( ! is_null($callback)) {
                if ( ! $callback($value)) {
                    unset($array[$key]);
                }
            }
            else {
                if ( ! (bool) $value) {
                    unset($array[$key]);
                }
            }
        }
    }
    unset($value);

    return $array;
}

function word2color($w){
  if (strlen($w)==0) return substr('00000' . dechex(mt_rand(0, 0xffffff)), -6);
  while (strlen($w)<6) $w.=$w;
  $minbrightness=1;  // range from 0 to 15, if this is 0 then for ex. black is allowed
  $max_brightness=14; // range from 0 to 15, if this is 15 then for ex. white is allowed
  $plus_red=0;    // set one of these to set the probability of one of these colors higher
  $plus_green=0;
  $plus_blue=0;
  $r='';
  for ($i=1; $i<6; $i++) {
      #$r.= '">';// this is a depug mode, to see the color written
      $plus=0;
      if ($plus_red<>0 && $i==0) $plus=$plus_red;
      if ($plus_green<>0 && $i==2) $plus=$plus_green;
      if ($plus_blue<>0 && $i==4) $plus=$plus_blue;

      $offset = round(strlen($w)/6*$i);
      $c= substr ($w, $offset, 1);
      $dec=ord($c)%($max_brightness+$plus-$minbrightness) +$minbrightness+$plus;
      if ($dec>$max_brightness-$minbrightness) $dec=$max_brightness-$minbrightness;
      $r.= strtoupper( dechex($dec) );
  }
  return $r;
}

function help_ico($message, sfOutputEscaperObjectDecorator $sf_user)
{
  if(! $sf_user->getHelpIcon()) return '';
  return '<div class="help_ico"><span>'.$message.'</span></div>';
}

/**
 * Construct the base url for given report
 * Return empty if not possible to construct
 * @return string the url constructed - empty if not possible
 * @param string $name name of report
 * @param string $lang language of report
 * @param string $format format of report (pdf, odt,...)
 */
function constructReportBaseUrl($name, $lang, $format){

  $servers_config = sfConfig::get('dw_reports_servers', array());
  $names_and_options_config = sfConfig::get('dw_reports_names_and_options');
  $url = '';
  if(!empty($name) && !empty($format)) {
    if (!empty($names_and_options_config[ $name ][ 'server' ]) &&
        !empty($servers_config[ $names_and_options_config[ $name ][ 'server' ] ]) &&
        !empty($servers_config[ $names_and_options_config[ $name ][ 'server' ] ][ 'server' ])
    ) {
      $url .=
        (
        (!empty($servers_config[ $names_and_options_config[ $name ][ 'server' ] ][ 'protocol' ])) ?
          $servers_config[ $names_and_options_config[ $name ][ 'server' ] ][ 'protocol' ] :
          'http'
        ) . '://' .
        (
        (!empty($servers_config[ $names_and_options_config[ $name ][ 'server' ] ][ 'username' ])) ?
          (
          (!empty($servers_config[ $names_and_options_config[ $name ][ 'server' ] ][ 'password' ])) ?
            $servers_config[ $names_and_options_config[ $name ][ 'server' ] ][ 'username' ] . ':' . $servers_config[ $names_and_options_config[ $name ][ 'server' ] ][ 'password' ] :
            $servers_config[ $names_and_options_config[ $name ][ 'server' ] ][ 'username' ]
          ) . '@' :
          ''
        ) .
        $servers_config[ $names_and_options_config[ $name ][ 'server' ] ][ 'server' ] .
        (
        (!empty($servers_config[ $names_and_options_config[ $name ][ 'server' ] ][ 'port' ])) ?
          $servers_config[ $names_and_options_config[ $name ][ 'server' ] ][ 'port' ] :
          ''
        ) .
        (
        (!empty($servers_config[ $names_and_options_config[ $name ][ 'server' ] ][ 'base_path' ])) ?
          $servers_config[ $names_and_options_config[ $name ][ 'server' ] ][ 'base_path' ] :
          ''
        ) . '/';
    }
    elseif (!empty($names_and_options_config[ 'default' ][ 'server' ]) &&
            !empty($servers_config[ $names_and_options_config[ 'default' ][ 'server' ] ]) &&
            !empty($servers_config[ $names_and_options_config[ 'default' ][ 'server' ] ][ 'server' ])
    ) {
      $url .=
        (
        (!empty($servers_config[ $names_and_options_config[ 'default' ][ 'server' ] ][ 'protocol' ])) ?
          $servers_config[ $names_and_options_config[ 'default' ][ 'server' ] ][ 'protocol' ] :
          'http'
        ) . '://' .
        (
        (!empty($servers_config[ $names_and_options_config[ 'default' ][ 'server' ] ][ 'username' ])) ?
          (
          (!empty($servers_config[ $names_and_options_config[ 'default' ][ 'server' ] ][ 'password' ])) ?
            $servers_config[ $names_and_options_config[ 'default' ][ 'server' ] ][ 'username' ] . ':' . $servers_config[ $names_and_options_config[ 'default' ][ 'server' ] ][ 'password' ] :
            $servers_config[ $names_and_options_config[ 'default' ][ 'server' ] ][ 'username' ]
          ) . '@' :
          ''
        ) .
        $servers_config[ $names_and_options_config[ 'default' ][ 'server' ] ][ 'server' ] .
        (
        (!empty($servers_config[ $names_and_options_config[ 'default' ][ 'server' ] ][ 'port' ])) ?
          ':' . $servers_config[ $names_and_options_config[ 'default' ][ 'server' ] ][ 'port' ] :
          ''
        ) .
        (
        (!empty($servers_config[ $names_and_options_config[ 'default' ][ 'server' ] ][ 'base_path' ])) ?
          $servers_config[ $names_and_options_config[ 'default' ][ 'server' ] ][ 'base_path' ] :
          ''
        ) . '/';
    }

    if (!empty($names_and_options_config[ $name ][ 'path' ])) {
      $url .= $names_and_options_config[ $name ][ 'path' ] . '/';
    }

    $url .= $name;

    if(!empty($lang)) {
      if (!empty($names_and_options_config[ $name ][ 'language_added_to_name' ])) {
        $url .= '_' . $lang;
      }
      elseif (!empty($names_and_options_config[ 'default' ][ 'language_added_to_name' ])) {
        $url .= '_' . $lang;
      }
    }

    if(!empty($format)) {
      $url .= '.' . $format;
    }
  }

  return $url;
}


    function startsWith($string, $test) 
    { 
        print("test $string $test");
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen) return false;
            return substr_compare($string, $test, 0, $testlen) === 0;
    }
    
    function endsWith($string, $test) 
    {
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen) return false;
            return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
    }
    
    function string_isset($str)
    {
        if(isset($str))
        {
            if(strlen(trim($str))>0)
            {
                return TRUE;
            }
        }
        return FALSE;
        
    }

  //ftheeten 2018 08 06
    class CustomDarwinError extends Exception
    {

        public function setMessage($message){
            $this->message = $message;
        }
    }
    
  //2019 02 27
    
  function hstore2array($param)
  {
        $param=html_entity_decode($param);
        return json_decode('{' . str_replace('"=>"', '":"', $param) . '}', true);
  }
  
  function detect_menu_hidden()
  {
     $flagMenu=true;
    if(array_key_exists("menu", $_REQUEST))
    {       
        if($_REQUEST['menu']=="off")
        {
          
            $flagMenu=false;
            $_SESSION['DW_REFERER']=$referer_domain;
            $_SESSION['menu']= $flagMenu;  
        }
        else
        {
             $_SESSION['menu']= "on";  
        }
    }
    elseif(array_key_exists("menu", $_SESSION))
    {      
        if($_SESSION['menu']=="off")
        {         
            $flagMenu=false;          
        }
        
    }
    return $flagMenu;
  }
  
 

?>

