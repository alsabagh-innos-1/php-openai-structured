<?php

namespace Omarsabbagh\PhpOpenaiStructured\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Omarsabbagh\PhpOpenaiStructured\Schema\CategorizationSchema;
use Omarsabbagh\PhpOpenaiStructured\Schema\ObjectSchema;

class SchemaIntegrationTest extends TestCase
{
    public function testComplexSchema()
    {
        // Create a main object schema
        $schema = new ObjectSchema('user_analysis');

        // Add basic properties
        $schema->addProperty('username', 'string', 'Username of the user', true);
        $schema->addProperty('active', 'boolean', 'Whether the user is active', true);

        // Add enum property
        $schema->addEnumProperty('role', ['admin', 'user', 'guest'], 'User role', true);

        // Add a nested object for user profile
        $schema->addObjectProperty(
            'profile',
            [
                'fullName' => ['type' => 'string', 'description' => 'Full name of the user'],
                'age' => ['type' => 'integer', 'description' => 'Age of the user'],
                'bio' => ['type' => 'string', 'description' => 'User biography']
            ],
            ['fullName'],
            'User profile information',
            true
        );

        // Add an array of skills
        $schema->addArrayProperty(
            'skills',
            [
                'type' => 'object',
                'properties' => [
                    'name' => ['type' => 'string', 'description' => 'Skill name'],
                    'level' => ['type' => 'integer', 'description' => 'Skill level (1-10)']
                ],
                'required' => ['name', 'level']
            ],
            'User skills',
            false
        );

        // Convert to array for OpenAI API
        $schemaArray = $schema->toArray();

        // Assertions for the top level structure
        $this->assertEquals('user_analysis', $schemaArray['name']);
        $this->assertTrue($schemaArray['strict']);
        $this->assertArrayHasKey('schema', $schemaArray);

        // Assertions for the schema definition
        $definition = $schemaArray['schema'];
        $this->assertEquals('object', $definition['type']);

        // Check properties
        $this->assertArrayHasKey('properties', $definition);
        $properties = $definition['properties'];

        // Username property
        $this->assertArrayHasKey('username', $properties);
        $this->assertEquals('string', $properties['username']['type']);

        // Role property with enum
        $this->assertArrayHasKey('role', $properties);
        $this->assertEquals(['admin', 'user', 'guest'], $properties['role']['enum']);

        // Profile object
        $this->assertArrayHasKey('profile', $properties);
        $this->assertEquals('object', $properties['profile']['type']);
        $this->assertArrayHasKey('fullName', $properties['profile']['properties']);
        $this->assertEquals(['fullName'], $properties['profile']['required']);

        // Skills array
        $this->assertArrayHasKey('skills', $properties);
        $this->assertEquals('array', $properties['skills']['type']);
        $this->assertArrayHasKey('name', $properties['skills']['items']['properties']);

        // Required properties
        $this->assertArrayHasKey('required', $definition);
        $required = $definition['required'];
        $this->assertContains('username', $required);
        $this->assertContains('active', $required);
        $this->assertContains('role', $required);
        $this->assertContains('profile', $required);
        $this->assertNotContains('skills', $required);
    }

    public function testCombiningWithCategorizationSchema()
    {
        // Create a categorization schema
        $categorizationSchema = new CategorizationSchema(
            'user_security_risk',
            ['low', 'medium', 'high', 'critical']
        );

        // Create an object schema for user data
        $userSchema = new ObjectSchema('user_data');
        $userSchema->addProperty('userId', 'string', 'User ID', true);
        $userSchema->addProperty('lastLogin', 'string', 'Last login timestamp', false);
        $userSchema->addArrayProperty(
            'accessHistory',
            [
                'type' => 'object',
                'properties' => [
                    'timestamp' => ['type' => 'string'],
                    'ipAddress' => ['type' => 'string'],
                    'success' => ['type' => 'boolean']
                ]
            ],
            'Access history',
            false
        );

        // Convert both schemas to arrays
        $categorizationArray = $categorizationSchema->toArray();
        $userDataArray = $userSchema->toArray();

        // Assertions for categorization schema
        $this->assertEquals('user_security_risk', $categorizationArray['name']);
        $this->assertArrayHasKey('schema', $categorizationArray);
        $this->assertArrayHasKey('category', $categorizationArray['schema']['properties']);
        $this->assertEquals(
            ['low', 'medium', 'high', 'critical'],
            $categorizationArray['schema']['properties']['category']['enum']
        );

        // Assertions for user data schema
        $this->assertEquals('user_data', $userDataArray['name']);
        $this->assertArrayHasKey('userId', $userDataArray['schema']['properties']);
        $this->assertArrayHasKey('accessHistory', $userDataArray['schema']['properties']);
    }
}
