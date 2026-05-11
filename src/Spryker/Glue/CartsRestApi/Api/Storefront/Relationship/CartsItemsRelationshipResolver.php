<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartsRestApi\Api\Storefront\Relationship;

use ArrayObject;
use Generated\Api\Storefront\CartItemsStorefrontResource;
use Generated\Api\Storefront\CartsStorefrontResource;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\ApiPlatform\Relationship\AbstractRelationshipResolver;
use Spryker\Glue\CartsRestApi\Api\Storefront\Mapper\StorefrontCartItemMapperInterface;
use Spryker\Service\Container\Attributes\Plugins;
use Spryker\Service\Serializer\SerializerServiceInterface;

/**
 * Builds `CartItems` (JSON:API type `items`) sub-resources from the parent `Carts` resource's
 * `items` array (carried as `ItemTransfer` collection). Mirrors the legacy
 * {@see \Spryker\Glue\CartsRestApi\Processor\RestResponseBuilder\CartRestResponseBuilder::addCartItemRelationships()}
 * behavior: data is read directly from the parent's QuoteTransfer-derived items — no extra
 * cart fetch — and the same `RestCartItemsAttributesMapperPluginInterface` plugin chain runs
 * via {@see StorefrontCartItemMapperInterface}.
 *
 * Runs the {@see \Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\CartItemFilterPluginInterface}
 * chain (e.g. {@see \Spryker\Glue\ProductBundleCartsRestApi\Plugin\CartsRestApi\ProductBundleCartItemFilterPlugin})
 * so bundled-out items roll back into a single bundle resource — same call order the legacy
 * Glue stack used.
 */
class CartsItemsRelationshipResolver extends AbstractRelationshipResolver
{
    /**
     * @param array<\Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\CartItemFilterPluginInterface> $cartItemFilterPlugins
     */
    public function __construct(
        protected StorefrontCartItemMapperInterface $cartItemMapper,
        protected SerializerServiceInterface $serializer,
        #[Plugins(dependencyProviderMethod: 'getCartItemFilterPlugins')]
        protected array $cartItemFilterPlugins = [],
    ) {
    }

    /**
     * @return array<\Generated\Api\Storefront\CartItemsStorefrontResource>
     */
    protected function resolveRelationship(): array
    {
        $localeName = $this->hasLocale() ? $this->getLocale()->getLocaleNameOrFail() : '';

        $resources = [];

        foreach ($this->getParentResources() as $parent) {
            if (!$parent instanceof CartsStorefrontResource) {
                continue;
            }

            $items = $this->filterItems($parent->items ?? [], $parent->bundleItems ?? []);

            foreach ($items as $itemTransfer) {
                $resources[] = $this->mapItemToResource($itemTransfer, $localeName, $parent);
            }
        }

        return $resources;
    }

    /**
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $bundleItemTransfers
     *
     * @return array<\Generated\Shared\Transfer\ItemTransfer>
     */
    protected function filterItems(array $itemTransfers, array $bundleItemTransfers): array
    {
        if ($this->cartItemFilterPlugins === []) {
            return $itemTransfers;
        }

        $quoteTransfer = (new QuoteTransfer())
            ->setItems(new ArrayObject($itemTransfers))
            ->setBundleItems(new ArrayObject($bundleItemTransfers));

        foreach ($this->cartItemFilterPlugins as $cartItemFilterPlugin) {
            $itemTransfers = $cartItemFilterPlugin->filterCartItems($itemTransfers, $quoteTransfer);
        }

        return $itemTransfers;
    }

    protected function mapItemToResource(
        ItemTransfer $itemTransfer,
        string $localeName,
        CartsStorefrontResource $parent,
    ): CartItemsStorefrontResource {
        $restItemsAttributesTransfer = $this->cartItemMapper->mapItemTransferToRestItemsAttributesTransfer(
            $itemTransfer,
            $localeName,
        );

        $resource = $this->serializer->denormalize(
            $restItemsAttributesTransfer->toArray(true, true),
            CartItemsStorefrontResource::class,
        );

        // IRI converter resolves the cartId URI variable from $resource->cart->uuid
        // (via `toProperty: cart` in cart-items.resource.yml) to build /carts/{cartId}/items/{groupKey}.
        $resource->cart = $parent;

        return $resource;
    }
}
