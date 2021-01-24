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

    public function save(){
        $this->validate();
        if(empty($this->errors)){
            echo "errors puste";
        }else{
            echo"errors: <br>";
            foreach($this->errors as $error){
                echo $error;
                echo "<br>";
            }
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
        if($this->comment>50){
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

}