<?php

class TestModuleAdminController extends AdminController
{
    public function initContent()
    {
        parent::initContent();

        $this->show_toolbar = false;
        $this->context->smarty->assign(array(
            'content' => $this->renderSettings() . $this->renderForm() . $this->displayFields(),
        ));
    }
}