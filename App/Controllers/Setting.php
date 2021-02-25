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
        if(Settings::changeCategoryOrPaymentMethod()){
            Flash::addMessage("Choosen item is changed.");
            $setting=new Settings($_POST);
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
            Flash::addMessage("Choosen item is deleted.",'warning');
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
