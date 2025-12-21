<?php

declare(strict_types=1);

namespace App\Legacy\HybridDataObject;

/**
 * Feed configuration - Legacy nested object (NO Doctrine).
 */
class FeedConfig
{
    private string $feedId;
    
    private string $provider;
    
    private string $endpoint;
    
    private array $credentials = [];
    
    private int $refreshIntervalSeconds = 300;
    
    private bool $isEnabled = true;
    
    public function __construct(string $feedId, string $provider, string $endpoint)
    {
        $this->feedId = $feedId;
        $this->provider = $provider;
        $this->endpoint = $endpoint;
    }
    
    public function getFeedId(): string
    {
        return $this->feedId;
    }
    
    public function getProvider(): string
    {
        return $this->provider;
    }
    
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }
    
    public function getCredentials(): array
    {
        return $this->credentials;
    }
    
    public function setCredentials(array $credentials): static
    {
        $this->credentials = $credentials;
        return $this;
    }
    
    public function getRefreshIntervalSeconds(): int
    {
        return $this->refreshIntervalSeconds;
    }
    
    public function setRefreshIntervalSeconds(int $refreshIntervalSeconds): static
    {
        $this->refreshIntervalSeconds = $refreshIntervalSeconds;
        return $this;
    }
    
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }
    
    public function setIsEnabled(bool $isEnabled): static
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }
}
