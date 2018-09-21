<?php

namespace DpnOneoffCosts;

use Doctrine\Common\Cache\Cache;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;

class DpnOneoffCosts extends Plugin
{
    /**
     * @param ActivateContext $context
     */
    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
    }

    /**
     * @param InstallContext $content
     *
     * @return bool
     */
    public function install(InstallContext $content)
    {
        /** @var CrudService $service */
        $crudService = $this->container->get('shopware_attribute.crud_service');

        $crudService->update('s_articles_attributes', 'oneoff_costs_price', 'float', [
            'label' => 'Price',
            'helpText' => 'One-off costs to be added to this article independent from amount',
            'displayInBackend' => true,
            'position' => 300,
            'custom' => false,
            'translatable' => false,
            'defaultValue' => 0,
        ]);

        $crudService->update('s_articles_attributes', 'oneoff_costs_tax', 'int', [
            'label' => 'Tax',
            'helpText' => 'Tax to be applied to one-off costs',
            'displayInBackend' => true,
            'allowBlank' => true,
            'position' => 301,
            'custom' => false,
            'translatable' => false,
        ]);

        $crudService->update('s_articles_attributes', 'oneoff_costs_label', 'string', [
            'label' => 'Label',
            'helpText' => 'One-off costs label shown in basket',
            'displayInBackend' => true,
            'position' => 302,
            'custom' => false,
            'translatable' => true,
        ]);

        $crudService->update('s_articles_attributes', 'oneoff_costs_ordernum', 'string', [
            'label' => 'Order number',
            'helpText' => 'One-off costs order number',
            'displayInBackend' => true,
            'position' => 303,
            'custom' => false,
            'translatable' => false,
        ]);

        $this->updateMetadataCacheAndModels();

        return true;
    }

    /**
     * @param UninstallContext $context
     *
     * @return bool
     */
    public function uninstall(UninstallContext $context)
    {
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

        return true;
    }

    protected function updateMetadataCacheAndModels()
    {
        /** @var Cache $metaDataCache */
        $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
        Shopware()->Models()->generateAttributeModels(['s_articles_attributes']);
    }

}

