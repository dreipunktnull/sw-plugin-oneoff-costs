<?php

namespace DpnOneoffCosts;

/**
 * Copyright notice
 *
 * (c) Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 *
 * All rights reserved
 */

use DpnOneoffCosts\Service\Installer;
use DpnOneoffCosts\Service\Uninstaller;
use DpnOneoffCosts\Service\Updater;
use Shopware\Bundle\AttributeBundle\Service\CrudServiceInterface;
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
        /** @var CrudServiceInterface $crudService */
        $crudService = $this->container->get('shopware_attribute.crud_service');
        /** @var ModelManager $modelManager */
        $modelManager = $this->container->get('models');
        $installer = new Installer($crudService, $modelManager, $context);
        $installer->install();
    }

    public function update(UpdateContext $context)
    {
        /** @var CrudServiceInterface $crudService */
        $crudService = $this->container->get('shopware_attribute.crud_service');
        /** @var ModelManager $modelManager */
        $modelManager = $this->container->get('models');
        $updater = new Updater($crudService, $modelManager, $context);
        $updater->update();
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
        if (!$context->keepUserData()) {
            /** @var CrudServiceInterface $crudService */
            $crudService = $this->container->get('shopware_attribute.crud_service');
            /** @var ModelManager $modelManager */
            $modelManager = $this->container->get('models');
            $uninstaller = new Uninstaller($crudService, $modelManager, $context);
            $uninstaller->uninstall();
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
}

