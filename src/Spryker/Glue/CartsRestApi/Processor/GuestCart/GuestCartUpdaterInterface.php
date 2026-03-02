<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi\Processor\GuestCart;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\RestCartsAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

interface GuestCartUpdaterInterface
{
    public function updateGuestCartCustomerReferenceOnCreate(
        RestRequestInterface $restRequest,
        CustomerTransfer $customerTransfer
    ): CustomerTransfer;

    public function updateQuote(RestRequestInterface $restRequest, RestCartsAttributesTransfer $restCartsAttributesTransfer): RestResponseInterface;
}
