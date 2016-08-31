<?php

if (!defined('_PS_VERSION_')){
    exit;
}

/**
 * AdminProductStock is a class for send e-mail for stock products change
 * Module created for exercise 1 for GT2I
 *
 * @author Christophedlr Daloz - De Los Rios
 */
class AdminProductStock extends Module
{
    public function __construct()
    {
        $this->name = 'adminproductstock';
        $this->tab = 'administration';
        $this->version = '1.0';
        $this->author = 'Christophe Daloz - De Los Rios';
        $this->need_instance = 0;
        $this->ps_version_compliancy = array('min' => '1.6',
            'max' => _PS_VERSION_);
        
        parent::__construct();
        
        $this->displayName = $this->l('Admin product stock');
        $this->description = $this->l('Send mail to admin for inform '
            . 'stock of product');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        
        if (!Configuration::get('ADMINPRODUCTSTOCK_NAME')){
            $this->warning = $this->l('No name provided');
        }
    }
    
    /**
     * Installation of module
     * @return boolean
     */
    public function install()
    {
        if (!parent::install() || !$this->registerHook('actionUpdateQuantity')){
            return false;
        }
        
        return true;
    }
    
   /**
    * Uninstall of module
    * @return boolean
    */
    public function uninstall()
    {
        if (!parent::uninstall()){
            return false;
        }
        
        return true;
    }
    
    /**
     * Hook for update quantity of product and send e-mail
     * @global type $cookie
     * @param type $params
     */
    public function hookActionUpdateQuantity($params)
    {
        global $cookie;
        
        $mail = Configuration::get('PS_SHOP_EMAIL');
        $employeeClass = new EmployeeCore();
        $employee = $employeeClass->getByemail($mail);
        
        $productClass = new ProductCore();
        $quantity = $productClass->getQuantity($params['id_product']);
        $productName = $productClass->getProductName($params['id_product']);
        
        $data = array('{admin}' => $employee->firstname,
            '{productname}' => $productName, '{stock}' => $quantity);
        
        Mail::Send(intval($cookie->id_lang), 'change',
            'Changement sur un produit', $data, $mail,
            null, 'test@chrislocal.fr.nf', null, null, null, __DIR__.'/views/mails/');
    }
}
