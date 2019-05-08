<?php

class Rem42WebserviceStats
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
     * Rem42WebserviceStats constructor.
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
        $this->webserviceReturn->isString = true;
        $this->renderEntity();
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
    protected function renderEntity()
    {
        $prestashopStats = new PrestashopStats();
        $prestashopStats->loadAll();

       $this->output->renderEntity($prestashopStats, 0);
    }

    protected function manageFilters()
    {
        $this->input->urlFragments['display'] = 'full';
        $this->input->resourceConfiguration = (new PrestashopStats())->getWebserviceParameters();
        $this->input->setFieldsToDisplay();
        $this->output->setFieldsToDisplay($this->input->fieldsToDisplay);
    }
}
