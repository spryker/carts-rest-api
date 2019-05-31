<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Business;

use Generated\Shared\Transfer\QuoteResponseTransfer;
use Spryker\Zed\CartsRestApi\CartsRestApiConfig;

trait ErrorIdentifierAdderTrait
{
    /**
     * @param \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    protected function addErrorIdentifiersToQuoteResponseErrors(
        QuoteResponseTransfer $quoteResponseTransfer
    ): QuoteResponseTransfer {
        $quoteErrorTransfers = $quoteResponseTransfer->getErrors();
        if (!$quoteErrorTransfers->count()) {
            return $quoteResponseTransfer;
        }

        foreach ($quoteErrorTransfers as $quoteErrorTransfer) {
            $errorIdentifier = CartsRestApiConfig::getErrorToErrorIdentifierMapping()[$quoteErrorTransfer->getMessage()] ?? null;
            if ($errorIdentifier) {
                $quoteErrorTransfer->setErrorIdentifier($errorIdentifier);
            }
        }

        return $quoteResponseTransfer;
    }
}
