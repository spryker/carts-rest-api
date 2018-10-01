<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi\Plugin;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ControllerBeforeActionPluginInterface;
use Spryker\Glue\Kernel\AbstractPlugin;

/**
 * @method \Spryker\Glue\CartsRestApi\CartsRestApiFactory getFactory()
 */
class GuestCartControllerBeforeActionPlugin extends AbstractPlugin implements ControllerBeforeActionPluginInterface
{
    protected const HEADER_ANONYMOUS_CUSTOMER_UNIQUE_ID = 'X-Anonymous-Customer-Unique-Id';

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $action
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return void
     */
    public function beforeAction(string $action, RestRequestInterface $restRequest): void
    {
        if ($restRequest->getUser()) {
            return;
        }

        $customerReference = $restRequest->getHttpRequest()->headers->get(static::HEADER_ANONYMOUS_CUSTOMER_UNIQUE_ID);

        if (empty($customerReference)) {
            return;
        }

        $customerReference = $this->getFactory()->getPersistentCartClient()->generateGuestCartCustomerReference($customerReference);

        $customerTransfer = (new CustomerTransfer())
            ->setIsDirty(false)
            ->setCustomerReference($customerReference);

        $restRequest->setUser('', $customerReference);

        $this->getFactory()
            ->getCustomerClient()
            ->setCustomer($customerTransfer);
    }
}
