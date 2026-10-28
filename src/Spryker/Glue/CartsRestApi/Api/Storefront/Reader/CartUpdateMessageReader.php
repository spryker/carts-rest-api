<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartsRestApi\Api\Storefront\Reader;

use Generated\Shared\Transfer\MessageTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class CartUpdateMessageReader implements CartUpdateMessageReaderInterface
{
    protected const string MESSAGE_TYPE_INFO = 'info';

    protected const string KEY_TYPE = 'type';

    protected const string KEY_VALUE = 'value';

    public function __construct(protected ZedRequestClientInterface $zedRequestClient)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getLastUpdateMessages(): array
    {
        return array_map(
            static fn (MessageTransfer $messageTransfer): array => [
                static::KEY_TYPE => static::MESSAGE_TYPE_INFO,
                static::KEY_VALUE => $messageTransfer->getValue(),
            ],
            array_values($this->zedRequestClient->getLastResponseInfoMessages()),
        );
    }
}
