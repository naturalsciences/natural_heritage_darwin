<?php

class darwinStatsTask extends sfBaseTask
{
  /* Add your requests in the array $request_array
     - title : is the title of the statistic displayed
     - description : will appear in our help icon
     - fields : set yours fields here they MUST be the same as the fields name in the query
     - request : the request which will be executed
     - expandable : set it to true if your request return too much record, the expandable option will add a "+" button in the widget to avoid having a too big widget
     - level : is level on wich a user is allowed to see or not the stat ( 1 : everybody, 2 : encoder and more, 4 : collection manager ans more, 8 : admin only)
  */
  private $request_array = array(
  "req1" => array(
    "title" => "All types and the record count associated",
    "description" => "",
    "fields" => array("Type","Count"),
    "request" => "SELECT Type, count(id) AS Count FROM specimens GROUP BY Type ORDER BY Type",
    "expandable" => true,
    "level" => 2,
    ),
  "req2" => array(
    "title" => "All objects in DaRWIN",
    "description" => "An average result of all objects already stored in our database",
    "fields" => array("Total"),
    "request" => "SELECT sum((specimen_count_min+specimen_count_max)/2) as total FROM specimens",
    "expandable" => false,
    "level" => 1,
    ),
  ) ;
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine')
      ));
    $this->namespace        = 'darwin';
    $this->name             = 'gen-stats';
    $this->briefDescription = 'Generate statistics';
    $this->detailedDescription = <<<EOF
The [darwin:gen-stats|INFO] task launch all request stored to create
a .yml file with all request results
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $array = array() ;
    foreach($this->request_array as $key=>$request)
    {
      $conn = Doctrine_Manager::connection()->getDbh();
      $statement = $conn->prepare($request['request']);
      $statement->execute();
      $results = $statement->fetchAll(PDO::FETCH_ASSOC);
      $array[$key] = array_merge($request, array('result' => $results)) ;
    }
    $array['date_gen_stat'] = date('d/m/Y - H:i');
    $yaml = sfYaml::dump($array);
    file_put_contents(sfConfig::get('sf_data_dir').'/stats/stats.yml', $yaml);
  }
}
