<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi\Processor\Cart;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsAttributesTransfer;
use Spryker\Client\CartsRestApi\CartsRestApiClientInterface;
use Spryker\Glue\CartsRestApi\Processor\Mapper\CartMapperInterface;
use Spryker\Glue\CartsRestApi\Processor\RestResponseBuilder\CartRestResponseBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class CartUpdater implements CartUpdaterInterface
{
    /**
     * @var \Spryker\Client\CartsRestApi\CartsRestApiClientInterface
     */
    protected $cartsRestApiClient;

    /**
     * @var \Spryker\Glue\CartsRestApi\Processor\Mapper\CartMapperInterface
     */
    protected $cartMapper;

    /**
     * @var \Spryker\Glue\CartsRestApi\Processor\RestResponseBuilder\CartRestResponseBuilderInterface
     */
    protected $cartRestResponseBuilder;

    /**
     * @var array<\Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\CustomerExpanderPluginInterface>
     */
    protected $customerExpanderPlugins;

    /**
     * @param \Spryker\Client\CartsRestApi\CartsRestApiClientInterface $cartsRestApiClient
     * @param \Spryker\Glue\CartsRestApi\Processor\Mapper\CartMapperInterface $cartMapper
     * @param \Spryker\Glue\CartsRestApi\Processor\RestResponseBuilder\CartRestResponseBuilderInterface $cartRestResponseBuilder
     * @param array<\Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\CustomerExpanderPluginInterface> $customerExpanderPlugins
     */
    public function __construct(
        CartsRestApiClientInterface $cartsRestApiClient,
        CartMapperInterface $cartMapper,
        CartRestResponseBuilderInterface $cartRestResponseBuilder,
        array $customerExpanderPlugins
    ) {
        $this->cartsRestApiClient = $cartsRestApiClient;
        $this->cartMapper = $cartMapper;
        $this->cartRestResponseBuilder = $cartRestResponseBuilder;
        $this->customerExpanderPlugins = $customerExpanderPlugins;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestCartsAttributesTransfer $restCartsAttributesTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function update(
        RestRequestInterface $restRequest,
        RestCartsAttributesTransfer $restCartsAttributesTransfer
    ): RestResponseInterface {
        $restUser = $restRequest->getRestUser();
        $customerTransfer = (new CustomerTransfer())
            ->setIdCustomer($restUser->getSurrogateIdentifier())
            ->setCustomerReference($restUser->getNaturalIdentifier());
        $customerTransfer = $this->executeCustomerExpanderPlugins($customerTransfer, $restRequest);
        $quoteTransfer = $this->cartMapper->mapRestCartsAttributesTransferToQuoteTransfer(
            $restCartsAttributesTransfer,
            (new QuoteTransfer())->setCustomerReference($restUser->getNaturalIdentifier()),
        );

        $quoteTransfer
            ->setUuid($restRequest->getResource()->getId())
            ->setCompanyUserId($restUser->getIdCompanyUser())
            ->setCustomerReference($restUser->getNaturalIdentifier())
            ->setCustomer($customerTransfer);

        $quoteResponseTransfer = $this->cartsRestApiClient->updateQuote($quoteTransfer);
        if (!$quoteResponseTransfer->getIsSuccessful()) {
            return $this->cartRestResponseBuilder->createFailedErrorResponse($quoteResponseTransfer->getErrors());
        }

        return $this->cartRestResponseBuilder->createCartRestResponse(
            $quoteResponseTransfer->getQuoteTransfer(),
            $restRequest->getMetadata()->getLocale(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function executeCustomerExpanderPlugins(CustomerTransfer $customerTransfer, RestRequestInterface $restRequest): CustomerTransfer
    {
        foreach ($this->customerExpanderPlugins as $customerExpanderPlugin) {
            $customerTransfer = $customerExpanderPlugin->expand($customerTransfer, $restRequest);
        }

        return $customerTransfer;
    }
}
