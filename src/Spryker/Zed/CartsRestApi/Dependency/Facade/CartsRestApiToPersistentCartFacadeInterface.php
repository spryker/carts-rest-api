<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Dependency\Facade;

use Generated\Shared\Transfer\PersistentCartChangeQuantityTransfer;
use Generated\Shared\Transfer\PersistentCartChangeTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\QuoteUpdateRequestTransfer;

interface CartsRestApiToPersistentCartFacadeInterface
{
    public function updateQuote(QuoteUpdateRequestTransfer $quoteUpdateRequestTransfer): QuoteResponseTransfer;

    public function createQuote(QuoteTransfer $quoteTransfer): QuoteResponseTransfer;

    public function deleteQuote(QuoteTransfer $quoteTransfer): QuoteResponseTransfer;

    public function remove(PersistentCartChangeTransfer $persistentCartChangeTransfer): QuoteResponseTransfer;

    public function changeItemQuantity(PersistentCartChangeQuantityTransfer $persistentCartChangeQuantityTransfer): QuoteResponseTransfer;

    public function validateQuote(QuoteTransfer $quoteTransfer): QuoteResponseTransfer;

    public function add(PersistentCartChangeTransfer $persistentCartChangeTransfer): QuoteResponseTransfer;
}
