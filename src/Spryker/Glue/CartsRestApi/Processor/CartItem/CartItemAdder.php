<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi\Processor\CartItem;

use Generated\Shared\Transfer\CartItemRequestTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\RestCartItemsAttributesTransfer;
use Spryker\Client\CartsRestApi\CartsRestApiClientInterface;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Glue\CartsRestApi\Processor\RestResponseBuilder\CartRestResponseBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class CartItemAdder implements CartItemAdderInterface
{
    /**
     * @var \Spryker\Client\CartsRestApi\CartsRestApiClientInterface
     */
    protected $cartsRestApiClient;

    /**
     * @var \Spryker\Glue\CartsRestApi\Processor\RestResponseBuilder\CartRestResponseBuilderInterface
     */
    protected $cartRestResponseBuilder;

    /**
     * @var array<\Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\CustomerExpanderPluginInterface>
     */
    protected $customerExpanderPlugins;

    /**
     * @var array<\Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\CartItemExpanderPluginInterface>
     */
    protected $cartItemExpanderPlugins;

    /**
     * @param \Spryker\Client\CartsRestApi\CartsRestApiClientInterface $cartsRestApiClient
     * @param \Spryker\Glue\CartsRestApi\Processor\RestResponseBuilder\CartRestResponseBuilderInterface $cartRestResponseBuilder
     * @param array<\Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\CustomerExpanderPluginInterface> $customerExpanderPlugins
     * @param array<\Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\CartItemExpanderPluginInterface> $cartItemExpanderPlugins
     */
    public function __construct(
        CartsRestApiClientInterface $cartsRestApiClient,
        CartRestResponseBuilderInterface $cartRestResponseBuilder,
        array $customerExpanderPlugins,
        array $cartItemExpanderPlugins
    ) {
        $this->cartsRestApiClient = $cartsRestApiClient;
        $this->cartRestResponseBuilder = $cartRestResponseBuilder;
        $this->customerExpanderPlugins = $customerExpanderPlugins;
        $this->cartItemExpanderPlugins = $cartItemExpanderPlugins;
    }

    public function addItem(
        RestRequestInterface $restRequest,
        RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer
    ): RestResponseInterface {
        if (!$this->findCartIdentifier($restRequest)) {
            return $this->cartRestResponseBuilder->createCartIdMissingErrorResponse();
        }

        $cartItemRequestTransfer = $this->createCartItemRequestTransfer($restRequest, $restCartItemsAttributesTransfer);

        $quoteResponseTransfer = $this->cartsRestApiClient->addToCart($cartItemRequestTransfer);
        if (!$quoteResponseTransfer->getIsSuccessful()) {
            return $this->cartRestResponseBuilder->createFailedErrorResponse($quoteResponseTransfer->getErrors());
        }

        return $this->cartRestResponseBuilder->createCartRestResponse(
            $quoteResponseTransfer->getQuoteTransfer(),
            $restRequest->getMetadata()->getLocale(),
        );
    }

    protected function findCartIdentifier(RestRequestInterface $restRequest): ?string
    {
        $cartsResource = $restRequest->findParentResourceByType(CartsRestApiConfig::RESOURCE_CARTS);
        if ($cartsResource) {
            return $cartsResource->getId();
        }

        return null;
    }

    protected function executeCustomerExpanderPlugins(CustomerTransfer $customerTransfer, RestRequestInterface $restRequest): CustomerTransfer
    {
        foreach ($this->customerExpanderPlugins as $customerExpanderPlugin) {
            $customerTransfer = $customerExpanderPlugin->expand($customerTransfer, $restRequest);
        }

        return $customerTransfer;
    }

    protected function executeCartItemExpanderPlugins(
        CartItemRequestTransfer $cartItemRequestTransfer,
        RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer
    ): CartItemRequestTransfer {
        foreach ($this->cartItemExpanderPlugins as $cartItemExpanderPlugin) {
            $cartItemRequestTransfer = $cartItemExpanderPlugin->expand(
                $cartItemRequestTransfer,
                $restCartItemsAttributesTransfer,
            );
        }

        return $cartItemRequestTransfer;
    }

    protected function createCartItemRequestTransfer(
        RestRequestInterface $restRequest,
        RestCartItemsAttributesTransfer $restCartItemsAttributesTransfer
    ): CartItemRequestTransfer {
        $customerTransfer = (new CustomerTransfer())
            ->setIdCustomer($restRequest->getRestUser()->getSurrogateIdentifier())
            ->setCustomerReference($restRequest->getRestUser()->getNaturalIdentifier());
        $customerTransfer = $this->executeCustomerExpanderPlugins($customerTransfer, $restRequest);

        $cartItemRequestTransfer = (new CartItemRequestTransfer())
            ->setQuoteUuid($this->findCartIdentifier($restRequest))
            ->setQuantity($restCartItemsAttributesTransfer->getQuantity())
            ->setSku($restCartItemsAttributesTransfer->getSku())
            ->setCustomer($customerTransfer);

        return $this->executeCartItemExpanderPlugins($cartItemRequestTransfer, $restCartItemsAttributesTransfer);
    }
}
