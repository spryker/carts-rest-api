<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Business\PermissionChecker;

use Generated\Shared\Transfer\QuoteTransfer;

interface QuotePermissionCheckerInterface
{
    public function checkQuoteReadPermission(QuoteTransfer $quoteTransfer): bool;

    public function checkQuoteWritePermission(QuoteTransfer $quoteTransfer): bool;
}
