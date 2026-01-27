<?php

namespace Boy132\PlayerCounter\Extensions\Query;

class QueryTypeService
{
    /** @var QueryTypeSchemaInterface[] */
    private array $schemas = [];

    public function get(string $id): ?QueryTypeSchemaInterface
    {
        return array_get($this->schemas, $id);
    }

    public function register(QueryTypeSchemaInterface $schema): void
    {
        if (array_key_exists($schema->getId(), $this->schemas)) {
            return;
        }

        $this->schemas[$schema->getId()] = $schema;
    }

    /** @return array<string, string> */
    public function getMappings(): array
    {
        return collect($this->schemas)->mapWithKeys(fn ($schema) => [$schema->getId() => $schema->getName()])->all();
    }
}
