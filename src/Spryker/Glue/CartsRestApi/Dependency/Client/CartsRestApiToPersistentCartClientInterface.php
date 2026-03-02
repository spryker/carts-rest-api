<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi\Dependency\Client;

use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\QuoteUpdateRequestTransfer;

interface CartsRestApiToPersistentCartClientInterface
{
    public function generateGuestCartCustomerReference(string $customerReference): string;

    public function deleteQuote(QuoteTransfer $quoteTransfer): QuoteResponseTransfer;

    public function createQuote(QuoteTransfer $quoteTransfer): QuoteResponseTransfer;

    public function updateQuote(QuoteUpdateRequestTransfer $quoteUpdateRequestTransfer): QuoteResponseTransfer;
}
