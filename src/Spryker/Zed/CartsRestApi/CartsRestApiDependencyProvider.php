<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartsRestApi;

use Orm\Zed\Quote\Persistence\SpyQuoteQuery;
use Spryker\Zed\CartsRestApi\Communication\Plugin\CartsRestApi\QuoteCreatorPlugin;
use Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToCartFacadeBridge;
use Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToPersistentCartFacadeBridge;
use Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToQuoteFacadeBridge;
use Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToStoreFacadeBridge;
use Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\QuoteCreatorPluginInterface;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\CartsRestApi\CartsRestApiConfig getConfig()
 */
class CartsRestApiDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_QUOTE = 'FACADE_QUOTE';

    /**
     * @var string
     */
    public const FACADE_PERSISTENT_CART = 'FACADE_PERSISTENT_CART';

    /**
     * @var string
     */
    public const FACADE_CART = 'FACADE_CART';

    /**
     * @var string
     */
    public const FACADE_STORE = 'FACADE_STORE';

    /**
     * @var string
     */
    public const PROPEL_QUERY_QUOTE = 'PROPEL_QUERY_QUOTE';

    /**
     * @var string
     */
    public const PLUGINS_CART_ITEM_MAPPER = 'PLUGINS_CART_ITEM_MAPPER';

    /**
     * @var string
     */
    public const PLUGIN_QUOTE_CREATOR = 'PLUGIN_QUOTE_CREATOR';

    /**
     * @var string
     */
    public const PLUGINS_QUOTE_COLLECTION_EXPANDER = 'PLUGINS_QUOTE_COLLECTION_EXPANDER';

    /**
     * @var string
     */
    public const PLUGINS_QUOTE_EXPANDER = 'PLUGINS_QUOTE_EXPANDER';

    /**
     * @var string
     */
    public const PLUGINS_QUOTE_ITEM_READ_VALIDATOR = 'PLUGINS_QUOTE_ITEM_READ_VALIDATOR';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addQuoteFacade($container);
        $container = $this->addPersistentCartFacade($container);
        $container = $this->addCartFacade($container);
        $container = $this->addStoreFacade($container);
        $container = $this->addQuoteCreatorPlugin($container);
        $container = $this->addQuoteCollectionExpanderPlugins($container);
        $container = $this->addQuoteExpanderPlugins($container);
        $container = $this->addCartItemMapperPlugins($container);
        $container = $this->addQuoteItemReadValidatorPlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function providePersistenceLayerDependencies(Container $container): Container
    {
        $container = parent::providePersistenceLayerDependencies($container);
        $container = $this->addQuotePropelQuery($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addQuotePropelQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_QUOTE, $container->factory(function () {
            return SpyQuoteQuery::create();
        }));

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addQuoteFacade(Container $container): Container
    {
        $container->set(static::FACADE_QUOTE, function (Container $container) {
            return new CartsRestApiToQuoteFacadeBridge($container->getLocator()->quote()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPersistentCartFacade(Container $container): Container
    {
        $container->set(static::FACADE_PERSISTENT_CART, function (Container $container) {
            return new CartsRestApiToPersistentCartFacadeBridge($container->getLocator()->persistentCart()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCartFacade(Container $container): Container
    {
        $container->set(static::FACADE_CART, function (Container $container) {
            return new CartsRestApiToCartFacadeBridge($container->getLocator()->cart()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStoreFacade(Container $container): Container
    {
        $container->set(static::FACADE_STORE, function (Container $container) {
            return new CartsRestApiToStoreFacadeBridge($container->getLocator()->store()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addQuoteCreatorPlugin(Container $container): Container
    {
        $container->set(static::PLUGIN_QUOTE_CREATOR, function () {
            return $this->getQuoteCreatorPlugin();
        });

        return $container;
    }

    /**
     * @return \Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\QuoteCreatorPluginInterface
     */
    protected function getQuoteCreatorPlugin(): QuoteCreatorPluginInterface
    {
        return new QuoteCreatorPlugin();
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addQuoteCollectionExpanderPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_QUOTE_COLLECTION_EXPANDER, function () {
            return $this->getQuoteCollectionExpanderPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\QuoteCollectionExpanderPluginInterface>
     */
    protected function getQuoteCollectionExpanderPlugins(): array
    {
        return [];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addQuoteExpanderPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_QUOTE_EXPANDER, function () {
            return $this->getQuoteExpanderPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\QuoteExpanderPluginInterface>
     */
    protected function getQuoteExpanderPlugins(): array
    {
        return [];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCartItemMapperPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_CART_ITEM_MAPPER, function () {
            return $this->getCartItemMapperPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\CartItemMapperPluginInterface>
     */
    protected function getCartItemMapperPlugins(): array
    {
        return [];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addQuoteItemReadValidatorPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_QUOTE_ITEM_READ_VALIDATOR, function () {
            return $this->getQuoteItemReadValidatorPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\QuoteItemReadValidatorPluginInterface>
     */
    protected function getQuoteItemReadValidatorPlugins(): array
    {
        return [];
    }
}
