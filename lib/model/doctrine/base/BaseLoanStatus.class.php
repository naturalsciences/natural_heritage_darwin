<?php

/**
 * BaseLoanStatus
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $loan_ref
 * @property integer $user_ref
 * @property string $status
 * @property string $modification_date_time
 * @property string $comment
 * @property boolean $is_last
 * @property Loans $Loan
 * @property Users $Users
 * 
 * @method integer    getId()                     Returns the current record's "id" value
 * @method integer    getLoanRef()                Returns the current record's "loan_ref" value
 * @method integer    getUserRef()                Returns the current record's "user_ref" value
 * @method string     getStatus()                 Returns the current record's "status" value
 * @method string     getModificationDateTime()   Returns the current record's "modification_date_time" value
 * @method string     getComment()                Returns the current record's "comment" value
 * @method boolean    getIsLast()                 Returns the current record's "is_last" value
 * @method Loans      getLoan()                   Returns the current record's "Loan" value
 * @method Users      getUsers()                  Returns the current record's "Users" value
 * @method LoanStatus setId()                     Sets the current record's "id" value
 * @method LoanStatus setLoanRef()                Sets the current record's "loan_ref" value
 * @method LoanStatus setUserRef()                Sets the current record's "user_ref" value
 * @method LoanStatus setStatus()                 Sets the current record's "status" value
 * @method LoanStatus setModificationDateTime()   Sets the current record's "modification_date_time" value
 * @method LoanStatus setComment()                Sets the current record's "comment" value
 * @method LoanStatus setIsLast()                 Sets the current record's "is_last" value
 * @method LoanStatus setLoan()                   Sets the current record's "Loan" value
 * @method LoanStatus setUsers()                  Sets the current record's "Users" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseLoanStatus extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('loan_status');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('loan_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('user_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('status', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('modification_date_time', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('comment', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('is_last', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Loans as Loan', array(
             'local' => 'loan_ref',
             'foreign' => 'id'));

        $this->hasOne('Users', array(
             'local' => 'user_ref',
             'foreign' => 'id'));
    }
}