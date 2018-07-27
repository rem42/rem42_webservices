<?php

$require = [
	'WebserviceReturn.php',
	'order/Rem42WebserviceOrder.php',
	'order/Rem42WebserviceInvoice.php',
];

foreach ($require as $item) {
	require_once $item;
}

class WebserviceSpecificManagementRem42Webservice implements WebserviceSpecificManagementInterface
{
	/** @var WebserviceOutputBuilder */
	protected $output;
	/**
	 * @var WebserviceReturn
	 */
	protected $webserviceReturn;

	/** @var WebserviceRequest */
	protected $input;

	/**
	 * @var array the webservices options
	 *            'get' => 'true', 'put' => 'true', 'post' => 'false', 'delete' => 'false', 'head' => 'true',
	 */
	protected $wsOptions = [
		'orders',
	];

	public function setObjectOutput(WebserviceOutputBuilderCore $obj)
	{
		$this->output = $obj;
		return $this;
	}

	public function getObjectOutput()
	{
		return $this->output;
	}

	public function getWsObject()
	{
		return $this->input;
	}

	public function setWsObject(WebserviceRequestCore $obj)
	{
		$this->input = $obj;
		return $this;
	}

	/**
	 * @return bool
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public function manage()
	{
		$this->webserviceReturn = new WebserviceReturn();
		$this->manageWebservices();
		return $this->input->getOutputEnabled();
	}

	/**
	 * @return bool
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	protected function manageWebservices()
	{
		if (isset($this->input->urlSegment)) {
			for ($i = 1; $i < 6; $i++) {
				if (count($this->input->urlSegment) == $i) {
					$this->input->urlSegment[$i] = '';
				}
			}
		}

		$firstLevel = [
			'orders' => Rem42WebserviceOrder::class,
		];
		if (isset($firstLevel[$this->input->urlSegment[1]])) {
			$this->webserviceReturn = $firstLevel[$this->input->urlSegment[1]]::load($this->input, $this->output);
		} elseif (strlen($this->input->urlSegment[1]) == 0) {
			$this->webserviceReturn->string .= $this->output->getObjectRender()->renderNodeHeader('rem42_webservices', []);
			foreach ($this->wsOptions as $wsOptions) {
				$more_attr            = [
					'xlink_resource' => $this->input->wsUrl . $this->input->urlSegment[0] . '/' . $wsOptions,
				];
				$this->webserviceReturn->string .= $this->output->getObjectRender()
					->renderNodeHeader($wsOptions, [], $more_attr, false)
				;
			}
			$this->webserviceReturn->string .= $this->output->getObjectRender()->renderNodeFooter('rem42_webservices', []);
		} else {
			$this->input->setError(404, "Method unknown", 1);
		}
	}

	/**
	 * @return array
	 * @throws WebserviceException
	 */
	public function getContent()
	{
		if ($this->webserviceReturn->isString) {
			return $this->output->getObjectRender()->overrideContent($this->webserviceReturn->string);
		}elseif ($this->webserviceReturn->contentType != ''){
			$this->output->setHeaderParams('Content-Type', $this->webserviceReturn->contentType);
			return $this->webserviceReturn->data;
		}
	}
}
