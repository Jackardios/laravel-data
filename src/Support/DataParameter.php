<?php

namespace Spatie\LaravelData\Support;

use ReflectionParameter;

class DataParameter
{
    public function __construct(
        public string $name,
        public bool $isPromoted,
        public bool $hasDefaultValue,
        public mixed $defaultValue,
        public DataType $type,
    ) {
    }

    public static function create(
        ReflectionParameter $parameter
    ): self {
        $hasDefaultValue = $parameter->isDefaultValueAvailable();

        return new self(
            $parameter->name,
            $parameter->isPromoted(),
            $hasDefaultValue,
            $hasDefaultValue ? $parameter->getDefaultValue() : null,
            DataType::create($parameter),
        );
    }
}
