<?php

namespace App\Resolver;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ConflictResolver
{
    private LoggerInterface $logger;
    private string $defaultStrategy = 'priority';

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Use Case 4: Resolve conflicts when multiple sources return different data
     * Strategies:
     * - priority: Use data from highest priority source
     * - merge: Merge all data (later sources override earlier)
     * - average: Take average of numeric values
     * - conservative: Take most conservative value (e.g., lowest score, highest risk)
     */
    public function resolve(array $dataFromSources, string $strategy = null): array
    {
        $strategy = $strategy ?? $this->defaultStrategy;

        if (empty($dataFromSources)) {
            return [];
        }

        if (count($dataFromSources) === 1) {
            return reset($dataFromSources);
        }

        $this->logger->info('Resolving conflict with strategy {strategy} from {count} sources', [
            'strategy' => $strategy,
            'count' => count($dataFromSources),
        ]);

        return match ($strategy) {
            'priority' => $this->resolvePriority($dataFromSources),
            'merge' => $this->resolveMerge($dataFromSources),
            'average' => $this->resolveAverage($dataFromSources),
            'conservative' => $this->resolveConservative($dataFromSources),
            default => $this->resolvePriority($dataFromSources),
        };
    }

    private function resolvePriority(array $dataFromSources): array
    {
        // Return first (highest priority) source data
        return reset($dataFromSources);
    }

    private function resolveMerge(array $dataFromSources): array
    {
        // Merge all arrays, later sources override earlier
        $merged = [];
        
        foreach ($dataFromSources as $sourceName => $data) {
            $merged = $this->deepMerge($merged, $data);
            $this->logger->debug('Merged data from source {source}', [
                'source' => $sourceName,
            ]);
        }
        
        return $merged;
    }

    private function resolveAverage(array $dataFromSources): array
    {
        // Take average of numeric values, first value for non-numeric
        $result = [];
        $counts = [];
        
        foreach ($dataFromSources as $sourceName => $data) {
            foreach ($data as $key => $value) {
                if (is_numeric($value)) {
                    $result[$key] = ($result[$key] ?? 0) + $value;
                    $counts[$key] = ($counts[$key] ?? 0) + 1;
                } elseif (!isset($result[$key])) {
                    $result[$key] = $value;
                }
            }
        }
        
        // Calculate averages
        foreach ($counts as $key => $count) {
            if ($count > 1) {
                $result[$key] = $result[$key] / $count;
                $this->logger->debug('Averaged {key} from {count} sources: {value}', [
                    'key' => $key,
                    'count' => $count,
                    'value' => $result[$key],
                ]);
            }
        }
        
        return $result;
    }

    private function resolveConservative(array $dataFromSources): array
    {
        // Take most conservative value
        // For scores: lowest
        // For risk levels: highest
        // For amounts: lowest (if positive) or highest (if negative/debt)
        
        $result = [];
        
        foreach ($dataFromSources as $sourceName => $data) {
            foreach ($data as $key => $value) {
                if (!isset($result[$key])) {
                    $result[$key] = $value;
                    continue;
                }
                
                // Apply conservative logic based on field name
                if ($this->isScoreField($key)) {
                    // Lower score is more conservative
                    if (is_numeric($value) && is_numeric($result[$key])) {
                        $result[$key] = min($result[$key], $value);
                    }
                } elseif ($this->isRiskField($key)) {
                    // Higher risk is more conservative
                    if ($this->compareRiskLevel($value, $result[$key]) > 0) {
                        $result[$key] = $value;
                    }
                } elseif (is_numeric($value) && is_numeric($result[$key])) {
                    // For other numeric values, take minimum
                    $result[$key] = min($result[$key], $value);
                }
            }
        }
        
        $this->logger->info('Applied conservative conflict resolution');
        
        return $result;
    }

    private function deepMerge(array $array1, array $array2): array
    {
        $merged = $array1;
        
        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->deepMerge($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
        
        return $merged;
    }

    private function isScoreField(string $key): bool
    {
        $scorePatterns = ['score', 'rating', 'grade'];
        $key = strtolower($key);
        
        foreach ($scorePatterns as $pattern) {
            if (str_contains($key, $pattern)) {
                return true;
            }
        }
        
        return false;
    }

    private function isRiskField(string $key): bool
    {
        return str_contains(strtolower($key), 'risk');
    }

    private function compareRiskLevel($level1, $level2): int
    {
        $levels = ['low' => 1, 'medium-low' => 2, 'medium' => 3, 'medium-high' => 4, 'high' => 5, 'critical' => 6];
        
        $value1 = $levels[strtolower($level1)] ?? 0;
        $value2 = $levels[strtolower($level2)] ?? 0;
        
        return $value1 <=> $value2;
    }

    public function setDefaultStrategy(string $strategy): void
    {
        $this->defaultStrategy = $strategy;
    }
}
