<?php

class DarwinTestFunctional extends sfTestFunctional
{
  public function loadData($configuration)
  {
    new sfDatabaseManager($configuration);
    self::initiateDB($configuration);
    Doctrine::loadData(sfConfig::get('sf_test_dir').'/fixtures');
    return $this;
  }

  public static function initiateDB()
  {
    $conn = Doctrine_Manager::connection();
    $conn->exec("SELECT nextval('taxonomy_id_seq')");
    $conn->exec("SELECT setval('taxonomy_id_seq',10000)");
    $conn->exec("SELECT nextval('chronostratigraphy_id_seq')");
    $conn->exec("SELECT setval('chronostratigraphy_id_seq',10000)");
    $conn->exec("SELECT nextval('lithostratigraphy_id_seq')");
    $conn->exec("SELECT setval('lithostratigraphy_id_seq',10000)");
    $conn->exec("SELECT nextval('mineralogy_id_seq')");
    $conn->exec("SELECT setval('mineralogy_id_seq',10000)");
    $conn->exec("SELECT nextval('lithology_id_seq')");
    $conn->exec("SELECT setval('lithology_id_seq',10000)");
    $conn->exec("SELECT nextval('expeditions_id_seq')");
    $conn->exec("SELECT setval('expeditions_id_seq',10000)");
    $conn->exec("SELECT nextval('classification_synonymies_id_seq')");
    $conn->exec("SELECT setval('classification_synonymies_id_seq',1)");
    $conn->exec("SELECT nextval('gtu_id_seq')");
    $conn->exec("SELECT setval('gtu_id_seq',1)");
    $conn->exec("SELECT nextval('identifications_id_seq')");
    $conn->exec("SELECT setval('identifications_id_seq',10000)");
    $conn->exec("SELECT nextval('specimens_id_seq')");
    $conn->exec("SELECT setval('specimens_id_seq',100)");
    $conn->exec("SELECT nextval('catalogue_people_id_seq')");
    $conn->exec("SELECT setval('catalogue_people_id_seq',100)");

    $conn->exec("SELECT nextval('loans_id_seq')");
    $conn->exec("SELECT setval('loans_id_seq',100)");
    $conn->exec("SELECT nextval('loan_status_id_seq')");
    $conn->exec("SELECT setval('loan_status_id_seq',100)");
    $conn->exec("SELECT nextval('loan_rights_id_seq')");
    $conn->exec("SELECT setval('loan_rights_id_seq',100)");
    
    $conn->exec("SELECT nextval('imports_id_seq')");
    $conn->exec("SELECT setval('imports_id_seq',100)");
    
    $conn->exec("TRUNCATE TABLE specimens CASCADE");
    $conn->exec("TRUNCATE TABLE insurances CASCADE");
    $conn->exec("TRUNCATE TABLE flat_dict CASCADE");
    $conn->exec("TRUNCATE TABLE catalogue_people CASCADE");
    
  }

  public function login($user,$pass)
  {
    $this->
      info('** initial login **')->
      get('account/login')->
      click('#login_page input[type="submit"]',array('login' => array(
        'username' => $user,
        'password' => $pass
        )))->
      with('user')->begin()->
        isAuthenticated(true)->
      end()
    ;
    return $this;
  }
  
  public function addCustomUserAndLogin($name = '' ,$password = 'nothing')
  { 
    if($user= Doctrine::getTable("Users")->findOneByFamilyName($name)) return($user->getId()) ;
  	if ($name == '') $name = 'user_test_'.rand(1,1000) ;
  	$this->setTester('doctrine', 'sfTesterDoctrine');
  	$this->
  	  info('** add a custom user **')->
    	  get('user/new')->
    	  with('response')->begin()->
    	  checkelement('#submit',1)->
  	  click('#submit', array('users' => array('family_name' => $name)))->end()->
       followRedirect()->
         info('** add login info for this user **')->
         click('#add_info')->
         with('response')->begin()->
		    isStatusCode(200)->
		    click('a.cancel_qtip #submit', array('users_login_infos' => array('user_name' => $name,
		    				'new_password' => $password,
		    				'confirm_password' => $password)))->
  		    isStatusCode(200)->end()->
       with('doctrine')->begin()->		    
       check('usersLoginInfos', array('user_name' => $name,
          						'login_type' => 'local'))->
	  end();    
	  return(Doctrine::getTable("Users")->findOneByFamilyName($name)->getId()) ;     
		   
  }
  
  public function addCustomCollection($code = 'code_test',$name = '')
  {
    if ($name == "") $name = 'collection_test_'.rand(1,1000) ;  
    $institution_id = $this->addCustomInstitution('Institution for test', 'ITF') ;
    if ($record = Doctrine::getTable("Users")->findOneByFamilyName('manager')) 
        $manager_id = $record->getId() ;
    else
    	   $manager_id = $this->addCustomUserAndLogin('manager','evil');    
    
    if ($record = Doctrine::getTable("Users")->findOneByFamilyName('encoder')) 
        $encoder_id = $record->getId() ;
    else
        $encoder_id = $this->addCustomUserAndLogin('encoder','evil');  
    if ($record = Doctrine::getTable("Users")->findOneByFamilyName('reguser')) 
        $reguser_id = $record->getId() ;
    else
        $reguser_id = $this->addCustomUserAndLogin('reguser','evil');               
    $this->
      info('** add a custom collection **')->
      get('collection/new')->
      with('response')->begin()->
      click('#submit', array(
        'collections' => array('code' => $code,
        'name' => $name,
        'institution_ref' => $institution_id,
        'main_manager_ref' => $manager_id,
        'newVal' => array(
          1 => array('user_ref' => $encoder_id,'db_user_type' => Users::ENCODER),
          2 => array('user_ref' => $reguser_id,'db_user_type' => Users::REGISTERED_USER)
          )
        )
      ))->
      end()->

      with('doctrine')->begin()->
        check('Collections', array(
          'code' => $code,
          'name' => $name,
          'institution_ref' => $institution_id,
          'main_manager_ref' => $manager_id)
        )->
      end();
  	$collection_id = Doctrine::getTable('Collections')->getCollectionByName($name)->getId() ;
    $this->
       with('doctrine')->begin()->		    
       	check('CollectionsRights', array('user_ref' => $encoder_id, 'collection_ref' => $collection_id))->		     				  
	  end();   	  
	  return($collection_id) ;	       	
  
  }
  
  public function addCustomInstitution($name = '', $add_name = 'more_intitution_test')
  {
     if ($name == "") $name = 'intitution_test_'.rand(1,1000) ;
  	$this->setTester('doctrine', 'sfTesterDoctrine');
  	$this->
  	  info('** add a custom institution **')->
    	  get('institution/new')->
    	  with('response')->begin()->
  	  click('#submit', array('institutions' => array('family_name' => $name,
  	  									     'additional_names' => $add_name)))->end()->	    					   
       with('doctrine')->begin()->		    
       	check('Institutions', array('family_name' => $name,
          					   'additional_names' => $add_name,
          					   'is_physical' => false))->
	  end(); 
	  return(Doctrine::getTable('Institutions')->getInstitutionByName($name)->getId()) ; 
  }
  
  public function addCustomTaxon($name = '', $level = 1)
  {
     if ($name == "") $name = 'taxon_test_'.rand(1,1000) ;
     $this->setTester('doctrine', 'sfTesterDoctrine');
  	$this->
  	  info('** add a custom Taxon **')->
    	  get('taxonomy/new')->
    	  with('response')->begin()->
  	  click('#submit', array('taxonomy' => array('name' => $name,
  	  									'level_ref' => $level)))->end()->	    					   
       with('doctrine')->begin()->		    
       	check('Taxonomy', array('name' => $name,
          				    'level_ref' => $level))->
	  end();  
	  return (Doctrine::getTable('Taxonomy')->getTaxonByName($name,$level,'/')->getId()) ;	
  }  
  
  public function addCustomExpedition($name = '')
  {
     if ($name == "") $name = 'expedition_test_'.rand(1,1000) ;
  	$this->setTester('doctrine', 'sfTesterDoctrine');
  	$this->
  	  info('** add a custom Expedition **')->
    	  get('expedition/new')->
    	  with('response')->begin()->
  	  click('#submit', array('expeditions' => array('name' => $name,
  	  									   'expedition_from_date' => array('day' => '12',
  	  									   							'month' => '10',
  	  									   							'year' => '1977')
  	  									   )))->end()->	    					   
       with('doctrine')->begin()->		    
       	check('Expeditions', array('name' => $name))->
	  end();  
  } 

  public function addCustomSpecimen()
  {
    $this->setTester('doctrine', 'sfTesterDoctrine');
    $collections = Doctrine::getTable('Collections')->findAll();
    $collection_id = $collections[rand(0,count($collections)-1)]->getId(); 
    $collector_1 = $this->addCustomPeople() ;
    $collector_2 = $this->addCustomPeople() ;
    $taxonomy = Doctrine::getTable('Taxonomy')->getRealTaxon();
    $taxon_id = $taxonomy[rand(0,count($taxonomy)-1)]->getId();	 		  
    $this->
      info('** add a custom specimen **')->
      get('specimen/new')->
      with('response')->begin()->
      click('#submit_spec_f1', array('specimen' => array(
              'collection_ref' => $collection_id,
              'taxon_ref' => $taxon_id,
              'Codes_holder' => 1,
              'Collectors_holder' => 1,
              'Comments_holder'=>1,
              'ident' => 1,
              'newCodes' => array(
                0 => array('code_category' => 'secondary','code_prefix' => 'sec', 'code_prefix_separator' => '/','code' => '987', 'code_suffix' => 'ary','code_suffix_separator' => '/'),
                1 => array('code_category' => 'main','code_prefix' => 'mn', 'code_prefix_separator' => '/','code' => '112', 'code_suffix' => 'nn','code_suffix_separator' => '/')
              ),
              'newCollectors' => array(
                  0 => array('people_ref' => $collector_1, 'referenced_relation' => 'specimens', 'order_by' => 1, 'people_type' => 'collector'),
                  1 => array('people_ref' => $collector_2, 'referenced_relation' => 'specimens', 'order_by' => 2, 'people_type' => 'collector')
                  ), 			
            'newComments' => array(
                  0 => array('notion_concerned' => 'collectors', 'comment' => 'Test comment for a collector')
                  ),
              'newIdentification' => array(
                                    0 => array('notion_date' => array('day' => 10, 'month' => 02,'year' => 1945),
                                                      'notion_concerned' => 'taxonomy',
                                                      'value_defined' => 'tst val ind',
                                                      'determination_status'=> '',
                                                      'referenced_relation' => 'specimens',
                                                      'order_by' => '1',
                                                      'newIdentifier' => array(
                              0 => array('people_ref' => $collector_1, 'referenced_relation' => 'identifications', 'order_by' => 1, 'people_type' => 'identifier'),
                          1 => array('people_ref' => $collector_2, 'referenced_relation' => 'identifications', 'order_by' => 2, 'people_type' => 'identifier')
                  ))),
              'acquisition_category' => 'mission',
              'acquisition_date' => array('day' => 01, 'month' => 06, 'year' => 1984)
            )))->
    end()->
    with('form')->begin()->
    hasErrors(0)->
    end()->
    with('doctrine')->begin()-> 
    check('Specimens', array(
      'collection_ref' => $collection_id,
      'taxon_ref' => $taxon_id,
      'acquisition_category' => 'mission'
      ))->
    end(); 
      return ($this) ;
  }  

  public function addCustomPeople($name = '')
  {
    if ($name == "") $name = 'people_test_'.rand(1,1000) ;
  	$this->setTester('doctrine', 'sfTesterDoctrine');
  	$this->
  	  info('** add a custom people **')->
    	  get('people/new')->
    	  with('response')->begin()->
  	  click('#submit', array('people' => array('family_name' => $name)
  	  				    ))->end()->
       with('doctrine')->begin()->		    
       	check('People', array('family_name' => $name))->
	  end();   
	return (Doctrine::getTable("People")->findOneByFamilyName($name)->getId()) ;
  }
}
