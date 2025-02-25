<?php

namespace Spatie\LaravelData\Support;

use Illuminate\Support\Collection;
use ReflectionAttribute;
use ReflectionProperty;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Mappers\NameMapper;
use Spatie\LaravelData\Resolvers\NameMappersResolver;
use Spatie\LaravelData\Transformers\Transformer;

class DataProperty
{
    public function __construct(
        public string $name,
        public string $className,
        public DataType $type,
        public bool $validate,
        public bool $isPromoted,
        public bool $hasDefaultValue,
        public mixed $defaultValue,
        public ?Cast $cast,
        public ?Transformer $transformer,
        public ?string $inputMappedName,
        public ?string $outputMappedName,
        public Collection $attributes,
    ) {
    }

    public static function create(
        ReflectionProperty $property,
        bool $hasDefaultValue = false,
        mixed $defaultValue = null,
        ?NameMapper $classInputNameMapper = null,
        ?NameMapper $classOutputNameMapper = null,
    ): self {
        $attributes = collect($property->getAttributes())->map(
            fn (ReflectionAttribute $reflectionAttribute) => $reflectionAttribute->newInstance()
        );

        $mappers = NameMappersResolver::create()->execute($attributes);

        $inputMappedName = match (true) {
            $mappers['inputNameMapper'] !== null => $mappers['inputNameMapper']->map($property->name),
            $classInputNameMapper !== null => $classInputNameMapper->map($property->name),
            default => null,
        };

        $outputMappedName = match (true) {
            $mappers['outputNameMapper'] !== null => $mappers['outputNameMapper']->map($property->name),
            $classOutputNameMapper !== null => $classOutputNameMapper->map($property->name),
            default => null,
        };

        return new self(
            name: $property->name,
            className: $property->class,
            type: DataType::create($property),
            validate: ! $attributes->contains(fn (object $attribute) => $attribute instanceof WithoutValidation),
            isPromoted: $property->isPromoted(),
            hasDefaultValue: $property->isPromoted() ? $hasDefaultValue : $property->hasDefaultValue(),
            defaultValue: $property->isPromoted() ? $defaultValue : $property->getDefaultValue(),
            cast: $attributes->first(fn (object $attribute) => $attribute instanceof WithCast)?->get(),
            transformer: $attributes->first(fn (object $attribute) => $attribute instanceof WithTransformer)?->get(),
            inputMappedName: $inputMappedName,
            outputMappedName: $outputMappedName,
            attributes: $attributes,
        );
    }
}
