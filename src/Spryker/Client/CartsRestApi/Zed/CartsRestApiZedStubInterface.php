<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\CartsRestApi\Zed;

use Generated\Shared\Transfer\AssignGuestQuoteRequestTransfer;
use Generated\Shared\Transfer\CartItemRequestTransfer;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemsAttributesTransfer;

interface CartsRestApiZedStubInterface
{
    public function findQuoteByUuid(QuoteTransfer $quoteTransfer): QuoteResponseTransfer;

    public function findQuoteByUuidWithQuoteItemReload(QuoteTransfer $quoteTransfer): QuoteResponseTransfer;

    public function getQuoteCollection(QuoteCriteriaFilterTransfer $quoteCriteriaFilterTransfer): QuoteCollectionTransfer;

    public function updateQuote(QuoteTransfer $quoteTransfer): QuoteResponseTransfer;

    public function createQuote(QuoteTransfer $quoteTransfer): QuoteResponseTransfer;

    public function deleteQuote(QuoteTransfer $quoteTransfer): QuoteResponseTransfer;

    /**
     * @deprecated Use {@link updateItemQuantity()} instead.
     *
     * @param \Generated\Shared\Transfer\RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function updateItem(RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer): QuoteResponseTransfer;

    public function updateItemQuantity(CartItemRequestTransfer $cartItemRequestTransfer): QuoteResponseTransfer;

    /**
     * @deprecated Use {@link addToCart()} instead().
     *
     * @param \Generated\Shared\Transfer\RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function addItem(RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer): QuoteResponseTransfer;

    public function addToCart(CartItemRequestTransfer $cartItemRequestTransfer): QuoteResponseTransfer;

    /**
     * @deprecated Use {@link removeItem()} instead.
     *
     * @param \Generated\Shared\Transfer\RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function deleteItem(RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer): QuoteResponseTransfer;

    public function removeItem(CartItemRequestTransfer $cartItemRequestTransfer): QuoteResponseTransfer;

    /**
     * @deprecated Use {@link addToGuestCart()} instead.
     *
     * @param \Generated\Shared\Transfer\RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function addItemToGuestCart(RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer): QuoteResponseTransfer;

    public function addToGuestCart(CartItemRequestTransfer $cartItemRequestTransfer): QuoteResponseTransfer;

    public function assignGuestCartToRegisteredCustomer(AssignGuestQuoteRequestTransfer $assignGuestQuoteRequestTransfer): QuoteResponseTransfer;
}
