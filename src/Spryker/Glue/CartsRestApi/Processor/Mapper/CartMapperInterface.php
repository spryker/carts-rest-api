<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi\Processor\Mapper;

use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsAttributesTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

interface CartMapperInterface
{
    public function mapQuoteTransferToRestCartsAttributesTransfer(QuoteTransfer $quoteTransfer): RestCartsAttributesTransfer;

    public function mapRestCartsAttributesTransferToQuoteTransfer(
        RestCartsAttributesTransfer $restCartsAttributesTransfer,
        QuoteTransfer $quoteTransfer
    ): QuoteTransfer;

    public function mapRestRequestToQuoteTransfer(RestRequestInterface $restRequest, QuoteTransfer $quoteTransfer): QuoteTransfer;

    public function mapQuoteErrorTransferToRestErrorMessageTransfer(
        QuoteErrorTransfer $quoteErrorTransfer,
        RestErrorMessageTransfer $restErrorMessageTransfer
    ): RestErrorMessageTransfer;
}
