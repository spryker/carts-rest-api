<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CartsRestApi\Business;

use Codeception\Stub;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OauthResponseTransfer;
use Generated\Shared\Transfer\PersistentCartChangeTransfer;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Zed\CartsRestApi\Business\CartsRestApiBusinessFactory;
use Spryker\Zed\CartsRestApi\Business\CartsRestApiFacade;
use Spryker\Zed\CartsRestApi\Business\CartsRestApiFacadeInterface;
use Spryker\Zed\CartsRestApi\CartsRestApiConfig;
use Spryker\Zed\CartsRestApi\CartsRestApiDependencyProvider;
use Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToPersistentCartFacadeInterface;
use Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToQuoteFacadeInterface;
use Spryker\Zed\CartsRestApiExtension\Dependency\Plugin\QuoteMergePersistentCartChangeExpanderPluginInterface;
use Spryker\Zed\ProductBundleCartsRestApi\Communication\Plugin\CartsRestApi\BundleItemQuoteMergePersistentCartChangeExpanderPlugin;
use Spryker\Zed\Quote\QuoteDependencyProvider;
use Spryker\Zed\QuoteExtension\Dependency\Plugin\QuoteWritePluginInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group CartsRestApi
 * @group Business
 * @group MergeGuestQuoteAndCustomerQuoteTest
 * Add your own group annotations below this line
 */
class MergeGuestQuoteAndCustomerQuoteTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\CartsRestApi\CartsRestApiBusinessTester
     */
    protected $tester;

    /**
     * @var \Spryker\Zed\CartsRestApi\Business\CartsRestApiFacadeInterface
     */
    protected $cartsRestApiFacade;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        /*
         * There is a current Store in context of RestApi usage
         */
        $this->tester->addCurrentStore($this->tester->haveStore([StoreTransfer::NAME => 'DE']));
    }

    /**
     * @return void
     */
    public function testGuestQuoteAndCustomerQuoteWillBeMerged(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $customerQuoteTransfer = $this->tester->buildQuoteTransfer($customerTransfer);
        $createGuestQuoteResponseTransfer = $this->tester->getFacade()
            ->createQuote($this->tester->prepareQuoteTransferForGuest());
        $createQuoteResponseTransfer = $this->tester->getFacade()->createQuote($customerQuoteTransfer);
        $quoteTransfer = $createQuoteResponseTransfer->getQuoteTransfer();
        $oauthResponseTransfer = $this->tester->buildOauthResponseTransfer($customerTransfer->getCustomerReference());

        // Act
        $this->tester->getFacade()->mergeGuestQuoteAndCustomerQuote($oauthResponseTransfer);
        $guestQuoteCollectionTransfer = $this->tester->getFacade()
            ->getQuoteCollection($this->tester->prepareQuoteCriteriaFilterTransferForGuest());
        $findQuoteResponseTransfer = $this->tester->getFacade()->findQuoteByUuid($quoteTransfer);

        // Assert
        $this->assertTrue($findQuoteResponseTransfer->getIsSuccessful());
        $this->assertNotEmpty($findQuoteResponseTransfer->getQuoteTransfer()->getItems());
        $this->assertNotEquals(
            $findQuoteResponseTransfer->getQuoteTransfer()->getCustomerReference(),
            $createGuestQuoteResponseTransfer->getQuoteTransfer()->getCustomerReference(),
        );
        $this->assertEmpty($findQuoteResponseTransfer->getErrors());
        $this->assertEmpty($guestQuoteCollectionTransfer->getQuotes());
    }

    public function testMergeGuestQuoteAndCustomerQuoteWillRunMergerPlugins(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $customerQuoteTransfer = $this->tester->buildQuoteTransfer($customerTransfer);
        $customerQuoteTransfer->setIdQuote(1);
        $guestQuoteTransfer = $this->tester->prepareQuoteTransferForGuest();

        $oauthResponseTransfer = $this->tester->buildOauthResponseTransfer($customerTransfer->getCustomerReference());

        $this->tester->setDependency(
            CartsRestApiDependencyProvider::FACADE_QUOTE,
            $this->createQuoteFacadeCartsRestApiMock($customerQuoteTransfer, $guestQuoteTransfer, $oauthResponseTransfer),
        );

        $this->tester->setDependency(
            CartsRestApiDependencyProvider::FACADE_PERSISTENT_CART,
            $this->createPersistentCartFacadeMock($customerQuoteTransfer, $guestQuoteTransfer),
        );

        $quoteMergePersistentCartChangeExpanderPluginMock = $this->createMock(QuoteMergePersistentCartChangeExpanderPluginInterface::class);
        $quoteMergePersistentCartChangeExpanderPluginMock
            ->expects($this->once())
            ->method('expand')
            ->with($this->isInstanceOf(PersistentCartChangeTransfer::class), $this->isInstanceOf(QuoteTransfer::class))
            ->willReturn(new PersistentCartChangeTransfer());

        $this->tester->setDependency(
            CartsRestApiDependencyProvider::PLUGINS_QUOTE_MERGE_PERSISTENT_CART_EXPANDER,
            [
                $quoteMergePersistentCartChangeExpanderPluginMock,
            ],
        );

        // Act and Assert
        (new CartsRestApiFacade())->mergeGuestQuoteAndCustomerQuote($oauthResponseTransfer);
    }

    public function testGuestQuoteWithProductBundleAndCustomerQuoteWillBeMergedCorrectly(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $customerQuoteTransfer = $this->tester->buildQuoteTransfer($customerTransfer);
        $customerQuoteTransfer->setIdQuote(1);
        $guestQuoteTransfer = $this->tester->prepareQuoteTransferForGuest();
        $guestQuoteTransfer->addBundleItem((new ItemTransfer())->setSku('BUNDLE')->setBundleItemIdentifier('BUNDLE')->setQuantity(1));

        $oauthResponseTransfer = $this->tester->buildOauthResponseTransfer($customerTransfer->getCustomerReference());

        $this->tester->setDependency(
            CartsRestApiDependencyProvider::FACADE_QUOTE,
            $this->createQuoteFacadeCartsRestApiMock($customerQuoteTransfer, $guestQuoteTransfer, $oauthResponseTransfer),
        );
        $this->tester->setDependency(
            CartsRestApiDependencyProvider::FACADE_PERSISTENT_CART,
            $this->createPersistentCartFacadeMockWithBundle($customerQuoteTransfer, $guestQuoteTransfer),
        );
        $this->tester->setDependency(
            CartsRestApiDependencyProvider::PLUGINS_QUOTE_MERGE_PERSISTENT_CART_EXPANDER,
            [
                new BundleItemQuoteMergePersistentCartChangeExpanderPlugin(),
            ],
        );

        // Act and Assert
        (new CartsRestApiFacade())->mergeGuestQuoteAndCustomerQuote($oauthResponseTransfer);
    }

    /**
     * @return void
     */
    public function testGuestQuoteAndCustomerQuoteWillNotBeMergedWithoutCustomerReference(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $customerQuoteTransfer = $this->tester->buildQuoteTransfer($customerTransfer);
        $createQuestQuoteResponseTransfer = $this->tester->getFacade()
            ->createQuote($this->tester->prepareQuoteTransferForGuest());
        $createQuoteResponseTransfer = $this->tester->getFacade()->createQuote($customerQuoteTransfer);
        $oauthResponseTransfer = $this->tester->prepareOauthResponseTransferWithoutCustomerReference();

        // Act
        $this->tester->getFacade()->mergeGuestQuoteAndCustomerQuote($oauthResponseTransfer);
        $findGuestQuoteResponseTransfer = $this->tester->getFacade()
            ->findQuoteByUuid($createQuestQuoteResponseTransfer->getQuoteTransfer());
        $findQuoteResponseTransfer = $this->tester->getFacade()
            ->findQuoteByUuid($createQuoteResponseTransfer->getQuoteTransfer());

        // Assert
        $this->assertEmpty($findQuoteResponseTransfer->getQuoteTransfer()->getItems());
        $this->assertTrue($findGuestQuoteResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testGuestQuoteAndCustomerQuoteWillNotBeMergedWithoutAnonymousCustomerReference(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $customerQuoteTransfer = $this->tester->buildQuoteTransfer($customerTransfer);
        $createQuestQuoteResponseTransfer = $this->tester->getFacade()
            ->createQuote($this->tester->prepareQuoteTransferForGuest());
        $createQuoteResponseTransfer = $this->tester->getFacade()->createQuote($customerQuoteTransfer);
        $oauthResponseTransfer = $this->tester->prepareOauthResponseTransferWithoutAnonymousCustomerReference();

        // Act
        $this->tester->getFacade()->mergeGuestQuoteAndCustomerQuote($oauthResponseTransfer);
        $findGuestQuoteResponseTransfer = $this->tester->getFacade()
            ->findQuoteByUuid($createQuestQuoteResponseTransfer->getQuoteTransfer());
        $findQuoteResponseTransfer = $this->tester->getFacade()
            ->findQuoteByUuid($createQuoteResponseTransfer->getQuoteTransfer());

        // Assert
        $this->assertEmpty($findQuoteResponseTransfer->getQuoteTransfer()->getItems());
        $this->assertTrue($findGuestQuoteResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testEmptyGuestQuoteAndCustomerQuoteWillNotBeMerged(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $customerQuoteTransfer = $this->tester->buildQuoteTransfer($customerTransfer);
        $createQuestQuoteResponseTransfer = $this->tester->getFacade()
            ->createQuote($this->tester->prepareEmptyQuoteTransferForGuest());
        $createQuoteResponseTransfer = $this->tester->getFacade()->createQuote($customerQuoteTransfer);
        $oauthResponseTransfer = $this->tester->buildOauthResponseTransfer($customerTransfer->getCustomerReference());

        // Act
        $this->tester->getFacade()->mergeGuestQuoteAndCustomerQuote($oauthResponseTransfer);
        $findGuestQuoteResponseTransfer = $this->tester->getFacade()->findQuoteByUuid($createQuestQuoteResponseTransfer->getQuoteTransfer());
        $findQuoteResponseTransfer = $this->tester->getFacade()->findQuoteByUuid($createQuoteResponseTransfer->getQuoteTransfer());

        // Assert
        $this->assertEmpty($findQuoteResponseTransfer->getQuoteTransfer()->getItems());
        $this->assertTrue($findGuestQuoteResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testCustomerQuoteWillBeCreatedIfNotExistsByEnableMergingWithGuestQuote(): void
    {
        // Arrange
        $this->tester->setDependency(
            QuoteDependencyProvider::PLUGINS_QUOTE_CREATE_BEFORE,
            [$this->getAddDefaultNameBeforeQuoteSavePluginMock()],
        );
        $cartsRestApiFacade = $this->createCartsRestApiFacadeWithMockedConfig(true);

        $customerTransfer = $this->tester->haveCustomer();
        $cartsRestApiFacade->createQuote($this->tester->prepareQuoteTransferForGuest());
        $oauthResponseTransfer = $this->tester->buildOauthResponseTransfer($customerTransfer->getCustomerReference());

        // Act
        $cartsRestApiFacade->mergeGuestQuoteAndCustomerQuote($oauthResponseTransfer);
        $guestQuoteCollectionTransfer = $cartsRestApiFacade
            ->getQuoteCollection($this->tester->prepareQuoteCriteriaFilterTransferForGuest());

        $quoteCriteriaFilterTransfer = $this->tester
            ->createQuoteCriteriaFilterTransfer($oauthResponseTransfer->getCustomerReference());
        $customerQuoteCollectionTransfer = $cartsRestApiFacade->getQuoteCollection($quoteCriteriaFilterTransfer);

        // Assert
        $this->assertNotEmpty($customerQuoteCollectionTransfer->getQuotes());
        $this->assertEmpty($guestQuoteCollectionTransfer->getQuotes());
    }

    /**
     * @return void
     */
    public function testCustomerQuoteWillNotBeCreatedIfNotExistsByDisableMergingWithGuestQuote(): void
    {
        // Arrange
        $cartsRestApiFacade = $this->createCartsRestApiFacadeWithMockedConfig(false);

        $customerTransfer = $this->tester->haveCustomer();
        $cartsRestApiFacade->createQuote($this->tester->prepareQuoteTransferForGuest());
        $oauthResponseTransfer = $this->tester->buildOauthResponseTransfer($customerTransfer->getCustomerReference());

        // Act
        $cartsRestApiFacade->mergeGuestQuoteAndCustomerQuote($oauthResponseTransfer);
        $guestQuoteCollectionTransfer = $cartsRestApiFacade
            ->getQuoteCollection($this->tester->prepareQuoteCriteriaFilterTransferForGuest());

        $quoteCriteriaFilterTransfer = $this->tester
            ->createQuoteCriteriaFilterTransfer($oauthResponseTransfer->getCustomerReference());
        $customerQuoteCollectionTransfer = $cartsRestApiFacade->getQuoteCollection($quoteCriteriaFilterTransfer);

        // Assert
        $this->assertEmpty($customerQuoteCollectionTransfer->getQuotes());
        $this->assertNotEmpty($guestQuoteCollectionTransfer->getQuotes());
    }

    /**
     * @param bool $isQuoteCreationWhileQuoteMergingEnabled
     *
     * @return \Spryker\Zed\CartsRestApi\Business\CartsRestApiFacadeInterface
     */
    protected function createCartsRestApiFacadeWithMockedConfig(
        bool $isQuoteCreationWhileQuoteMergingEnabled
    ): CartsRestApiFacadeInterface {
        $cartRestApiConfigMock = $this->getCartRestApiConfigMock($isQuoteCreationWhileQuoteMergingEnabled);
        $cartsRestApiBusinessFactory = new CartsRestApiBusinessFactory();
        $cartsRestApiBusinessFactory->setConfig($cartRestApiConfigMock);

        $cartsRestApiFacade = new CartsRestApiFacade();
        $cartsRestApiFacade->setFactory($cartsRestApiBusinessFactory);

        return $cartsRestApiFacade;
    }

    /**
     * @param bool $isQuoteCreationWhileQuoteMergingEnabled
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CartsRestApi\CartsRestApiConfig
     */
    protected function getCartRestApiConfigMock(bool $isQuoteCreationWhileQuoteMergingEnabled): CartsRestApiConfig
    {
        $configMock = Stub::make(CartsRestApiConfig::class, [
            'isQuoteCreationWhileQuoteMergingEnabled' => function () use ($isQuoteCreationWhileQuoteMergingEnabled) {
                return $isQuoteCreationWhileQuoteMergingEnabled;
            },
        ]);

        return $configMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\QuoteExtension\Dependency\Plugin\QuoteWritePluginInterface
     */
    protected function getAddDefaultNameBeforeQuoteSavePluginMock(): QuoteWritePluginInterface
    {
        $addDefaultNameBeforeQuoteSavePluginMock = Stub::makeEmpty(QuoteWritePluginInterface::class, [
            'execute' => function (QuoteTransfer $quoteTransfer) {
                if (!$quoteTransfer->getName()) {
                    $quoteTransfer->setName('Shopping Cart Test');
                }

                return $quoteTransfer;
            },
        ]);

        return $addDefaultNameBeforeQuoteSavePluginMock;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $customerQuoteTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $guestQuoteTransfer
     * @param \Generated\Shared\Transfer\OauthResponseTransfer $oauthResponseTransfer
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToQuoteFacadeInterface
     */
    protected function createQuoteFacadeCartsRestApiMock(
        QuoteTransfer $customerQuoteTransfer,
        QuoteTransfer $guestQuoteTransfer,
        OauthResponseTransfer $oauthResponseTransfer
    ): CartsRestApiToQuoteFacadeInterface {
        $quoteFacadeMock = Stub::makeEmpty(
            CartsRestApiToQuoteFacadeInterface::class,
            [
                'getQuoteCollection' => function (QuoteCriteriaFilterTransfer $quoteCriteriaFilterTransfer) use (
                    $customerQuoteTransfer,
                    $guestQuoteTransfer,
                    $oauthResponseTransfer,
                ) {
                    $quoteCollectionTransfer = new QuoteCollectionTransfer();

                    if ($quoteCriteriaFilterTransfer->getCustomerReference() === $oauthResponseTransfer->getCustomerReference()) {
                        $quoteCollectionTransfer->addQuote($customerQuoteTransfer);
                    } elseif ($quoteCriteriaFilterTransfer->getCustomerReference() === $oauthResponseTransfer->getAnonymousCustomerReference()) {
                        $quoteCollectionTransfer->addQuote($guestQuoteTransfer);
                    }

                    return $quoteCollectionTransfer;
                },
            ],
        );

        return $quoteFacadeMock;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $customerQuoteTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $guestQuoteTransfer
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToPersistentCartFacadeInterface
     */
    protected function createPersistentCartFacadeMock(
        QuoteTransfer $customerQuoteTransfer,
        QuoteTransfer $guestQuoteTransfer
    ): CartsRestApiToPersistentCartFacadeInterface {
        $persistentCartFacadeMock = Stub::makeEmpty(
            CartsRestApiToPersistentCartFacadeInterface::class,
            [
                'add' => function (PersistentCartChangeTransfer $persistentCartChangeTransfer) use ($customerQuoteTransfer, $guestQuoteTransfer) {
                    return (new QuoteResponseTransfer())
                        ->setIsSuccessful(true)
                        ->setQuoteTransfer($customerQuoteTransfer);
                },
            ],
        );

        return $persistentCartFacadeMock;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $customerQuoteTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $guestQuoteTransfer
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CartsRestApi\Dependency\Facade\CartsRestApiToPersistentCartFacadeInterface
     */
    protected function createPersistentCartFacadeMockWithBundle(
        QuoteTransfer $customerQuoteTransfer,
        QuoteTransfer $guestQuoteTransfer
    ): CartsRestApiToPersistentCartFacadeInterface {
        $persistentCartFacadeMock = Stub::makeEmpty(
            CartsRestApiToPersistentCartFacadeInterface::class,
            [
                'add' => function (PersistentCartChangeTransfer $persistentCartChangeTransfer) use ($customerQuoteTransfer, $guestQuoteTransfer) {
                    $indexedPersistentCartChangeItems = [];
                    foreach ($persistentCartChangeTransfer->getItems() as $itemTransfer) {
                        $indexedPersistentCartChangeItems[$itemTransfer->getSku()] = $itemTransfer;
                    }
                    $this->assertArrayHasKey($guestQuoteTransfer->getBundleItems()->offsetGet(0)->getSku(), $indexedPersistentCartChangeItems);

                    return (new QuoteResponseTransfer())
                        ->setIsSuccessful(true)
                        ->setQuoteTransfer($customerQuoteTransfer);
                },
            ],
        );

        return $persistentCartFacadeMock;
    }
}
