<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

/**
 * Exception thrown when OpenF1 API requests fail.
 */
class OpenF1ApiException extends Exception
{
    /**
     * Create a new OpenF1ApiException instance.
     *
     * @param string $message Exception message
     * @param int $code HTTP status code
     * @param Response|null $response The failed HTTP response
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        protected ?Response $response = null,
    ) {
        parent::__construct($message, $code);
    }

    /**
     * Create an exception from a failed HTTP response.
     *
     * @param Response $response The failed HTTP response
     * @return static
     */
    public static function fromResponse(Response $response): static
    {
        $message = sprintf(
            'OpenF1 API request failed: %s %s (HTTP %d)',
            $response->transferStats?->getRequest()?->getMethod() ?? 'UNKNOWN',
            $response->transferStats?->getRequest()?->getUri() ?? 'UNKNOWN',
            $response->status(),
        );

        return new static(
            message: $message,
            code: $response->status(),
            response: $response,
        );
    }

    /**
     * Get the HTTP response that caused this exception.
     *
     * @return Response|null
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }
}
