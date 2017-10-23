<?php
class myValidatedFile extends sfValidatedFile
{
  public function save($file = null, $fileMode = 0666, $create = true, $dirMode = 0777) {
    // let the parent class save the file and do what it normally does
    $saved = parent::save($file, $fileMode, $create, $dirMode);
    return $saved;
  }
}

