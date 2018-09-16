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
            'label' => 'Oneoff costs price',
            'helpText' => 'Oneoff costs to be added to this article independent from amount',
            'displayInBackend' => true,
            'position' => 10,
            'custom' => false,
            'translatable' => false,
            'defaultValue' => 0,
        ]);

        $crudService->update('s_articles_attributes', 'oneoff_costs_label', 'string', [
            'label' => 'Oneoff costs label',
            'helpText' => 'Oneoff costs label shown in basket',
            'displayInBackend' => true,
            'position' => 11,
            'custom' => false,
            'translatable' => true,
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
