<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration);

$user_array = array( 'collmanager' => 'collmanager', 'encoder' => 'encoder', 'reguser' => 'reguser', 'root' => 'evil');

$exp_val_arr = array('collmanager' => array(
                                             'f_widg'  => 0,   
                                             'l_widg'  => 1,
                                             'nr_rows' => array( 'page1' => 5, 'page2' => 1 ),
                                             'pager'        => true,
                                             'add_link'     => true,
                                             'no_more_name' => array( 'page1' => 3, 'page2' => 1 ),
                                             'more_name'    => array( 'page1' => 2, 'page2' => 0 ),
                                             'enc_rights'   => array( 'page1' => array( 
                                                                                   array(true,true,true), 
                                                                                   array(true,false,false), 
                                                                                   array(true,false,false), 
                                                                                   array(true,false,false), 
                                                                                   array(true,true,true), 
                                                                                 ),
                                                                      'page2' => array(
                                                                                   array(true,true,true), 
                                                                                 )
                                                               ),
                                      ), 
                              'encoder' => array(
                                             'f_widg'  => 0,   
                                             'l_widg'  => 1,
                                             'nr_rows' => array( 'page1' => 1 ),
                                             'pager'        => false,
                                             'add_link'     => true,
                                             'no_more_name' => array( 'page1' => 1 ),
                                             'more_name'    => array( 'page1' => 0 ),
                                             'enc_rights'   => array( 'page1' => array( 
                                                                                   array(true,true,true), 
                                                                                 ),
                                                               ),
                                      ), 
                              'reguser' => array(
                                             'f_widg'  => 0,   
                                             'l_widg'  => 1,
                                             'nr_rows' => array( 'page1' => 1 ),
                                             'pager'        => false,
                                             'add_link'     => false,
                                             'no_more_name' => array( 'page1' => 1 ),
                                             'more_name'    => array( 'page1' => 0 ),
                                             'enc_rights'   => array( 'page1' => array( 
                                                                                   array(true,true,true), 
                                                                                 ),
                                                               ),
                                      ), 
                              'root' => array(
                                             'f_widg'  => 2,   
                                             'l_widg'  => 4,
                                             'nr_rows' => array( 'page1' => 1 ),
                                             'pager'        => false,
                                             'add_link'     => false,
                                             'no_more_name' => array( 'page1' => 0 ),
                                             'more_name'    => array( 'page1' => 0 ),
                                             'enc_rights'   => array( 'page1' => array( 
                                                                                   array(false,false,false), 
                                                                                 ),
                                                               ),
                                      ),
                        );

foreach( $user_array as $usr_k => $usr_v  )
{
   $browser->login( $usr_k, $usr_v );

   $browser->
     info('1 - GetBoard')->
     get('/board/index')->

     with('request')->begin()->
       isParameter('module', 'board')->
       isParameter('action', 'index')->
     end()->

     info('1.1 - is everything ok on the board')->
     with('response')->begin()->
       isStatusCode(200)->
       checkElement('title','Dashboard')->
       checkElement('.board_col',2)->
       checkElement('.board_col:first .widget', intval($exp_val_arr[$usr_k]['f_widg']))-> 
       checkElement('.board_col:last .widget', intval($exp_val_arr[$usr_k]['l_widg']))->
     end();

     if( $usr_k == 'root' )
     {
        $browser->
          with('response')->begin()->
            checkElement('.board_col:last .widget:last .widget_top_bar span','/My Loans/')->
            checkElement('#myLoans .widget_content', '/Nothing here/')->
          end();
     }
     else
     {
       $browser->
         with('response')->begin()->
           checkElement('.board_col:last .widget:first .widget_top_bar span','/My Loans/')->
         end();
       $browser->
 	 info('1 - My Loans')->
	 get('/widgets/reloadContent?category=board&widget=myLoans')->
	 with('response')->begin()->
	   isStatusCode(200)->
	   checkElement('tbody tr', intval($exp_val_arr[$usr_k]['nr_rows']['page1']))->
	   checkElement('tbody tr .no_more_name', intval($exp_val_arr[$usr_k]['no_more_name']['page1']))->
	   checkElement('tbody tr .more_name', intval($exp_val_arr[$usr_k]['more_name']['page1']))->
	   checkElement('tbody tr:eq(0) .view_loan'  , $exp_val_arr[$usr_k]['enc_rights']['page1'][0][0])->
	   checkElement('tbody tr:eq(0) .edit_loan'  , $exp_val_arr[$usr_k]['enc_rights']['page1'][0][1])->
	   checkElement('tbody tr:eq(0) .delete_loan', $exp_val_arr[$usr_k]['enc_rights']['page1'][0][2])->
	 end();
  
       if( $usr_k == 'collmanager' )
       {
         for( $i = 1; $i < intval($exp_val_arr[$usr_k]['nr_rows']['page1']); $i++ )
         {
	    $browser->
	      with('response')->begin()->
		checkElement('tbody tr:eq('.$i.') .view_loan'  , $exp_val_arr[$usr_k]['enc_rights']['page1'][$i][0])->
		checkElement('tbody tr:eq('.$i.') .edit_loan'  , $exp_val_arr[$usr_k]['enc_rights']['page1'][$i][1])->
		checkElement('tbody tr:eq('.$i.') .delete_loan', $exp_val_arr[$usr_k]['enc_rights']['page1'][$i][2])->
	      end();
         }
	 $browser->
	   with('response')->begin()->
	     checkElement('tfoot .pager_separator', $exp_val_arr[$usr_k]['pager'])->
	     checkElement('tfoot tr:first a', 3)->
	     checkElement('tfoot tr:first a:first', '2')->
	     checkElement('tfoot tr:first a:first[href*=/widgets/reloadContent/category/board/widget/myLoans/page/2]')->
	     checkElement('tfoot tr:eq(1) a', 2)->
	     checkElement('tfoot tr:eq(1) .add_link', $exp_val_arr[$usr_k]['add_link'])->   
	     checkElement('tfoot tr:eq(1) .view_all', true)->   
	   end()->

	   info('1 - My Loans - page 2')->
	   get('/widgets/reloadContent?category=board&widget=myLoans&page=2')->
	   with('response')->begin()->
	     isStatusCode(200)->
	     checkElement('tbody tr', intval($exp_val_arr[$usr_k]['nr_rows']['page2']))->
	     checkElement('tbody tr .no_more_name', intval($exp_val_arr[$usr_k]['no_more_name']['page2']))->
	     checkElement('tbody tr .more_name', intval($exp_val_arr[$usr_k]['more_name']['page2']))->
	     checkElement('tbody tr:eq(0) .view_loan'  , $exp_val_arr[$usr_k]['enc_rights']['page2'][0][0])->
	     checkElement('tbody tr:eq(0) .edit_loan'  , $exp_val_arr[$usr_k]['enc_rights']['page2'][0][1])->
	     checkElement('tbody tr:eq(0) .delete_loan', $exp_val_arr[$usr_k]['enc_rights']['page2'][0][2])->
	     checkElement('tfoot .pager_separator', $exp_val_arr[$usr_k]['pager'])->
	     checkElement('tfoot tr:first a', 3)->
	     checkElement('tfoot tr:first a:eq(2)', '1')->
	     checkElement('tfoot tr:first a:first[href*=/widgets/reloadContent/category/board/widget/myLoans/page/1]')->
	     checkElement('tfoot tr:eq(1) a', 2)->
	     checkElement('tfoot tr:eq(1) .add_link', $exp_val_arr[$usr_k]['add_link'])->
	     checkElement('tfoot tr:eq(1) .view_all', true)->   
	   end();

	 $browser->
           info(sprintf('Checking: Clicking on the edit button for the first loan for %s', $usr_k))->
	   get('/widgets/reloadContent?category=board&widget=myLoans')->
	   with('response')->begin()->
	     isStatusCode(200)->
           end()->

           click('a.edit_loan', array() )->
             with('request')->begin()->
               isParameter('module', 'loan')->
               isParameter('action', 'edit')->
               isParameter('id', 5)->
             end()->

             with('response')->begin()->
               isStatusCode(200)->
               checkElement('#tab_0', '/Edit loan/')->
               checkElement('a.selected', '/Edit loan/')->
             end();
        
         $browser->  
           info(sprintf('Checking: Clicking on the "Add" button for %s', $usr_k))->
	   get('/widgets/reloadContent?category=board&widget=myLoans')->
	   with('response')->begin()->
	     isStatusCode(200)->
           end()->

           click('a.add_link', array() )->
             with('request')->begin()->
               isParameter('module', 'loan')->
               isParameter('action', 'new')->
             end()->

             with('response')->begin()->
               isStatusCode(200)->
               checkElement('a.selected', '/New loan/')->
           end();
      }
      else
      {
         $browser->
           with('response')->begin()->
             checkElement('tfoot .pager_separator', $exp_val_arr[$usr_k]['pager'])->
	     checkElement('tfoot tr:eq(1) .add_link', $exp_val_arr[$usr_k]['add_link'])->  
	     checkElement('tfoot tr:eq(1) .view_all', true)->   
           end();
      }
    } 
    $browser->
      get('account/logout')->
      with('response')->begin()->
        isRedirected()->
      end()->

      followRedirect()->

      with('user')->begin()->
	isAuthenticated(false)->
      end()->

      info(sprintf('De user ["  %s  "] is uitgelogged',$usr_k))->info("")->info("");
}
	
