<?php

namespace Omarsabbagh\PhpOpenaiStructured\Schema;

abstract class BaseSchema implements SchemaInterface
{
    /**
     * @var string The name of the schema
     */
    protected string $name;

    /**
     * @var bool Whether the schema should be strict
     */
    protected bool $strict = true;

    /**
     * Create a new schema instance
     *
     * @param string $name The name of the schema
     * @param bool $strict Whether the schema should be strict
     */
    public function __construct(string $name, bool $strict = true)
    {
        $this->name = $name;
        $this->strict = $strict;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setStrict(bool $strict): self
    {
        $this->strict = $strict;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isStrict(): bool
    {
        return $this->strict;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'strict' => $this->strict,
            'schema' => $this->getSchemaDefinition()
        ];
    }

    /**
     * Get the JSON schema definition
     *
     * @return array
     */
    abstract protected function getSchemaDefinition(): array;
}
