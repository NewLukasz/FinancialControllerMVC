<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Token;
use \DateTime;

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

    public function getTableFromDB(){
        $db=static::getDB();
        $userId=$_SESSION['user_id'];
        $sql="SELECT * FROM incomes WHERE user_id='".$userId."'";
        $stmt=$db->prepare($sql);
        $stmt->execute();

        $result=$stmt->fetchAll();
        
        foreach($result as $one){
            foreach($one as $onlyOne){
                echo $onlyOne."<br>";
                echo '-------------------------';
            }
        }
        exit();
    }



}