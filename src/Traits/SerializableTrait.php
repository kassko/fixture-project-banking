<?php

namespace App\Traits;

trait SerializableTrait
{
    public function toArray(): array
    {
        $data = [];
        $reflection = new \ReflectionClass($this);
        
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $name = $property->getName();
            $value = $property->getValue($this);
            
            if ($value instanceof \DateTime) {
                $data[$name] = $value->format('Y-m-d H:i:s');
            } elseif (is_object($value) && method_exists($value, 'toArray')) {
                $data[$name] = $value->toArray();
            } else {
                $data[$name] = $value;
            }
        }
        
        return $data;
    }

    public function fromArray(array $data): self
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        
        return $this;
    }
}
