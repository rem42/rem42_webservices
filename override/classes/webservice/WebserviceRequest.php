<?php


class WebserviceRequest extends WebserviceRequestCore
{
    public function getInputXML()
    {
        return $this->_inputXml;
    }

    public static function getResources()
    {
        $resources = WebserviceRequestCore::getResources();

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            return $resources;
        }

        $hookResources = Hook::exec('addWebserviceResources', array('resources' => $resources), null, true, false);

        $resources = array_merge($resources, $hookResources);

        ksort($resources);
        return $resources;
    }
}
