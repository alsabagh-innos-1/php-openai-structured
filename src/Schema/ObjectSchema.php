<?php

namespace Omarsabbagh\PhpOpenaiStructured\Schema;

class ObjectSchema extends BaseSchema
{
    /**
     * @var array The schema properties
     */
    protected array $properties = [];

    /**
     * @var array The required property names
     */
    protected array $required = [];

    /**
     * @var bool Whether additional properties are allowed
     */
    protected bool $additionalProperties = false;

    /**
     * Create a new object schema
     *
     * @param string $name The schema name
     * @param bool $strict Whether the schema should be strict
     */
    public function __construct(string $name, bool $strict = true)
    {
        parent::__construct($name, $strict);
    }

    /**
     * Add a property to the schema
     *
     * @param string $name The property name
     * @param string $type The property type (string, number, boolean, etc.)
     * @param string|null $description The property description
     * @param bool $required Whether the property is required
     * @param array $additionalOptions Additional schema options for the property
     * @return self
     */
    public function addProperty(
        string $name,
        string $type,
        ?string $description = null,
        bool $required = false,
        array $additionalOptions = []
    ): self {
        $property = array_merge([
            'type' => $type
        ], $additionalOptions);

        if ($description !== null) {
            $property['description'] = $description;
        }

        $this->properties[$name] = $property;

        if ($required) {
            $this->required[] = $name;
        }

        return $this;
    }

    /**
     * Add an enum property to the schema
     *
     * @param string $name The property name
     * @param array $values The allowed values
     * @param string|null $description The property description
     * @param bool $required Whether the property is required
     * @return self
     */
    public function addEnumProperty(
        string $name,
        array $values,
        ?string $description = null,
        bool $required = false
    ): self {
        return $this->addProperty(
            $name,
            'string',
            $description,
            $required,
            ['enum' => $values]
        );
    }

    /**
     * Add a nested object property to the schema
     *
     * @param string $name The property name
     * @param array $properties The object properties
     * @param array $required The required property names
     * @param string|null $description The property description
     * @param bool $isRequired Whether the property is required
     * @return self
     */
    public function addObjectProperty(
        string $name,
        array $properties,
        array $required = [],
        ?string $description = null,
        bool $isRequired = false
    ): self {
        $objectDef = [
            'type' => 'object',
            'properties' => $properties
        ];

        if (!empty($required)) {
            $objectDef['required'] = $required;
        }

        if ($description !== null) {
            $objectDef['description'] = $description;
        }

        $this->properties[$name] = $objectDef;

        if ($isRequired) {
            $this->required[] = $name;
        }

        return $this;
    }

    /**
     * Add an array property to the schema
     *
     * @param string $name The property name
     * @param array $items The item schema
     * @param string|null $description The property description
     * @param bool $required Whether the property is required
     * @return self
     */
    public function addArrayProperty(
        string $name,
        array $items,
        ?string $description = null,
        bool $required = false
    ): self {
        $arrayDef = [
            'type' => 'array',
            'items' => $items
        ];

        if ($description !== null) {
            $arrayDef['description'] = $description;
        }

        $this->properties[$name] = $arrayDef;

        if ($required) {
            $this->required[] = $name;
        }

        return $this;
    }

    /**
     * Set whether additional properties are allowed
     *
     * @param bool $allowed Whether additional properties are allowed
     * @return self
     */
    public function setAdditionalProperties(bool $allowed): self
    {
        $this->additionalProperties = $allowed;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSchemaDefinition(): array
    {
        $schema = [
            'type' => 'object',
            'properties' => $this->properties,
            'additionalProperties' => $this->additionalProperties
        ];

        if (!empty($this->required)) {
            $schema['required'] = $this->required;
        }

        return $schema;
    }
}
