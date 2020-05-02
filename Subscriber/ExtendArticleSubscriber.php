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
use Enlight_Event_EventArgs;
use Enlight_View_Default;

class ExtendArticleSubscriber implements SubscriberInterface
{
    /**
     * @var string
     */
    protected $pluginDirectory;

    /**
     * @param string $pluginDirectory
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
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Article' => 'onBackendArticlePostDispatch'
        ];
    }

    /**
     * @param Enlight_Event_EventArgs $args
     */
    public function onBackendArticlePostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var Enlight_View_Default $view */
        $view = $args->getSubject()->View();
        $view->addTemplateDir($this->pluginDirectory . '/Resources/views');

        if ($args->getRequest()->getActionName() === 'index') {
            $view->extendsTemplate('backend/dpn_oneoff_costs/article/app.js');
        }

        if ($args->getRequest()->getActionName() === 'load') {
            $view->extendsTemplate('backend/dpn_oneoff_costs/article/view/detail/window.js');
            $view->extendsTemplate('backend/dpn_oneoff_costs/article/controller/detail.js');
        }
    }
}