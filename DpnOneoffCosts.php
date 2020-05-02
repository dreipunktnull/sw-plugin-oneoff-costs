<?php

namespace DpnOneoffCosts;

/**
 * Copyright notice
 *
 * (c) Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 *
 * All rights reserved
 */

use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Bundle\AttributeBundle\Service\TypeMapping;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;

class DpnOneoffCosts extends Plugin
{
    /**
     * @param InstallContext $content
     */
    public function install(InstallContext $context)
    {
        $this->createOrUpdateAttributes();
    }

    public function update(UpdateContext $context)
    {
        $this->createOrUpdateAttributes();
        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
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
            $crudService->delete('s_articles_attributes', 'oneoff_costs_price_net');
            $crudService->delete('s_articles_attributes', 'oneoff_costs_label');
            $crudService->delete('s_articles_attributes', 'oneoff_costs_tax');
            $crudService->delete('s_articles_attributes', 'oneoff_costs_ordernum');

            $this->container->get('models')->generateAttributeModels(['s_articles_attributes']);
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

    protected function createOrUpdateAttributes()
    {
        /** @var CrudService $service */
        $crudService = $this->container->get('shopware_attribute.crud_service');

        try {
            $crudService->update('s_articles_attributes', 'oneoff_costs_price', TypeMapping::TYPE_FLOAT, [
                'displayInBackend' => false,
                'position' => 300,
                'custom' => false,
                'translatable' => false,
                'defaultValue' => 0,
            ]);

            $crudService->update('s_articles_attributes', 'oneoff_costs_price_net', TypeMapping::TYPE_BOOLEAN, [
                'displayInBackend' => false,
                'allowBlank' => false,
                'position' => 301,
                'custom' => false,
                'translatable' => false,
                'defaultValue' => 0,
            ]);

            $crudService->update('s_articles_attributes', 'oneoff_costs_tax', TypeMapping::TYPE_SINGLE_SELECTION, [
                'displayInBackend' => false,
                'allowBlank' => true,
                'position' => 302,
                'custom' => false,
                'translatable' => false,
                'entity' => 'Shopware\Models\Tax\Tax',
            ]);

            $crudService->update('s_articles_attributes', 'oneoff_costs_label', TypeMapping::TYPE_STRING, [
                'displayInBackend' => false,
                'position' => 303,
                'custom' => false,
                'translatable' => true,
            ]);

            $crudService->update('s_articles_attributes', 'oneoff_costs_ordernum', TypeMapping::TYPE_STRING, [
                'displayInBackend' => false,
                'position' => 304,
                'custom' => false,
                'translatable' => false,
            ]);

            $this->container->get('models')->generateAttributeModels(['s_articles_attributes']);
        }
        catch (\Exception $e) {
        }
    }
}

