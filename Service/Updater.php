<?php

namespace DpnOneoffCosts\Service;

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
    }

    public function updateTo12()
    {
        $this->createAttributesFor12();
        $this->modelManager->generateAttributeModels(['s_articles_attributes']);
        $this->context->scheduleClearCache(UpdateContext::CACHE_LIST_DEFAULT);
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
}