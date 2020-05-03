<?php

namespace DpnOneoffCosts\Service;

use Shopware\Bundle\AttributeBundle\Service\CrudServiceInterface;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\Context\UninstallContext;

class Uninstaller
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
     * @var UninstallContext
     */
    private $context;

    public function __construct(CrudServiceInterface $crudService, ModelManager $modelManager, UninstallContext $context)
    {
        $this->crudService = $crudService;
        $this->modelManager = $modelManager;
        $this->context = $context;
    }

    public function uninstall()
    {
        $attributes = [
            'oneoff_costs_price',
            'oneoff_costs_label',
            'oneoff_costs_tax',
            'oneoff_costs_ordernum'
        ];

        if ($this->context->assertMinimumVersion('1.2')) {
            $attributes[] = 'oneoff_costs_price_net';
        }

        try {
            foreach ($attributes as $attribute) {
                $this->crudService->delete('s_articles_attributes', $attribute);
            }
            $this->modelManager->generateAttributeModels(['s_articles_attributes']);
        }
        catch (\Exception $e) {
            $this->context->scheduleMessage($e->getMessage());
        }
    }
}