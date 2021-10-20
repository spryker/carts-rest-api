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
use Spryker\Client\CartsRestApi\Dependency\Client\CartsRestApiToZedRequestClientInterface;

class CartsRestApiZedStub implements CartsRestApiZedStubInterface
{
    /**
     * @var \Spryker\Client\CartsRestApi\Dependency\Client\CartsRestApiToZedRequestClientInterface
     */
    protected $zedRequestClient;

    /**
     * @param \Spryker\Client\CartsRestApi\Dependency\Client\CartsRestApiToZedRequestClientInterface $zedRequestClient
     */
    public function __construct(CartsRestApiToZedRequestClientInterface $zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * @uses \Spryker\Zed\CartsRestApi\Communication\Controller\GatewayController::findQuoteByUuidAction()
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function findQuoteByUuid(QuoteTransfer $quoteTransfer): QuoteResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer */
        $quoteResponseTransfer = $this->zedRequestClient->call('/carts-rest-api/gateway/find-quote-by-uuid', $quoteTransfer);

        return $quoteResponseTransfer;
    }

    /**
     * @uses \Spryker\Zed\CartsRestApi\Communication\Controller\GatewayController::findQuoteByUuidAction()
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function findQuoteByUuidWithQuoteItemReload(QuoteTransfer $quoteTransfer): QuoteResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer */
        $quoteResponseTransfer = $this->zedRequestClient->call(
            '/carts-rest-api/gateway/find-quote-by-uuid-with-quote-item-reload',
            $quoteTransfer,
        );

        return $quoteResponseTransfer;
    }

    /**
     * @uses \Spryker\Zed\CartsRestApi\Communication\Controller\GatewayController::getQuoteCollectionAction()
     *
     * @param \Generated\Shared\Transfer\QuoteCriteriaFilterTransfer $quoteCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    public function getQuoteCollection(QuoteCriteriaFilterTransfer $quoteCriteriaFilterTransfer): QuoteCollectionTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteCollectionTransfer $quoteCollectionTransfer */
        $quoteCollectionTransfer = $this->zedRequestClient
            ->call('/carts-rest-api/gateway/get-quote-collection', $quoteCriteriaFilterTransfer);

        return $quoteCollectionTransfer;
    }

    /**
     * @uses \Spryker\Zed\CartsRestApi\Communication\Controller\GatewayController::updateQuoteAction()
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function updateQuote(QuoteTransfer $quoteTransfer): QuoteResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer */
        $quoteResponseTransfer = $this->zedRequestClient->call('/carts-rest-api/gateway/update-quote', $quoteTransfer);

        return $quoteResponseTransfer;
    }

    /**
     * @uses \Spryker\Zed\CartsRestApi\Communication\Controller\GatewayController::createQuoteAction()
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function createQuote(QuoteTransfer $quoteTransfer): QuoteResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer */
        $quoteResponseTransfer = $this->zedRequestClient->call('/carts-rest-api/gateway/create-quote', $quoteTransfer);

        return $quoteResponseTransfer;
    }

    /**
     * @uses \Spryker\Zed\CartsRestApi\Communication\Controller\GatewayController::deleteQuoteAction()
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function deleteQuote(QuoteTransfer $quoteTransfer): QuoteResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer */
        $quoteResponseTransfer = $this->zedRequestClient->call('/carts-rest-api/gateway/delete-quote', $quoteTransfer);

        return $quoteResponseTransfer;
    }

    /**
     * @uses \Spryker\Zed\CartsRestApi\Communication\Controller\GatewayController::updateItemAction()
     *
     * @deprecated Use {@link updateItemQuantity()} instead.
     *
     * @param \Generated\Shared\Transfer\RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function updateItem(RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer): QuoteResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer */
        $quoteResponseTransfer = $this->zedRequestClient->call('/carts-rest-api/gateway/update-item', $restCartItemsAttributesTransfer);

        return $quoteResponseTransfer;
    }

    /**
     * @uses \Spryker\Zed\CartsRestApi\Communication\Controller\GatewayController::updateItemQuantityAction()
     *
     * @param \Generated\Shared\Transfer\CartItemRequestTransfer $cartItemRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function updateItemQuantity(CartItemRequestTransfer $cartItemRequestTransfer): QuoteResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer */
        $quoteResponseTransfer = $this->zedRequestClient->call('/carts-rest-api/gateway/update-item-quantity', $cartItemRequestTransfer);

        return $quoteResponseTransfer;
    }

    /**
     * @uses \Spryker\Zed\CartsRestApi\Communication\Controller\GatewayController::addItemAction()
     *
     * @deprecated Use {@link addToCart()} instead.
     *
     * @param \Generated\Shared\Transfer\RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function addItem(RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer): QuoteResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer */
        $quoteResponseTransfer = $this->zedRequestClient->call('/carts-rest-api/gateway/add-item', $restCartItemsAttributesTransfer);

        return $quoteResponseTransfer;
    }

    /**
     * @uses \Spryker\Zed\CartsRestApi\Communication\Controller\GatewayController::addToCartAction()
     *
     * @param \Generated\Shared\Transfer\CartItemRequestTransfer $cartItemRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function addToCart(CartItemRequestTransfer $cartItemRequestTransfer): QuoteResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer */
        $quoteResponseTransfer = $this->zedRequestClient->call('/carts-rest-api/gateway/add-to-cart', $cartItemRequestTransfer);

        return $quoteResponseTransfer;
    }

    /**
     * @uses \Spryker\Zed\CartsRestApi\Communication\Controller\GatewayController::deleteItemAction()
     *
     * @deprecated Use {@link removeItem()} instead.
     *
     * @param \Generated\Shared\Transfer\RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function deleteItem(RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer): QuoteResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer */
        $quoteResponseTransfer = $this->zedRequestClient->call('/carts-rest-api/gateway/delete-item', $restCartItemsAttributesTransfer);

        return $quoteResponseTransfer;
    }

    /**
     * @uses \Spryker\Zed\CartsRestApi\Communication\Controller\GatewayController::removeItemAction()
     *
     * @param \Generated\Shared\Transfer\CartItemRequestTransfer $cartItemRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function removeItem(CartItemRequestTransfer $cartItemRequestTransfer): QuoteResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer */
        $quoteResponseTransfer = $this->zedRequestClient->call('/carts-rest-api/gateway/remove-item', $cartItemRequestTransfer);

        return $quoteResponseTransfer;
    }

    /**
     * @uses \Spryker\Zed\CartsRestApi\Communication\Controller\GatewayController::addItemToGuestCartAction()
     *
     * @param \Generated\Shared\Transfer\RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function addItemToGuestCart(RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer): QuoteResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer */
        $quoteResponseTransfer = $this->zedRequestClient->call('/carts-rest-api/gateway/add-item-to-guest-cart', $restCartItemsAttributesTransfer);

        return $quoteResponseTransfer;
    }

    /**
     * @uses \Spryker\Zed\CartsRestApi\Communication\Controller\GatewayController::addToGuestCartAction()
     *
     * @param \Generated\Shared\Transfer\CartItemRequestTransfer $cartItemRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function addToGuestCart(CartItemRequestTransfer $cartItemRequestTransfer): QuoteResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer */
        $quoteResponseTransfer = $this->zedRequestClient->call('/carts-rest-api/gateway/add-to-guest-cart', $cartItemRequestTransfer);

        return $quoteResponseTransfer;
    }

    /**
     * @uses \Spryker\Zed\CartsRestApi\Communication\Controller\GatewayController::assignGuestCartToRegisteredCustomerAction()
     *
     * @param \Generated\Shared\Transfer\AssignGuestQuoteRequestTransfer $assignGuestQuoteRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function assignGuestCartToRegisteredCustomer(AssignGuestQuoteRequestTransfer $assignGuestQuoteRequestTransfer): QuoteResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer */
        $quoteResponseTransfer = $this->zedRequestClient->call('/carts-rest-api/gateway/assign-guest-cart-to-registered-customer', $assignGuestQuoteRequestTransfer);

        return $quoteResponseTransfer;
    }
}
