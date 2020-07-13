<?php

/**
 * @param $class
 *
 * @throws Exception
 */
function load_webservice_class_colissimo_new($class)
{
    if (is_dir(_PS_MODULE_DIR_ . '/colissimo')) {
        if (file_exists(_PS_MODULE_DIR_ . '/colissimo/classes/' . $class . '.php') !== false) {
            require_once _PS_MODULE_DIR_ . '/colissimo/classes/' . $class . '.php';
        }
    } else {
        throw new Exception("Colissimo module doesn't exist, please install it !");
    }
}

spl_autoload_register('load_webservice_class_colissimo_new');

class Rem42WebserviceColissimoTrackingNumber
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
    public function execute() {
        if($this->input->getInputXML() === null) {
            return $this->webserviceReturn;
        }
        $xml = new SimpleXMLElement($this->input->getInputXML());
        $xmlEntities = $xml->children();
        $attributes = $xmlEntities->children();

        $idOrder = $attributes->id_order->__toString();

        if(class_exists(ColissimoLabel::class)) {
            $colissimoOrderId = ColissimoOrder::getIdByOrderId($idOrder);
            if($colissimoOrderId) {
                $colissimoOrder = new ColissimoOrder($colissimoOrderId);
                $colissimoLabel = new ColissimoLabel();
                $colissimoLabel->id_colissimo_order = $colissimoOrder->id_colissimo_order;
                $colissimoLabel->shipping_number = $attributes->tracking_number->__toString();
                $colissimoLabel->label_format = 'pdf';
                $colissimoLabel->return_label = 0;
                $colissimoLabel->coliship = 0;
                $colissimoLabel->migration = 0;
                $colissimoLabel->insurance = 0;
                $colissimoLabel->cn23 = false;
                $colissimoLabel->file_deleted = true;
                $colissimoLabel->save();
            }
        }

        $this->webserviceReturn->string = true;

        return $this->webserviceReturn;
    }
}
