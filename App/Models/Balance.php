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

    public function getTableFromDB(){
        $db=static::getDB();
        $userId=$_SESSION['user_id'];
        $sql="SELECT * FROM incomes WHERE user_id='".$userId."'";
        $stmt=$db->prepare($sql);
        $stmt->execute();

        $financialMovementsArray=[];
        while($result=$stmt->fetch(PDO::FETCH_ASSOC)){
            $financialMovementsArray[]=array(
                'id'=>$result['id'],
                'amount'=>$result['amount'],
                'source'=>FinancialMovement::getCategoryOrMethodNameById($result['income_category_assigned_to_user_id'],static::getUserTableWithIncomesCategory()),
                'date'=>$result['date_of_income'],
                'comment'=>$result['income_comment']
            );
        }

        foreach($financialMovementsArray as $finacialMovement){
            foreach($finacialMovement as $signleDataOfFinancialMovement){
                echo $signleDataOfFinancialMovement."<br>";
            }
            echo '<br>';
        }
        exit();
/*  
        $financialMovements=$stmt->fetchAll();

        var_dump($financialMovements);
        echo '<br><br>';
        
        $financialMovementsArray=[];
        
        foreach($financialMovements as $financialMovement){
            var_dump($financialMovement);
            echo '<br>';
            $financialMovementsArray[]=array(
                'id'=>$financialMovement['id'],
                'amount'=>$financialMovement['amount'],
                'source'=>FinancialMovement::getCategoryOrMethodNameById($financialMovement['income_category_assigned_to_user_id'],static::getUserTableWithIncomesCategory()),
                'date'=>$financialMovement['date_of_income'],
                'comment'=>$financialMovement['income_comment']
            );

            foreach($financialMovementsArray as $finacialMovement){
                
                foreach($finacialMovement as $signleDataOfFinancialMovement){
                    echo $signleDataOfFinancialMovement."<br>";
                }
                echo '<br>';
            }
        exit();
        }
*/



    }
}

