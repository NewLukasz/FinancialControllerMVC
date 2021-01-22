<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
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
    }

    public function validate(){


        var_dump($_POST);

        $this->amount=static::checkCommaAndChangeForDot($this->amount);

        if(checkHowManyDecimalPlacesHasAmount($this->amount)>2){
            $this->errors[]='Your amount has more than two decimal places.'
        }
        if($this->amount==''){
            $this->erorrs[]='Amount is required';
        }
        if($this->comment>50){
            $this->errors[]='Your comment exceeded 50 signs.';
        }

        echo "<br><br>Testy wyrazen regularnych <br>";


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

}