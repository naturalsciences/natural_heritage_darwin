<?php
//ftheeten 2017 09 07
class RMCAXMLTabValidator extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('notab', 'The imported text file is not a tab delimtied file');
  }
  
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
  
    return $values;  
  }
  
  function getFileDelimiter($file, $checkLines = 2)
  {
        $file = new SplFileObject($file);
        $delimiters = array(
          ',',
          '\t',
          ';',
          '|',
          ':'
        );
        $results = array();
        $i = 0;
         while($file->valid() && $i <= $checkLines){
            $line = $file->fgets();
            foreach ($delimiters as $delimiter){
                $regExp = '/['.$delimiter.']/';
                $fields = preg_split($regExp, $line);
                if(count($fields) > 1){
                    if(!empty($results[$delimiter])){
                        $results[$delimiter]++;
                    } else {
                        $results[$delimiter] = 1;
                    }   
                }
            }
           $i++;
        }
        $results = array_keys($results, max($results));
        return $results[0];
    }


}