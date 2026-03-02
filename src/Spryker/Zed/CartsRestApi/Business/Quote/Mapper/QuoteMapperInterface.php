<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Business\Quote\Mapper;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\QuoteUpdateRequestTransfer;

interface QuoteMapperInterface
{
    public function mapQuoteTransferToQuoteUpdateRequestTransfer(
        QuoteTransfer $quoteTransfer,
        QuoteUpdateRequestTransfer $quoteUpdateRequestTransfer
    ): QuoteUpdateRequestTransfer;

    public function mapQuoteTransferToOriginalQuoteTransfer(
        QuoteTransfer $quoteTransfer,
        QuoteTransfer $originalQuoteTransfer
    ): QuoteTransfer;
}
