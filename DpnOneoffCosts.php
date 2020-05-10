<?php

namespace DpnOneoffCosts;

/**
 * Copyright notice
 *
 * (c) Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 *
 * All rights reserved
 */

use Shopware\Bundle\AttributeBundle\Service\CrudServiceInterface;
use Shopware\Bundle\AttributeBundle\Service\TypeMapping;
use Shopware\Components\Model\ModelManager;
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
        try {
            $this->createOrUpdateAttributes();
        }
        catch (\Exception $e) {
            $context->scheduleMessage($e->getMessage());
        }
        /** @var ModelManager $modelManager */
        $modelManager = $this->container->get('models');
        $modelManager->generateAttributeModels(['s_articles_attributes']);
    }

    public function update(UpdateContext $context)
    {
        try {
            $this->createOrUpdateAttributes();
        }
        catch (\Exception $e) {
            $context->scheduleMessage($e->getMessage());
        }
        /** @var ModelManager $modelManager */
        $modelManager = $this->container->get('models');
        $modelManager->generateAttributeModels(['s_articles_attributes']);
        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
    }

    /**
     * @param ActivateContext $context
     */
    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(ActivateContext::CACHE_LIST_DEFAULT);
    }

    /**
     * @param UninstallContext $context
     */
    public function uninstall(UninstallContext $context)
    {
        if (!$context->keepUserData()) {
            $this->removeAttributes($context);
        }
        $context->scheduleClearCache(UninstallContext::CACHE_LIST_DEFAULT);
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
        /** @var CrudServiceInterface $crudService */
        $crudService = $this->container->get('shopware_attribute.crud_service');

        $crudService->update(
            's_articles_attributes',
            'oneoff_costs_price',
            TypeMapping::TYPE_FLOAT,
            [
                'displayInBackend' => true,
                'position' => 300,
                'custom' => false,
                'translatable' => false,
                'label' => 'Price',
                'helpText' => 'One-off costs to be added to this article independent from amount',
            ]
        );

        $crudService->update(
            's_articles_attributes',
            'oneoff_costs_price_net',
            TypeMapping::TYPE_BOOLEAN,
            [
                'displayInBackend' => true,
                'position' => 301,
                'custom' => false,
                'translatable' => false,
                'label' => 'Price net',
                'helpText' => 'One-off costs price is entered net',
            ]
        );

        $crudService->update(
            's_articles_attributes',
            'oneoff_costs_tax',
            TypeMapping::TYPE_SINGLE_SELECTION,
            [
                'displayInBackend' => true,
                'allowBlank' => true,
                'position' => 302,
                'custom' => false,
                'translatable' => false,
                'entity' => 'Shopware\Models\Tax\Tax',
                'label' => 'Tax',
                'helpText' => 'Tax to be applied to one-off costs (article\'s tax rule is used by default)',
            ]
        );

        $crudService->update(
            's_articles_attributes',
            'oneoff_costs_label',
            TypeMapping::TYPE_STRING,
            [
                'displayInBackend' => true,
                'position' => 303,
                'custom' => false,
                'translatable' => true,
                'label' => 'Label',
                'helpText' => 'One-off costs label shown in basket',
            ]
        );

        $crudService->update(
            's_articles_attributes',
            'oneoff_costs_ordernum',
            TypeMapping::TYPE_STRING,
            [
                'displayInBackend' => true,
                'position' => 304,
                'custom' => false,
                'translatable' => false,
                'label' => 'Order number',
                'helpText' => 'One-off costs order number',
            ]
        );
    }

    /**
     * @param UninstallContext $context
     */
    protected function removeAttributes(UninstallContext $context)
    {
        /** @var CrudServiceInterface $crudService */
        $crudService = $this->container->get('shopware_attribute.crud_service');

        $attributes = [
            'oneoff_costs_price',
            'oneoff_costs_label',
            'oneoff_costs_tax',
            'oneoff_costs_ordernum'
        ];

        if ($context->assertMinimumVersion('1.2')) {
            $attributes[] = 'oneoff_costs_price_net';
        }

        try {
            foreach ($attributes as $attribute) {
                $crudService->delete('s_articles_attributes', $attribute);
            }
        }
        catch (\Exception $e) {
            $context->scheduleMessage($e->getMessage());
        }
        /** @var ModelManager $modelManager */
        $modelManager = $this->container->get('models');
        $modelManager->generateAttributeModels(['s_articles_attributes']);
    }
}

