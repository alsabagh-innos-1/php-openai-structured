<?php

namespace Omarsabbagh\PhpOpenaiStructured\Schema;

class CategorizationSchema extends BaseSchema
{
    /**
     * @var array The available categories
     */
    protected array $categories;

    /**
     * Create a new categorization schema
     *
     * @param string $name The schema name
     * @param array $categories The available categories
     * @param bool $strict Whether the schema should be strict
     */
    public function __construct(string $name, array $categories, bool $strict = true)
    {
        parent::__construct($name, $strict);
        $this->categories = $categories;
    }

    /**
     * Set the available categories
     *
     * @param array $categories The categories
     * @return self
     */
    public function setCategories(array $categories): self
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * Get the available categories
     *
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSchemaDefinition(): array
    {
        return [
            "type" => "object",
            "properties" => [
                "category" => [
                    "type" => "string",
                    "description" => "Category of the input",
                    "enum" => $this->categories
                ],
                "reason" => [
                    "type" => "string",
                    "description" => "Reason for the categorization"
                ]
            ],
            "required" => ["category", "reason"],
            "additionalProperties" => false
        ];
    }
}
