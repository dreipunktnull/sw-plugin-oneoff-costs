<?php

namespace DpnOneoffCosts\Service;

use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\PriceCalculatorInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ProductContextInterface;

/**
 * Copyright notice
 *
 * (c) Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 *
 * All rights reserved
 */
class PriceService
{
    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    /**
     * @var PriceCalculatorInterface
     */
    private $priceCalculator;

    public function __construct(ContextServiceInterface $contextService, PriceCalculatorInterface $priceCalculator)
    {
        $this->contextService = $contextService;
        $this->priceCalculator = $priceCalculator;
    }

    public function getNetPrice(float $price, bool $priceNet, int $taxId, int $defaultTaxRate)
    {
        /** @var ProductContextInterface $context */
        $context = $this->contextService->getProductContext();
        $tax = $context->getTaxRule($taxId);
        $taxRate = null !== $tax ? $tax->getTax() : $defaultTaxRate;

        if (!$priceNet) {
            $price = $price / ($taxRate + 100) * 100;
        }

        if ($context->getCurrentCustomerGroup()->insertedGrossPrices()) {
            $price = $this->priceCalculator->calculatePrice($oneoffCostsPrice, $tax, $context);
        }

    }
}