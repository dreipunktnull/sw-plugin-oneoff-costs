<?php

namespace DpnOneoffCosts\Service;

use Shopware\Bundle\AttributeBundle\Service\CrudServiceInterface;
use Shopware\Bundle\AttributeBundle\Service\TypeMapping;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\Context\UpdateContext;

/**
 * Copyright notice
 *
 * (c) Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 *
 * All rights reserved
 */

class Updater
{
    /**
     * @var CrudServiceInterface
     */
    private $crudService;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var UpdateContext
     */
    private $context;

    public function __construct(CrudServiceInterface $crudService, ModelManager $modelManager, UpdateContext $context)
    {
        $this->crudService = $crudService;
        $this->modelManager = $modelManager;
        $this->context = $context;
    }

    public function update()
    {
        if (version_compare($this->context->getCurrentVersion(), '1.2', '<')) {
            $this->updateTo12();
        }
        if (version_compare($this->context->getCurrentVersion(), '1.2.1', '<=')) {
            $this->updateTo122();
        }
    }

    public function updateTo12()
    {
        $this->createAttributesFor12();
        $this->modelManager->generateAttributeModels(['s_articles_attributes']);
        $this->context->scheduleClearCache(UpdateContext::CACHE_LIST_DEFAULT);
        $this->context->scheduleMessage('Bitte das Backend neu laden. Please refresh the backed.');
    }

    public function createAttributesFor12()
    {
        try {
            $this->crudService->update('s_articles_attributes', 'oneoff_costs_price_net', TypeMapping::TYPE_BOOLEAN, [
                'displayInBackend' => false,
                'allowBlank' => false,
                'position' => 301,
                'custom' => false,
                'translatable' => false,
                'defaultValue' => 0,
            ]);
        }
        catch (\Exception $e) {
            $this->context->scheduleMessage($e->getMessage());
        }
    }

    public function updateTo122()
    {
        $this->createAttributesFor122();
        $this->modelManager->generateAttributeModels(['s_articles_attributes']);
        $this->context->scheduleClearCache(UpdateContext::CACHE_LIST_DEFAULT);
        $this->context->scheduleMessage('Bitte das Backend neu laden. Please refresh the backed.');
    }

    public function createAttributesFor122()
    {
        try {
            $this->crudService->update('s_articles_attributes', 'oneoff_costs_price', TypeMapping::TYPE_FLOAT, [
                'displayInBackend' => false,
                'position' => 300,
                'custom' => false,
                'translatable' => false,
            ]);

            $this->crudService->update('s_articles_attributes', 'oneoff_costs_tax', TypeMapping::TYPE_SINGLE_SELECTION, [
                'displayInBackend' => false,
                'allowBlank' => true,
                'position' => 302,
                'custom' => false,
                'translatable' => false,
                'entity' => 'Shopware\Models\Tax\Tax',
            ]);

            $this->crudService->update('s_articles_attributes', 'oneoff_costs_label', TypeMapping::TYPE_STRING, [
                'displayInBackend' => false,
                'position' => 303,
                'custom' => false,
                'translatable' => true,
            ]);

            $this->crudService->update('s_articles_attributes', 'oneoff_costs_ordernum', TypeMapping::TYPE_STRING, [
                'displayInBackend' => false,
                'position' => 304,
                'custom' => false,
                'translatable' => false,
            ]);
        }
        catch (\Exception $e) {
            $this->context->scheduleMessage($e->getMessage());
        }
    }
}