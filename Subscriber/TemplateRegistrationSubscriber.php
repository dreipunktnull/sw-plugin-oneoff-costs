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
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\PriceCalculatorInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ProductContextInterface;

class TemplateRegistrationSubscriber implements SubscriberInterface
{
    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    /**
     * @var PriceCalculatorInterface
     */
    private $priceCalculator;

    /**
     * @var string
     */
    protected $pluginDirectory;

    public function __construct(ContextServiceInterface $contextService, PriceCalculatorInterface $priceCalculator, $pluginDirectory)
    {
        $this->contextService = $contextService;
        $this->priceCalculator = $priceCalculator;
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
        /** @var \Shopware_Controllers_Frontend_Detail $controller */
        $controller = $args->getSubject();
        /** @var \Enlight_View_Default $view */
        $view = $controller->View();

        $view->addTemplateDir($this->pluginDirectory . '/Resources/views/');
        $view->extendsTemplate('frontend/dpn_oneoff_costs/detail/data.tpl');

        $article = $view->getAssign('sArticle');
        $oneoffCostsPrice = $article['oneoff_costs_price'];
        $oneoffCostsNet = (bool) $article['oneoff_costs_price_net'];
        $oneoffCostsTaxId = $article['oneoff_costs_tax'] ?: $article['taxID'];

        /** @var ProductContextInterface $context */
        $context = $this->contextService->getProductContext();
        $tax = $context->getTaxRule($oneoffCostsTaxId);
        $taxRate = null !== $tax ? $tax->getTax() : $article['tax'];

        if (!$oneoffCostsNet) {
            $oneoffCostsPrice = $oneoffCostsPrice / ($taxRate + 100) * 100;
        }

        $oneoffCostsPrice = $this->priceCalculator->calculatePrice($oneoffCostsPrice, $tax, $context);

        $view->assign('oneoffCostsPrice', $oneoffCostsPrice);
    }
}