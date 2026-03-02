<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Business\QuoteItem\Mapper;

use Generated\Shared\Transfer\CartItemRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class QuoteItemMapper implements QuoteItemMapperInterface
{
    public function mapCartItemsRequestTransferToQuoteTransfer(
        CartItemRequestTransfer $cartItemRequestTransfer,
        QuoteTransfer $quoteTransfer
    ): QuoteTransfer {
        return $quoteTransfer
            ->setUuid($cartItemRequestTransfer->getQuoteUuid())
            ->setCustomerReference($cartItemRequestTransfer->getCustomer()->getCustomerReference())
            ->setCustomer($cartItemRequestTransfer->getCustomer());
    }
}
