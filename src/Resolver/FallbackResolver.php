<?php

namespace App\Resolver;

use App\DataSource\Contract\DataSourceInterface;
use App\DataSource\Contract\FallbackAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class FallbackResolver
{
    private LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Use Case 2: Fallback cascade
     * Try sources in order until one succeeds:
     * CreditBureauSource → CacheSource → LegacyDatabaseSource → DefaultValuesSource
     */
    public function fetchWithFallback(array $sources, string $type, int $id): ?array
    {
        foreach ($sources as $source) {
            if (!$source instanceof DataSourceInterface) {
                continue;
            }

            $this->logger->info('Trying source {source} for {type}#{id}', [
                'source' => $source->getName(),
                'type' => $type,
                'id' => $id,
            ]);

            if (!$source->isAvailable()) {
                $this->logger->warning('Source {source} is not available, trying fallback', [
                    'source' => $source->getName(),
                ]);
                continue;
            }

            if (!$source->supports($type)) {
                $this->logger->debug('Source {source} does not support type {type}', [
                    'source' => $source->getName(),
                    'type' => $type,
                ]);
                continue;
            }

            try {
                $data = $source->fetchData($type, $id);
                
                if ($data !== null) {
                    $this->logger->info('Successfully fetched data from source {source}', [
                        'source' => $source->getName(),
                    ]);
                    return $data;
                }

                $this->logger->warning('Source {source} returned null, trying fallback', [
                    'source' => $source->getName(),
                ]);
            } catch (\Exception $e) {
                $this->logger->error('Source {source} threw exception: {message}', [
                    'source' => $source->getName(),
                    'message' => $e->getMessage(),
                ]);
                continue;
            }
        }

        $this->logger->error('All sources failed for {type}#{id}', [
            'type' => $type,
            'id' => $id,
        ]);

        return null;
    }

    /**
     * Setup fallback chain for sources
     */
    public function setupFallbackChain(array $sources): void
    {
        for ($i = 0; $i < count($sources) - 1; $i++) {
            $current = $sources[$i];
            $next = $sources[$i + 1];

            if ($current instanceof FallbackAwareInterface) {
                $current->setFallback($next);
                $this->logger->debug('Set fallback {next} for {current}', [
                    'current' => $current->getName(),
                    'next' => $next->getName(),
                ]);
            }
        }
    }
}
