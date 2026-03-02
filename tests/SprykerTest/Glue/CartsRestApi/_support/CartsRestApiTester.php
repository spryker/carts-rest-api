<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\CartsRestApi;

use ArrayObject;
use Codeception\Actor;
use Generated\Shared\Transfer\DiscountTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsAttributesTransfer;
use Generated\Shared\Transfer\RestCartsDiscountsTransfer;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class CartsRestApiTester extends Actor
{
    use _generated\CartsRestApiTesterActions;

    public function assertCartsResource(RestResourceInterface $actualCartsRestResource, QuoteTransfer $quoteTransfer): void
    {
        /** @var \Generated\Shared\Transfer\RestCartsAttributesTransfer $actualCartsRestResourceAttributesTransfer */
        $actualCartsRestResourceAttributesTransfer = $actualCartsRestResource->getAttributes();

        $this->assertCartsResourceStructure($quoteTransfer, $actualCartsRestResource);
        $this->assertCartsBaseAttributes($quoteTransfer, $actualCartsRestResourceAttributesTransfer);
        $this->assertCartsDiscountsAttributes($quoteTransfer, $actualCartsRestResourceAttributesTransfer);
        $this->assertCartsTotalsAttributes($quoteTransfer, $actualCartsRestResourceAttributesTransfer);
    }

    public function assertGuestCartsResource(RestResourceInterface $actualCartsRestResource, QuoteTransfer $quoteTransfer): void
    {
        /** @var \Generated\Shared\Transfer\RestCartsAttributesTransfer $actualCartsRestResourceAttributesTransfer */
        $actualCartsRestResourceAttributesTransfer = $actualCartsRestResource->getAttributes();

        $this->assertGuestCartsResourceStructure($quoteTransfer, $actualCartsRestResource);
        $this->assertCartsBaseAttributes($quoteTransfer, $actualCartsRestResourceAttributesTransfer);
        $this->assertCartsDiscountsAttributes($quoteTransfer, $actualCartsRestResourceAttributesTransfer);
        $this->assertCartsTotalsAttributes($quoteTransfer, $actualCartsRestResourceAttributesTransfer);
    }

    protected function assertCartsBaseAttributes(
        QuoteTransfer $quoteTransfer,
        RestCartsAttributesTransfer $actualCartsRestResourceAttributesTransfer
    ): void {
        $this->assertSame($quoteTransfer->getStore()->getName(), $actualCartsRestResourceAttributesTransfer->getStore());
        $this->assertSame($quoteTransfer->getCurrency()->getCode(), $actualCartsRestResourceAttributesTransfer->getCurrency());
        $this->assertSame($quoteTransfer->getIsDefault(), $actualCartsRestResourceAttributesTransfer->getIsDefault());
        $this->assertSame($quoteTransfer->getPriceMode(), $actualCartsRestResourceAttributesTransfer->getPriceMode());
    }

    protected function assertCartsDiscountsAttributes(
        QuoteTransfer $quoteTransfer,
        RestCartsAttributesTransfer $actualCartsRestResourceAttributesTransfer
    ): void {
        foreach ($actualCartsRestResourceAttributesTransfer->getDiscounts() as $actualRestCartsDiscountsTransfer) {
            $foundDiscount = $this->assertDiscountIfFound(
                $quoteTransfer->getVoucherDiscounts(),
                $actualRestCartsDiscountsTransfer,
            );

            if ($foundDiscount) {
                continue;
            }

            $foundDiscount = $this->assertDiscountIfFound(
                $quoteTransfer->getCartRuleDiscounts(),
                $actualRestCartsDiscountsTransfer,
            );

            if (!$foundDiscount) {
                $this->fail('Rest attributes discount must be found among either cart rules or voucher discounts');
            }
        }
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\DiscountTransfer> $expectedDiscountTransfers
     * @param \Generated\Shared\Transfer\RestCartsDiscountsTransfer $actualRestCartsDiscountsTransfer
     *
     * @return bool
     */
    protected function assertDiscountIfFound(
        ArrayObject $expectedDiscountTransfers,
        RestCartsDiscountsTransfer $actualRestCartsDiscountsTransfer
    ): bool {
        foreach ($expectedDiscountTransfers as $expectedDiscountTransfer) {
            if ($actualRestCartsDiscountsTransfer->getCode() !== $expectedDiscountTransfer->getVoucherCode()) {
                return false;
            }

            $this->assertDiscountAttributes($expectedDiscountTransfer, $actualRestCartsDiscountsTransfer);
        }

        return true;
    }

    protected function assertDiscountAttributes(
        DiscountTransfer $expectedDiscountTransfer,
        RestCartsDiscountsTransfer $actualRestCartsDiscountsTransfer
    ): void {
        $this->assertSame($expectedDiscountTransfer->getVoucherCode(), $actualRestCartsDiscountsTransfer->getCode());
        $this->assertSame($expectedDiscountTransfer->getAmount(), $actualRestCartsDiscountsTransfer->getAmount());
        $this->assertSame($expectedDiscountTransfer->getDisplayName(), $actualRestCartsDiscountsTransfer->getDisplayName());
    }

    protected function assertCartsTotalsAttributes(
        QuoteTransfer $quoteTransfer,
        RestCartsAttributesTransfer $actualCartsRestResourceAttributesTransfer
    ): void {
        $expectedRestCartsTotalsTransfer = $actualCartsRestResourceAttributesTransfer->getTotals();
        $expectedTotalsTransfer = $quoteTransfer->getTotals();
        $this->assertSame($expectedTotalsTransfer->getDiscountTotal(), $expectedRestCartsTotalsTransfer->getDiscountTotal());
        $this->assertSame($expectedTotalsTransfer->getExpenseTotal(), $expectedRestCartsTotalsTransfer->getExpenseTotal());
        $this->assertSame($expectedTotalsTransfer->getGrandTotal(), $expectedRestCartsTotalsTransfer->getGrandTotal());
        $this->assertSame($expectedTotalsTransfer->getPriceToPay(), $expectedRestCartsTotalsTransfer->getPriceToPay());
        $this->assertSame($expectedTotalsTransfer->getSubtotal(), $expectedRestCartsTotalsTransfer->getSubtotal());
        $this->assertSame($expectedTotalsTransfer->getTaxTotal(), $expectedRestCartsTotalsTransfer->getTaxTotal());
    }

    protected function assertCartsResourceStructure(QuoteTransfer $quoteTransfer, RestResourceInterface $actualCartsRestResource): void
    {
        $this->assertSame($quoteTransfer->getUuid(), $actualCartsRestResource->getId());
        $this->assertSame(CartsRestApiConfig::RESOURCE_CARTS, $actualCartsRestResource->getType());
        $this->assertSame($quoteTransfer, $actualCartsRestResource->getPayload());
    }

    protected function assertGuestCartsResourceStructure(QuoteTransfer $quoteTransfer, RestResourceInterface $actualCartsRestResource): void
    {
        $this->assertSame($quoteTransfer->getUuid(), $actualCartsRestResource->getId());
        $this->assertSame(CartsRestApiConfig::RESOURCE_GUEST_CARTS, $actualCartsRestResource->getType());
        $this->assertSame($quoteTransfer, $actualCartsRestResource->getPayload());
    }
}
