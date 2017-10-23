<?php

class LoanItemsTable extends DarwinTable
{
  public static function getInstance()
  {
    return Doctrine_Core::getTable('LoanItems');
  }

  public function findForLoan($id)
  {
    $q = Doctrine_Query::create()
      ->From('LoanItems i')
      ->andwhere('i.loan_ref = ?', $id)
      ->orderBy('to_date DESC, i.id');
    return $q->execute();
  }

  public function deleteChecked($ids)
  {
    Doctrine_Query::create()
      ->delete('LoanItems i')
      ->andwhereIn('i.id', $ids)
      ->execute();
  }

  public function getLoanRef($ids)
  {
    $loan_ref = null;
    $q = Doctrine_Query::create()
      ->select('i.loan_ref')
      ->From('LoanItems i')
      ->andwhereIn('i.id', $ids)
      ->execute();

    foreach($q as $item){
      if($loan_ref === null) $loan_ref = $item->getLoanRef() ;
      if ($loan_ref != $item->getLoanRef()) return false ;
    }
    return $loan_ref ;
  }
}
