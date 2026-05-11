<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartsRestApi\Api\Storefront\Provider;

use Generated\Api\Storefront\GuestCartsStorefrontResource;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\ApiPlatform\State\Provider\AbstractStorefrontProvider;
use Spryker\Client\CartsRestApi\CartsRestApiClientInterface;
use Spryker\Glue\CartsRestApi\Api\Storefront\Exception\CartsExceptionFactory;
use Spryker\Glue\CartsRestApi\Api\Storefront\Mapper\StorefrontCartMapperInterface;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Service\Serializer\SerializerServiceInterface;

class GuestCartsStorefrontProvider extends AbstractStorefrontProvider
{
    protected const string KEY_UUID = 'uuid';

    /**
     * @uses \Spryker\Shared\PersistentCart\PersistentCartConfig::PERSISTENT_CART_ANONYMOUS_PREFIX
     */
    protected const string ANONYMOUS_CUSTOMER_REFERENCE_PREFIX = 'anonymous:';

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

        $anonymousCustomerReference = $this->getAnonymousCustomerReference();

        $quoteTransfer = (new QuoteTransfer())
            ->setUuid($cartUuid)
            ->setCustomerReference($anonymousCustomerReference)
            ->setCustomer((new CustomerTransfer())->setCustomerReference($anonymousCustomerReference));

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
     * @return array<\Generated\Api\Storefront\GuestCartsStorefrontResource>
     */
    protected function provideCollection(): array
    {
        $anonymousCustomerReference = $this->getAnonymousCustomerReference();

        $quoteCollectionTransfer = $this->cartsRestApiClient->getQuoteCollection(
            (new QuoteCriteriaFilterTransfer())->setCustomerReference($anonymousCustomerReference),
        );

        $resources = [];

        foreach ($quoteCollectionTransfer->getQuotes() as $quoteTransfer) {
            $resources[] = $this->mapQuoteTransferToResource($quoteTransfer);
        }

        return $resources;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function getAnonymousCustomerReference(): string
    {
        $anonymousCustomerUniqueId = $this->getRequest()->headers->get(
            CartsRestApiConfig::HEADER_ANONYMOUS_CUSTOMER_UNIQUE_ID,
        );

        if ($anonymousCustomerUniqueId === null || $anonymousCustomerUniqueId === '') {
            throw $this->exceptionFactory->createAnonymousCustomerUniqueIdEmptyException();
        }

        return static::ANONYMOUS_CUSTOMER_REFERENCE_PREFIX . $anonymousCustomerUniqueId;
    }

    /**
     * Carries the QuoteTransfer's `voucherDiscounts`, `cartRuleDiscounts` and `promotionItems`
     * onto the resource as internal (`readable: false`) arrays so the relationship resolvers
     * for `vouchers`, `cart-rules` and `promotional-items` can build sub-resources without
     * re-fetching the cart — same data flow as the legacy expander stack.
     */
    protected function mapQuoteTransferToResource(QuoteTransfer $quoteTransfer): GuestCartsStorefrontResource
    {
        $restCartsAttributesTransfer = $this->cartMapper->mapQuoteTransferToRestCartsAttributesTransfer($quoteTransfer);

        $resource = $this->serializer->denormalize(
            ['uuid' => $quoteTransfer->getUuid()] + $restCartsAttributesTransfer->toArray(true, true),
            GuestCartsStorefrontResource::class,
        );

        $resource->voucherDiscounts = iterator_to_array($quoteTransfer->getVoucherDiscounts());
        $resource->cartRuleDiscounts = iterator_to_array($quoteTransfer->getCartRuleDiscounts());
        $resource->promotionItems = iterator_to_array($quoteTransfer->getPromotionItems());
        $resource->giftCards = iterator_to_array($quoteTransfer->getGiftCards());
        $resource->bundleItems = iterator_to_array($quoteTransfer->getBundleItems());
        $resource->items = iterator_to_array($quoteTransfer->getItems());

        return $resource;
    }
}
