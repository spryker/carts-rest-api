<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi\Plugin\GlueApplication;

use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRelationshipPluginInterface;
use Spryker\Glue\Kernel\AbstractPlugin;

/**
 * @Glue({
 *     "resourceAttributesClassName": "\\Generated\\Shared\\Transfer\\RestItemsAttributesTransfer"
 * })
 *
 * @method \Spryker\Glue\CartsRestApi\CartsRestApiFactory getFactory()
 */
class GuestCartItemsByQuoteResourceRelationshipPlugin extends AbstractPlugin implements ResourceRelationshipPluginInterface
{
    /**
     * {@inheritDoc}
     * - Adds `guest-cart-items` resource as relationship by quote.
     * - Requires `QuoteTransfer` to be provided in resource payload.
     *
     * @api
     *
     * @param array<\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface> $resources
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return void
     */
    public function addResourceRelationships(array $resources, RestRequestInterface $restRequest): void
    {
        $this->getFactory()
            ->createCartItemByQuoteResourceRelationshipExpander()
            ->addGuestCartItemResourceRelationships($resources, $restRequest);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string
     */
    public function getRelationshipResourceType(): string
    {
        return CartsRestApiConfig::RESOURCE_GUEST_CARTS_ITEMS;
    }
}
