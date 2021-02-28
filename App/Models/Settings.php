<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Models\Balance;
use \App\Models\FinancialMovement;


class Settings extends \Core\Model{

    public $errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };

        $this->incomeCategoriesID=Balance::getIncomeCategories();
        $this->expenseCategoriesID=Balance::getExpenseCategories();
        $this->paymentMethodsCategoriesID=Balance::getPaymentMethods();
        
        $this->incomeCategoriesNames=[];
        foreach($this->incomeCategoriesID as $incomeCategory){
            $this->incomeCategoriesNames[]=FinancialMovement::getCategoryOrMethodNameById($incomeCategory, static::getUserTableWithIncomesCategory());
        }

        $this->expenseCategoriesNamesAndLimits=[];
        foreach($this->expenseCategoriesID as $expenseCategoryID){
            $this->expenseCategoriesNamesAndLimits[]=array(
                'name'=>FinancialMovement::getCategoryOrMethodNameById($expenseCategoryID, static::getUserTableWithExpensesCategory()),
                'limit'=>static::getLimitForExpenseById($expenseCategoryID,static::getUserTableWithExpensesCategory())
            );
        }

        $this->paymentMethodsNames=[];
        foreach($this->paymentMethodsCategoriesID as $paymentMethod){
            $this->paymentMethodsNames[]=FinancialMovement::getCategoryOrMethodNameById($paymentMethod, static::getUserTableWithPaymentMethods());
        }
    }

    public static function changeCategoryOrPaymentMethod()
    {
        if(isset($_POST['newName'])){
            
            $userId=$_SESSION['user_id'];
            $newName=$_POST['newName'];
            $nameToChange=$_POST['categoryOrPaymentMethodName'];
            $tableWithCategories=static::getCorrectTableToInsertData($_POST['whatToChange']);
    
            if(!empty($_POST['newName'])){
                $id=FinancialMovement::getCategoryOrMethodIdByName($nameToChange, $tableWithCategories);
                $sql="UPDATE ".$tableWithCategories." SET name=:name WHERE id=:id ";
                $db=static::getDB();
                $stmt=$db->prepare($sql);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->bindValue(':name', $newName, PDO::PARAM_STR);
                $stmt->execute();
            }
        }
        return true;
    }

    public function validateLimit($limit){
        $limit=FinancialMovement::checkCommaAndChangeForDot($limit);
        
        if(!is_numeric($limit)){
            $this->errors[]="Wrong value of amount";
        }else{
            if(FinancialMovement::checkHowManyDecimalPlacesHasAmount($limit)>2){
                $this->errors[]='Your amount has more than two decimal places.';
            }
            if($limit==''){
                $this->errors[]='Amount is required';
            }
        }
        if(empty($this->errors)){
            return true;
        }else{
            return false;
        }
    }

    public function checkThatNameIsAvailable($whatToChange, $nameToCheck){
        if($whatToChange=="expenseCategory"){
            foreach($this->expenseCategoriesNamesAndLimits as $expenseCategoryData){
                 if($expenseCategoryData['name']==$nameToCheck){
                    $this->errors[]='Name is already in use. Please use different one.';
                     return false;
                 }
            }
        }elseif($whatToChange=="incomeCategory"){
            foreach($this->incomeCategoriesNames as $incomeCategoryName){
                if($incomeCategoryName==$nameToCheck){
                    $this->errors[]='Name is already in use. Please use different one.';
                     return false;
                }
            }
        }elseif($whatToChange=="paymentMethod"){
            foreach($this->paymentMethodsNames as $paymentMethodName){
                if($paymentMethodName==$nameToCheck){
                    $this->errors[]='Name is already in use. Please use different one.';
                     return false;
                }
            }
        }
        return true;
    }

    public function addLimit(){
        $nameToChange=$_POST['categoryOrPaymentMethodName'];
        $tableWithCategories=static::getCorrectTableToInsertData($_POST['whatToChange']);
        $limit=$_POST['limit'];
        $id=FinancialMovement::getCategoryOrMethodIdByName($nameToChange, $tableWithCategories);
        $sql="UPDATE ".$tableWithCategories." SET expense_limit=:expense_limit  WHERE id=:id ";
        $db=static::getDB();
        $stmt=$db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':expense_limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function addNewCategoryOrPaymentMethod(){
        if(isset($_POST['newNameToAdd'])){
            $name=$_POST['newNameToAdd'];
            $userId=$_SESSION['user_id'];
            $tableWithCategories=static::getCorrectTableToInsertData($_POST['whatToAdd']);
            $sql="INSERT INTO ".$tableWithCategories." (user_id, name) VALUES(".$userId.",:name)";
            $db=static::getDB();
            $stmt=$db->prepare($sql);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
        }
        return true;
    }

    public static function deleteCategoryOrPaymentMethod(){
        if(isset($_POST['whatToDelete'])){
            $tableWithCategories=static::getCorrectTableToInsertData($_POST['whatToDelete']);
            $id=FinancialMovement::getCategoryOrMethodIdByName($_POST['categoryOrMethodNameToDelete'], $tableWithCategories);
            $sql="DELETE FROM ".$tableWithCategories." WHERE id='".$id."'";
            $db=static::getDB();
            $stmt=$db->prepare($sql);
            $stmt->execute();
            return true;
        }
    }

    protected static function getCorrectTableToInsertData($stringWhichDefineTable){
        if($stringWhichDefineTable=='incomeCategory'){
            static::setChangeIncomeSettingFlagON();
            return static::getUserTableWithIncomesCategory();
        }else if($stringWhichDefineTable=='paymentMethod'){
            static::setChangePaymentMethodsSettingFlagON();
            return static::getUserTableWithPaymentMethods();
        }else if($stringWhichDefineTable=='expenseCategory'){
            static::setChangeExpenseSettingFlagON();
            return static::getUserTableWithExpensesCategory();
        }
    }

    public static function setChangeIncomeSettingFlagON(){
        $_SESSION['incomeChangeSettingFlag']=true;
    }

    public static function setChangeExpenseSettingFlagON(){
        $_SESSION['expenseChangeSettingFlag']=true;
    }

    public static function setChangePaymentMethodsSettingFlagON(){
        $_SESSION['methodsChangeSettingFlag']=true;
    }

    public static function getLimitForExpenseById($id, $tableWithData){
        $sql="SELECT * FROM ".$tableWithData." WHERE id=:id";
        $db=static::getDB();
        $stmt=$db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result=$stmt->fetch(PDO::FETCH_ASSOC);
        if(isset($result['expense_limit'])){
            return $result['expense_limit'];
        }
    }

}