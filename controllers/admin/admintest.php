<?php

class AdminTestController extends ModuleAdminController
{

    public function __construct()
    {
        //	Enable	bootstrap
        $this->bootstrap = true;

        parent::__construct();
    }

    public function initContent()
    {

        $this->show_toolbar = false;
        $this->context->smarty->createTemplate('admintest.tpl', $this->context->smarty);

        parent::initContent();
    }

    public function renderView()
    {
        $tpl = $this->context->smarty->createTemplate(_PS_MODULE_DIR_.'/testmodule/views/templates/admin/admintest.tpl');
        return $tpl->fetch();
    }

}