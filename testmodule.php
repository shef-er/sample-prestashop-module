<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_.'testmodule/models/Students.php');

class TestModule extends Module
{
    public function __construct()
    {
        $this->name = 'testmodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Ernest Shefer';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Test module');
        $this->description = $this->l('Description of my module.');
	    $this->controllers = Array('test', 'admintest');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
            !$this->registerHook('moduleRoutes') ||
            !Configuration::updateValue('TESTMODULE_PRICE_FROM', '0.0') ||
            !Configuration::updateValue('TESTMODULE_PRICE_TO', '1000.0')
        ) {
            return false;
        }

        if	(!$this->installTab( 'AdminTest', 'AdminTest'))
            return false;

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('TESTMODULE_PRICE_FROM') ||
            !Configuration::deleteByName('TESTMODULE_PRICE_TO')
        ) {
            return false;
        }

        // Uninstall admin tab
        if (!$this->uninstallTab('AdminTest'))
            return false;

        return true;
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            $test_module_price_from = strval(Tools::getValue('TESTMODULE_PRICE_FROM'));
            $test_module_price_to = strval(Tools::getValue('TESTMODULE_PRICE_TO'));

            if (
                   !$test_module_price_from
                || !$test_module_price_to
                || empty($test_module_price_from)
                || empty($test_module_price_to)
                || !Validate::isGenericName($test_module_price_from)
                || !Validate::isGenericName($test_module_price_to)
            )
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            else {
                Configuration::updateValue('TESTMODULE_PRICE_FROM', $test_module_price_from);
                Configuration::updateValue('TESTMODULE_PRICE_TO', $test_module_price_to);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Настройки'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Цена ОТ'),
                    'name' => 'TESTMODULE_PRICE_FROM',
                    'size' => 20,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Цена ДО'),
                    'name' => 'TESTMODULE_PRICE_TO',
                    'size' => 20,
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Сохранить'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' => array(
                'desc' => $this->l('Сохранить'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                        '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Обратно к списку')
            )
        );

        // Load current value
        $helper->fields_value['TESTMODULE_PRICE_FROM'] = Configuration::get('TESTMODULE_PRICE_FROM');
        $helper->fields_value['TESTMODULE_PRICE_TO'] = Configuration::get('TESTMODULE_PRICE_TO');

        return $helper->generateForm($fields_form);
    }

    public function hookModuleRoutes($params)
    {
        return array(
            'module-testmodule-test' => array(
                'controller' => 'test',
                /*'rule' =>       'test/{id}',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+', 'param' => 'id'),
                ),*/
                'params' => array(
                    'fc' => 'module',
                    'module' => 'testmodule',
                ),
            )
        );
    }

    public function installTab($class_name, $name)
    {
        $tab = new Tab();
        $tab->id_parent	= 0;
        $tab->name = array();
        foreach	(Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $name;
        }
        $tab->class_name = $class_name;
        $tab->module = $this->name;
        $tab->active = 1;
        return $tab->add();
    }

    public function uninstallTab($class_name)
    {
        //	Retrieve Tab ID
        $id_tab	= (int)Tab::getIdFromClassName($class_name);
        //	Load tab
        $tab = new Tab((int)$id_tab);
        //	Delete it
        return $tab->delete();
    }


    /*
    public function hookDisplayFooter($params)
    {
        $test_module_price_from = Configuration::get('TESTMODULE_PRICE_FROM');
        $test_module_price_to = Configuration::get('TESTMODULE_PRICE_TO');

        $products = $this->getProductsQuantityByPrice($this->context->language->id, $test_module_price_from, $test_module_price_to);


        $this->context->smarty->assign(
            array(
                'test_module_price_from' => Configuration::get('TESTMODULE_PRICE_FROM'),
                'test_module_price_to' => Configuration::get('TESTMODULE_PRICE_TO'),
                'test_module_product_count' => count($products),
            )
        );
        return $this->display(__FILE__, 'testmodule.tpl');
    }
    public function getProductsQuantityByPrice($id_lang, $price_from = 0, $price_to = 9999999,  $id_category = false,
                                       $only_active = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        $sql = 'SELECT p.*, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)'.
            ($id_category ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
				WHERE pl.`id_lang` = '.(int)$id_lang.
            ($id_category ? ' AND c.`id_category` = '.(int)$id_category : '').
            ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
            ($only_active ? ' AND product_shop.`active` = 1' : '').
            ' AND p.`price` BETWEEN '. $price_from .' AND '. $price_to;
//            'ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way).
//            ($limit > 0 ? ' LIMIT '.(int)$start.','.(int)$limit : '');

        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);


        return ($rq);
    }
    */

}
