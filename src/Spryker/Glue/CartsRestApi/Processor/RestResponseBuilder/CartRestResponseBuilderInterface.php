<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi\Processor\RestResponseBuilder;

use ArrayObject;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

interface CartRestResponseBuilderInterface
{
    public function createCartRestResponse(QuoteTransfer $quoteTransfer, string $localeName): RestResponseInterface;

    public function createRestQuoteCollectionResponse(
        QuoteCollectionTransfer $quoteCollectionTransfer,
        RestRequestInterface $restRequest
    ): RestResponseInterface;

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\QuoteErrorTransfer> $errors
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function createFailedErrorResponse(ArrayObject $errors): RestResponseInterface;

    public function createRestResponse(): RestResponseInterface;

    public function createCartIdMissingErrorResponse(): RestResponseInterface;

    public function createCustomerUnauthorizedErrorResponse(): RestResponseInterface;
}
