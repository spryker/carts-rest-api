<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartsRestApi\Api\Storefront\Relationship;

use ArrayObject;
use Generated\Api\Storefront\GuestCartItemsStorefrontResource;
use Generated\Api\Storefront\GuestCartsStorefrontResource;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\ApiPlatform\Relationship\AbstractRelationshipResolver;
use Spryker\Glue\CartsRestApi\Api\Storefront\Mapper\StorefrontCartItemMapperInterface;
use Spryker\Service\Container\Attributes\Plugins;
use Spryker\Service\Serializer\SerializerServiceInterface;

/**
 * Guest-cart variant of {@see CartsItemsRelationshipResolver} — emits `GuestCartItems`
 * (JSON:API type `guest-cart-items`) sub-resources from the parent `GuestCarts`'
 * `items` array.
 *
 * Runs the {@see \Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\CartItemFilterPluginInterface}
 * chain (e.g. `ProductBundleCartItemFilterPlugin`) so bundled-out items roll back into a
 * single bundle resource — same call order the legacy Glue stack used.
 */
class GuestCartsItemsRelationshipResolver extends AbstractRelationshipResolver
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
     * @return array<\Generated\Api\Storefront\GuestCartItemsStorefrontResource>
     */
    protected function resolveRelationship(): array
    {
        $localeName = $this->hasLocale() ? $this->getLocale()->getLocaleNameOrFail() : '';

        $resources = [];

        foreach ($this->getParentResources() as $parent) {
            if (!$parent instanceof GuestCartsStorefrontResource) {
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
        GuestCartsStorefrontResource $parent,
    ): GuestCartItemsStorefrontResource {
        $restItemsAttributesTransfer = $this->cartItemMapper->mapItemTransferToRestItemsAttributesTransfer(
            $itemTransfer,
            $localeName,
        );

        $resource = $this->serializer->denormalize(
            $restItemsAttributesTransfer->toArray(true, true),
            GuestCartItemsStorefrontResource::class,
        );

        // IRI converter resolves the cartId URI variable from $resource->guestCart->uuid
        // (via `toProperty: guestCart` in guest-cart-items.resource.yml) to build
        // /guest-carts/{cartId}/guest-cart-items/{groupKey}.
        $resource->guestCart = $parent;

        return $resource;
    }
}
