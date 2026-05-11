<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartsRestApi\Api\Storefront\Processor;

use Generated\Api\Storefront\CartsStorefrontResource;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsAttributesTransfer;
use Spryker\ApiPlatform\State\Processor\AbstractStorefrontProcessor;
use Spryker\Client\CartsRestApi\CartsRestApiClientInterface;
use Spryker\Glue\CartsRestApi\Api\Storefront\Exception\CartsExceptionFactory;
use Spryker\Glue\CartsRestApi\Api\Storefront\Mapper\StorefrontCartMapperInterface;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Service\Serializer\SerializerServiceInterface;

class CartsStorefrontProcessor extends AbstractStorefrontProcessor
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
    protected function processPost(mixed $data): mixed
    {
        $customerReference = $this->getCustomerReference();

        $quoteTransfer = $this->cartMapper->mapRestCartsAttributesTransferToQuoteTransfer(
            $this->buildRestCartsAttributesTransfer($data),
            (new QuoteTransfer())->setCustomerReference($customerReference),
        );

        $quoteResponseTransfer = $this->cartsRestApiClient->createQuote($quoteTransfer);

        if (!$quoteResponseTransfer->getIsSuccessful()) {
            throw $this->exceptionFactory->createExceptionFromQuoteResponse(
                $quoteResponseTransfer,
                CartsRestApiConfig::RESPONSE_CODE_FAILED_CREATING_CART,
                CartsRestApiConfig::EXCEPTION_MESSAGE_FAILED_TO_CREATE_CART,
            );
        }

        return $this->mapQuoteTransferToResource($quoteResponseTransfer->getQuoteTransferOrFail());
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processPatch(mixed $data): mixed
    {
        $cartUuid = $this->resolveCartUuid();
        $customerReference = $this->getCustomerReference();

        $quoteTransfer = $this->cartMapper->mapRestCartsAttributesTransferToQuoteTransfer(
            $this->buildRestCartsAttributesTransfer($data),
            (new QuoteTransfer())->setCustomerReference($customerReference),
        );

        $quoteTransfer
            ->setUuid($cartUuid)
            ->setCustomerReference($customerReference)
            ->setCustomer($this->getCustomer());

        $quoteResponseTransfer = $this->cartsRestApiClient->updateQuote($quoteTransfer);

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
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processDelete(): mixed
    {
        $cartUuid = $this->resolveCartUuid();
        $customerReference = $this->getCustomerReference();

        $quoteTransfer = (new QuoteTransfer())
            ->setUuid($cartUuid)
            ->setCustomerReference($customerReference)
            ->setCustomer($this->getCustomer());

        $quoteResponseTransfer = $this->cartsRestApiClient->deleteQuote($quoteTransfer);

        if (!$quoteResponseTransfer->getIsSuccessful()) {
            throw $this->exceptionFactory->createExceptionFromQuoteResponse(
                $quoteResponseTransfer,
                CartsRestApiConfig::RESPONSE_CODE_FAILED_DELETING_CART,
                CartsRestApiConfig::EXCEPTION_MESSAGE_FAILED_DELETING_CART,
            );
        }

        return null;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function resolveCartUuid(): string
    {
        $cartUuid = $this->getUriVariables()[static::KEY_UUID] ?? null;

        if (!is_string($cartUuid) || $cartUuid === '') {
            throw $this->exceptionFactory->createCartIdMissingException();
        }

        return $cartUuid;
    }

    protected function buildRestCartsAttributesTransfer(mixed $data): RestCartsAttributesTransfer
    {
        if ($data instanceof RestCartsAttributesTransfer) {
            return $data;
        }

        $payload = $data instanceof CartsStorefrontResource
            ? get_object_vars($data)
            : (array)$data;

        return (new RestCartsAttributesTransfer())->fromArray(
            array_filter($payload, static fn ($value): bool => $value !== null),
            true,
        );
    }

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
        $resource->bundleItems = iterator_to_array($quoteTransfer->getBundleItems());
        $resource->items = iterator_to_array($quoteTransfer->getItems());

        return $resource;
    }
}
