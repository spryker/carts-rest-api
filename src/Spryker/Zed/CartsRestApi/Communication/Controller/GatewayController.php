<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Communication\Controller;

use Generated\Shared\Transfer\QuoteByIdCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \Spryker\Zed\CartsRestApi\Business\CartsRestApiFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param \Generated\Shared\Transfer\QuoteByIdCriteriaFilterTransfer $quoteByIdCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function findQuoteByUuidAction(QuoteByIdCriteriaFilterTransfer $quoteByIdCriteriaFilterTransfer): QuoteResponseTransfer
    {
        return $this->getFacade()->findQuoteByUuid(
            $quoteByIdCriteriaFilterTransfer->getUuid(),
            $quoteByIdCriteriaFilterTransfer->getQuoteCriteriaFilter()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteCriteriaFilterTransfer $quoteCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    public function getQuoteCollectionByCriteriaAction(QuoteCriteriaFilterTransfer $quoteCriteriaFilterTransfer): QuoteCollectionTransfer
    {
        return $this->getFacade()->getQuoteCollectionByCriteria($quoteCriteriaFilterTransfer);
    }
}
