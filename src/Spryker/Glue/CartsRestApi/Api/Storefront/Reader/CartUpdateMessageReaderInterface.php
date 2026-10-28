<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartsRestApi\Api\Storefront\Reader;

interface CartUpdateMessageReaderInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getLastUpdateMessages(): array;
}
