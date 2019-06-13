<?php

namespace DpnOneoffCosts\Subscriber;

/**
 * Copyright notice
 *
 * (c) Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 *
 * All rights reserved
 */

use Enlight\Event\SubscriberInterface;

class TemplateRegistrationSubscriber implements SubscriberInterface
{

    /**
     * @var string
     */
    protected $pluginDirectory;

    /**
     * @param $pluginDirectory
     */
    public function __construct($pluginDirectory)
    {
        $this->pluginDirectory = $pluginDirectory;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail' => 'onFrontendDetailPostDispatch',
        ];
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     */
    public function onFrontendDetailPostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Backend_Article $controller */
        $controller = $args->getSubject();
        $controller->View()->addTemplateDir($this->pluginDirectory . '/Resources/views/');
        $controller->View()->extendsTemplate('frontend/dpn_oneoff_costs/detail/data.tpl');
    }

}