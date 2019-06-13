<?php

namespace DpnOneoffCosts;

/**
 * Copyright notice
 *
 * (c) Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 *
 * All rights reserved
 */

use Doctrine\Common\Cache\Cache;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Bundle\AttributeBundle\Service\TypeMapping;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;

class DpnOneoffCosts extends Plugin
{
    /**
     * @param InstallContext $content
     */
    public function install(InstallContext $content)
    {
        /** @var CrudService $service */
        $crudService = $this->container->get('shopware_attribute.crud_service');

        try {
            $crudService->update('s_articles_attributes', 'oneoff_costs_price', TypeMapping::TYPE_FLOAT, [
                'label' => 'Price',
                'helpText' => 'One-off costs to be added to this article independent from amount',
                'displayInBackend' => true,
                'position' => 300,
                'custom' => false,
                'translatable' => false,
                'defaultValue' => 0,
            ]);

            $crudService->update('s_articles_attributes', 'oneoff_costs_tax', TypeMapping::TYPE_INTEGER, [
                'label' => 'Tax',
                'helpText' => 'Tax to be applied to one-off costs',
                'displayInBackend' => true,
                'allowBlank' => true,
                'position' => 301,
                'custom' => false,
                'translatable' => false,
            ]);

            $crudService->update('s_articles_attributes', 'oneoff_costs_label', TypeMapping::TYPE_STRING, [
                'label' => 'Label',
                'helpText' => 'One-off costs label shown in basket',
                'displayInBackend' => true,
                'position' => 302,
                'custom' => false,
                'translatable' => true,
            ]);

            $crudService->update('s_articles_attributes', 'oneoff_costs_ordernum', TypeMapping::TYPE_STRING, [
                'label' => 'Order number',
                'helpText' => 'One-off costs order number',
                'displayInBackend' => true,
                'position' => 303,
                'custom' => false,
                'translatable' => false,
            ]);

            $this->updateMetadataCacheAndModels();
        }
        catch (\Exception $e) {
        }
    }

    /**
     * @param ActivateContext $context
     */
    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
    }

    /**
     * @param UninstallContext $context
     */
    public function uninstall(UninstallContext $context)
    {
        if ($context->keepUserData()) {
            return;
        }

        /** @var CrudService $crudService */
        $crudService = $this->container->get('shopware_attribute.crud_service');

        try {
            $crudService->delete('s_articles_attributes', 'oneoff_costs_price');
            $crudService->delete('s_articles_attributes', 'oneoff_costs_label');
            $crudService->delete('s_articles_attributes', 'oneoff_costs_tax');
            $crudService->delete('s_articles_attributes', 'oneoff_costs_ordernum');

            $this->updateMetadataCacheAndModels();
        }
        catch (\Exception $e) {
        }

        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
    }

    /**
     * @param DeactivateContext $context
     */
    public function deactivate(DeactivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
    }

    protected function updateMetadataCacheAndModels()
    {
        /** @var Cache $metaDataCache */
        $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
        Shopware()->Models()->generateAttributeModels(['s_articles_attributes']);
    }

}

