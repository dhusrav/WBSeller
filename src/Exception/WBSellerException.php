<?php

declare(strict_types=1);

namespace Dakword\WBSeller\Exception;

class WBSellerException extends \Exception
{
    protected array $responseHeaders = [];
    protected ?int $responseCode = null;
    protected ?string $responsePhrase = null;

    public function __construct(
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null,
        array $responseHeaders = [],
        ?int $responseCode = null,
        ?string $responsePhrase = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->responseHeaders = $responseHeaders;
        $this->responseCode = $responseCode;
        $this->responsePhrase = $responsePhrase;
    }

    public function getResponseHeaders(): array
    {
        return $this->responseHeaders;
    }

    public function getRatelimitRetry(): ?string
    {
        return $this->getRatelimitHeader('X-Ratelimit-Retry');
    }

    public function getRatelimitLimit(): ?string
    {
        return $this->getRatelimitHeader('X-Ratelimit-Limit');
    }

    public function getRatelimitReset(): ?string
    {
        return $this->getRatelimitHeader('X-Ratelimit-Reset');
    }

    private function getRatelimitHeader(string $header): ?string
    {
        $values = $this->responseHeaders[$header] ?? null;

        if (! is_array($values) || $values === []) {
            return null;
        }

        $value = current($values);

        return $value === false ? null : (string) $value;
    }

    public function getResponseCode(): ?int
    {
        return $this->responseCode;
    }

    public function getResponsePhrase(): ?string
    {
        return $this->responsePhrase;
    }
}
