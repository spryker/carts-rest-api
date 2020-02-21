<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi\Controller;

use Generated\Shared\Transfer\RestCartsAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\Kernel\Controller\AbstractController;

/**
 * @method \Spryker\Glue\CartsRestApi\CartsRestApiFactory getFactory()
 */
class GuestCartsResourceController extends AbstractController
{
    /**
     * @Glue({
     *      "getResourceById": {
     *          "summary": [
     *              "Retrieves a guest cart by id."
     *          ],
     *          "parameters": [{
     *              "name": "X-Anonymous-Customer-Unique-Id",
     *              "in": "header",
     *              "required": true,
     *              "description": "Guest customer unique ID."
     *          }],
     *          "responses": {
     *              "404": "Cart not found."
     *          }
     *     },
     *     "getCollection": {
     *           "summary": [
     *              "Retrieves list of customer's guest carts."
     *          ],
     *          "parameters": [{
     *              "name": "X-Anonymous-Customer-Unique-Id",
     *              "in": "header",
     *              "required": true,
     *              "description": "Guest customer unique ID."
     *          }]
     *     }
     * })
     *
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function getAction(RestRequestInterface $restRequest): RestResponseInterface
    {
        $uuidQuote = $restRequest->getResource()->getId();

        if ($uuidQuote !== null) {
            return $this->getFactory()->createGuestCartReader()->readByIdentifier($uuidQuote, $restRequest);
        }

        return $this->getFactory()->createGuestCartReader()->readCurrentCustomerCarts($restRequest);
    }

    /**
     * @Glue({
     *     "patch": {
     *          "summary": [
     *              "Updates a guest cart."
     *          ],
     *          "parameters": [{
     *              "name": "X-Anonymous-Customer-Unique-Id",
     *              "in": "header",
     *              "required": true,
     *              "description": "Guest customer unique ID."
     *          }],
     *          "responses": {
     *              "400": "Cart id is missing.",
     *              "404": "Cart with given uuid not found.",
     *              "422": "Can’t switch price mode when there are items in the cart."
     *          }
     *     }
     * })
     *
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestCartsAttributesTransfer $restCartsAttributesTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function patchAction(RestRequestInterface $restRequest, RestCartsAttributesTransfer $restCartsAttributesTransfer): RestResponseInterface
    {
        return $this->getFactory()->createGuestCartUpdater()->updateQuote($restRequest, $restCartsAttributesTransfer);
    }
}
