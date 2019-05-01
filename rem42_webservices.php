<?php

if (!defined('_PS_VERSION_')) {
	exit;
}

require_once __DIR__.'/classes/WebserviceSpecificManagementRem42Webservice.php';

class Rem42_webservices extends Module
{
	protected $config_form = false;

	public function __construct()
	{
		$this->name          = 'rem42_webservices';
		$this->tab           = 'others';
		$this->version       = '1.0.0';
		$this->author        = 'rem42';
		$this->need_instance = 0;

		/**
		 * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
		 */
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Extends webservices');
		$this->description = $this->l('Add new missing webservices for prestashop 1.7');

		$this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
	}

	/**
	 * Don't forget to create update methods if needed:
	 * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
	 */
	public function install()
	{

		return parent::install() &&
			$this->registerHook('addWebserviceResources');
	}

	public function uninstall()
	{

		return parent::uninstall();
	}

	/**
	 * Load the configuration form
	 */
	public function getContent()
	{
		$this->context->smarty->assign('module_dir', $this->_path);

		$output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

		return $output;
	}

	public function hookAddWebserviceResources(array $resources)
	{
		$resources = [];

		$resources['rem42_webservice'] = ['description' => 'Extends Webservices', 'specific_management' => true];

		return $resources;
	}
}
