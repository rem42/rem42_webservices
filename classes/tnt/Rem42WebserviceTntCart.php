<?php

class Rem42WebserviceTntCart
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
        protected $validFilter = [];

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

        $this->manageFilters();
        $depth = 0;
        $this->webserviceReturn->isString = true;


        if(strlen($this->input->urlSegment[3]) > 0) {
            $address = TNTOfficielCart::loadCartID($this->input->urlSegment[3], false);
            $this->webserviceReturn->string .= $this->output->renderEntity($address, null);
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

        if(!isset($this->input->urlFragments['display'])){
            $this->input->urlFragments['display'] = 'full';
        }
        $this->input->setFieldsToDisplay();
        $this->output->setFieldsToDisplay($this->input->fieldsToDisplay);
    }
}
