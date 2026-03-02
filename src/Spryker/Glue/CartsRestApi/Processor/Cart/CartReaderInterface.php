<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi\Processor\Cart;

use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

interface CartReaderInterface
{
    public function getCustomerQuoteByUuid(string $uuidCart, RestRequestInterface $restRequest): RestResponseInterface;

    public function readCurrentCustomerCarts(RestRequestInterface $restRequest): RestResponseInterface;

    public function getCustomerQuotes(RestRequestInterface $restRequest): QuoteCollectionTransfer;

    public function readCustomerCarts(RestRequestInterface $restRequest): RestResponseInterface;
}
