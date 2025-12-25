<?php

namespace App\DataSource\Contract;

interface DataSourceInterface
{
    /**
     * Check if this data source is currently available
     */
    public function isAvailable(): bool;

    /**
     * Check if this data source supports the given type
     */
    public function supports(string $type): bool;

    /**
     * Fetch data for the given type and identifier
     */
    public function fetchData(string $type, int $id): ?array;

    /**
     * Get the name of this data source
     */
    public function getName(): string;

    /**
     * Get the priority of this data source (higher = more priority)
     */
    public function getPriority(): int;
}
