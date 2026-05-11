<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartsRestApi\Api\Storefront\Processor;

use Generated\Api\Storefront\GuestCartItemsStorefrontResource;
use Generated\Api\Storefront\GuestCartsStorefrontResource;
use Generated\Shared\Transfer\CartItemRequestTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemsAttributesTransfer;
use Spryker\ApiPlatform\State\Processor\AbstractStorefrontProcessor;
use Spryker\Client\CartsRestApi\CartsRestApiClientInterface;
use Spryker\Glue\CartsRestApi\Api\Storefront\Exception\CartsExceptionFactory;
use Spryker\Glue\CartsRestApi\Api\Storefront\Mapper\StorefrontCartItemMapperInterface;
use Spryker\Glue\CartsRestApi\Api\Storefront\Mapper\StorefrontCartMapperInterface;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Service\Container\Attributes\Plugins;
use Spryker\Service\Serializer\SerializerServiceInterface;

class GuestCartItemsStorefrontProcessor extends AbstractStorefrontProcessor
{
    protected const string KEY_CART_ID = 'cartId';

    protected const string KEY_GROUP_KEY = 'groupKey';

    protected const string RELATIONSHIP_NAME_GUEST_CART_ITEMS = 'guest-cart-items';

    /**
     * @uses \Spryker\Shared\PersistentCart\PersistentCartConfig::PERSISTENT_CART_ANONYMOUS_PREFIX
     */
    protected const string ANONYMOUS_CUSTOMER_REFERENCE_PREFIX = 'anonymous:';

    /**
     * @param array<\Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\CartItemExpanderPluginInterface> $cartItemExpanderPlugins
     * @param array<\Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\CartItemFilterPluginInterface> $cartItemFilterPlugins
     */
    public function __construct(
        protected CartsRestApiClientInterface $cartsRestApiClient,
        protected StorefrontCartMapperInterface $cartMapper,
        protected StorefrontCartItemMapperInterface $cartItemMapper,
        protected SerializerServiceInterface $serializer,
        protected CartsExceptionFactory $exceptionFactory,
        #[Plugins(dependencyProviderMethod: 'getCartItemExpanderPlugins')]
        protected array $cartItemExpanderPlugins = [],
        #[Plugins(dependencyProviderMethod: 'getCartItemFilterPlugins')]
        protected array $cartItemFilterPlugins = [],
    ) {
    }

    /**
     * Handles both `POST /guest-cart-items` (auto-creates the guest cart if none exists) and
     * `POST /guest-carts/{cartId}/guest-cart-items` (adds to an existing cart) — Zed-side
     * `addToGuestCart()` accepts an empty `quoteUuid` and provisions a new guest cart on demand.
     *
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processPost(mixed $data): mixed
    {
        $cartUuid = $this->getUriVariables()[static::KEY_CART_ID] ?? null;
        $anonymousCustomerReference = $this->getAnonymousCustomerReference();
        $restCartItemsAttributesTransfer = $this->buildRestCartItemsAttributesTransfer($data);

        $cartItemRequestTransfer = (new CartItemRequestTransfer())
            ->setSku($restCartItemsAttributesTransfer->getSku())
            ->setQuantity($restCartItemsAttributesTransfer->getQuantity())
            ->setCustomer((new CustomerTransfer())->setCustomerReference($anonymousCustomerReference));

        if (is_string($cartUuid) && $cartUuid !== '') {
            $cartItemRequestTransfer->setQuoteUuid($cartUuid);
        }

        $cartItemRequestTransfer = $this->executeCartItemExpanderPlugins(
            $cartItemRequestTransfer,
            $restCartItemsAttributesTransfer,
        );

        $quoteResponseTransfer = $this->cartsRestApiClient->addToGuestCart($cartItemRequestTransfer);

        if (!$quoteResponseTransfer->getIsSuccessful()) {
            throw $this->exceptionFactory->createExceptionFromQuoteResponse(
                $quoteResponseTransfer,
                CartsRestApiConfig::RESPONSE_CODE_FAILED_ADDING_CART_ITEM,
                CartsRestApiConfig::EXCEPTION_MESSAGE_FAILED_ADDING_CART_ITEM,
            );
        }

        return $this->mapQuoteTransferToGuestCartsResource($quoteResponseTransfer->getQuoteTransferOrFail());
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processPatch(mixed $data): mixed
    {
        $cartUuid = $this->resolveCartUuid();
        $groupKey = $this->resolveGroupKey();
        $anonymousCustomerReference = $this->getAnonymousCustomerReference();
        $restCartItemsAttributesTransfer = $this->buildRestCartItemsAttributesTransfer($data);

        $cartItemRequestTransfer = (new CartItemRequestTransfer())
            ->setQuoteUuid($cartUuid)
            ->setQuantity($restCartItemsAttributesTransfer->getQuantity())
            ->setSku($groupKey)
            ->setGroupKey($groupKey)
            ->setCustomer((new CustomerTransfer())->setCustomerReference($anonymousCustomerReference));

        $quoteResponseTransfer = $this->cartsRestApiClient->updateItemQuantity($cartItemRequestTransfer);

        if (!$quoteResponseTransfer->getIsSuccessful()) {
            throw $this->exceptionFactory->createExceptionFromQuoteResponse(
                $quoteResponseTransfer,
                CartsRestApiConfig::RESPONSE_CODE_FAILED_UPDATING_CART_ITEM,
                CartsRestApiConfig::EXCEPTION_MESSAGE_FAILED_UPDATING_CART_ITEM,
            );
        }

        return $this->mapQuoteTransferToGuestCartsResource($quoteResponseTransfer->getQuoteTransferOrFail());
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processDelete(): mixed
    {
        $cartUuid = $this->resolveCartUuid();
        $groupKey = $this->resolveGroupKey();
        $anonymousCustomerReference = $this->getAnonymousCustomerReference();

        $cartItemRequestTransfer = (new CartItemRequestTransfer())
            ->setQuoteUuid($cartUuid)
            ->setSku($groupKey)
            ->setGroupKey($groupKey)
            ->setCustomer((new CustomerTransfer())->setCustomerReference($anonymousCustomerReference));

        $quoteResponseTransfer = $this->cartsRestApiClient->removeItem($cartItemRequestTransfer);

        if (!$quoteResponseTransfer->getIsSuccessful()) {
            throw $this->exceptionFactory->createExceptionFromQuoteResponse(
                $quoteResponseTransfer,
                CartsRestApiConfig::RESPONSE_CODE_FAILED_DELETING_CART_ITEM,
                CartsRestApiConfig::EXCEPTION_MESSAGE_FAILED_DELETING_CART_ITEM,
            );
        }

        return null;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function resolveCartUuid(): string
    {
        $cartUuid = $this->getUriVariables()[static::KEY_CART_ID] ?? null;

        if (!is_string($cartUuid) || $cartUuid === '') {
            throw $this->exceptionFactory->createCartIdMissingException();
        }

        return $cartUuid;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function resolveGroupKey(): string
    {
        $groupKey = $this->getUriVariables()[static::KEY_GROUP_KEY] ?? null;

        if (!is_string($groupKey) || $groupKey === '') {
            throw $this->exceptionFactory->createCartIdMissingException();
        }

        return $groupKey;
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
     * Runs the registered {@see \Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\CartItemExpanderPluginInterface}
     * stack against the prepared `CartItemRequestTransfer` — same plugin chain the legacy
     * `GuestCartItemAdder` uses, so per-product feature integrations (product configurations,
     * sales units, product options, merchant product offers, …) keep populating their
     * payloads under API Platform.
     */
    protected function executeCartItemExpanderPlugins(
        CartItemRequestTransfer $cartItemRequestTransfer,
        RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer,
    ): CartItemRequestTransfer {
        foreach ($this->cartItemExpanderPlugins as $cartItemExpanderPlugin) {
            $cartItemRequestTransfer = $cartItemExpanderPlugin->expand(
                $cartItemRequestTransfer,
                $restCartItemsAttributesTransfer,
            );
        }

        return $cartItemRequestTransfer;
    }

    protected function buildRestCartItemsAttributesTransfer(mixed $data): RestCartItemsAttributesTransfer
    {
        if ($data instanceof RestCartItemsAttributesTransfer) {
            return $data;
        }

        $payload = $data instanceof GuestCartItemsStorefrontResource
            ? get_object_vars($data)
            : (array)$data;

        return (new RestCartItemsAttributesTransfer())->fromArray(
            array_filter($payload, static fn ($value): bool => $value !== null),
            true,
        );
    }

    protected function mapQuoteTransferToGuestCartsResource(QuoteTransfer $quoteTransfer): GuestCartsStorefrontResource
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

        $this->preResolveGuestCartItemsRelationship($quoteTransfer);

        return $resource;
    }

    /**
     * Pre-populates the `guest-cart-items` relationship on the current request so that the
     * JSON:API response always includes them under `included`, regardless of the `?include=`
     * query parameter — same eager-include behavior as the legacy
     * {@see \Spryker\Glue\CartsRestApi\Plugin\GlueApplication\GuestCartItemsByQuoteResourceRelationshipPlugin}.
     */
    protected function preResolveGuestCartItemsRelationship(QuoteTransfer $quoteTransfer): void
    {
        if (!$this->hasRequest()) {
            return;
        }

        $localeName = $this->hasLocale() ? $this->getLocale()->getLocaleNameOrFail() : '';
        $itemTransfers = $this->filterItemTransfers(iterator_to_array($quoteTransfer->getItems()), $quoteTransfer);
        $itemResources = [];

        foreach ($itemTransfers as $itemTransfer) {
            $itemResources[] = $this->mapItemToResource($itemTransfer, $localeName);
        }

        $this->setResolvedRelationships($this->getRequest(), static::RELATIONSHIP_NAME_GUEST_CART_ITEMS, $itemResources);
    }

    protected function mapItemToResource(
        ItemTransfer $itemTransfer,
        string $localeName,
    ): GuestCartItemsStorefrontResource {
        $restItemsAttributesTransfer = $this->cartItemMapper->mapItemTransferToRestItemsAttributesTransfer(
            $itemTransfer,
            $localeName,
        );

        return $this->serializer->denormalize(
            $restItemsAttributesTransfer->toArray(true, true),
            GuestCartItemsStorefrontResource::class,
        );
    }

    /**
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     *
     * @return array<\Generated\Shared\Transfer\ItemTransfer>
     */
    protected function filterItemTransfers(array $itemTransfers, QuoteTransfer $quoteTransfer): array
    {
        foreach ($this->cartItemFilterPlugins as $cartItemFilterPlugin) {
            $itemTransfers = $cartItemFilterPlugin->filterCartItems($itemTransfers, $quoteTransfer);
        }

        return $itemTransfers;
    }
}
