<?php

class ParsingMultimedia
{
  public $multimedia_data = array() ;
  public $multimedia ;
  public function __construct()
  {
    $this->multimedia= new Multimedia() ;
  }
  private function getContext()
  {
    $proxy = 'tcp://'.sfConfig::get('dw_recaptcha_proxy_host').":".sfConfig::get('dw_recaptcha_proxy_port');
    $opts = array(
      'http' => array(
          'proxy' => $proxy,
          'request_fulluri'=>true,
      ),
      'ftp' => array(
          'proxy' => $proxy,
          'request_fulluri'=>true,
      )
    );
    $context = stream_context_create($opts);
    return($context);
  }

  public function getFile($file)
  {
    $prefix = substr($file,0,strpos($file,"://")) ;
    if(substr($file,0,2) == "\\\\") $prefix = "smb" ; $file = str_replace("\\\\","smb://", $file) ; $file = str_replace("\\","/", $file) ;
    switch ($prefix)
    {
      case "http":
      case 'https':
      case "ftp" : $this->copyDistantFile($file) ; break ;
      case "smb" : $this->copySmbFile($file) ; break ;
      default : break ;
    }
  }

  private function copySmbFile($file)
  {
    $smb = new smb_stream_wrapper() ;
    if(!$smb->stream_open ($file, 'r', false,$this->getContext())) return false ;
    $this->saveFile($smb->stream,$file) ;
    $smb->stream_close() ;
  }

  private function copyDistantFile($file)
  {
    if(strpos($file,'naturalsciences.be')||strpos($file,'natuurwetenschappen.be')||strpos($file,'sciencesnaturelles.be'))
      $src = @fopen($file,'r') ;
    else
      $src = @fopen($file,'r',false, $this->getContext()) ;
    if(!$src) return false ;
    $this->saveFile($src,$file) ;
    fclose($src) ;
  }

  private function saveFile($src,$file)
  {
    $tempfilepath = sfConfig::get("dw_tempFilePath", "/tmp/temp_file");
    $dest = fopen($tempfilepath,'a') ;
    if(stream_copy_to_stream($src,$dest))
    {
      $this->multimedia_data['uri'] = sha1(substr($file,strrpos($file,'/', strlen($file))).rand());
      $this->multimedia_data['title'] = substr($file, strrpos($file,'/')+1, strlen($file)) ;
      $this->multimedia_data['mime_type'] = mime_content_type('/tmp/temp_file');
      $this->multimedia_data['type'] = ".".array_search($this->multimedia_data['mime_type'],Multimedia::$allowed_mime_type) ;
      if(!Multimedia::CheckMimeType($this->multimedia_data['mime_type'])) die('mauvais mime_type') ;
      rename($tempfilepath,sfConfig::get('sf_upload_dir')."/multimedia/temp/".$this->multimedia_data['uri']);
    }
    else
      return (false) ;
    fclose($dest) ;
  }

  public function isFileOk()
  {
    if(isset($this->multimedia_data['uri'])&&$this->multimedia_data['uri']!='')
    {
      $this->multimedia->fromArray($this->multimedia_data) ;
      return true ;
    }
    return false ;
  }
}
