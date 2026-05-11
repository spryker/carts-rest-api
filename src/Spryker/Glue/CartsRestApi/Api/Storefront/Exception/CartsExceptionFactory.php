<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartsRestApi\Api\Storefront\Exception;

use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Symfony\Component\HttpFoundation\Response;

/**
 * Builds pre-configured `GlueApiException` instances for known cart error scenarios.
 *
 * Uses {@see CartsRestApiConfig::getErrorIdentifierToRestErrorMapping()} as the single
 * source of truth for `errorIdentifier → [code, status, detail]` translation. This is the
 * same map the legacy `CartMapper::mapQuoteErrorTransferToRestErrorMessageTransfer()`
 * consults, so JSON:API responses stay byte-equivalent to the legacy stack.
 */
class CartsExceptionFactory
{
    protected const string RESPONSE_CODE_ANONYMOUS_WITH_AUTHORIZATION = '005';

    protected const string EXCEPTION_MESSAGE_ANONYMOUS_WITH_AUTHORIZATION = 'Headers request error. A user can\'t act as logged and not logged user at the same time.';

    public function __construct(
        protected CartsRestApiConfig $cartsRestApiConfig = new CartsRestApiConfig(),
    ) {
    }

    public function createCartIdMissingException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            CartsRestApiConfig::RESPONSE_CODE_CART_ID_MISSING,
            CartsRestApiConfig::EXCEPTION_MESSAGE_CART_ID_MISSING,
        );
    }

    public function createCartNotFoundException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_NOT_FOUND,
            CartsRestApiConfig::RESPONSE_CODE_CART_NOT_FOUND,
            CartsRestApiConfig::EXCEPTION_MESSAGE_CART_WITH_ID_NOT_FOUND,
        );
    }

    public function createCustomerUnauthorizedException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_FORBIDDEN,
            CartsRestApiConfig::RESPONSE_CODE_CUSTOMER_UNAUTHORIZED,
            CartsRestApiConfig::RESPONSE_DETAILS_CUSTOMER_UNAUTHORIZED,
        );
    }

    public function createUnauthorizedCartActionException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_FORBIDDEN,
            CartsRestApiConfig::RESPONSE_CODE_UNAUTHORIZED_CART_ACTION,
            CartsRestApiConfig::EXCEPTION_MESSAGE_UNAUTHORIZED_CART_ACTION,
        );
    }

    public function createAnonymousCustomerUniqueIdEmptyException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            CartsRestApiConfig::RESPONSE_CODE_ANONYMOUS_CUSTOMER_UNIQUE_ID_EMPTY,
            CartsRestApiConfig::EXCEPTION_MESSAGE_ANONYMOUS_CUSTOMER_UNIQUE_ID_EMPTY,
        );
    }

    public function createAnonymousWithAuthorizationException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            static::RESPONSE_CODE_ANONYMOUS_WITH_AUTHORIZATION,
            static::EXCEPTION_MESSAGE_ANONYMOUS_WITH_AUTHORIZATION,
        );
    }

    /**
     * Builds a `GlueApiException` from a non-successful `QuoteResponseTransfer`.
     *
     * Iterates every `QuoteErrorTransfer` and emits one error entry per — mirrors the legacy
     * {@see \Spryker\Glue\CartsRestApi\Processor\RestResponseBuilder\CartRestResponseBuilder::createFailedErrorResponse()}
     * which appends each error to the JSON:API `errors[]` array (multiple validation
     * failures returned together, e.g. invalid `priceMode` and `currency` in the same POST).
     *
     * Per-error fallback uses `RESPONSE_CODE_ITEM_VALIDATION` (102) when the identifier is
     * unknown but a `message` is present — matches legacy
     * `CartMapper::mapQuoteErrorTransferToRestErrorMessageTransfer()`. The HTTP status of the
     * resulting response is the status of the first mapped error (or `$fallbackStatus`);
     * individual `errors[*].status` values stay independent.
     */
    public function createExceptionFromQuoteResponse(
        QuoteResponseTransfer $quoteResponseTransfer,
        string $fallbackCode,
        string $fallbackDetail,
        int $fallbackStatus = Response::HTTP_UNPROCESSABLE_ENTITY,
    ): GlueApiException {
        $errors = $quoteResponseTransfer->getErrors();

        if ($errors->count() === 0) {
            return new GlueApiException($fallbackStatus, $fallbackCode, $fallbackDetail);
        }

        $mapping = $this->cartsRestApiConfig->getErrorIdentifierToRestErrorMapping();
        $errorPayloads = [];
        $primaryStatus = null;
        $primaryCode = null;
        $primaryDetail = null;

        foreach ($errors as $errorTransfer) {
            $errorPayload = $this->buildErrorPayload($errorTransfer, $mapping);

            if ($errorPayload === null) {
                continue;
            }

            $errorPayloads[] = $errorPayload;

            if ($primaryStatus === null) {
                $primaryStatus = $errorPayload['status'];
                $primaryCode = $errorPayload['code'];
                $primaryDetail = $errorPayload['detail'];
            }
        }

        if ($errorPayloads === []) {
            return new GlueApiException($fallbackStatus, $fallbackCode, $fallbackDetail);
        }

        return (new GlueApiException(
            (int)$primaryStatus,
            (string)$primaryCode,
            (string)$primaryDetail,
        ))->setErrors($errorPayloads);
    }

    /**
     * @param array<string, array<string, mixed>> $mapping
     *
     * @return array{code: string, status: int, detail: string}|null
     */
    protected function buildErrorPayload(QuoteErrorTransfer $errorTransfer, array $mapping): ?array
    {
        $errorIdentifier = $errorTransfer->getErrorIdentifier();

        if ($errorIdentifier !== null && isset($mapping[$errorIdentifier])) {
            $entry = $mapping[$errorIdentifier];

            return [
                'code' => (string)$entry[RestErrorMessageTransfer::CODE],
                'status' => (int)$entry[RestErrorMessageTransfer::STATUS],
                'detail' => (string)$entry[RestErrorMessageTransfer::DETAIL],
            ];
        }

        $message = $errorTransfer->getMessage();

        if ($message !== null && $message !== '') {
            return [
                'code' => CartsRestApiConfig::RESPONSE_CODE_ITEM_VALIDATION,
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'detail' => $message,
            ];
        }

        return null;
    }

    /**
     * Maps a single `QuoteErrorTransfer` through the legacy errorIdentifier mapping.
     * Returns `null` when `errorIdentifier` is absent or unknown — callers fall back
     * to their generic exception.
     */
    public function mapQuoteError(QuoteErrorTransfer $quoteErrorTransfer): ?GlueApiException
    {
        $errorIdentifier = $quoteErrorTransfer->getErrorIdentifier();

        if ($errorIdentifier === null) {
            return null;
        }

        $mapping = $this->cartsRestApiConfig->getErrorIdentifierToRestErrorMapping();

        if (!isset($mapping[$errorIdentifier])) {
            return null;
        }

        $entry = $mapping[$errorIdentifier];

        return new GlueApiException(
            (int)$entry[RestErrorMessageTransfer::STATUS],
            (string)$entry[RestErrorMessageTransfer::CODE],
            (string)$entry[RestErrorMessageTransfer::DETAIL],
        );
    }
}
