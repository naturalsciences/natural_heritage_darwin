<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(9, new lime_output_color());

$userEvil = Doctrine::getTable('Users')->findOneByFamilyName('Evil')->getId();

$t->info('getMyLoans for Evil ');
$cat = Doctrine::getTable('Loans')->getMyLoans($userEvil);
$t->is(count($cat),5,'Number of different loans for user Evil: "5"');
$conn = Doctrine_Manager::connection();
$loan_history_count = $conn->fetchOne("SELECT count(*)
                                        FROM loan_history
                                        WHERE loan_ref = 9"
                                      );
$t->is($loan_history_count,0,'Number of snapshot entries taken for loan 9 is well "0"');
$t->info('Taking snapshot of loan 9');
Doctrine::getTable('Loans')->syncHistory(9);
$t->info('Counting the number of elements snapshooted... should be 2: one for loans table and one for loan_items table');
$loan_history_count = $conn->fetchOne("SELECT count(*)
                                        FROM loan_history
                                        WHERE loan_ref = 9"
);
$t->is($loan_history_count,2,'Number of snapshot entries taken for loan 9 is well "2"');
$loan_history_count = $conn->fetchAll("SELECT id,
                                              loan_ref,
                                              referenced_table
                                       FROM loan_history
                                       WHERE loan_ref = 9
                                       ORDER BY id"
);
$t->info('Checking the content of elements snapshooted');
$sql = "select * from each((select record_line::hstore
                            from loan_history
                            where id = ?
                          ))
        where key in ('description', 'details')
       ";
foreach ($loan_history_count as $value) {
  $t->is($value['loan_ref'],9,'Loan ref is well "9"');
  $loan_history_record_line = $conn->fetchAll($sql,
                                              array($value['id'])
  );
  if($value['referenced_table']==='loans') {
    $t->is($loan_history_record_line[0]['key'], 'description', "Loan archived is correct !");
    $t->is($loan_history_record_line[0]['value'], 'This is a funny sheep to watch, "babe" => "agreed ?"', "Loan archived is correct !");
  }
  elseif($value['referenced_table']==='loan_items') {
    $t->is($loan_history_record_line[0]['key'], 'details', "Loan item archived is correct !");
    $t->is($loan_history_record_line[0]['value'], 'A little test', "Loan item archived is correct !");
  }
}
