<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Business\QuoteItem;

use ArrayObject;
use Generated\Shared\Transfer\CartItemRequestTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\PersistentCartChangeTransfer;
use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemsAttributesTransfer;
use Spryker\Shared\CartsRestApi\CartsRestApiConfig as CartsRestApiSharedConfig;
use Spryker\Zed\CartsRestApi\Business\PermissionChecker\QuotePermissionCheckerInterface;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteReaderInterface;
use Spryker\Zed\CartsRestApi\Business\QuoteItem\Mapper\QuoteItemMapperInterface;
use Spryker\Zed\CartsRestApi\Business\Reloader\QuoteReloaderInterface;
use Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToPersistentCartFacadeInterface;

class QuoteItemAdder implements QuoteItemAdderInterface
{
    /**
     * @var \Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToPersistentCartFacadeInterface
     */
    protected $persistentCartFacade;

    /**
     * @var \Spryker\Zed\CartsRestApi\Business\Quote\QuoteReaderInterface
     */
    protected $quoteReader;

    /**
     * @var \Spryker\Zed\CartsRestApi\Business\QuoteItem\Mapper\QuoteItemMapperInterface
     */
    protected $quoteItemMapper;

    /**
     * @var \Spryker\Zed\CartsRestApi\Business\PermissionChecker\QuotePermissionCheckerInterface
     */
    protected $quotePermissionChecker;

    /**
     * @var \Spryker\Zed\CartsRestApi\Business\Reloader\QuoteReloaderInterface
     */
    protected $quoteReloader;

    /**
     * @var array<\Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\CartItemMapperPluginInterface>
     */
    protected $cartItemMapperPlugins;

    /**
     * @param \Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToPersistentCartFacadeInterface $persistentCartFacade
     * @param \Spryker\Zed\CartsRestApi\Business\Quote\QuoteReaderInterface $quoteReader
     * @param \Spryker\Zed\CartsRestApi\Business\QuoteItem\Mapper\QuoteItemMapperInterface $quoteItemMapper
     * @param \Spryker\Zed\CartsRestApi\Business\PermissionChecker\QuotePermissionCheckerInterface $quotePermissionChecker
     * @param \Spryker\Zed\CartsRestApi\Business\Reloader\QuoteReloaderInterface $quoteReloader
     * @param array<\Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\CartItemMapperPluginInterface> $cartItemMapperPlugins
     */
    public function __construct(
        CartsRestApiToPersistentCartFacadeInterface $persistentCartFacade,
        QuoteReaderInterface $quoteReader,
        QuoteItemMapperInterface $quoteItemMapper,
        QuotePermissionCheckerInterface $quotePermissionChecker,
        QuoteReloaderInterface $quoteReloader,
        array $cartItemMapperPlugins
    ) {
        $this->persistentCartFacade = $persistentCartFacade;
        $this->quoteReader = $quoteReader;
        $this->quoteItemMapper = $quoteItemMapper;
        $this->quotePermissionChecker = $quotePermissionChecker;
        $this->quoteReloader = $quoteReloader;
        $this->cartItemMapperPlugins = $cartItemMapperPlugins;
    }

    /**
     * @deprecated Use {@link addToCart()} instead.
     *
     * @param \Generated\Shared\Transfer\RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function add(RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer): QuoteResponseTransfer
    {
        $restCartItemsAttributesTransfer
            ->requireCustomerReference()
            ->requireSku()
            ->requireQuoteUuid();

        $cartItemRequestTransfer = (new CartItemRequestTransfer())
            ->fromArray($restCartItemsAttributesTransfer->toArray(), true);

        return $this->addToCart($cartItemRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\CartItemRequestTransfer $cartItemRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function addToCart(CartItemRequestTransfer $cartItemRequestTransfer): QuoteResponseTransfer
    {
        $cartItemRequestTransfer
            ->requireCustomer()
            ->requireSku()
            ->requireQuantity()
            ->requireQuoteUuid();

        $cartItemRequestTransfer->getCustomer()->requireCustomerReference();

        $quoteTransfer = $this->quoteItemMapper->mapCartItemsRequestTransferToQuoteTransfer(
            $cartItemRequestTransfer,
            new QuoteTransfer(),
        );

        $quoteResponseTransfer = $this->quoteReader->findQuoteByUuid($quoteTransfer);

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

        $quoteResponseTransfer = $this->persistentCartFacade->add($persistentCartChangeTransfer);
        if (!$quoteResponseTransfer->getIsSuccessful()) {
            $quoteResponseTransfer
                ->addError((new QuoteErrorTransfer())
                    ->setErrorIdentifier(CartsRestApiSharedConfig::ERROR_IDENTIFIER_FAILED_ADDING_CART_ITEM));
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
        $persistentCartChangeTransfer = (new PersistentCartChangeTransfer())
            ->fromArray($quoteTransfer->toArray(), true)
            ->setItems(new ArrayObject([
                (new ItemTransfer())
                    ->setSku($cartItemRequestTransfer->getSku())
                    ->setQuantity($cartItemRequestTransfer->getQuantity()),
            ]));

        return $this->executeCartItemMapperPlugins($cartItemRequestTransfer, $persistentCartChangeTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\CartItemRequestTransfer $cartItemRequestTransfer
     * @param \Generated\Shared\Transfer\PersistentCartChangeTransfer $persistentCartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\PersistentCartChangeTransfer
     */
    protected function executeCartItemMapperPlugins(
        CartItemRequestTransfer $cartItemRequestTransfer,
        PersistentCartChangeTransfer $persistentCartChangeTransfer
    ): PersistentCartChangeTransfer {
        foreach ($this->cartItemMapperPlugins as $cartItemMapperPlugin) {
            $persistentCartChangeTransfer = $cartItemMapperPlugin->mapCartItemRequestTransferToPersistentCartChangeTransfer(
                $cartItemRequestTransfer,
                $persistentCartChangeTransfer,
            );
        }

        return $persistentCartChangeTransfer;
    }
}
