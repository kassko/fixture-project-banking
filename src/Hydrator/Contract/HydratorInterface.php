<?php

namespace App\Hydrator\Contract;

interface HydratorInterface
{
    /**
     * Hydrate an object from array data
     *
     * @param array $data The data to hydrate from
     * @param object|null $object Optional object to hydrate into (creates new if null)
     * @return object The hydrated object
     */
    public function hydrate(array $data, ?object $object = null): object;

    /**
     * Extract array data from an object
     *
     * @param object $object The object to extract from
     * @return array The extracted data
     */
    public function extract(object $object): array;
}
