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
        if(!isset($this->firstLimitDate)){
            $this->firstLimitDate=(static::getTodaysDate())[0];
            $this->secondLimitDate=(static::getTodaysDate())[1];
        }
        if(isset($_POST['fisrtLimitDate'])){
            $this->firstLimitDate=$_POST['fisrtLimitDate'];
            $this->secondLimitDate=$_POST['secondLimitDate'];
        }
    }

    public static function getTodaysDate(){
        $dateWithFirstDayOfCurrentMounth=date("Y-m-1");
	    $d= new DateTime($dateWithFirstDayOfCurrentMounth);
        $dateWithLastDayOfCurrentMounth=$d->format('Y-m-t');
        
        return $rangeForBalanceFromCurrentMounth=array($dateWithFirstDayOfCurrentMounth,$dateWithLastDayOfCurrentMounth);
    }

    public function getDetailedIncomes(){
        return $this->getDetailedTableFromDB(static::getTableWithIncomes(),1);
    }

    public function getDetailedExpenses(){
        return $this->getDetailedTableFromDB(static::getTableWithExpenses(),2);
    }

    protected function getDetailedTableFromDB($table, $indicator){
        /*Indicator - 1 - incomes, 2 - expenses*/
        $db=static::getDB();
        $userId=$_SESSION['user_id'];
        
        if($indicator==1){
            $sql="SELECT * FROM ".$table." WHERE user_id='".$userId."' AND date_of_income>= ' ".$this->firstLimitDate."' AND date_of_income<= '".$this->secondLimitDate."'";
        }elseif($indicator==2){
            $sql="SELECT * FROM ".$table." WHERE user_id='".$userId."' AND date_of_expense>= ' ".$this->firstLimitDate."' AND date_of_expense<= '".$this->secondLimitDate."'";
        }
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

    protected static function getIncomeCategories(){
        return static::getCategoriesOrPaymentMethodsAssignedToUser(static::getUserTableWithIncomesCategory());
    }

    public function getExpenseCategories(){
        return static::getCategoriesOrPaymentMethodsAssignedToUser(static::getUserTableWithExpensesCategory());
    }

    public function countAmountDependsOnCategoryOfIncomes(){
        return $this->categoriesAndAmountOfIncomes=$this->countAmountDependsOnCategory(static::getIncomeCategories(),static::getUserTableWithIncomesCategory(),1);
    }

    public function countAmountDependsOnCategoryOfExpenses(){
        return $this->categoriesAndAmountOfExpenses=$this->countAmountDependsOnCategory(static::getExpenseCategories(), static::getUserTableWithExpensesCategory(),2);
    }

    protected function countAmountDependsOnCategory($categoriesId=0, $table=0, $indicator=1){
        /*Indicator - 1 - incomes, 2 - expenses*/
        $categoryNameAndAmountValue=[];
        foreach($categoriesId as $categoryId){
            $db=static::getDB();
            if($indicator==1){
                $sql="SELECT SUM(amount) AS summary from incomes WHERE income_category_assigned_to_user_id='".$categoryId."' AND date_of_income>= '".$this->firstLimitDate."' AND date_of_income<= '".$this->secondLimitDate."'";
            }else{
                $sql="SELECT SUM(amount) AS summary from expenses WHERE expense_category_assigned_to_user_id='".$categoryId."' AND date_of_expense>= '".$this->firstLimitDate."' AND date_of_expense<= '".$this->secondLimitDate."'";
            }
            $stmt=$db->prepare($sql);
            $stmt->execute();
            $result=$stmt->fetch();
           if($result['summary']){
                $categoryNameAndAmountValue[]=array(
                    'name'=>FinancialMovement::getCategoryOrMethodNameById($categoryId,$table),
                    'amount'=>$result['summary']
                );
           }
        }
        return $categoryNameAndAmountValue;
    }
    protected static function getCategoriesOrPaymentMethodsAssignedToUser($table){
        $db=static::getDB();
        $userId=$_SESSION['user_id'];
        $sql="SELECT * FROM ".$table." WHERE user_id='".$userId."'";
        $stmt=$db->prepare($sql);
        $stmt->execute();
        $categoriesIdArray=[];
        while($result=$stmt->fetch(PDO::FETCH_ASSOC)){
            $categoriesIdArray[]=$result['id'];
        }
        return $categoriesIdArray;
    }
}


