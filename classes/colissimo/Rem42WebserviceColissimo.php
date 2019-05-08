<?php

class Rem42WebserviceColissimo
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
	 * @var array the webservices options
	 */
	protected $wsOptions = [
		'pickup_points' => 'Rem42WebserviceColissimoPickupPoint',
	];

	/**
	 * Rem42WebserviceInvoice constructor.
	 *
	 * @param WebserviceRequest       $input
	 * @param WebserviceOutputBuilder $output
	 */
	public function __construct(WebserviceRequest $input, WebserviceOutputBuilder $output)
	{
		$this->input  = $input;
		$this->output = $output;
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
		if (isset($this->wsOptions[$this->input->urlSegment[2]])) {
			$this->webserviceReturn = $this->wsOptions[$this->input->urlSegment[2]]::load($this->input, $this->output);
		} elseif (strlen($this->input->urlSegment[2]) == 0) {
			$this->webserviceReturn->isString = true;
			$this->webserviceReturn->string .= $this->output->getObjectRender()->renderNodeHeader('rem42_webservices', []);
			foreach (array_keys($this->wsOptions) as $wsOptions) {
				$more_attr          = [
					'xlink_resource' => $this->input->wsUrl . $this->input->urlSegment[0] . '/' . $this->input->urlSegment[1] . '/' . $wsOptions,
				];
				$this->webserviceReturn->string .= $this->output->getObjectRender()
					->renderNodeHeader($wsOptions, [], $more_attr, false)
				;
			}
			$this->webserviceReturn->string .= $this->output->getObjectRender()->renderNodeFooter('rem42_webservices', []);
		} else {
			$this->input->setError(404, "Method unknown", 2);
		}
		return $this->webserviceReturn;
	}
}
