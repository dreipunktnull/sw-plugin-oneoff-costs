<?php

namespace DpnOneoffCosts\Subscriber;

use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExtendAddArticleSubscriber implements SubscriberInterface
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
        $db = $this->container->get('db');
        $session = $this->container->get('session');
        $context = $this->container->get('shopware_storefront.context_service')->getContext();
        $priceCalculator = $this->container->get('shopware_storefront.price_calculator');
        $basketId = $args->get('id');
        $orderNumber = $db->fetchOne('SELECT ordernumber FROM s_order_basket WHERE id = ?', $basketId);
        $product = Shopware()->Modules()->Articles()->sGetProductByOrdernumber($orderNumber);
        $oneoffCostsPrice = (float) $product['oneoff_costs_price'];
        $oneoffCostsLabel = $product['oneoff_costs_label'];
        if ($oneoffCostsPrice > 0) {
            $tax = $context->getTaxRule($product['taxID']);
            $params = [
                'sessionID' => $session->get('sessionId'),
                'articlename' => sprintf('%s %s', $oneoffCostsLabel, $product['articleName']),
                'articleID' => 0,
                'ordernumber' => '',
                'quantity' => 1,
                'price' => $priceCalculator->calculatePrice($oneoffCostsPrice, $tax, $context),
                'netprice' => $oneoffCostsPrice,
                'tax_rate' => $product['tax'],
                'datum' => date('Y-m-d H:i:s'),
                'modus' => 4,
                'currencyFactor' => $context->getCurrency()->getFactor(),
                'config' => $basketId,
            ];
            $db->insert('s_order_basket', $params);
        }
    }

}