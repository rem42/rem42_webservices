<?php

class Rem42WebserviceInvoice
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
		if ($this->input->urlSegment[3] > 0) {
            @ini_set('display_errors', 'on');
            @error_reporting(E_ALL | E_STRICT);
			$orderInvoice = new OrderInvoice($this->input->urlSegment[3]);
			$order = new Order((int)$orderInvoice->id_order);
			$order_invoice_list = $order->getInvoicesCollection();
			Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $order_invoice_list));
			$pdf = new PDF($order_invoice_list->getFirst(), PDF::TEMPLATE_INVOICE, Context::getContext()->smarty);
			$pdf->render('I');
		} else {
			$orderInvoice         = new OrderInvoice();
			$webserviceParameters = $orderInvoice->getWebserviceParameters();
			$orderInvoices        = $orderInvoice->getWebserviceObjectList(null, null, null, null);

			$this->webserviceReturn->isString = true;
			$this->webserviceReturn->string   .= $this->output->getObjectRender()
				->renderNodeHeader('rem42_webservices', $webserviceParameters)
			;
			foreach ($orderInvoices as $order) {
				$more_attr                      = [
					'id' => $order["id_order_invoice"],
					'xlink_resource' => $this->input->wsUrl . $this->input->urlSegment[0] . '/' . $this->input->urlSegment[1] . '/' . $this->input->urlSegment[2] . '/' . $order["id_order_invoice"],
				];
				$this->webserviceReturn->string .= $this->output->getObjectRender()
					->renderNodeHeader('invoice', $webserviceParameters, $more_attr, false)
				;
			}
			$this->webserviceReturn->string .= $this->output->getObjectRender()
				->renderNodeFooter('rem42_webservices', $webserviceParameters)
			;
		}
		return $this->webserviceReturn;
	}
}
