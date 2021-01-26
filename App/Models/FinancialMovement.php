<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Token;


/**
 * User model
 *
 * PHP version 7.0
 */
class FinancialMovement extends \Core\Model
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

    public function addIncome(){
        if(isset($this->sourceOfIncome)){
            $categoryId=static::getCategoryOrMethodIdByName($this->sourceOfIncome, static::getUserTableWithIncomesCategory());

            $argumentsForBindValueFunction=array(
                $userId=array(':user_id', $this->userId, PDO::PARAM_INT),
                $incomeCategory=array(':income_category_assigned_to_user_id', $categoryId, PDO::PARAM_INT),
                $amount=array(':amount' , $this->amount, PDO::PARAM_INT),
                $dateOfIncome=array(':date_of_income',$this->dateOfIncome, PDO::PARAM_STR),
                $incomeComment=array(':income_comment', $this->comment, PDO::PARAM_STR)
            );
            return $this->addMovement(static::getTableWithIncomes(),$argumentsForBindValueFunction);
        }
    }


    protected static function getCategoryOrMethodIdByName($name,$tableWithData){
        $sql="SELECT id FROM ".$tableWithData." WHERE name= :name";
        $db=static::getDB();
        $stmt=$db->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $result=$stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }

    protected function addMovement($tableForData, $argumentsForBindValueFunction=[]){
        $this->validate();
        if(empty($this->errors)){
            $valuesToQuery='';
            foreach($argumentsForBindValueFunction as $arguments){
                $valuesToQuery=$valuesToQuery.$arguments[0].',';
            }
            $valuesToQuery=substr($valuesToQuery,0,-1);
            $sql='INSERT INTO '.$tableForData.' VALUES(NULL,'.$valuesToQuery.')';

            $db=static::getDB();
            $stmt=$db->prepare($sql);
            foreach($argumentsForBindValueFunction as $arguments){
                 $stmt->bindValue($arguments[0],$arguments[1],$arguments[2]);
            }
            return $stmt->execute();

        }else{
            return false;
        }
    }

    public function validate(){
        //amount
        if(!is_numeric($this->amount)){
            $this->errors[]="Wrong value of amount";
        }else{
            $this->amount=static::checkCommaAndChangeForDot($this->amount);
            
            if(static::checkHowManyDecimalPlacesHasAmount($this->amount)>2){
                $this->errors[]='Your amount has more than two decimal places.';
            }
            if($this->amount==''){
                $this->erorrs[]='Amount is required';
            }
        }
       //date
       if(!static::checkIsAValidDate($this->dateOfIncome)){
           $this->errors[]='You typed wrong data. Please remeber that format is: YYYY-MM-DD';
       }
       //comment
        if(strlen($this->comment)>50){
            $this->errors[]='Your comment exceeded 50 signs.';
        }
    }

    public static function checkCommaAndChangeForDot($amount){
        for($i=0;$i<strlen($amount);$i++){
            if($amount[$i]==',')$amount[$i]='.';
        }
        return $amount;
    }

    public static function checkHowManyDecimalPlacesHasAmount($amount){
        for($i=0;$i<strlen($amount);$i++){
            if($amount[$i]=='.') return strlen(substr($amount,$i+1));
        }
    }

    public static function checkIsAValidDate($myDateString){
        $pauseCounter=0;
        for($i=0;$i<strlen($myDateString);$i++){
            if($myDateString[$i]=='-') $pauseCounter++;
        }
        return ($pauseCounter==2)?(bool)strtotime($myDateString):false;
    }

    public static function fulfilUserDataTablesWithDefaultValues($value){

        $token = new Token($value);
        $hashed_token = $token->getHash();

        $userId=static::queryForUserIdBaseHashedToken($hashed_token);


        static::fulfilUserTablesWithDefaultCategoriesAndPaymentMethods(
            $userId, 
            static::getDefaultIncomesTable(),
            static::getUserTableWithIncomesCategory()
        );

        static::fulfilUserTablesWithDefaultCategoriesAndPaymentMethods(
            $userId,
            static::getDefaultExpensesTable(),
            static::getUserTableWithExpensesCategory()
        );

        static::fulfilUserTablesWithDefaultCategoriesAndPaymentMethods(
            $userId,
            static::getDefaultPaymentMethods(),
            static::getUserTableWithPaymentMethods(),
        );
    }

    public static function queryForUserIdBaseHashedToken($token){
        $sql="SELECT id FROM users WHERE activation_hash=:hashed_token";

        $db=static::getDB();
        $stmt=$db->prepare($sql);

        $stmt->bindValue(':hashed_token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $result=$stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }

    public static function fulfilUserTablesWithDefaultCategoriesAndPaymentMethods($userId, $tableDefault, $tableUserCategory){
        $db=static::getDB();
        $defaultCategoriesQuery=$db->query(("SELECT name FROM ").$tableDefault);
        $defaultCategoriesResult=$defaultCategoriesQuery->fetchAll();
        foreach($defaultCategoriesResult as $nameArray){
            $name=$nameArray['name'];
            $db->query("INSERT INTO ".$tableUserCategory." VALUES(NULL,'$userId','$name')");
        }
    }

    public static function getIncomeCategories(){
        if(!isset($_SESSION['incomeCategories'])){
            return $_SESSION['incomeCategories']=static::getCategoriesAndMethodFromDB(static::getUserTableWithIncomesCategory());
        }else{
            return $_SESSION['incomeCategories'];
        }
    }

    public static function getExpenseCategories(){
        if(!isset($_SESSION['expenseCategories'])){
            return $_SESSION['expenseCategories']=static::getCategoriesAndMethodFromDB(static::getUserTableWithExpensesCategory());
        }else{
            return $_SESSION['expenseCategories'];
        }
    }

    public static function getPaymentMethods(){
        if(!isset($_SESSION['paymentMethods'])){
            return $_SESSION['paymentMethods']=static::getCategoriesAndMethodFromDB(static::getUserTableWithPaymentMethods());
        }else{
            return $_SESSION['paymentMethods'];
        }
    }

    protected static function getCategoriesAndMethodFromDB($table){
        $db=static::getDB();
        $categoriesQuery=$db->query(("SELECT name FROM ").$table.(" WHERE user_id='").$_SESSION['user_id'].("'"));
        $result=$categoriesQuery->fetchAll();
        $categories=[];
        foreach($result as $nameArray){
            $categories[]=$nameArray['name'];
        }
        return $categories;
    }
}