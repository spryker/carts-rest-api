<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartsRestApi\Api\Storefront\Provider;

use Generated\Api\Storefront\CartsStorefrontResource;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\ApiPlatform\State\Provider\AbstractStorefrontProvider;
use Spryker\Client\CartsRestApi\CartsRestApiClientInterface;
use Spryker\Glue\CartsRestApi\Api\Storefront\Exception\CartsExceptionFactory;
use Spryker\Glue\CartsRestApi\Api\Storefront\Mapper\StorefrontCartMapperInterface;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Service\Serializer\SerializerServiceInterface;

class CartsStorefrontProvider extends AbstractStorefrontProvider
{
    protected const string KEY_UUID = 'uuid';

    public function __construct(
        protected CartsRestApiClientInterface $cartsRestApiClient,
        protected StorefrontCartMapperInterface $cartMapper,
        protected SerializerServiceInterface $serializer,
        protected CartsExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function provideItem(): ?object
    {
        $cartUuid = $this->getUriVariables()[static::KEY_UUID] ?? null;

        if (!is_string($cartUuid) || $cartUuid === '') {
            throw $this->exceptionFactory->createCartIdMissingException();
        }

        // A bearer-authenticated request hitting an authenticated cart endpoint must not also
        // carry the X-Anonymous-Customer-Unique-Id header — the client cannot act as both
        // logged-in and anonymous at once.
        if ($this->getRequest()->headers->has(CartsRestApiConfig::HEADER_ANONYMOUS_CUSTOMER_UNIQUE_ID)) {
            throw $this->exceptionFactory->createAnonymousWithAuthorizationException();
        }

        $quoteTransfer = (new QuoteTransfer())
            ->setUuid($cartUuid)
            ->setCustomerReference($this->getCustomerReference())
            ->setCustomer($this->getCustomer());

        $quoteResponseTransfer = $this->cartsRestApiClient->findQuoteByUuidWithQuoteItemReload($quoteTransfer);

        if (!$quoteResponseTransfer->getIsSuccessful()) {
            throw $this->exceptionFactory->createExceptionFromQuoteResponse(
                $quoteResponseTransfer,
                CartsRestApiConfig::RESPONSE_CODE_CART_NOT_FOUND,
                CartsRestApiConfig::EXCEPTION_MESSAGE_CART_WITH_ID_NOT_FOUND,
            );
        }

        return $this->mapQuoteTransferToResource($quoteResponseTransfer->getQuoteTransferOrFail());
    }

    /**
     * @return array<\Generated\Api\Storefront\CartsStorefrontResource>
     */
    protected function provideCollection(): array
    {
        $quoteCollectionTransfer = $this->cartsRestApiClient->getQuoteCollection(
            (new QuoteCriteriaFilterTransfer())->setCustomerReference($this->getCustomerReference()),
        );

        return $this->mapQuoteCollectionToResources($quoteCollectionTransfer);
    }

    /**
     * @return array<\Generated\Api\Storefront\CartsStorefrontResource>
     */
    protected function mapQuoteCollectionToResources(QuoteCollectionTransfer $quoteCollectionTransfer): array
    {
        $resources = [];

        foreach ($quoteCollectionTransfer->getQuotes() as $quoteTransfer) {
            $resources[] = $this->mapQuoteTransferToResource($quoteTransfer);
        }

        return $resources;
    }

    /**
     * Reuses the legacy {@see CartMapperInterface::mapQuoteTransferToRestCartsAttributesTransfer()}
     * which runs the registered `RestCartAttributesMapperPluginInterface` stack — keeps any
     * project-level field expansion intact in the API Platform flow.
     *
     * Carries the QuoteTransfer's `voucherDiscounts`, `cartRuleDiscounts` and `promotionItems`
     * onto the resource as internal (`readable: false`) arrays so the relationship resolvers
     * for `vouchers`, `cart-rules` and `promotional-items` can build sub-resources without
     * re-fetching the cart — same data flow as the legacy expander stack.
     */
    protected function mapQuoteTransferToResource(QuoteTransfer $quoteTransfer): CartsStorefrontResource
    {
        $restCartsAttributesTransfer = $this->cartMapper->mapQuoteTransferToRestCartsAttributesTransfer($quoteTransfer);

        $resource = $this->serializer->denormalize(
            ['uuid' => $quoteTransfer->getUuid()] + $restCartsAttributesTransfer->toArray(true, true),
            CartsStorefrontResource::class,
        );

        $resource->voucherDiscounts = iterator_to_array($quoteTransfer->getVoucherDiscounts());
        $resource->cartRuleDiscounts = iterator_to_array($quoteTransfer->getCartRuleDiscounts());
        $resource->promotionItems = iterator_to_array($quoteTransfer->getPromotionItems());
        $resource->giftCards = iterator_to_array($quoteTransfer->getGiftCards());
        $resource->shareDetails = iterator_to_array($quoteTransfer->getShareDetails());
        $resource->bundleItems = iterator_to_array($quoteTransfer->getBundleItems());
        $resource->items = iterator_to_array($quoteTransfer->getItems());

        return $resource;
    }
}
