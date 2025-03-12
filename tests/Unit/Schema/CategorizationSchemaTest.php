<?php

namespace Omarsabbagh\PhpOpenaiStructured\Tests\Unit\Schema;

use PHPUnit\Framework\TestCase;
use Omarsabbagh\PhpOpenaiStructured\Schema\CategorizationSchema;

class CategorizationSchemaTest extends TestCase
{
    public function testConstructorSetsCorrectCategoriesAndName()
    {
        $categories = ['Category1', 'Category2', 'Category3'];
        $schema = new CategorizationSchema('test_categorization', $categories);

        $this->assertEquals('test_categorization', $schema->getName());
        $this->assertEquals($categories, $schema->getCategories());
    }

    public function testSetCategoriesUpdatesCategories()
    {
        $initialCategories = ['Initial1', 'Initial2'];
        $schema = new CategorizationSchema('test_categorization', $initialCategories);

        $this->assertEquals($initialCategories, $schema->getCategories());

        $newCategories = ['New1', 'New2', 'New3'];
        $schema->setCategories($newCategories);

        $this->assertEquals($newCategories, $schema->getCategories());
    }

    public function testToArrayIncludesCategoriesInEnumProperty()
    {
        $categories = ['Real', 'Fake'];
        $schema = new CategorizationSchema('test_categorization', $categories);

        $schemaArray = $schema->toArray();

        // Check the overall structure
        $this->assertArrayHasKey('name', $schemaArray);
        $this->assertArrayHasKey('strict', $schemaArray);
        $this->assertArrayHasKey('schema', $schemaArray);

        // Check that the schema has the right properties
        $schemaDefinition = $schemaArray['schema'];
        $this->assertEquals('object', $schemaDefinition['type']);
        $this->assertArrayHasKey('properties', $schemaDefinition);
        $this->assertArrayHasKey('category', $schemaDefinition['properties']);
        $this->assertArrayHasKey('reason', $schemaDefinition['properties']);

        // Check that the category property has the enum values
        $categoryProperty = $schemaDefinition['properties']['category'];
        $this->assertEquals('string', $categoryProperty['type']);
        $this->assertArrayHasKey('enum', $categoryProperty);
        $this->assertEquals($categories, $categoryProperty['enum']);

        // Check that the required fields are set correctly
        $this->assertArrayHasKey('required', $schemaDefinition);
        $this->assertEquals(['category', 'reason'], $schemaDefinition['required']);

        // Check additionalProperties is false
        $this->assertArrayHasKey('additionalProperties', $schemaDefinition);
        $this->assertFalse($schemaDefinition['additionalProperties']);
    }
}
