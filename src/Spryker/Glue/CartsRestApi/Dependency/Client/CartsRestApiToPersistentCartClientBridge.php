<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi\Dependency\Client;

use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\QuoteUpdateRequestTransfer;

class CartsRestApiToPersistentCartClientBridge implements CartsRestApiToPersistentCartClientInterface
{
    /**
     * @var \Spryker\Client\PersistentCart\PersistentCartClientInterface
     */
    protected $persistentCartClient;

    /**
     * @param \Spryker\Client\PersistentCart\PersistentCartClientInterface $persistentCartClient
     */
    public function __construct($persistentCartClient)
    {
        $this->persistentCartClient = $persistentCartClient;
    }

    public function generateGuestCartCustomerReference(string $customerReference): string
    {
        return $this->persistentCartClient->generateGuestCartCustomerReference($customerReference);
    }

    public function deleteQuote(QuoteTransfer $quoteTransfer): QuoteResponseTransfer
    {
        return $this->persistentCartClient->deleteQuote($quoteTransfer);
    }

    public function createQuote(QuoteTransfer $quoteTransfer): QuoteResponseTransfer
    {
        return $this->persistentCartClient->createQuote($quoteTransfer);
    }

    public function updateQuote(QuoteUpdateRequestTransfer $quoteUpdateRequestTransfer): QuoteResponseTransfer
    {
        return $this->persistentCartClient->updateQuote($quoteUpdateRequestTransfer);
    }
}
