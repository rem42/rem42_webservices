<?php


class WebserviceRequest extends WebserviceRequestCore
{
    public static function getResources()
    {
        $resources = WebserviceRequestCore::getResources();

        $hookResources = Hook::exec('addWebserviceResources', array('resources' => $resources), null, true, false);

        $resources = array_merge($resources, $hookResources);

        ksort($resources);
        return $resources;
    }
}
