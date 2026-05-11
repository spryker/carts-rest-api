<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartsRestApi\Api\Storefront\Mapper;

use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsAttributesTransfer;
use Generated\Shared\Transfer\RestCartsDiscountsTransfer;
use Generated\Shared\Transfer\RestCartsTotalsTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Service\Container\Attributes\Plugins;

/**
 * Maps `QuoteTransfer` ↔ `RestCartsAttributesTransfer` for the API Platform flow.
 *
 * Reproduces the field-level translation that the legacy {@see \Spryker\Glue\CartsRestApi\Processor\Mapper\CartMapper}
 * performs and runs the same `RestCartAttributesMapperPluginInterface` plugin chain so any
 * project-level field expansion (e.g. shipping totals, custom totals) keeps working under
 * the API Platform stack.
 *
 * Lives under `Api/Storefront/Mapper/` so it is picked up by `ApiClassAutoDiscoveryPass`;
 * the legacy `CartMapper` itself sits outside that scan path and pulls in heavy legacy
 * Glue collaborators (`RestResourceBuilder`) we do not need here.
 */
class StorefrontCartMapper implements StorefrontCartMapperInterface
{
    /**
     * @param array<\Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\RestCartAttributesMapperPluginInterface> $restCartAttributesMapperPlugins
     */
    public function __construct(
        #[Plugins(dependencyProviderMethod: 'getRestCartAttributesMapperPlugins')]
        protected array $restCartAttributesMapperPlugins = [],
    ) {
    }

    public function mapQuoteTransferToRestCartsAttributesTransfer(QuoteTransfer $quoteTransfer): RestCartsAttributesTransfer
    {
        $restCartsAttributesTransfer = new RestCartsAttributesTransfer();

        $this->setBaseCartData($quoteTransfer, $restCartsAttributesTransfer);
        $this->setTotals($quoteTransfer, $restCartsAttributesTransfer);
        $this->setDiscounts($quoteTransfer, $restCartsAttributesTransfer);

        return $this->executeRestCartAttributesMapperPlugins($quoteTransfer, $restCartsAttributesTransfer);
    }

    public function mapRestCartsAttributesTransferToQuoteTransfer(
        RestCartsAttributesTransfer $restCartsAttributesTransfer,
        QuoteTransfer $quoteTransfer,
    ): QuoteTransfer {
        $currencyTransfer = (new CurrencyTransfer())->setCode($restCartsAttributesTransfer->getCurrency());
        $customerTransfer = (new CustomerTransfer())->setCustomerReference($quoteTransfer->getCustomerReference());
        $storeTransfer = (new StoreTransfer())->setName($restCartsAttributesTransfer->getStore());

        return $quoteTransfer
            ->fromArray($restCartsAttributesTransfer->toArray(), true)
            ->setCurrency($currencyTransfer)
            ->setCustomer($customerTransfer)
            ->setPriceMode($restCartsAttributesTransfer->getPriceMode())
            ->setStore($storeTransfer);
    }

    protected function executeRestCartAttributesMapperPlugins(
        QuoteTransfer $quoteTransfer,
        RestCartsAttributesTransfer $restCartsAttributesTransfer,
    ): RestCartsAttributesTransfer {
        foreach ($this->restCartAttributesMapperPlugins as $restCartAttributesMapperPlugin) {
            $restCartsAttributesTransfer = $restCartAttributesMapperPlugin->mapQuoteTransferToRestCartAttributesTransfer(
                $quoteTransfer,
                $restCartsAttributesTransfer,
            );
        }

        return $restCartsAttributesTransfer;
    }

    protected function setBaseCartData(
        QuoteTransfer $quoteTransfer,
        RestCartsAttributesTransfer $restCartsAttributesTransfer,
    ): void {
        $restCartsAttributesTransfer->fromArray($quoteTransfer->toArray(), true);

        $currencyTransfer = $quoteTransfer->getCurrency();
        $storeTransfer = $quoteTransfer->getStore();

        if ($currencyTransfer !== null) {
            $restCartsAttributesTransfer->setCurrency($currencyTransfer->getCode());
        }

        if ($storeTransfer !== null) {
            $restCartsAttributesTransfer->setStore($storeTransfer->getName());
        }
    }

    protected function setTotals(QuoteTransfer $quoteTransfer, RestCartsAttributesTransfer $restCartsAttributesTransfer): void
    {
        $totalsTransfer = $quoteTransfer->getTotals();

        if ($totalsTransfer === null) {
            $restCartsAttributesTransfer->setTotals(new RestCartsTotalsTransfer());

            return;
        }

        $cartsTotalsTransfer = (new RestCartsTotalsTransfer())->fromArray($totalsTransfer->toArray(), true);

        $taxTotalTransfer = $totalsTransfer->getTaxTotal();

        if ($taxTotalTransfer !== null) {
            $cartsTotalsTransfer->setTaxTotal($taxTotalTransfer->getAmount());
        }

        $restCartsAttributesTransfer->setTotals($cartsTotalsTransfer);
    }

    protected function setDiscounts(QuoteTransfer $quoteTransfer, RestCartsAttributesTransfer $restCartsAttributesTransfer): void
    {
        foreach ($quoteTransfer->getVoucherDiscounts() as $discountTransfer) {
            $restCartsDiscounts = (new RestCartsDiscountsTransfer())
                ->fromArray($discountTransfer->toArray(), true)
                ->setCode($discountTransfer->getVoucherCode());
            $restCartsAttributesTransfer->addDiscount($restCartsDiscounts);
        }

        foreach ($quoteTransfer->getCartRuleDiscounts() as $discountTransfer) {
            $restCartsDiscounts = (new RestCartsDiscountsTransfer())
                ->fromArray($discountTransfer->toArray(), true);
            $restCartsAttributesTransfer->addDiscount($restCartsDiscounts);
        }
    }
}
