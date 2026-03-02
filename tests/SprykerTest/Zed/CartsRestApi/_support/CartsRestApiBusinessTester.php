<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CartsRestApi;

use Codeception\Actor;
use Generated\Shared\DataBuilder\AssignGuestQuoteRequestBuilder;
use Generated\Shared\DataBuilder\CartItemRequestBuilder;
use Generated\Shared\DataBuilder\OauthResponseBuilder;
use Generated\Shared\DataBuilder\QuoteBuilder;
use Generated\Shared\DataBuilder\QuoteCollectionBuilder;
use Generated\Shared\DataBuilder\QuoteCriteriaFilterBuilder;
use Generated\Shared\DataBuilder\QuoteResponseBuilder;
use Generated\Shared\DataBuilder\RestCartItemsAttributesBuilder;
use Generated\Shared\Transfer\AssignGuestQuoteRequestTransfer;
use Generated\Shared\Transfer\CartItemRequestTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\OauthResponseTransfer;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemsAttributesTransfer;

/**
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 * @method \Spryker\Zed\CartsRestApi\Business\CartsRestApiFacadeInterface getFacade()
 *
 * @SuppressWarnings(PHPMD)
 */
class CartsRestApiBusinessTester extends Actor
{
    use _generated\CartsRestApiBusinessTesterActions;

    /**
     * @var int
     */
    public const TEST_ID_QUOTE = 67238;

    /**
     * @var string
     */
    public const TEST_QUOTE_UUID = 'test-quote-uuid';

    /**
     * @var string
     */
    public const TEST_CUSTOMER_REFERENCE = 'DE--666';

    /**
     * @var string
     */
    public const TEST_ANONYMOUS_CUSTOMER_REFERENCE = 'anonymous:DE--666';

    /**
     * @var string
     */
    public const TEST_QUANTITY = '3';

    /**
     * @var string
     */
    public const TEST_SKU = 'test-sku';

    /**
     * @var string
     */
    public const TEST_SKU2 = 'test-sku2';

    /**
     * @var array
     */
    public const COLLECTION_QUOTES = [
        [
            'id_quote' => 1,
            'name' => 'Shopping cart',
            'store' => 'DE',
            'priceMode' => 'GROSS_MODE',
            'currency' => 'EUR',
            'customerReference' => 'tester-de',
            'uuid' => '7fd5cc11-87ff-55e2-b413-7e07f9640404',

        ],
        [
            'id_quote' => 2,
            'name' => 'test quote two',
            'store' => 'DE',
            'priceMode' => 'GROSS_MODE',
            'currency' => 'EUR',
            'customerReference' => 'tester-de',
            'uuid' => '22b43a18-e46c-55bf-bc00-65f4dee0727a',
        ],
    ];

    /**
     * @var array
     */
    public const ITEMS = [
        [
            'sku' => 'test sku',
            'quantity' => '666',

        ],
        [
            'sku' => 'test sku 2',
            'quantity' => '666',
        ],
    ];

    public function prepareQuoteResponseTransfer(): QuoteResponseTransfer
    {
        return (new QuoteResponseBuilder([
            'isSuccessful' => true,
            'quoteTransfer' => (new QuoteBuilder(['customer' => new CustomerTransfer()]))->build(),
        ]))->build();
    }

    public function prepareQuoteResponseTransferWithQuote(): QuoteResponseTransfer
    {
        $quoteOverride = [
            'uuid' => static::TEST_QUOTE_UUID,
            'customerReference' => static::TEST_CUSTOMER_REFERENCE,
            'idQuote' => static::TEST_ID_QUOTE,
        ];
        $itemOverride = ['groupKey' => static::TEST_SKU, 'sku' => static::TEST_SKU];
        $item2Override = ['groupKey' => static::TEST_SKU2, 'sku' => static::TEST_SKU2];

        return (new QuoteResponseBuilder(['isSuccessful' => true]))
            ->withQuoteTransfer(
                (new QuoteBuilder($quoteOverride))
                    ->withItem($itemOverride)
                    ->withAnotherItem($item2Override),
            )
            ->build();
    }

    public function prepareQuoteResponseTransferWithoutQuote(): QuoteResponseTransfer
    {
        return (new QuoteResponseBuilder(['isSuccessful' => false]))->build();
    }

    public function prepareQuoteCriteriaFilterTransfer(): QuoteCriteriaFilterTransfer
    {
        return (new QuoteCriteriaFilterBuilder(['customerReference' => static::TEST_CUSTOMER_REFERENCE]))
            ->build();
    }

    public function prepareQuoteCriteriaFilterTransferForGuest(): QuoteCriteriaFilterTransfer
    {
        return (new QuoteCriteriaFilterBuilder(['customerReference' => static::TEST_ANONYMOUS_CUSTOMER_REFERENCE]))
            ->build();
    }

    public function prepareEmptyQuoteCriteriaFilterTransfer(): QuoteCriteriaFilterTransfer
    {
        return (new QuoteCriteriaFilterBuilder())->build();
    }

    public function prepareQuoteTransfer(): QuoteTransfer
    {
        return (new QuoteBuilder(
            [
                'uuid' => static::TEST_QUOTE_UUID,
                'customerReference' => static::TEST_CUSTOMER_REFERENCE,
                'customer' => (new CustomerTransfer())->setCustomerReference(static::TEST_CUSTOMER_REFERENCE),
            ],
        ))->build();
    }

    public function prepareQuoteTransferForGuest(): QuoteTransfer
    {
        return (new QuoteBuilder(
            [
                'uuid' => static::TEST_QUOTE_UUID,
                'customerReference' => static::TEST_ANONYMOUS_CUSTOMER_REFERENCE,
                'customer' => (new CustomerTransfer())->setCustomerReference(static::TEST_ANONYMOUS_CUSTOMER_REFERENCE),
                'items' => static::ITEMS,
            ],
        ))->build();
    }

    public function prepareEmptyQuoteTransferForGuest(): QuoteTransfer
    {
        return (new QuoteBuilder(
            [
                'uuid' => static::TEST_QUOTE_UUID,
                'customerReference' => static::TEST_ANONYMOUS_CUSTOMER_REFERENCE,
                'customer' => (new CustomerTransfer())->setCustomerReference(static::TEST_ANONYMOUS_CUSTOMER_REFERENCE),
            ],
        ))->build();
    }

    public function prepareQuoteTransferWithoutCustomer(): QuoteTransfer
    {
        return (new QuoteBuilder(
            [
                'uuid' => static::TEST_QUOTE_UUID,
                'customerReference' => static::TEST_CUSTOMER_REFERENCE,
            ],
        ))->build();
    }

    public function prepareRestCartItemsAttributesTransferWithoutQuantity(): RestCartItemsAttributesTransfer
    {
        return (new RestCartItemsAttributesBuilder(
            [
                'quoteUuid' => static::TEST_QUOTE_UUID,
                'customerReference' => static::TEST_CUSTOMER_REFERENCE,
                'sku' => static::TEST_SKU,
            ],
        ))->build();
    }

    public function prepareRestCartItemsAttributesTransferWithQuantity(): RestCartItemsAttributesTransfer
    {
        $restCartItemsAttributesTransfer = (new RestCartItemsAttributesBuilder(
            [
                'quoteUuid' => static::COLLECTION_QUOTES[0]['uuid'],
                'customerReference' => static::TEST_CUSTOMER_REFERENCE,
                'sku' => static::TEST_SKU,
                'groupKey' => static::TEST_SKU,
                'quantity' => static::TEST_QUANTITY,
            ],
        ))->build();

        $restCartItemsAttributesTransfer
            ->setCustomer((new CustomerTransfer())->setCustomerReference($restCartItemsAttributesTransfer->getCustomerReference()));

        return $restCartItemsAttributesTransfer;
    }

    public function prepareRestCartItemsAttributesTransferForSecondItem(): RestCartItemsAttributesTransfer
    {
        $restCartItemsAttributesTransfer = (new RestCartItemsAttributesBuilder(
            [
                'quoteUuid' => static::COLLECTION_QUOTES[0]['uuid'],
                'customerReference' => static::TEST_CUSTOMER_REFERENCE,
                'sku' => static::TEST_SKU2,
                'groupKey' => static::TEST_SKU2,
                'quantity' => static::TEST_QUANTITY,
            ],
        ))->build();

        $restCartItemsAttributesTransfer
            ->setCustomer((new CustomerTransfer())->setCustomerReference($restCartItemsAttributesTransfer->getCustomerReference()));

        return $restCartItemsAttributesTransfer;
    }

    public function prepareRestCartItemsAttributesTransferWithoutCustomerReference(): RestCartItemsAttributesTransfer
    {
        return (new RestCartItemsAttributesBuilder(
            [
                'quoteUuid' => static::TEST_QUOTE_UUID,
                'sku' => static::TEST_SKU,
                'quantity' => static::TEST_QUANTITY,
            ],
        ))->build();
    }

    public function prepareCartItemRequestTransferWithQuantity(): CartItemRequestTransfer
    {
        return (new CartItemRequestBuilder(
            [
                'quoteUuid' => static::COLLECTION_QUOTES[0]['uuid'],
                'quantity' => static::TEST_QUANTITY,
                'customer' => (new CustomerTransfer())->setCustomerReference(static::TEST_CUSTOMER_REFERENCE),
                'sku' => static::TEST_SKU,
            ],
        ))->build();
    }

    public function prepareCartItemRequestTransferWithoutCustomer(): CartItemRequestTransfer
    {
        return (new CartItemRequestBuilder(
            [
                'quoteUuid' => static::TEST_QUOTE_UUID,
                'quantity' => static::TEST_QUANTITY,
                'sku' => static::TEST_SKU,
            ],
        ))->build();
    }

    public function prepareOauthResponseTransfer(): OauthResponseTransfer
    {
        return (new OauthResponseBuilder(
            [
                'customerReference' => static::TEST_CUSTOMER_REFERENCE,
                'anonymousCustomerReference' => static::TEST_ANONYMOUS_CUSTOMER_REFERENCE,
            ],
        ))->build();
    }

    public function prepareOauthResponseTransferWithoutCustomerReference(): OauthResponseTransfer
    {
        return (new OauthResponseBuilder(
            [
                'anonymousCustomerReference' => static::TEST_ANONYMOUS_CUSTOMER_REFERENCE,
            ],
        ))->build();
    }

    public function prepareOauthResponseTransferWithoutAnonymousCustomerReference(): OauthResponseTransfer
    {
        return (new OauthResponseBuilder(
            [
                'customerReference' => static::TEST_CUSTOMER_REFERENCE,
            ],
        ))->build();
    }

    public function prepareCartItemRequestTransferWithoutQuantity(): CartItemRequestTransfer
    {
        return (new CartItemRequestBuilder(
            [
                'quoteUuid' => static::TEST_QUOTE_UUID,
                'customer' => (new CustomerTransfer())->setCustomerReference(static::TEST_CUSTOMER_REFERENCE),
                'sku' => static::TEST_SKU,
            ],
        ))->build();
    }

    public function prepareCartItemRequestTransferWithoutUuid(): CartItemRequestTransfer
    {
        return (new CartItemRequestBuilder(
            [
                'sku' => static::TEST_SKU,
                'customer' => (new CustomerTransfer())->setCustomerReference(static::TEST_CUSTOMER_REFERENCE),
                'quantity' => static::TEST_QUANTITY,
            ],
        ))->build();
    }

    public function prepareCartItemRequestTransferWithoutSku(): CartItemRequestTransfer
    {
        return (new CartItemRequestBuilder(
            [
                'quoteUuid' => static::TEST_QUOTE_UUID,
                'customer' => (new CustomerTransfer())->setCustomerReference(static::TEST_CUSTOMER_REFERENCE),
                'quantity' => static::TEST_QUANTITY,
            ],
        ))->build();
    }

    public function prepareAssignGuestQuoteRequestTransfer(): AssignGuestQuoteRequestTransfer
    {
        return (new AssignGuestQuoteRequestBuilder(
            [
                'anonymousCustomerReference' => static::TEST_ANONYMOUS_CUSTOMER_REFERENCE,
                'customerReference' => static::TEST_CUSTOMER_REFERENCE,
            ],
        ))->build();
    }

    public function prepareAssignGuestQuoteRequestTransferWithoutCustomerReference(): AssignGuestQuoteRequestTransfer
    {
        return (new AssignGuestQuoteRequestBuilder(
            [
                'anonymousCustomerReference' => static::TEST_ANONYMOUS_CUSTOMER_REFERENCE,
            ],
        ))->build();
    }

    public function prepareAssignGuestQuoteRequestTransferWithoutAnonymousCustomerReference(): AssignGuestQuoteRequestTransfer
    {
        return (new AssignGuestQuoteRequestBuilder(
            [
                'customerReference' => static::TEST_CUSTOMER_REFERENCE,
            ],
        ))->build();
    }

    public function prepareRestCartItemsAttributesTransferWithoutSku(): RestCartItemsAttributesTransfer
    {
        return (new RestCartItemsAttributesBuilder(
            [
                'quoteUuid' => static::TEST_QUOTE_UUID,
                'customerReference' => static::TEST_CUSTOMER_REFERENCE,
                'quantity' => static::TEST_QUANTITY,
            ],
        ))->build();
    }

    public function prepareRestCartItemsAttributesTransferWithoutUuid(): RestCartItemsAttributesTransfer
    {
        return (new RestCartItemsAttributesBuilder(
            [
                'sku' => static::TEST_SKU,
                'customerReference' => static::TEST_CUSTOMER_REFERENCE,
                'quantity' => static::TEST_QUANTITY,
            ],
        ))->build();
    }

    public function prepareQuoteTransferWithoutCustomerReference(): QuoteTransfer
    {
        return (new QuoteBuilder(['uuid' => static::TEST_QUOTE_UUID]))->build();
    }

    public function prepareQuoteTransferWithoutCartUuid(): QuoteTransfer
    {
        return (new QuoteBuilder(['customerReference' => static::TEST_CUSTOMER_REFERENCE]))->build();
    }

    public function prepareEmptyQuoteCollectionTransfer(): QuoteCollectionTransfer
    {
        return (new QuoteCollectionBuilder())->build();
    }

    public function prepareQuotesCollectionTransfer(): QuoteCollectionTransfer
    {
        $quoteCollectionTransfer = new QuoteCollectionTransfer();
        foreach (static::COLLECTION_QUOTES as $quote) {
            $quoteCollectionTransfer->addQuote((new QuoteTransfer())->fromArray($quote));
        }

        return $quoteCollectionTransfer;
    }

    public function buildQuoteTransfer(CustomerTransfer $customerTransfer): QuoteTransfer
    {
        return (new QuoteBuilder(
            [
                'customerReference' => $customerTransfer->getCustomerReference(),
                'customer' => $customerTransfer,
            ],
        ))->build();
    }

    public function buildOauthResponseTransfer(string $customerReference): OauthResponseTransfer
    {
        return (new OauthResponseBuilder(
            [
                'customerReference' => $customerReference,
                'anonymousCustomerReference' => static::TEST_ANONYMOUS_CUSTOMER_REFERENCE,
            ],
        ))->build();
    }

    public function buildQuoteCriteriaFilterTransfer(string $customerReference): QuoteCriteriaFilterTransfer
    {
        return (new QuoteCriteriaFilterBuilder(
            [
                'customerReference' => $customerReference,
            ],
        ))->build();
    }

    public function createQuoteCriteriaFilterTransfer(string $customerReference): QuoteCriteriaFilterTransfer
    {
        return (new QuoteCriteriaFilterTransfer())->setCustomerReference($customerReference);
    }
}
