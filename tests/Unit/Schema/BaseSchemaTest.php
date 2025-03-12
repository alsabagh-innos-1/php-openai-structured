<?php

namespace Omarsabbagh\PhpOpenaiStructured\Tests\Unit\Schema;

use PHPUnit\Framework\TestCase;
use Omarsabbagh\PhpOpenaiStructured\Schema\BaseSchema;

class ConcreteSchema extends BaseSchema
{
    protected function getSchemaDefinition(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'test' => [
                    'type' => 'string'
                ]
            ]
        ];
    }
}

class BaseSchemaTest extends TestCase
{
    /**
     * Test implementation of BaseSchema for testing
     */
    public function testConstructorSetsNameAndStrict()
    {
        $schema = new ConcreteSchema('test_schema', true);

        $this->assertEquals('test_schema', $schema->getName());
        $this->assertTrue($schema->isStrict());

        $schema2 = new ConcreteSchema('another_schema', false);

        $this->assertEquals('another_schema', $schema2->getName());
        $this->assertFalse($schema2->isStrict());
    }

    public function testSetStrictUpdatesStrictValue()
    {
        $schema = new ConcreteSchema('test_schema', true);
        $this->assertTrue($schema->isStrict());

        $schema->setStrict(false);
        $this->assertFalse($schema->isStrict());

        $schema->setStrict(true);
        $this->assertTrue($schema->isStrict());
    }

    public function testToArrayReturnsCorrectStructure()
    {
        $schema = new ConcreteSchema('test_schema', true);

        $expected = [
            'name' => 'test_schema',
            'strict' => true,
            'schema' => [
                'type' => 'object',
                'properties' => [
                    'test' => [
                        'type' => 'string'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $schema->toArray());

        // Test with strict set to false
        $schema->setStrict(false);

        $expected['strict'] = false;

        $this->assertEquals($expected, $schema->toArray());
    }
}
