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
        $this->definitionDatesForBalanceBasedOnPressedButtons();
    }

    public function countingHeaderOfBalance(){
        $this->getSummaryOfExpenses();
        $this->getSummaryOfIncomes();
        $this->difference=$this->getSummaryOfIncomes()-$this->getSummaryOfExpenses();
        $this->getCommentAboutDifference();
    }

    public function definitionDatesForBalanceBasedOnPressedButtons(){
        if(isset($_POST['customPeriodOfTime'])){
            $_SESSION['customPeriodOfTime']=$_POST['customPeriodOfTime'];
        }
        if(!isset($this->firstLimitDate)){
            $this->firstLimitDate=(static::getTodaysDate())[0];
            $this->secondLimitDate=(static::getTodaysDate())[1];
        }
        if(isset($_POST['fisrtLimitDate'])){
            
                $this->firstLimitDate=$_POST['fisrtLimitDate'];
                $this->secondLimitDate=$_POST['secondLimitDate'];
            
        }
        elseif(isset($_POST['currentMonth'])){
            $this->firstLimitDate=(static::getTodaysDate())[0];
            $this->secondLimitDate=(static::getTodaysDate())[1];
            unset($_POST['currentMonth']);
        }elseif(isset($_POST['previousMonth'])){
            if(isset($_SESSION['customPeriodOfTime'])){
                $this->firstLimitDate=(static::getTodaysDate())[0];
                $this->secondLimitDate=(static::getTodaysDate())[1];
                unset($_SESSION['customPeriodOfTime']);
            }
            $temporatyDate=$this->firstLimitDate;
            $this->firstLimitDate=static::getDateWithPreviousMonthWithFirstDay($this->firstLimitDate);
            $this->secondLimitDate=static::getDateWithPreviousMonthWithLastDay($temporatyDate);
        }
    }

    public function validateDates(){
       $flag=true;
       if(!FinancialMovement::checkIsAValidDate($this->firstLimitDate)){
            $this->errors[]="First limit is invalid.";
            $flag=false;
            
       }
       if(!FinancialMovement::checkIsAValidDate($this->secondLimitDate)){
            $this->errors[]="Second limit is invalid.";
            $flag=false;
        }
        if(strtotime($this->firstLimitDate)>strtotime($this->secondLimitDate)){
            $this->errors[]="First limit is greater than second.";
            $flag=false;
        }
        return $flag;
    }

    protected static function getDateWithPreviousMonthWithLastDay($date){
        return date('Y-m-d', strtotime('last day of last month',strtotime($date)));
    }

    protected static function getDateWithPreviousMonthWithFirstDay($date){
        return date('Y-m-d', strtotime('first day of last month',strtotime($date)));
    }

    public static function getTodaysDate(){
        $dateWithFirstDayOfCurrentMounth=date("Y-m-1");
	    $d= new DateTime($dateWithFirstDayOfCurrentMounth);
        $dateWithLastDayOfCurrentMounth=$d->format('Y-m-t');
        
        return $rangeForBalanceFromCurrentMounth=array($dateWithFirstDayOfCurrentMounth,$dateWithLastDayOfCurrentMounth);
    }

    public function setDatesToCurrentMonth(){
        $this->firstLimitDate=static::getTodaysDate()[0];
        $this->secondLimitDate=static::getTodaysDate()[1];
    }

    public function getCommentAboutDifference(){
        if($this->difference>=0){
            $this->commentAboutDifference='Your incomes is equal or greater than expenses. Good job';
        }else{
            $this->commentAboutDifference='Your expenses exceed incomes. Change your financial strategy.';
        }
    }

    public function getDetailedIncomes(){
        return $this->getDetailedTableFromDB(static::getTableWithIncomes(),1);
    }

    public function getDetailedExpenses(){
        return $this->getDetailedTableFromDB(static::getTableWithExpenses(),2);
    }

    public function getSummaryOfExpenses(){
        $arrayWithExpenses=$this->getDetailedTableFromDB(static::getTableWithExpenses(),2);
        $summary=0;
        foreach($arrayWithExpenses as $data){
            $summary+=$data['amount'];
        }
        return $this->summaryOfExpenses=$summary;
    }

    public function getSummaryOfIncomes(){
        $arrayWithIncomes=$this->getDetailedTableFromDB(static::getTableWithIncomes(),1);
        $summary=0;
        foreach($arrayWithIncomes as $data){
            $summary+=$data['amount'];
        }
        return $this->summaryOfIncomes=$summary;
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

    public static function getIncomeCategories(){
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


