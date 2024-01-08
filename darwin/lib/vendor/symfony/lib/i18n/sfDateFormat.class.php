<?php
//ftheeten debug

class sfDateFormat
{

 public function format($time, $pattern = 'F', $inputPattern = null, $charset = 'UTF-8')
  {
    return date('Y-m-d H:m:s',strtotime($time));
  }
}