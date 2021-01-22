<?php

namespace App\Models;

use PDO;
use \Core\View;

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

}