<?php

namespace DpnOneoffCosts\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContext;
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
        /** @var Session $session */
        $session = $this->container->get('session');
        /** @var ShopContext $context */
        $context = $this->container->get('shopware_storefront.context_service')->getContext();
        /** @var string $basketId */
        $basketId = $args->get('id');
        /** @var array $product */
        $product = $this->getProductData($basketId);

        $oneoffCostsPrice = (float) $product['oneoff_costs_price'];

        if ($oneoffCostsPrice === 0.0) {
            return;
        }

        // Use tax rule from product
        $tax = $context->getTaxRule($product['taxID']);

        // Convert gross price if applicable for current customer group
        $customerGroup = $context->getCurrentCustomerGroup();
        if ($customerGroup->insertedGrossPrices()) {
            $oneoffCostsPriceGross = $oneoffCostsPrice;
            $oneoffCostsPriceNet = $oneoffCostsPrice / (100 + $tax->getTax()) * 100;
        } else {
            $priceCalculator = $this->container->get('shopware_storefront.price_calculator');
            $oneoffCostsPriceGross = $priceCalculator->calculatePrice($oneoffCostsPrice, $tax, $context);
            $oneoffCostsPriceNet = $oneoffCostsPrice;
        }

        $params = [
            'sessionID' => $session->get('sessionId'),
            'articlename' => $product['oneoff_costs_label'],
            'articleID' => 0,
            'ordernumber' => '',
            'quantity' => 1,
            'price' => $oneoffCostsPriceGross,
            'netprice' => $oneoffCostsPriceNet,
            'tax_rate' => $product['tax'],
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