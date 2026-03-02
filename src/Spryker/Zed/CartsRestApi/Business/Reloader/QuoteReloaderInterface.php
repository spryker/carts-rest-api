<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Business\Reloader;

use Generated\Shared\Transfer\QuoteTransfer;

interface QuoteReloaderInterface
{
    public function reloadQuoteItems(QuoteTransfer $quoteTransfer): QuoteTransfer;
}
