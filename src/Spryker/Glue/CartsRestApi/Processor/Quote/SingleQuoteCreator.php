<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi\Processor\Quote;

use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Glue\CartsRestApi\Dependency\Client\CartsRestApiToPersistentCartClientInterface;
use Spryker\Glue\CartsRestApi\Processor\Cart\CartReaderInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class SingleQuoteCreator implements SingleQuoteCreatorInterface
{
    /**
     * @var \Spryker\Glue\CartsRestApi\Processor\Cart\CartReaderInterface
     */
    protected $cartReader;

    /**
     * @var \Spryker\Glue\CartsRestApi\Dependency\Client\CartsRestApiToPersistentCartClientInterface
     */
    protected $persistentCartClient;

    /**
     * @param \Spryker\Glue\CartsRestApi\Processor\Cart\CartReaderInterface $cartReader
     * @param \Spryker\Glue\CartsRestApi\Dependency\Client\CartsRestApiToPersistentCartClientInterface $persistentCartClient
     */
    public function __construct(CartReaderInterface $cartReader, CartsRestApiToPersistentCartClientInterface $persistentCartClient)
    {
        $this->cartReader = $cartReader;
        $this->persistentCartClient = $persistentCartClient;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function createQuote(RestRequestInterface $restRequest, QuoteTransfer $quoteTransfer): QuoteResponseTransfer
    {
        $quoteCollectionTransfer = $this->cartReader->getCustomerQuotes($restRequest);
        if ($quoteCollectionTransfer->getQuotes()->count() > 0) {
            $quoteErrorTransfer = (new QuoteErrorTransfer())
                ->setMessage(CartsRestApiConfig::EXCEPTION_MESSAGE_CUSTOMER_ALREADY_HAS_QUOTE);

            return (new QuoteResponseTransfer())
                ->addError($quoteErrorTransfer)
                ->setQuoteTransfer($quoteTransfer)
                ->setIsSuccessful(false);
        }

        return $this->persistentCartClient->createQuote($quoteTransfer);
    }
}
