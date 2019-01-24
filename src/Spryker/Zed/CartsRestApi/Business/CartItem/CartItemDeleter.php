<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Business\CartItem;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PersistentCartChangeTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemRequestTransfer;
use Spryker\Zed\CartsRestApi\Business\Cart\CartReaderInterface;
use Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToPersistentCartFacadeInterface;

class CartItemDeleter implements CartItemDeleterInterface
{
    /**
     * @var \Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToPersistentCartFacadeInterface
     */
    protected $persistentCartFacade;

    /**
     * @var \Spryker\Zed\CartsRestApi\Business\Cart\CartReaderInterface
     */
    protected $cartReader;

    /**
     * @param \Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToPersistentCartFacadeInterface $persistentCartFacade
     * @param \Spryker\Zed\CartsRestApi\Business\Cart\CartReaderInterface $cartReader
     */
    public function __construct(
        CartsRestApiToPersistentCartFacadeInterface $persistentCartFacade,
        CartReaderInterface $cartReader
    ) {
        $this->persistentCartFacade = $persistentCartFacade;
        $this->cartReader = $cartReader;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCartItemRequestTransfer $restCartItemRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function remove(RestCartItemRequestTransfer $restCartItemRequestTransfer): QuoteResponseTransfer
    {
        $restCartItemRequestTransfer
            ->requireCartUuid()
            ->requireCustomerReference()
            ->requireCartItem();

        $quoteResponseTransfer = $this->cartReader->findQuoteByUuid(
            (new QuoteTransfer())->setUuid($restCartItemRequestTransfer->getCartUuid())
        );

        if (!$quoteResponseTransfer->getIsSuccessful()) {
            return $quoteResponseTransfer;
        }

        $persistentCartChangeTransfer = (new PersistentCartChangeTransfer())
            ->setIdQuote($quoteResponseTransfer->getQuoteTransfer()->getIdQuote())
            ->addItem($restCartItemRequestTransfer->getCartItem())
            ->setCustomer((new CustomerTransfer())->setCustomerReference($restCartItemRequestTransfer->getCustomerReference()));

        return $this->persistentCartFacade->remove($persistentCartChangeTransfer);
    }
}
