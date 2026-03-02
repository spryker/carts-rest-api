<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Dependency\Facade;

use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface CartsRestApiToQuoteFacadeInterface
{
    public function findQuoteByUuid(QuoteTransfer $quoteTransfer): QuoteResponseTransfer;

    public function getQuoteCollection(QuoteCriteriaFilterTransfer $quoteCriteriaFilterTransfer): QuoteCollectionTransfer;

    public function deleteQuote(QuoteTransfer $quoteTransfer): QuoteResponseTransfer;
}
