<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi\Business;

use Spryker\Zed\CartsRestApi\Business\PermissionChecker\QuotePermissionChecker;
use Spryker\Zed\CartsRestApi\Business\PermissionChecker\QuotePermissionCheckerInterface;
use Spryker\Zed\CartsRestApi\Business\Quote\Mapper\QuoteMapper;
use Spryker\Zed\CartsRestApi\Business\Quote\Mapper\QuoteMapperInterface;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteCreator;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteCreatorInterface;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteDeleter;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteDeleterInterface;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteErrorIdentifierAdder;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteErrorIdentifierAdderInterface;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteMerger;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteMergerInterface;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteReader;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteReaderInterface;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteUpdater;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteUpdaterInterface;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteUuidWriter;
use Spryker\Zed\CartsRestApi\Business\Quote\QuoteUuidWriterInterface;
use Spryker\Zed\CartsRestApi\Business\Quote\SingleQuoteCreator;
use Spryker\Zed\CartsRestApi\Business\Quote\SingleQuoteCreatorInterface;
use Spryker\Zed\CartsRestApi\Business\QuoteItem\GuestQuoteItemAdder;
use Spryker\Zed\CartsRestApi\Business\QuoteItem\GuestQuoteItemAdderInterface;
use Spryker\Zed\CartsRestApi\Business\QuoteItem\Mapper\QuoteItemMapper;
use Spryker\Zed\CartsRestApi\Business\QuoteItem\Mapper\QuoteItemMapperInterface;
use Spryker\Zed\CartsRestApi\Business\QuoteItem\QuoteItemAdder;
use Spryker\Zed\CartsRestApi\Business\QuoteItem\QuoteItemAdderInterface;
use Spryker\Zed\CartsRestApi\Business\QuoteItem\QuoteItemDeleter;
use Spryker\Zed\CartsRestApi\Business\QuoteItem\QuoteItemDeleterInterface;
use Spryker\Zed\CartsRestApi\Business\QuoteItem\QuoteItemReader;
use Spryker\Zed\CartsRestApi\Business\QuoteItem\QuoteItemReaderInterface;
use Spryker\Zed\CartsRestApi\Business\QuoteItem\QuoteItemUpdater;
use Spryker\Zed\CartsRestApi\Business\QuoteItem\QuoteItemUpdaterInterface;
use Spryker\Zed\CartsRestApi\Business\Reloader\QuoteReloader;
use Spryker\Zed\CartsRestApi\Business\Reloader\QuoteReloaderInterface;
use Spryker\Zed\CartsRestApi\CartsRestApiDependencyProvider;
use Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToCartFacadeInterface;
use Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToPersistentCartFacadeInterface;
use Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToQuoteFacadeInterface;
use Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToStoreFacadeInterface;
use Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\QuoteCreatorPluginInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \Spryker\Zed\CartsRestApi\Persistence\CartsRestApiEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\CartsRestApi\CartsRestApiConfig getConfig()
 */
class CartsRestApiBusinessFactory extends AbstractBusinessFactory
{
    public function createQuoteUuidWriter(): QuoteUuidWriterInterface
    {
        return new QuoteUuidWriter(
            $this->getEntityManager(),
        );
    }

    public function createQuoteReader(): QuoteReaderInterface
    {
        return new QuoteReader(
            $this->getQuoteFacade(),
            $this->getStoreFacade(),
            $this->createQuotePermissionChecker(),
            $this->createQuoteReloader(),
            $this->getQuoteCollectionExpanderPlugins(),
            $this->getQuoteExpanderPlugins(),
        );
    }

    public function createQuoteCreator(): QuoteCreatorInterface
    {
        return new QuoteCreator(
            $this->getQuoteCreatorPlugin(),
            $this->getStoreFacade(),
            $this->createQuoteErrorIdentifierAdder(),
        );
    }

    public function createSingleQuoteCreator(): SingleQuoteCreatorInterface
    {
        return new SingleQuoteCreator(
            $this->getPersistentCartFacade(),
            $this->createQuoteReader(),
        );
    }

    public function createQuoteDeleter(): QuoteDeleterInterface
    {
        return new QuoteDeleter(
            $this->getPersistentCartFacade(),
            $this->createQuoteReader(),
            $this->createQuotePermissionChecker(),
        );
    }

    public function createQuoteUpdater(): QuoteUpdaterInterface
    {
        return new QuoteUpdater(
            $this->getPersistentCartFacade(),
            $this->getCartFacade(),
            $this->createQuoteReader(),
            $this->createQuoteMapper(),
            $this->createQuotePermissionChecker(),
            $this->createQuoteErrorIdentifierAdder(),
        );
    }

    public function createQuoteMerger(): QuoteMergerInterface
    {
        return new QuoteMerger(
            $this->createQuoteReader(),
            $this->getPersistentCartFacade(),
            $this->getQuoteFacade(),
            $this->getConfig(),
            $this->getQuoteMergePersistentCartChangeExpanderPlugins(),
        );
    }

    public function createQuoteItemAdder(): QuoteItemAdderInterface
    {
        return new QuoteItemAdder(
            $this->getPersistentCartFacade(),
            $this->createQuoteReader(),
            $this->createQuoteItemMapper(),
            $this->createQuotePermissionChecker(),
            $this->createQuoteReloader(),
            $this->getCartItemMapperPlugins(),
        );
    }

    public function createGuestQuoteItemAdder(): GuestQuoteItemAdderInterface
    {
        return new GuestQuoteItemAdder(
            $this->createQuoteReader(),
            $this->createQuoteItemAdder(),
            $this->createQuoteCreator(),
            $this->getStoreFacade(),
        );
    }

    public function createQuoteItemReader(): QuoteItemReaderInterface
    {
        return new QuoteItemReader(
            $this->createQuoteReader(),
            $this->createQuoteItemMapper(),
            $this->getQuoteItemReadValidatorPlugins(),
        );
    }

    public function createQuoteItemDeleter(): QuoteItemDeleterInterface
    {
        return new QuoteItemDeleter(
            $this->getPersistentCartFacade(),
            $this->createQuoteItemReader(),
            $this->createQuotePermissionChecker(),
            $this->createQuoteReloader(),
        );
    }

    public function createQuoteItemUpdater(): QuoteItemUpdaterInterface
    {
        return new QuoteItemUpdater(
            $this->getPersistentCartFacade(),
            $this->createQuoteItemReader(),
            $this->createQuotePermissionChecker(),
            $this->createQuoteReloader(),
        );
    }

    public function createQuoteErrorIdentifierAdder(): QuoteErrorIdentifierAdderInterface
    {
        return new QuoteErrorIdentifierAdder();
    }

    public function createQuotePermissionChecker(): QuotePermissionCheckerInterface
    {
        return new QuotePermissionChecker();
    }

    public function createQuoteItemMapper(): QuoteItemMapperInterface
    {
        return new QuoteItemMapper();
    }

    public function createQuoteMapper(): QuoteMapperInterface
    {
        return new QuoteMapper();
    }

    public function createQuoteReloader(): QuoteReloaderInterface
    {
        return new QuoteReloader(
            $this->getCartFacade(),
            $this->getConfig(),
        );
    }

    public function getQuoteFacade(): CartsRestApiToQuoteFacadeInterface
    {
        return $this->getProvidedDependency(CartsRestApiDependencyProvider::FACADE_QUOTE);
    }

    public function getStoreFacade(): CartsRestApiToStoreFacadeInterface
    {
        return $this->getProvidedDependency(CartsRestApiDependencyProvider::FACADE_STORE);
    }

    public function getPersistentCartFacade(): CartsRestApiToPersistentCartFacadeInterface
    {
        return $this->getProvidedDependency(CartsRestApiDependencyProvider::FACADE_PERSISTENT_CART);
    }

    public function getCartFacade(): CartsRestApiToCartFacadeInterface
    {
        return $this->getProvidedDependency(CartsRestApiDependencyProvider::FACADE_CART);
    }

    public function getQuoteCreatorPlugin(): QuoteCreatorPluginInterface
    {
        return $this->getProvidedDependency(CartsRestApiDependencyProvider::PLUGIN_QUOTE_CREATOR);
    }

    /**
     * @return array<\Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\QuoteCollectionExpanderPluginInterface>
     */
    public function getQuoteCollectionExpanderPlugins(): array
    {
        return $this->getProvidedDependency(CartsRestApiDependencyProvider::PLUGINS_QUOTE_COLLECTION_EXPANDER);
    }

    /**
     * @return array<\Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\QuoteExpanderPluginInterface>
     */
    public function getQuoteExpanderPlugins(): array
    {
        return $this->getProvidedDependency(CartsRestApiDependencyProvider::PLUGINS_QUOTE_EXPANDER);
    }

    /**
     * @return array<\Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\CartItemMapperPluginInterface>
     */
    public function getCartItemMapperPlugins(): array
    {
        return $this->getProvidedDependency(CartsRestApiDependencyProvider::PLUGINS_CART_ITEM_MAPPER);
    }

    /**
     * @return array<\Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\QuoteItemReadValidatorPluginInterface>
     */
    public function getQuoteItemReadValidatorPlugins(): array
    {
        return $this->getProvidedDependency(CartsRestApiDependencyProvider::PLUGINS_QUOTE_ITEM_READ_VALIDATOR);
    }

    /**
     * @return array<int, \Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\QuoteMergePersistentCartChangeExpanderPluginInterface>
     */
    public function getQuoteMergePersistentCartChangeExpanderPlugins(): array
    {
        return $this->getProvidedDependency(CartsRestApiDependencyProvider::PLUGINS_QUOTE_MERGE_PERSISTENT_CART_EXPANDER);
    }
}
