<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Business\QuoteItem;

use Generated\Shared\Transfer\CartItemRequestTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;

interface QuoteItemReaderInterface
{
    public function readItem(CartItemRequestTransfer $cartItemRequestTransfer): QuoteResponseTransfer;
}
