<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Business\Quote\Mapper;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\QuoteUpdateRequestAttributesTransfer;
use Generated\Shared\Transfer\QuoteUpdateRequestTransfer;

class QuoteMapper implements QuoteMapperInterface
{
    public function mapQuoteTransferToQuoteUpdateRequestTransfer(
        QuoteTransfer $quoteTransfer,
        QuoteUpdateRequestTransfer $quoteUpdateRequestTransfer
    ): QuoteUpdateRequestTransfer {
        $quoteUpdateRequestTransfer->fromArray($quoteTransfer->modifiedToArray(), true);
        $quoteUpdateRequestAttributesTransfer = (new QuoteUpdateRequestAttributesTransfer())
            ->fromArray($quoteTransfer->modifiedToArray(), true);
        $quoteUpdateRequestTransfer->setQuoteUpdateRequestAttributes($quoteUpdateRequestAttributesTransfer);

        return $quoteUpdateRequestTransfer;
    }

    public function mapQuoteTransferToOriginalQuoteTransfer(
        QuoteTransfer $quoteTransfer,
        QuoteTransfer $originalQuoteTransfer
    ): QuoteTransfer {
        $originalQuoteTransfer->setCustomer($quoteTransfer->getCustomer());

        $currencyTransfer = $quoteTransfer->getCurrency();
        $name = $quoteTransfer->getName();
        $priceMode = $quoteTransfer->getPriceMode();

        if ($priceMode) {
            $originalQuoteTransfer->setPriceMode($priceMode);
        }

        if ($currencyTransfer && $currencyTransfer->getCode()) {
            $originalQuoteTransfer->setCurrency($currencyTransfer);
        }

        if ($name) {
            $originalQuoteTransfer->setName($name);
        }

        return $originalQuoteTransfer;
    }
}
