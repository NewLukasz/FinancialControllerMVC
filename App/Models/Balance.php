<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Token;
use \DateTime;
use \App\Models\FinancialMovement;

/**
 * User model
 *
 * PHP version 7.0
 */
class Balance extends \Core\Model
{
    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public static function getTodaysDate(){
        $dateWithFirstDayOfCurrentMounth=date("Y-m-1");
	    $d= new DateTime($dateWithFirstDayOfCurrentMounth);
        $dateWithLastDayOfCurrentMounth=$d->format('Y-m-t');
        
        return $rangeForBalanceFromCurrentMounth=array($dateWithFirstDayOfCurrentMounth,$dateWithLastDayOfCurrentMounth);
    }

    public function getDetailedIncomes(){
        return $this->getTableFromDB(static::getTableWithIncomes(),1);
    }

    public function getDetailedExpenses(){
        return $this->getTableFromDB(static::getTableWithExpenses(),2);
    }

    protected function getTableFromDB($table, $indicator){
        /*Indicator - 1 - incomes, 2 - expenses, 3 - payments category */
        $db=static::getDB();
        $userId=$_SESSION['user_id'];
        $sql="SELECT * FROM ".$table." WHERE user_id='".$userId."'";
        $stmt=$db->prepare($sql);
        $stmt->execute();

        $financialMovementsArray=[];
        
        while($result=$stmt->fetch(PDO::FETCH_ASSOC)){
            if($indicator==1){
                $financialMovementsArray[]=array(
                    'id'=>$result['id'],
                    'amount'=>$result['amount'],
                    'source'=>FinancialMovement::getCategoryOrMethodNameById($result['income_category_assigned_to_user_id'],static::getUserTableWithIncomesCategory()),
                    'date'=>$result['date_of_income'],
                    'comment'=>$result['income_comment']
                );
            }
            elseif($indicator==2){
                $financialMovementsArray[]=array(
                    'id'=>$result['id'],
                    'amount'=>$result['amount'],
                    'reason'=>FinancialMovement::getCategoryOrMethodNameById($result['expense_category_assigned_to_user_id'],static::getUserTableWithExpensesCategory()),
                    'paymentMethod'=>FinancialMovement::getCategoryOrMethodNameById($result['payment_method_assigned_to_user_id'], static::getUserTableWithPaymentMethods()),
                    'date'=>$result['date_of_expense'],
                    'comment'=>$result['expense_comment']
                );
            }

        }
        
        return $this->table=$financialMovementsArray;
    }

    
}

