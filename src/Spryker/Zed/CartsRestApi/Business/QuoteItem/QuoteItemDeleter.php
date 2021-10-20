<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Business\QuoteItem;

use Generated\Shared\Transfer\CartItemRequestTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\PersistentCartChangeTransfer;
use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemsAttributesTransfer;
use Spryker\Shared\CartsRestApi\CartsRestApiConfig as CartsRestApiSharedConfig;
use Spryker\Zed\CartsRestApi\Business\PermissionChecker\QuotePermissionCheckerInterface;
use Spryker\Zed\CartsRestApi\Business\Reloader\QuoteReloaderInterface;
use Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToPersistentCartFacadeInterface;

class QuoteItemDeleter implements QuoteItemDeleterInterface
{
    /**
     * @var \Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToPersistentCartFacadeInterface
     */
    protected $persistentCartFacade;

    /**
     * @var \Spryker\Zed\CartsRestApi\Business\QuoteItem\QuoteItemReaderInterface
     */
    protected $quoteItemReader;

    /**
     * @var \Spryker\Zed\CartsRestApi\Business\PermissionChecker\QuotePermissionCheckerInterface
     */
    protected $quotePermissionChecker;

    /**
     * @var \Spryker\Zed\CartsRestApi\Business\Reloader\QuoteReloaderInterface
     */
    protected $quoteReloader;

    /**
     * @param \Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToPersistentCartFacadeInterface $persistentCartFacade
     * @param \Spryker\Zed\CartsRestApi\Business\QuoteItem\QuoteItemReaderInterface $quoteItemReader
     * @param \Spryker\Zed\CartsRestApi\Business\PermissionChecker\QuotePermissionCheckerInterface $quotePermissionChecker
     * @param \Spryker\Zed\CartsRestApi\Business\Reloader\QuoteReloaderInterface $quoteReloader
     */
    public function __construct(
        CartsRestApiToPersistentCartFacadeInterface $persistentCartFacade,
        QuoteItemReaderInterface $quoteItemReader,
        QuotePermissionCheckerInterface $quotePermissionChecker,
        QuoteReloaderInterface $quoteReloader
    ) {
        $this->persistentCartFacade = $persistentCartFacade;
        $this->quoteItemReader = $quoteItemReader;
        $this->quotePermissionChecker = $quotePermissionChecker;
        $this->quoteReloader = $quoteReloader;
    }

    /**
     * @deprecated Use {@link removeItem()} instead.
     *
     * @param \Generated\Shared\Transfer\RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function remove(RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer): QuoteResponseTransfer
    {
        $restCartItemsAttributesTransfer
            ->requireQuoteUuid()
            ->requireCustomerReference()
            ->requireSku();

        $cartItemRequestTransfer = (new CartItemRequestTransfer())
            ->fromArray($restCartItemsAttributesTransfer->toArray(), true);

        return $this->removeItem($cartItemRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\CartItemRequestTransfer $cartItemRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function removeItem(CartItemRequestTransfer $cartItemRequestTransfer): QuoteResponseTransfer
    {
        $cartItemRequestTransfer
            ->requireQuoteUuid()
            ->requireCustomer()
            ->requireSku();

        $cartItemRequestTransfer->getCustomer()->requireCustomerReference();

        $quoteResponseTransfer = $this->quoteItemReader->readItem($cartItemRequestTransfer);
        if (!$quoteResponseTransfer->getIsSuccessful()) {
            return $quoteResponseTransfer;
        }

        if (!$this->quotePermissionChecker->checkQuoteWritePermission($quoteResponseTransfer->getQuoteTransfer())) {
            return $quoteResponseTransfer
                ->setIsSuccessful(false)
                ->addError((new QuoteErrorTransfer())
                    ->setErrorIdentifier(CartsRestApiSharedConfig::ERROR_IDENTIFIER_UNAUTHORIZED_CART_ACTION));
        }

        $persistentCartChangeTransfer = $this->createPersistentCartChangeTransfer(
            $quoteResponseTransfer->getQuoteTransfer(),
            $cartItemRequestTransfer,
        );

        $quoteResponseTransfer = $this->persistentCartFacade->remove($persistentCartChangeTransfer);
        if (!$quoteResponseTransfer->getIsSuccessful()) {
            return $quoteResponseTransfer
                ->addError((new QuoteErrorTransfer())
                    ->setErrorIdentifier(CartsRestApiSharedConfig::ERROR_IDENTIFIER_FAILED_DELETING_CART_ITEM));
        }

        $quoteTransfer = $this->quoteReloader->reloadQuoteItems(
            $quoteResponseTransfer->getQuoteTransfer(),
        );

        return $quoteResponseTransfer->setQuoteTransfer($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CartItemRequestTransfer $cartItemRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PersistentCartChangeTransfer
     */
    protected function createPersistentCartChangeTransfer(
        QuoteTransfer $quoteTransfer,
        CartItemRequestTransfer $cartItemRequestTransfer
    ): PersistentCartChangeTransfer {
        return (new PersistentCartChangeTransfer())
            ->setIdQuote($quoteTransfer->getIdQuote())
            ->addItem((new ItemTransfer())
                ->setSku($cartItemRequestTransfer->getSku())
                ->setGroupKey($cartItemRequestTransfer->getGroupKey())
                ->setQuantity($cartItemRequestTransfer->getQuantity()))
            ->setCustomer($cartItemRequestTransfer->getCustomer());
    }
}
