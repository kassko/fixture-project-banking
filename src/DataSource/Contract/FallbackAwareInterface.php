<?php

namespace App\DataSource\Contract;

interface FallbackAwareInterface
{
    /**
     * Set the fallback data source
     */
    public function setFallback(?DataSourceInterface $fallback): void;

    /**
     * Get the fallback data source
     */
    public function getFallback(): ?DataSourceInterface;
}
