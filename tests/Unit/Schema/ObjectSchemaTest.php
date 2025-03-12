<?php

namespace Omarsabbagh\PhpOpenaiStructured\Tests\Unit\Schema;

use PHPUnit\Framework\TestCase;
use Omarsabbagh\PhpOpenaiStructured\Schema\ObjectSchema;

class ObjectSchemaTest extends TestCase
{
    public function testConstructorSetsName()
    {
        $schema = new ObjectSchema('test_object');

        $this->assertEquals('test_object', $schema->getName());
    }

    public function testAddPropertyAddsPropertyToSchema()
    {
        $schema = new ObjectSchema('test_object');

        $schema->addProperty('name', 'string', 'Person name', true);
        $schema->addProperty('age', 'integer', 'Person age', false);

        $schemaArray = $schema->toArray();
        $schemaDefinition = $schemaArray['schema'];

        // Check properties were added
        $this->assertArrayHasKey('properties', $schemaDefinition);
        $this->assertArrayHasKey('name', $schemaDefinition['properties']);
        $this->assertArrayHasKey('age', $schemaDefinition['properties']);

        // Check property types and descriptions
        $this->assertEquals('string', $schemaDefinition['properties']['name']['type']);
        $this->assertEquals('Person name', $schemaDefinition['properties']['name']['description']);
        $this->assertEquals('integer', $schemaDefinition['properties']['age']['type']);
        $this->assertEquals('Person age', $schemaDefinition['properties']['age']['description']);

        // Check required fields
        $this->assertArrayHasKey('required', $schemaDefinition);
        $this->assertContains('name', $schemaDefinition['required']);
        $this->assertNotContains('age', $schemaDefinition['required']);
    }

    public function testAddEnumPropertyAddsEnumPropertyToSchema()
    {
        $schema = new ObjectSchema('test_object');

        $statuses = ['active', 'inactive', 'pending'];
        $schema->addEnumProperty('status', $statuses, 'Account status', true);

        $schemaArray = $schema->toArray();
        $schemaDefinition = $schemaArray['schema'];

        // Check property was added
        $this->assertArrayHasKey('properties', $schemaDefinition);
        $this->assertArrayHasKey('status', $schemaDefinition['properties']);

        // Check property type and enum values
        $this->assertEquals('string', $schemaDefinition['properties']['status']['type']);
        $this->assertEquals('Account status', $schemaDefinition['properties']['status']['description']);
        $this->assertArrayHasKey('enum', $schemaDefinition['properties']['status']);
        $this->assertEquals($statuses, $schemaDefinition['properties']['status']['enum']);

        // Check required fields
        $this->assertArrayHasKey('required', $schemaDefinition);
        $this->assertContains('status', $schemaDefinition['required']);
    }

    public function testAddObjectPropertyAddsNestedObjectToSchema()
    {
        $schema = new ObjectSchema('test_object');

        $addressProperties = [
            'street' => ['type' => 'string', 'description' => 'Street name'],
            'city' => ['type' => 'string', 'description' => 'City name'],
            'zipcode' => ['type' => 'string', 'description' => 'Postal code']
        ];

        $schema->addObjectProperty(
            'address',
            $addressProperties,
            ['street', 'city'],
            'Person address',
            true
        );

        $schemaArray = $schema->toArray();
        $schemaDefinition = $schemaArray['schema'];

        // Check property was added
        $this->assertArrayHasKey('properties', $schemaDefinition);
        $this->assertArrayHasKey('address', $schemaDefinition['properties']);

        // Check object property structure
        $addressProperty = $schemaDefinition['properties']['address'];
        $this->assertEquals('object', $addressProperty['type']);
        $this->assertEquals('Person address', $addressProperty['description']);
        $this->assertArrayHasKey('properties', $addressProperty);
        $this->assertEquals($addressProperties, $addressProperty['properties']);

        // Check required fields in the nested object
        $this->assertArrayHasKey('required', $addressProperty);
        $this->assertEquals(['street', 'city'], $addressProperty['required']);

        // Check the address property is required in the main schema
        $this->assertArrayHasKey('required', $schemaDefinition);
        $this->assertContains('address', $schemaDefinition['required']);
    }

    public function testAddArrayPropertyAddsArrayToSchema()
    {
        $schema = new ObjectSchema('test_object');

        $tagItem = [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string', 'description' => 'Tag name'],
                'color' => ['type' => 'string', 'description' => 'Tag color']
            ],
            'required' => ['name']
        ];

        $schema->addArrayProperty('tags', $tagItem, 'List of tags', true);

        $schemaArray = $schema->toArray();
        $schemaDefinition = $schemaArray['schema'];

        // Check property was added
        $this->assertArrayHasKey('properties', $schemaDefinition);
        $this->assertArrayHasKey('tags', $schemaDefinition['properties']);

        // Check array property structure
        $tagsProperty = $schemaDefinition['properties']['tags'];
        $this->assertEquals('array', $tagsProperty['type']);
        $this->assertEquals('List of tags', $tagsProperty['description']);
        $this->assertArrayHasKey('items', $tagsProperty);
        $this->assertEquals($tagItem, $tagsProperty['items']);

        // Check the tags property is required in the main schema
        $this->assertArrayHasKey('required', $schemaDefinition);
        $this->assertContains('tags', $schemaDefinition['required']);
    }

    public function testSetAdditionalPropertiesChangesAdditionalPropertiesFlag()
    {
        $schema = new ObjectSchema('test_object');

        // Default is false
        $schemaArray = $schema->toArray();
        $this->assertFalse($schemaArray['schema']['additionalProperties']);

        // Set to true
        $schema->setAdditionalProperties(true);
        $schemaArray = $schema->toArray();
        $this->assertTrue($schemaArray['schema']['additionalProperties']);

        // Set back to false
        $schema->setAdditionalProperties(false);
        $schemaArray = $schema->toArray();
        $this->assertFalse($schemaArray['schema']['additionalProperties']);
    }
}
