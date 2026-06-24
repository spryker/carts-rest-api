<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi;

use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\Glue\Kernel\AbstractBundleConfig;
use Spryker\Shared\CartsRestApi\CartsRestApiConfig as CartsRestApiSharedConfig;
use Symfony\Component\HttpFoundation\Response;

class CartsRestApiConfig extends AbstractBundleConfig
{
    /**
     * @api
     *
     * @var string
     */
    public const RESOURCE_CARTS = 'carts';

    /**
     * @api
     *
     * @var string
     */
    public const RESOURCE_CART_ITEMS = 'items';

    /**
     * @api
     *
     * @uses \Spryker\Glue\CustomersRestApi\CustomersRestApiConfig::RESOURCE_CUSTOMERS
     *
     * @var string
     */
    public const RESOURCE_CUSTOMERS = 'customers';

    /**
     * @api
     *
     * @var string
     */
    public const RESOURCE_GUEST_CARTS = 'guest-carts';

    /**
     * @api
     *
     * @var string
     */
    public const RESOURCE_GUEST_CARTS_ITEMS = 'guest-cart-items';

    /**
     * @api
     *
     * @var string
     */
    public const CONTROLLER_CARTS = 'carts-resource';

    /**
     * @api
     *
     * @var string
     */
    public const CONTROLLER_CART_ITEMS = 'cart-items-resource';

    /**
     * @api
     *
     * @var string
     */
    public const CONTROLLER_GUEST_CARTS = 'guest-carts-resource';

    /**
     * @api
     *
     * @var string
     */
    public const CONTROLLER_GUEST_CART_ITEMS = 'guest-cart-items-resource';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_CART_NOT_FOUND = '101';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_ITEM_VALIDATION = '102';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_ITEM_NOT_FOUND = '103';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_CART_ID_MISSING = '104';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_FAILED_DELETING_CART = '105';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_FAILED_DELETING_CART_ITEM = '106';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_FAILED_CREATING_CART = '107';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_ANONYMOUS_CUSTOMER_UNIQUE_ID_EMPTY = '109';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_CUSTOMER_ALREADY_HAS_CART = '110';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_CART_CANT_BE_UPDATED = '111';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_STORE_DATA_IS_INVALID = '112';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_FAILED_ADDING_CART_ITEM = '113';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_FAILED_UPDATING_CART_ITEM = '114';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_UNAUTHORIZED_CART_ACTION = '115';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_CURRENCY_DATA_IS_MISSING = '116';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_CURRENCY_DATA_IS_INCORRECT = '117';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_PRICE_MODE_DATA_IS_MISSING = '118';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_PRICE_MODE_DATA_IS_INCORRECT = '119';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_CUSTOMER_UNAUTHORIZED = '802';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_CART_ID_MISSING = 'Cart uuid is missing.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_CART_ITEM_NOT_FOUND = 'Item with the given group key not found in the cart.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_FAILED_TO_CREATE_CART = 'Failed to create cart.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_CART_WITH_ID_NOT_FOUND = 'Cart with given uuid not found.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_FAILED_DELETING_CART = 'Cart could not be deleted.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_FAILED_DELETING_CART_ITEM = 'Cart item could not be deleted.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_ANONYMOUS_CUSTOMER_UNIQUE_ID_EMPTY = 'Anonymous customer unique id is empty.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_CUSTOMER_ALREADY_HAS_CART = 'Customer already has a cart.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_PRICE_MODE_CANT_BE_CHANGED = 'Can’t switch price mode when there are items in the cart.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_STORE_DATA_IS_INVALID = 'Store data is invalid.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_FAILED_ADDING_CART_ITEM = 'Cart item could not be added.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_FAILED_UPDATING_CART_ITEM = 'Cart item could not be updated.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_UNAUTHORIZED_CART_ACTION = 'Unauthorized cart action.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_CURRENCY_DATA_IS_MISSING = 'Currency is missing.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_CURRENCY_DATA_IS_INCORRECT = 'Currency is incorrect.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_PRICE_MODE_DATA_IS_MISSING = 'Price mode is missing.';

    /**
     * @api
     *
     * @var string
     */
    public const EXCEPTION_MESSAGE_PRICE_MODE_DATA_IS_INCORRECT = 'Price mode is incorrect.';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_DETAILS_CUSTOMER_UNAUTHORIZED = 'Unauthorized request.';

    /**
     * @api
     *
     * @var string
     */
    public const HEADER_ANONYMOUS_CUSTOMER_UNIQUE_ID = 'X-Anonymous-Customer-Unique-Id';

    /**
     * @api
     *
     * @var string
     */
    public const HEADER_AUTHORIZATION = 'authorization';

    /**
     * @var array<string>
     */
    protected const GUEST_CART_RESOURCES = [
        self::RESOURCE_GUEST_CARTS,
        self::RESOURCE_GUEST_CARTS_ITEMS,
    ];

    /**
     * @var bool
     */
    protected const ALLOWED_CART_ITEM_EAGER_RELATIONSHIP = true;

    /**
     * @var bool
     */
    protected const ALLOWED_GUEST_CART_ITEM_EAGER_RELATIONSHIP = true;

    /**
     * @api
     *
     * @return array<string, array<string, mixed>>
     */
    public function getErrorIdentifierToRestErrorMapping(): array
    {
        return [
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_CART_NOT_FOUND => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_CART_NOT_FOUND,
                RestErrorMessageTransfer::STATUS => Response::HTTP_NOT_FOUND,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_CART_WITH_ID_NOT_FOUND,
            ],
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_FAILED_CREATING_CART => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_FAILED_CREATING_CART,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_FAILED_TO_CREATE_CART,
            ],
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_CART_CANT_BE_UPDATED => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_CART_CANT_BE_UPDATED,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_PRICE_MODE_CANT_BE_CHANGED,
            ],
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_ITEM_NOT_FOUND => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_ITEM_NOT_FOUND,
                RestErrorMessageTransfer::STATUS => Response::HTTP_NOT_FOUND,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_CART_ITEM_NOT_FOUND,
            ],
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_FAILED_DELETING_CART => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_FAILED_DELETING_CART,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_FAILED_DELETING_CART,
            ],
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_FAILED_ADDING_CART_ITEM => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_FAILED_ADDING_CART_ITEM,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_FAILED_ADDING_CART_ITEM,
            ],
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_FAILED_UPDATING_CART_ITEM => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_FAILED_UPDATING_CART_ITEM,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_FAILED_UPDATING_CART_ITEM,
            ],
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_FAILED_DELETING_CART_ITEM => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_FAILED_DELETING_CART_ITEM,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_FAILED_DELETING_CART_ITEM,
            ],
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_ANONYMOUS_CUSTOMER_UNIQUE_ID_EMPTY => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_ANONYMOUS_CUSTOMER_UNIQUE_ID_EMPTY,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_ANONYMOUS_CUSTOMER_UNIQUE_ID_EMPTY,
            ],
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_CUSTOMER_ALREADY_HAS_CART => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_CUSTOMER_ALREADY_HAS_CART,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_CUSTOMER_ALREADY_HAS_CART,
            ],
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_STORE_DATA_IS_INVALID => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_STORE_DATA_IS_INVALID,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_STORE_DATA_IS_INVALID,
            ],
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_UNAUTHORIZED_CART_ACTION => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_UNAUTHORIZED_CART_ACTION,
                RestErrorMessageTransfer::STATUS => Response::HTTP_FORBIDDEN,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_UNAUTHORIZED_CART_ACTION,
            ],
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_CURRENCY_DATA_IS_MISSING => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_CURRENCY_DATA_IS_MISSING,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_CURRENCY_DATA_IS_MISSING,
            ],
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_CURRENCY_DATA_IS_INCORRECT => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_CURRENCY_DATA_IS_INCORRECT,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_CURRENCY_DATA_IS_INCORRECT,
            ],
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_PRICE_MODE_DATA_IS_INCORRECT => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_PRICE_MODE_DATA_IS_INCORRECT,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_PRICE_MODE_DATA_IS_INCORRECT,
            ],
            CartsRestApiSharedConfig::ERROR_IDENTIFIER_PRICE_MODE_DATA_IS_MISSING => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_PRICE_MODE_DATA_IS_MISSING,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_PRICE_MODE_DATA_IS_MISSING,
            ],
        ];
    }

    /**
     * @api
     *
     * @return array<string>
     */
    public function getGuestCartResources(): array
    {
        return static::GUEST_CART_RESOURCES;
    }

    /**
     * Specification:
     * - Returns true if `carts` resource should automatically get `items` relationship.
     *
     * @api
     *
     * @return bool
     */
    public function getAllowedCartItemEagerRelationship(): bool
    {
        return static::ALLOWED_CART_ITEM_EAGER_RELATIONSHIP;
    }

    /**
     * Specification:
     * - Returns true if `guest-carts` resource should automatically get `guest-cart-items` relationship.
     *
     * @api
     *
     * @return bool
     */
    public function getAllowedGuestCartItemEagerRelationship(): bool
    {
        return static::ALLOWED_GUEST_CART_ITEM_EAGER_RELATIONSHIP;
    }
}
