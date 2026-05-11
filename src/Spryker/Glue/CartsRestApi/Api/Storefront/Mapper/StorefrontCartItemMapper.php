<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartsRestApi\Api\Storefront\Mapper;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\RestCartItemCalculationsTransfer;
use Generated\Shared\Transfer\RestItemsAttributesTransfer;
use Spryker\Service\Container\Attributes\Plugins;

/**
 * Maps `ItemTransfer` to `RestItemsAttributesTransfer` for the API Platform flow.
 *
 * Reproduces the field-level translation that the legacy {@see \Spryker\Glue\CartsRestApi\Processor\Mapper\CartItemMapper}
 * performs and runs the same `RestCartItemsAttributesMapperPluginInterface` plugin chain so any
 * project-level field expansion (sales units, product configurations, merchant product offers,
 * configured bundles, …) keeps working under the API Platform stack.
 *
 * Lives under `Api/Storefront/Mapper/` so it is picked up by `ApiClassAutoDiscoveryPass`.
 */
class StorefrontCartItemMapper implements StorefrontCartItemMapperInterface
{
    /**
     * @param array<\Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\RestCartItemsAttributesMapperPluginInterface> $restCartItemsAttributesMapperPlugins
     */
    public function __construct(
        #[Plugins(dependencyProviderMethod: 'getRestCartItemsAttributesMapperPlugins')]
        protected array $restCartItemsAttributesMapperPlugins = [],
    ) {
    }

    public function mapItemTransferToRestItemsAttributesTransfer(
        ItemTransfer $itemTransfer,
        string $localeName,
    ): RestItemsAttributesTransfer {
        $restItemsAttributesTransfer = (new RestItemsAttributesTransfer())
            ->fromArray($itemTransfer->toArray(), true);

        $restCartItemCalculationsTransfer = $restItemsAttributesTransfer->getCalculations()
            ?? new RestCartItemCalculationsTransfer();

        $restItemsAttributesTransfer->setCalculations(
            $restCartItemCalculationsTransfer->fromArray($itemTransfer->toArray(), true),
        );

        foreach ($this->restCartItemsAttributesMapperPlugins as $mapperPlugin) {
            $restItemsAttributesTransfer = $mapperPlugin->mapItemTransferToRestItemsAttributesTransfer(
                $itemTransfer,
                $restItemsAttributesTransfer,
                $localeName,
            );
        }

        return $restItemsAttributesTransfer;
    }
}
