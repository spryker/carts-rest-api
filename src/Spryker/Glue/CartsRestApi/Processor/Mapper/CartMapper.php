<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi\Processor\Mapper;

use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsAttributesTransfer;
use Generated\Shared\Transfer\RestCartsDiscountsTransfer;
use Generated\Shared\Transfer\RestCartsTotalsTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Symfony\Component\HttpFoundation\Response;

class CartMapper implements CartMapperInterface
{
    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilder;

    /**
     * @var \Spryker\Glue\CartsRestApi\CartsRestApiConfig
     */
    protected $config;

    /**
     * @var array<\Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\RestCartAttributesMapperPluginInterface>
     */
    protected $restCartAttributesMapperPlugins;

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     * @param \Spryker\Glue\CartsRestApi\CartsRestApiConfig $config
     * @param array<\Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\RestCartAttributesMapperPluginInterface> $restCartAttributesMapperPlugins
     */
    public function __construct(
        RestResourceBuilderInterface $restResourceBuilder,
        CartsRestApiConfig $config,
        array $restCartAttributesMapperPlugins
    ) {
        $this->config = $config;
        $this->restResourceBuilder = $restResourceBuilder;
        $this->restCartAttributesMapperPlugins = $restCartAttributesMapperPlugins;
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
        QuoteTransfer $quoteTransfer
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

    public function mapRestRequestToQuoteTransfer(RestRequestInterface $restRequest, QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $restUserTransfer = $restRequest->getRestUser();

        $customerTransfer = (new CustomerTransfer())
            ->setIdCustomer($restUserTransfer->getSurrogateIdentifier())
            ->setCustomerReference($restUserTransfer->getNaturalIdentifier());

        return $quoteTransfer
            ->setCustomerReference($restUserTransfer->getNaturalIdentifier())
            ->setCustomer($customerTransfer)
            ->setUuid($restRequest->getResource()->getId());
    }

    public function mapQuoteErrorTransferToRestErrorMessageTransfer(
        QuoteErrorTransfer $quoteErrorTransfer,
        RestErrorMessageTransfer $restErrorMessageTransfer
    ): RestErrorMessageTransfer {
        $errorIdentifier = $quoteErrorTransfer->getErrorIdentifier();
        $errorIdentifierToRestErrorMapping = $this->config->getErrorIdentifierToRestErrorMapping();
        if ($errorIdentifier && isset($errorIdentifierToRestErrorMapping[$errorIdentifier])) {
            $errorIdentifierMapping = $errorIdentifierToRestErrorMapping[$errorIdentifier];
            $restErrorMessageTransfer->fromArray($errorIdentifierMapping, true);

            return $restErrorMessageTransfer;
        }

        if ($quoteErrorTransfer->getMessage()) {
            return $this->createErrorMessageTransfer($quoteErrorTransfer);
        }

        return $restErrorMessageTransfer;
    }

    protected function executeRestCartAttributesMapperPlugins(
        QuoteTransfer $quoteTransfer,
        RestCartsAttributesTransfer $restCartsAttributesTransfer
    ): RestCartsAttributesTransfer {
        foreach ($this->restCartAttributesMapperPlugins as $restCartAttributesMapperPlugin) {
            $restCartsAttributesTransfer = $restCartAttributesMapperPlugin->mapQuoteTransferToRestCartAttributesTransfer(
                $quoteTransfer,
                $restCartsAttributesTransfer,
            );
        }

        return $restCartsAttributesTransfer;
    }

    protected function createErrorMessageTransfer(QuoteErrorTransfer $quoteErrorTransfer): RestErrorMessageTransfer
    {
        return (new RestErrorMessageTransfer())
            ->setCode(CartsRestApiConfig::RESPONSE_CODE_ITEM_VALIDATION)
            ->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->setDetail($quoteErrorTransfer->getMessage());
    }

    protected function setDiscounts(QuoteTransfer $quoteTransfer, RestCartsAttributesTransfer $restCartsAttributesTransfer): void
    {
        foreach ($quoteTransfer->getVoucherDiscounts() as $discountTransfer) {
            $restCartsDiscounts = new RestCartsDiscountsTransfer();
            $restCartsDiscounts->fromArray($discountTransfer->toArray(), true);
            $restCartsDiscounts->setCode($discountTransfer->getVoucherCode());
            $restCartsAttributesTransfer->addDiscount($restCartsDiscounts);
        }

        foreach ($quoteTransfer->getCartRuleDiscounts() as $discountTransfer) {
            $restCartsDiscounts = new RestCartsDiscountsTransfer();
            $restCartsDiscounts->fromArray($discountTransfer->toArray(), true);
            $restCartsAttributesTransfer->addDiscount($restCartsDiscounts);
        }
    }

    protected function setTotals(QuoteTransfer $quoteTransfer, RestCartsAttributesTransfer $restCartsAttributesTransfer): void
    {
        if ($quoteTransfer->getTotals() === null) {
            $restCartsAttributesTransfer->setTotals(new RestCartsTotalsTransfer());

            return;
        }

        $cartsTotalsTransfer = (new RestCartsTotalsTransfer())
            ->fromArray($quoteTransfer->getTotals()->toArray(), true);

        $taxTotalTransfer = $quoteTransfer->getTotals()->getTaxTotal();
        if ($taxTotalTransfer) {
            $cartsTotalsTransfer->setTaxTotal($taxTotalTransfer->getAmount());
        }

        $restCartsAttributesTransfer->setTotals($cartsTotalsTransfer);
    }

    protected function setBaseCartData(
        QuoteTransfer $quoteTransfer,
        RestCartsAttributesTransfer $restCartsAttributesTransfer
    ): void {
        $restCartsAttributesTransfer->fromArray($quoteTransfer->toArray(), true);

        $restCartsAttributesTransfer
            ->setCurrency($quoteTransfer->getCurrency()->getCode())
            ->setStore($quoteTransfer->getStore()->getName());
    }
}
