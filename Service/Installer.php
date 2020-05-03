<?php

namespace DpnOneoffCosts\Service;

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
use Shopware\Components\Plugin\Context\InstallContext;

class Installer
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
     * @var InstallContext
     */
    private $context;

    public function __construct(CrudServiceInterface $crudService, ModelManager $modelManager, InstallContext $context)
    {
        $this->crudService = $crudService;
        $this->modelManager = $modelManager;
        $this->context = $context;
    }

    public function install()
    {
        $this->createAttributes();
        $this->modelManager->generateAttributeModels(['s_articles_attributes']);
    }

    protected function createAttributes()
    {
        try {
            $this->crudService->update('s_articles_attributes', 'oneoff_costs_price', TypeMapping::TYPE_FLOAT, [
                'displayInBackend' => false,
                'position' => 300,
                'custom' => false,
                'translatable' => false,
                'defaultValue' => 0,
            ]);

            $this->crudService->update('s_articles_attributes', 'oneoff_costs_price_net', TypeMapping::TYPE_BOOLEAN, [
                'displayInBackend' => false,
                'allowBlank' => false,
                'position' => 301,
                'custom' => false,
                'translatable' => false,
                'defaultValue' => 0,
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