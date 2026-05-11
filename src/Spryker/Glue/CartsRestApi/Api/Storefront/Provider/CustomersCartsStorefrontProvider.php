<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartsRestApi\Api\Storefront\Provider;

use Generated\Api\Storefront\CustomersCartsStorefrontResource;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\ApiPlatform\State\Provider\AbstractStorefrontProvider;
use Spryker\Client\CartsRestApi\CartsRestApiClientInterface;
use Spryker\Glue\CartsRestApi\Api\Storefront\Exception\CartsExceptionFactory;
use Spryker\Glue\CartsRestApi\Api\Storefront\Mapper\StorefrontCartMapperInterface;
use Spryker\Service\Serializer\SerializerServiceInterface;

class CustomersCartsStorefrontProvider extends AbstractStorefrontProvider
{
    public function __construct(
        protected CartsRestApiClientInterface $cartsRestApiClient,
        protected StorefrontCartMapperInterface $cartMapper,
        protected SerializerServiceInterface $serializer,
        protected CartsExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @return array<\Generated\Api\Storefront\CustomersCartsStorefrontResource>
     */
    protected function provideCollection(): array
    {
        $quoteCollectionTransfer = $this->cartsRestApiClient->getQuoteCollection(
            (new QuoteCriteriaFilterTransfer())->setCustomerReference($this->getCustomerReference()),
        );

        return $this->mapQuoteCollectionToResources($quoteCollectionTransfer);
    }

    /**
     * @return array<\Generated\Api\Storefront\CustomersCartsStorefrontResource>
     */
    protected function mapQuoteCollectionToResources(QuoteCollectionTransfer $quoteCollectionTransfer): array
    {
        $resources = [];

        foreach ($quoteCollectionTransfer->getQuotes() as $quoteTransfer) {
            $resources[] = $this->mapQuoteTransferToResource($quoteTransfer);
        }

        return $resources;
    }

    protected function mapQuoteTransferToResource(QuoteTransfer $quoteTransfer): CustomersCartsStorefrontResource
    {
        $restCartsAttributesTransfer = $this->cartMapper->mapQuoteTransferToRestCartsAttributesTransfer($quoteTransfer);

        return $this->serializer->denormalize(
            ['uuid' => $quoteTransfer->getUuid()] + $restCartsAttributesTransfer->toArray(true, true),
            CustomersCartsStorefrontResource::class,
        );
    }
}
