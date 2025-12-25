<?php

namespace App\Resolver;

use App\DataSource\Contract\DataSourceInterface;
use App\Context\UserContext;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class SourceResolver
{
    private array $sources = [];
    private LoggerInterface $logger;

    public function __construct(array $sources = [], ?LoggerInterface $logger = null)
    {
        $this->sources = $sources;
        $this->logger = $logger ?? new NullLogger();
    }

    public function addSource(DataSourceInterface $source): void
    {
        $this->sources[] = $source;
    }

    /**
     * Resolve the best source for the given context
     * 
     * Use Case 1: Runtime resolution based on customer type
     * - PremiumCustomer → CreditBureauSource + PartnerApiSource
     * - CorporateCustomer → PartnerApiSource + MarketDataSource  
     * - Standard → InternalApiSource only
     */
    public function resolve(string $type, ?UserContext $userContext = null, array $options = []): array
    {
        $customerType = $options['customerType'] ?? 'standard';
        $availableSources = [];

        // Filter sources by type support and availability
        foreach ($this->sources as $source) {
            if (!$source->supports($type)) {
                continue;
            }

            if (!$source->isAvailable()) {
                $this->logger->warning('Source {source} is not available', [
                    'source' => $source->getName(),
                ]);
                continue;
            }

            $availableSources[] = $source;
        }

        // Sort by priority (higher first)
        usort($availableSources, function (DataSourceInterface $a, DataSourceInterface $b) {
            return $b->getPriority() <=> $a->getPriority();
        });

        // Apply customer type specific filtering
        $resolvedSources = $this->filterByCustomerType($availableSources, $customerType);

        $this->logger->info('Resolved {count} source(s) for type {type} and customer type {customerType}', [
            'count' => count($resolvedSources),
            'type' => $type,
            'customerType' => $customerType,
            'sources' => array_map(fn($s) => $s->getName(), $resolvedSources),
        ]);

        return $resolvedSources;
    }

    private function filterByCustomerType(array $sources, string $customerType): array
    {
        $sourceNames = array_map(fn($s) => $s->getName(), $sources);

        return match ($customerType) {
            'premium' => $this->selectSources($sources, [
                'credit_bureau',
                'partner_api',
                'cache',
                'internal_api'
            ]),
            'corporate' => $this->selectSources($sources, [
                'partner_api',
                'market_data',
                'internal_api'
            ]),
            default => $this->selectSources($sources, [
                'internal_api',
                'cache'
            ]),
        };
    }

    private function selectSources(array $allSources, array $preferredNames): array
    {
        $selected = [];
        
        // First, add preferred sources in order
        foreach ($preferredNames as $name) {
            foreach ($allSources as $source) {
                if ($source->getName() === $name) {
                    $selected[] = $source;
                    break;
                }
            }
        }
        
        return $selected;
    }

    public function getSources(): array
    {
        return $this->sources;
    }
}
