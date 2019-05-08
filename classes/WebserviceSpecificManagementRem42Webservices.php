<?php

function load_webservice_class($class)
{
	if (file_exists(__DIR__ . '/' . $class . '.php') !== false) {
		require_once __DIR__ . '/' . $class . '.php';
	} else {
		foreach (glob(__DIR__ . '/*', GLOB_ONLYDIR) as $dir) {
			if (file_exists($dir . '/' . $class . '.php') !== false) {
				require_once $dir . '/' . $class . '.php';
				break;
			}
		}
	}
}

spl_autoload_register('load_webservice_class');

class WebserviceSpecificManagementRem42Webservices implements WebserviceSpecificManagementInterface
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
	 */
	protected $wsOptions = [
		'orders' => 'Rem42WebserviceOrder',
		'socolissimo' => 'Rem42WebserviceSocolissimo',
		'colissimo' => 'Rem42WebserviceColissimo',
		'stock_available' => 'Rem42WebserviceStockAvailable',
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
	 */
	public function manage()
	{
		$this->webserviceReturn = new WebserviceReturn();
		$this->manageWebservices();
		return $this->input->getOutputEnabled();
	}

	/**
	 *
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

		if (isset($this->wsOptions[$this->input->urlSegment[1]])) {
			$this->webserviceReturn = $this->wsOptions[$this->input->urlSegment[1]]::load($this->input, $this->output);
		} elseif (strlen($this->input->urlSegment[1]) == 0) {
			$this->webserviceReturn->isString = true;
			$this->webserviceReturn->string   .= $this->output->getObjectRender()
				->renderNodeHeader('rem42_webservices', [])
			;
			foreach ($this->wsOptions as $wsOptions => $value) {
				$more_attr                      = [
					'xlink_resource' => $this->input->wsUrl . $this->input->urlSegment[0] . '/' . $wsOptions,
				];
				$this->webserviceReturn->string .= $this->output->getObjectRender()
					->renderNodeHeader($wsOptions, [], $more_attr, false)
				;
			}
			$this->webserviceReturn->string .= $this->output->getObjectRender()
				->renderNodeFooter('rem42_webservices', [])
			;
		} else {
			$this->input->setError(404, "Method unknown", 1);
		}
	}

	/**
	 * @return array|mixed
	 * @throws WebserviceException
	 */
	public function getContent()
	{
		if ($this->webserviceReturn->isString) {
			return $this->output->getObjectRender()->overrideContent($this->webserviceReturn->string);
		} elseif ($this->webserviceReturn->contentType != '') {
			$this->output->setHeaderParams('Content-Type', $this->webserviceReturn->contentType);
			return $this->webserviceReturn->data;
		}
	}
}
