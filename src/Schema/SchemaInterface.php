<?php

namespace Omarsabbagh\PhpOpenaiStructured\Schema;

interface SchemaInterface
{
    /**
     * Get the schema name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set whether the schema should be strict
     *
     * @param bool $strict
     * @return self
     */
    public function setStrict(bool $strict): self;

    /**
     * Get whether the schema is strict
     *
     * @return bool
     */
    public function isStrict(): bool;

    /**
     * Convert the schema to an array for use with the OpenAI API
     *
     * @return array
     */
    public function toArray(): array;
}
