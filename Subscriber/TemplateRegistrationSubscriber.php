<?php

namespace DpnOneoffCosts\Subscriber;

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
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Article' => 'onBackendArticlePostDispatch',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail' => 'onFrontendDetailPostDispatch',
        ];
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     */
    public function onBackendArticlePostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Backend_Article $controller */
        $controller = $args->getSubject();
        $controller->View()->addTemplateDir($this->pluginDirectory . '/Resources/views/');

        if ($controller->Request()->getActionName() === 'load') {
            $controller->View()->extendsTemplate('backend/dpn_oneoff_costs/article/controller/detail.js');
            $controller->View()->extendsTemplate('backend/dpn_oneoff_costs/article/view/detail/base.js');
        }
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