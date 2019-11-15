<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartsRestApi;

use Spryker\Glue\CartsRestApi\Dependency\Client\CartsRestApiToPersistentCartClientBridge;
use Spryker\Glue\Kernel\AbstractBundleDependencyProvider;
use Spryker\Glue\Kernel\Container;

/**
 * @method \Spryker\Glue\CartsRestApi\CartsRestApiConfig getConfig()
 */
class CartsRestApiDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_CART = 'CLIENT_CART';
    public const CLIENT_ZED_REQUEST = 'CLIENT_ZED_REQUEST';
    public const CLIENT_QUOTE = 'CLIENT_QUOTE';
    public const CLIENT_PERSISTENT_CART = 'CLIENT_PERSISTENT_CART';
    public const PLUGINS_CUSTOMER_EXPANDER = 'PLUGINS_CUSTOMER_EXPANDER';
    public const PLUGINS_REST_CART_ITEMS_ATTRIBUTES_MAPPER = 'PLUGINS_REST_CART_ITEMS_ATTRIBUTES_MAPPER';

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);
        $container = $this->addPersistentCartClient($container);
        $container = $this->addCustomerExpanderPlugins($container);
        $container = $this->addItemToRestOrderItemsAttributesMapperPlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addPersistentCartClient(Container $container): Container
    {
        $container[static::CLIENT_PERSISTENT_CART] = function (Container $container) {
            return new CartsRestApiToPersistentCartClientBridge($container->getLocator()->persistentCart()->client());
        };

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addCustomerExpanderPlugins(Container $container): Container
    {
        $container[static::PLUGINS_CUSTOMER_EXPANDER] = function () {
            return $this->getCustomerExpanderPlugins();
        };

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addItemToRestOrderItemsAttributesMapperPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_REST_CART_ITEMS_ATTRIBUTES_MAPPER, function () {
            return $this->getItemToRestCartItemsAttributesMapperPlugins();
        });

        return $container;
    }

    /**
     * @return \Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\CustomerExpanderPluginInterface[]
     */
    protected function getCustomerExpanderPlugins(): array
    {
        return [];
    }

    /**
     * @return \Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\RestCartItemsAttributesMapperPluginInterface[]
     */
    protected function getItemToRestCartItemsAttributesMapperPlugins(): array
    {
        return [];
    }
}
