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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class AddArticleSubscriber implements SubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Modules_Basket_AddArticle_Added' => 'onArticleAdded',
        ];
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     */
    public function onArticleAdded(\Enlight_Event_EventArgs $args)
    {
        /** @var string $basketId */
        $basketId = $args->get('id');

        /** @var array $product */
        $product = $this->getProductData($basketId);

        $oneoffCostsPrice = (float) $product['oneoff_costs_price'];

        if ($oneoffCostsPrice === 0.0) {
            return;
        }

        /** @var ContextServiceInterface $contextService */
        $contextService = $this->container->get('shopware_storefront.context_service');

        /** @var ProductContextInterface $context */
        $context = $contextService->getProductContext();

        $taxId = $product['oneoff_costs_tax'] ?: $product['taxID'];
        $tax = $context->getTaxRule($taxId);
        $taxRate = null !== $tax ? $tax->getTax() : $product['tax'];

        $oneoffCostsNet = (bool) $product['oneoff_costs_price_net'];

        if (!$oneoffCostsNet) {
            $oneoffCostsPrice = $oneoffCostsPrice / ($taxRate + 100) * 100;
        }

        $oneoffCostsPriceNet = $oneoffCostsPriceGross = $oneoffCostsPrice;

        /** @var PriceCalculatorInterface $priceCalculator */
        $priceCalculator = $this->container->get('shopware_storefront.price_calculator');
        $oneoffCostsPriceGross = $priceCalculator->calculatePrice($oneoffCostsPrice, $tax, $context);

        /** @var Session $session */
        $session = $this->container->get('session');

        $params = [
            'sessionID' => $session->get('sessionId'),
            'articlename' => (string) $product['oneoff_costs_label'],
            'articleID' => 0,
            'ordernumber' => (string) $product['oneoff_costs_ordernum'],
            'quantity' => 1,
            'price' => $oneoffCostsPriceGross,
            'netprice' => $oneoffCostsPriceNet,
            'tax_rate' => $taxRate,
            'datum' => date('Y-m-d H:i:s'),
            'modus' => 4,
            'currencyFactor' => $context->getCurrency()->getFactor(),
            'config' => $basketId,
        ];

        $this->insertOneoffCostsToBasket($params);
    }

    /**
     * @param int $basketId
     * @return array
     */
    protected function getProductData($basketId)
    {
        $db = $this->container->get('db');
        $orderNumber = $db->fetchOne('SELECT ordernumber FROM s_order_basket WHERE id = ?', $basketId);
        return Shopware()->Modules()->Articles()->sGetProductByOrdernumber($orderNumber);
    }

    /**
     * @param array $params
     */
    protected function insertOneoffCostsToBasket(array $params){
        $db = $this->container->get('db');
        $db->insert('s_order_basket', $params);
    }
}