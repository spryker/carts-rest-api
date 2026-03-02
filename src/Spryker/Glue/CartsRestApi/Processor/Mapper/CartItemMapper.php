<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi\Processor\Mapper;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\RestCartItemCalculationsTransfer;
use Generated\Shared\Transfer\RestItemsAttributesTransfer;

class CartItemMapper implements CartItemMapperInterface
{
    /**
     * @var array<\Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\RestCartItemsAttributesMapperPluginInterface>
     */
    protected $restCartItemsAttributesMapperPlugins;

    /**
     * @param array<\Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\RestCartItemsAttributesMapperPluginInterface> $restCartItemsAttributesMapperPlugins
     */
    public function __construct(array $restCartItemsAttributesMapperPlugins)
    {
        $this->restCartItemsAttributesMapperPlugins = $restCartItemsAttributesMapperPlugins;
    }

    public function mapItemTransferToRestItemsAttributesTransfer(
        ItemTransfer $itemTransfer,
        RestItemsAttributesTransfer $restItemsAttributesTransfer,
        string $localeName
    ): RestItemsAttributesTransfer {
        $restItemsAttributesTransfer->fromArray($itemTransfer->toArray(), true);

        $restCartItemCalculationsTransfer = $restItemsAttributesTransfer->getCalculations();
        if (!$restCartItemCalculationsTransfer) {
            $restCartItemCalculationsTransfer = new RestCartItemCalculationsTransfer();
        }
        $restItemsAttributesTransfer->setCalculations(
            $restCartItemCalculationsTransfer->fromArray($itemTransfer->toArray(), true),
        );

        return $this->executeRestCartItemsAttributesMapperPlugins(
            $itemTransfer,
            $restItemsAttributesTransfer,
            $localeName,
        );
    }

    protected function executeRestCartItemsAttributesMapperPlugins(
        ItemTransfer $itemTransfer,
        RestItemsAttributesTransfer $restItemsAttributesTransfer,
        string $localeName
    ): RestItemsAttributesTransfer {
        foreach ($this->restCartItemsAttributesMapperPlugins as $restOrderItemsAttributesMapperPlugin) {
            $restItemsAttributesTransfer =
                $restOrderItemsAttributesMapperPlugin->mapItemTransferToRestItemsAttributesTransfer(
                    $itemTransfer,
                    $restItemsAttributesTransfer,
                    $localeName,
                );
        }

        return $restItemsAttributesTransfer;
    }
}
