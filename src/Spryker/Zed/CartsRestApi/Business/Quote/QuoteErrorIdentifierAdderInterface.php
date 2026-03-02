<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Business\Quote;

use Generated\Shared\Transfer\QuoteResponseTransfer;

interface QuoteErrorIdentifierAdderInterface
{
    public function addErrorIdentifiersToQuoteResponseErrors(QuoteResponseTransfer $quoteResponseTransfer): QuoteResponseTransfer;
}
