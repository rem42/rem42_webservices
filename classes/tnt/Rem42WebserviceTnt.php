<?php

/**
 * @param $class
 *
 * @throws Exception
 */
function load_webservice_class_tnt($class)
{
    if (is_dir(_PS_MODULE_DIR_ . '/tntofficiel')) {
        if (file_exists(_PS_MODULE_DIR_ . '/tntofficiel/classes/' . $class . '.php') !== false) {
            require_once _PS_MODULE_DIR_ . '/tntofficiel/classes/' . $class . '.php';
        }
    } else {
        throw new Exception("Tnt module doesn't exist, please install it !");
    }
}

spl_autoload_register('load_webservice_class_tnt');

class Rem42WebserviceTnt
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
        'tntofficiel_receiver' => 'Rem42WebserviceTntReceiver',
        'tntofficiel_order' => 'Rem42WebserviceTntOrder',
        'tntofficiel_cart' => 'Rem42WebserviceTntCart',
    ];

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
        if (isset($this->wsOptions[$this->input->urlSegment[2]])) {
            $valueClass = $this->wsOptions[$this->input->urlSegment[2]];
            $this->webserviceReturn = $valueClass::load($this->input, $this->output);
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
