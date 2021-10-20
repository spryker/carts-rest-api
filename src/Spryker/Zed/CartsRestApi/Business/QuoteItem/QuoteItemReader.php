<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Business\QuoteItem;

use Generated\Shared\Transfer\CartItemRequestTransfer;
use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\CartsRestApi\CartsRestApiConfig as CartsRestApiSharedConfig;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteReaderInterface;
use Spryker\Zed\CartsRestApi\Business\QuoteItem\Mapper\QuoteItemMapperInterface;

class QuoteItemReader implements QuoteItemReaderInterface
{
    /**
     * @var \Spryker\Zed\CartsRestApi\Business\Quote\QuoteReaderInterface
     */
    protected $quoteReader;

    /**
     * @var \Spryker\Zed\CartsRestApi\Business\QuoteItem\Mapper\QuoteItemMapperInterface
     */
    protected $quoteItemMapper;

    /**
     * @var array<\Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\QuoteItemReadValidatorPluginInterface>
     */
    protected $quoteItemReadValidatorPlugins;

    /**
     * @param \Spryker\Zed\CartsRestApi\Business\Quote\QuoteReaderInterface $quoteReader
     * @param \Spryker\Zed\CartsRestApi\Business\QuoteItem\Mapper\QuoteItemMapperInterface $quoteItemMapper
     * @param array<\Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\QuoteItemReadValidatorPluginInterface> $quoteItemReadValidatorPlugins
     */
    public function __construct(
        QuoteReaderInterface $quoteReader,
        QuoteItemMapperInterface $quoteItemMapper,
        array $quoteItemReadValidatorPlugins
    ) {
        $this->quoteReader = $quoteReader;
        $this->quoteItemMapper = $quoteItemMapper;
        $this->quoteItemReadValidatorPlugins = $quoteItemReadValidatorPlugins;
    }

    /**
     * @param \Generated\Shared\Transfer\CartItemRequestTransfer $cartItemRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function readItem(CartItemRequestTransfer $cartItemRequestTransfer): QuoteResponseTransfer
    {
        $quoteTransfer = $this->quoteItemMapper->mapCartItemsRequestTransferToQuoteTransfer(
            $cartItemRequestTransfer,
            new QuoteTransfer(),
        );

        $quoteResponseTransfer = $this->quoteReader->findQuoteByUuid($quoteTransfer);
        if (!$quoteResponseTransfer->getIsSuccessful()) {
            return $quoteResponseTransfer;
        }

        $isRequestedItemInQuote = $this->isCartItemInQuote(
            $cartItemRequestTransfer,
            $quoteResponseTransfer->getQuoteTransfer(),
        );

        if (!$isRequestedItemInQuote) {
            $quoteResponseTransfer
                ->setIsSuccessful(false)
                ->addError((new QuoteErrorTransfer())
                    ->setErrorIdentifier(CartsRestApiSharedConfig::ERROR_IDENTIFIER_ITEM_NOT_FOUND));
        }

        return $quoteResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CartItemRequestTransfer $cartItemRequestTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    protected function isCartItemInQuote(CartItemRequestTransfer $cartItemRequestTransfer, QuoteTransfer $quoteTransfer): bool
    {
        foreach ($this->quoteItemReadValidatorPlugins as $quoteItemReadValidatorPlugin) {
            if ($quoteItemReadValidatorPlugin->validate($cartItemRequestTransfer, $quoteTransfer)) {
                return true;
            }
        }

        return $this->isItemInQuote($cartItemRequestTransfer, $quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\CartItemRequestTransfer $cartItemRequestTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    protected function isItemInQuote(CartItemRequestTransfer $cartItemRequestTransfer, QuoteTransfer $quoteTransfer): bool
    {
        if (!$quoteTransfer->getItems()->count()) {
            return false;
        }

        $itemFound = false;
        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            if ($cartItemRequestTransfer->getGroupKey()) {
                $itemFound = $itemTransfer->getGroupKey() === $cartItemRequestTransfer->getGroupKey();
            }

            if (!$itemFound && $itemTransfer->getSku() === $cartItemRequestTransfer->getSku()) {
                $itemFound = true;
            }

            if ($itemFound && !$itemTransfer->getRelatedBundleItemIdentifier()) {
                return true;
            }
        }

        return false;
    }
}
