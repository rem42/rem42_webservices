<?php

/**
 * @param $class
 *
 * @throws Exception
 */
function load_webservice_class_socolissimo($class)
{
	if (is_dir(_PS_MODULE_DIR_ . '/soflexibilite')) {
		if (file_exists(_PS_MODULE_DIR_ . '/soflexibilite/classes/' . $class . '.php') !== false) {
			require_once _PS_MODULE_DIR_ . '/soflexibilite/classes/' . $class . '.php';
		}
	} else {
		throw new Exception("Soflexibilite module doesn't exist, please install it !");
	}
}

spl_autoload_register('load_webservice_class_socolissimo');

class Rem42WebserviceSocolissimoDeliveryInfo
{
	/**
	 * @var WebserviceRequest
	 */
	protected $input;
	/**
	 * @var WebserviceOutputBuilder
	 */
	protected $output;
	/**
	 * @var WebserviceReturn
	 */
	protected $webserviceReturn;
	/**
	 * @var array
	 */
	protected $filter;
	/**
	 * @var array
	 */
	protected $validFilter = ['id_cart', 'id_customer', 'id_order'];

	/**
	 * Rem42WebserviceInvoice constructor.
	 *
	 * @param WebserviceRequest       $input
	 * @param WebserviceOutputBuilder $output
	 */
	public function __construct(WebserviceRequest $input, WebserviceOutputBuilder $output)
	{
		$this->input            = $input;
		$this->output           = $output;
		$this->webserviceReturn = new WebserviceReturn();
	}

	/**
	 * @param WebserviceRequest       $input
	 * @param WebserviceOutputBuilder $output
	 *
	 * @return WebserviceReturn
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public static function load(WebserviceRequest $input, WebserviceOutputBuilder $output)
	{
		$self = new self($input, $output);
		return $self->execute();
	}

	/**
	 * @return WebserviceReturn
	 */
	public function execute()
	{
		$this->manageFilters();
		if (sizeof($this->filter) > 0) {
			if (isset($this->filter['id_order']) && $this->filter['id_order'] > 0) {
				$order        = new Order($this->filter['id_order']);
				$deliveryInfo = new WsSoFlexibiliteDelivery($order->id_cart, $order->id_customer);
			}elseif(isset($this->filter['id_cart']) && $this->filter['id_cart'] > 0 && isset($this->filter['id_customer']) && $this->filter['id_customer'] > 0){
				$order = Order::getByCartId($this->filter['id_cart']);
				$deliveryInfo = new WsSoFlexibiliteDelivery($this->filter['id_cart'], $this->filter['id_customer']);
			}else{
				$this->input->setError(400, 'Error on filter, you must fill id_order or (id_cart and id_customer)', 2);
				return $this->webserviceReturn;
			}
			$deliveryInfo->loadDelivery();
			$deliveryInfo->id = $order->id;
			$deliveryInfo->id_order = $order->id;
			$this->output->setFieldsToDisplay('full');
			$this->webserviceReturn->isString = true;
			$this->webserviceReturn->string   .= $this->output->renderEntity($deliveryInfo, 0);
		} else {
			$this->input->setError(400, 'Error on filter, you must fill id_order or (id_cart and id_customer)', 3);
		}
		return $this->webserviceReturn;
	}

	protected function manageFilters()
	{
		if (isset($this->input->urlFragments["filter"])) {
			foreach ($this->input->urlFragments["filter"] as $urlFragment => $value) {
				if (in_array($urlFragment, $this->validFilter)) {
					$this->filter[$urlFragment] = $value;
				} else {
					$this->input->setErrorDidYouMean(400, 'This filter does not exist for this linked table', $urlFragment, $this->validFilter, 1);
				}
			}
		}
	}
}
