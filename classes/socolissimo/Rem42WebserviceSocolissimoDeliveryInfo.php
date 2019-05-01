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
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public function execute()
	{
		$this->manageFilters();
		if (null !== $this->filter && sizeof($this->filter) > 0) {
			$this->webserviceReturn->isString = true;
			if (isset($this->filter['id_order']) && sizeof($this->filter['id_order']) > 0) {
				$depth = 0;
				if (sizeof($this->filter['id_order']) > 1) {
					$this->webserviceReturn->string .= $this->output->setIndent($depth) . $this->output->getObjectRender()
							->renderNodeHeader(WsSoFlexibiliteDelivery::$webserviceParameters['objectsNodeName'], WsSoFlexibiliteDelivery::$webserviceParameters)
					;
				}
				foreach ($this->filter['id_order'] as $idOrder) {
					$this->renderEntity($idOrder, null, $depth);
				}
				if (sizeof($this->filter['id_order']) > 1) {
					$this->webserviceReturn->string .= $this->output->setIndent($depth) . $this->output->getObjectRender()
							->renderNodeFooter(WsSoFlexibiliteDelivery::$webserviceParameters['objectsNodeName'], WsSoFlexibiliteDelivery::$webserviceParameters)
					;
				}
			} elseif (isset($this->filter['id_cart']) && $this->filter['id_cart'] > 0 && isset($this->filter['id_customer']) && $this->filter['id_customer'] > 0) {
				$this->renderEntity(null, $this->filter['id_cart']);
			} else {
				$this->input->setError(400, 'Error on filter, you must fill id_order or (id_cart and id_customer)', 2);
				return $this->webserviceReturn;
			}
		} else {
			$this->input->setError(400, 'Error on filter, you must fill id_order or id_cart', 3);
		}
		return $this->webserviceReturn;
	}

	protected function manageFilters()
	{
		if (isset($this->input->urlFragments["filter"])) {
			foreach ($this->input->urlFragments["filter"] as $urlFragment => $value) {
				if (in_array($urlFragment, $this->validFilter)) {
					$value                      = str_replace(['[', ']'], '', $value);
					$value                      = explode('|', $value);
					$this->filter[$urlFragment] = $value;
				} else {
					$this->input->setErrorDidYouMean(400, 'This filter does not exist for this linked table', $urlFragment, $this->validFilter, 1);
				}
			}
		}
		if(isset($this->input->urlSegment[3]) && $this->input->urlSegment[3] > 0){
			$this->filter['id_order'] = [$this->input->urlSegment[3]];
			if(!isset($this->input->urlFragments['display'])){
				$this->input->urlFragments['display'] = 'full';
			}
		}
		$this->input->resourceConfiguration = WsSoFlexibiliteDelivery::$webserviceParameters;
		$this->input->setFieldsToDisplay();
		$this->output->setFieldsToDisplay($this->input->fieldsToDisplay);
	}

	/**
	 * @param null $idOrder
	 * @param null $idCart
	 * @param null $depth
	 *
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	protected function renderEntity($idOrder = null, $idCart = null, $depth = null)
	{
		if ($idOrder) {
			$order = new Order($idOrder);
		} else {
			$order = Order::getByCartId($idCart);
		}
		$deliveryInfo = new WsSoFlexibiliteDelivery($order->id_cart, $order->id_customer);
		$deliveryInfo->loadDelivery();

		$deliveryInfo->id       = $order->id_address_delivery;
		$deliveryInfo->id_order = $order->id;

		if ($this->input->fieldsToDisplay === 'minimum') {
			$this->webserviceReturn->string .= $this->output->renderEntityMinimum($deliveryInfo, $depth);
		} else {
			$this->webserviceReturn->string .= $this->output->renderEntity($deliveryInfo, $depth);
		}
	}
}
