<?php

declare(strict_types=1);

namespace Tempest\Mapper;

use ReflectionClass;
use Tempest\Database\Query;
use function Tempest\make;

final readonly class QueryToModelMapper implements Mapper
{
    public function canMap(object|string $to, mixed $from): bool
    {
        return $from instanceof Query;
    }

    public function map(object|string $to, mixed $from): array|object
    {
        /** @var Query $from */
        if ($from->bindings['id'] ?? null) {
            return make($to)->from($this->resolveData($to, $from->fetchFirst()));
        } else {
            return array_map(
                fn (array $item) => make($to)->from($this->resolveData($to, $item)),
                $from->fetch(),
            );
        }
    }

    private function resolveData(object|string $objectOrClass, array $data): array
    {
        $className = (new ReflectionClass($objectOrClass))->getShortName();

        $values = [];

        foreach ($data as $key => $value) {
            if (! strpos($key, ':')) {
                $values[$key] = $value;

                continue;
            }

            // TODO: we need to properly map table names to relation fields, as they aren't always the same
            [$table, $field] = explode(':', $key);

            if ($table === $className) {
                $values[$field] = $value;
            } else {
                $values[lcfirst($table)][$field] = $value;
            }
        }

        return $values;
    }
}
