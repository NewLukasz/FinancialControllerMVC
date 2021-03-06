<?php

namespace App\Controllers;

use \Core\View;
use \App\Flash;
use \App\Models\Settings;

class Setting extends Authenticated
{

    /**
     * Require the user to be authenticated before giving access to all methods in the controller
     *
     * @return void
     */
    /*
    protected function before()
    {
        $this->requireLogin();
    }
    */

    /**
     * Items index
     *
     * @return void
     */
    public function indexAction(){
        $setting=new Settings($_POST);
        View::renderTemplate('Setting/index.html',[
            'setting'=>$setting
        ]);
    }

    public function changeCategoryOrPaymentMethodAction(){
        $setting=new Settings($_POST);
        if(isset($_POST['limit'])){
            if($setting->validateLimit($_POST['limit'])){
                $setting->addLimit();
                Flash::addMessage("Limit is updated.");
                if($_POST['newName']==""){
                    $setting=new Settings($_POST);
                    View::renderTemplate('Setting/index.html',[
                    'setting'=>$setting
                ]);
                }
            }else{
                Flash::addMessage("Limit isn't updated.",'warning');
                if($_POST['newName']==""){
                    View::renderTemplate('Setting/index.html',[
                        'setting'=>$setting
                    ]);
                }
            }
        }
        if(isset($_POST['newName']) and $_POST['newName']!=""){
            if($setting->checkThatNameIsAvailable($_POST['whatToChange'],$_POST['newName'])){
                if(Settings::changeCategoryOrPaymentMethod()){
                    Flash::addMessage("Name is changed.");
                    $setting=new Settings($_POST);
                    View::renderTemplate('Setting/index.html',[
                        'setting'=>$setting
                    ]);
                }
            }else{
                Flash::addMessage("Choosen item is not changed.",'warning');
                View::renderTemplate('Setting/index.html',[
                    'setting'=>$setting
                ]);
            }
        }
        if(!isset($_POST['newName'])){
            View::renderTemplate('Setting/index.html',[
                'setting'=>$setting
            ]);
        }
    }

    public function addNewCategoryOrPaymentMethodAction(){
        if(Settings::addNewCategoryOrPaymentMethod()){
            Flash::addMessage("New item is added.");
            $setting=new Settings($_POST);
            View::renderTemplate('Setting/index.html',[
                'setting'=>$setting
            ]);
        }
    }

    public function deleteCategoryOrPaymentMethodAction(){
        if(Settings::deleteCategoryOrPaymentMethod()){
            Flash::addMessage("Category is deleted. Items which were aligned to deleted category is moved to another category.",'warning');
            $setting=new Settings($_POST);
            View::renderTemplate('Setting/index.html',[
                'setting'=>$setting
            ]);
        }
    }

    /**
     * Add a new item
     *
     * @return void
     */
    public function newAction()
    {
        echo "new action";
    }

    /**
     * Show an item
     *
     * @return void
     */
    public function showAction()
    {
        echo "show action";
    }
}
