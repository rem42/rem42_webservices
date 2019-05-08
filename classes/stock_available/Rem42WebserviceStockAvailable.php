<?php

class Rem42WebserviceStockAvailable
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
	protected $validFilter = ['id_stock_available', 'id_product', 'id_product_attribute'];

	/**
	 * Rem42WebserviceStockAvailable constructor.
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
	 * @return string
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public static function load(WebserviceRequest $input, WebserviceOutputBuilder $output)
	{
		$self = new self($input, $output);
		return $self->execute();
	}

	/**
	 * @return string
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public function execute()
	{
		$this->manageFilters();
		if ($this->input->urlSegment[2] > 0) {

		} else {
			$stockAvailable       = new StockAvailable();
			$webserviceParameters = $stockAvailable->getWebserviceParameters();
			$stockAvailables      = $stockAvailable->getWebserviceObjectList(null, null, null, null);

			$this->webserviceReturn->isString = true;
			$this->webserviceReturn->string   .= $this->output->getObjectRender()
				->renderNodeHeader('rem42_webservices', $webserviceParameters)
			;
			foreach ($stockAvailables as $stock) {
				$more_attr                      = [
					'id' => $stock["id_stock_available"],
					'xlink_resource' => $this->input->wsUrl . $this->input->urlSegment[0] . '/' . $this->input->urlSegment[1] . '/' . $stock["id_stock_available"],
				];
				$this->webserviceReturn->string .= $this->output->getObjectRender()
					->renderNodeHeader('stock_available', $webserviceParameters, $more_attr, false)
				;
			}
			$this->webserviceReturn->string .= $this->output->getObjectRender()
				->renderNodeFooter('rem42_webservices', $webserviceParameters)
			;
		}
		return $this->webserviceReturn;
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
		$deliveryInfo->id       = $order->id;
		$deliveryInfo->id_order = $order->id;

		if ($this->input->fieldsToDisplay === 'minimum') {
			$this->webserviceReturn->string .= $this->output->renderEntityMinimum($deliveryInfo, $depth);
		} else {
			$this->webserviceReturn->string .= $this->output->renderEntity($deliveryInfo, $depth);
		}
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
		$this->input->resourceConfiguration = (new StockAvailable())->getWebserviceParameters();
		$this->input->setFieldsToDisplay();
		$this->output->setFieldsToDisplay($this->input->fieldsToDisplay);
	}
}
