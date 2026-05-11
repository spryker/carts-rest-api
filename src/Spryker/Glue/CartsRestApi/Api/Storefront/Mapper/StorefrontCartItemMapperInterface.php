<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartsRestApi\Api\Storefront\Mapper;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\RestItemsAttributesTransfer;

interface StorefrontCartItemMapperInterface
{
    public function mapItemTransferToRestItemsAttributesTransfer(
        ItemTransfer $itemTransfer,
        string $localeName,
    ): RestItemsAttributesTransfer;
}
