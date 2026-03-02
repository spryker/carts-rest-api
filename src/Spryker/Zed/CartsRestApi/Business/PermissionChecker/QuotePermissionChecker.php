<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Business\PermissionChecker;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Kernel\PermissionAwareTrait;

class QuotePermissionChecker implements QuotePermissionCheckerInterface
{
    use PermissionAwareTrait;

    /**
     * @uses \Spryker\Client\SharedCart\Plugin\ReadSharedCartPermissionPlugin::KEY
     *
     * @var string
     */
    protected const PERMISSION_PLUGIN_KEY_READ_SHARED_CART = 'ReadSharedCartPermissionPlugin';

    /**
     * @uses \Spryker\Client\SharedCart\Plugin\WriteSharedCartPermissionPlugin::KEY
     *
     * @var string
     */
    protected const PERMISSION_PLUGIN_KEY_WRITE_SHARED_CART = 'WriteSharedCartPermissionPlugin';

    public function checkQuoteReadPermission(QuoteTransfer $quoteTransfer): bool
    {
        return $this->checkQuotePermission($quoteTransfer, static::PERMISSION_PLUGIN_KEY_READ_SHARED_CART);
    }

    public function checkQuoteWritePermission(QuoteTransfer $quoteTransfer): bool
    {
        return $this->checkQuotePermission($quoteTransfer, static::PERMISSION_PLUGIN_KEY_WRITE_SHARED_CART);
    }

    protected function checkQuotePermission(QuoteTransfer $quoteTransfer, string $permissionPluginKey): bool
    {
        $quoteTransfer->requireIdQuote();

        if (!$quoteTransfer->getCustomer()) {
            return false;
        }

        if ($quoteTransfer->getCustomer()->getCustomerReference() === $quoteTransfer->getCustomerReference()) {
            return true;
        }

        if (
            !$quoteTransfer->getCustomer()->getCompanyUserTransfer()
            || !$quoteTransfer->getCustomer()->getCompanyUserTransfer()->getIdCompanyUser()
        ) {
            return false;
        }

        return $this->can(
            $permissionPluginKey,
            $quoteTransfer->getCustomer()->getCompanyUserTransfer()->getIdCompanyUser(),
            $quoteTransfer->getIdQuote(),
        );
    }
}
