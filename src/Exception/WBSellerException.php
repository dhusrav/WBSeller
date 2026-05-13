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
        return current($this->responseHeaders['X-Ratelimit-Retry'] ?? null);
    }
    public function getRatelimitLimit(): ?string
    {
        return current($this->responseHeaders['X-Ratelimit-Limit'] ?? null);
    }
    public function getRatelimitReset(): ?string
    {
        return current($this->responseHeaders['X-Ratelimit-Reset'] ?? null);
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
