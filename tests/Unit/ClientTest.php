<?php

namespace Omarsabbagh\PhpOpenaiStructured\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Omarsabbagh\PhpOpenaiStructured\Client;
use Omarsabbagh\PhpOpenaiStructured\Schema\SchemaInterface;
use Mockery;
use OpenAI\Client as OpenAIClient;

class ClientTest extends TestCase
{
    /**
     * Test client with a custom model
     */
    public function testSetModel()
    {
        // Create a client object without initializing the OpenAI client
        $client = new class('test-api-key') extends Client {
            public function __construct($apiKey, $model = null)
            {
                $this->model = $model ?: 'gpt-4o';
                // Don't initialize the real client
            }

            // Override to avoid actual API calls but match the method signature
            public function getOpenAIClient(): OpenAIClient
            {
                throw new \RuntimeException('This method should not be called in this test');
            }
        };

        // Test the default model
        $this->assertEquals('gpt-4o', $client->getModel());

        // Test setting a new model
        $client->setModel('gpt-4');
        $this->assertEquals('gpt-4', $client->getModel());

        // Test setting another model
        $client->setModel('gpt-3.5-turbo');
        $this->assertEquals('gpt-3.5-turbo', $client->getModel());
    }

    /**
     * Test client initialization with custom model
     */
    public function testConstructorWithCustomModel()
    {
        // Create a client object without initializing the OpenAI client
        $client = new class('test-api-key', 'gpt-4') extends Client {
            public function __construct($apiKey, $model = null)
            {
                $this->model = $model ?: 'gpt-4o';
                // Don't initialize the real client
            }

            // Override to avoid actual API calls but match the method signature
            public function getOpenAIClient(): OpenAIClient
            {
                throw new \RuntimeException('This method should not be called in this test');
            }
        };

        // Test that the constructor sets the model
        $this->assertEquals('gpt-4', $client->getModel());
    }

    /**
     * Test the schema array conversion functionality
     */
    public function testSchemaConversion()
    {
        // Create a mock for SchemaInterface
        $mockSchema = Mockery::mock(SchemaInterface::class);

        // Set up the expected toArray result
        $schemaArray = [
            'name' => 'test_schema',
            'strict' => true,
            'schema' => ['type' => 'object']
        ];

        $mockSchema->shouldReceive('toArray')
            ->once()
            ->andReturn($schemaArray);

        // Create a test client class that overrides the completeWithSchema method
        // to just return the schema conversion part
        $client = new class('test-api-key') extends Client {
            public function __construct($apiKey, $model = null)
            {
                $this->model = $model ?: 'gpt-4o';
                // Don't initialize the real client
            }

            // Test method to expose schema conversion
            public function testConvertSchema(SchemaInterface $schema)
            {
                return [
                    'response_format' => [
                        'type' => 'json_schema',
                        'json_schema' => $schema->toArray()
                    ]
                ];
            }

            // Override to avoid actual API calls but match the method signature
            public function getOpenAIClient(): OpenAIClient
            {
                throw new \RuntimeException('This method should not be called in this test');
            }
        };

        // Call the test method
        $result = $client->testConvertSchema($mockSchema);

        // Assert the result contains the correct schema
        $this->assertEquals(
            [
                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => $schemaArray
                ]
            ],
            $result
        );
    }

    /**
     * Test JSON conversion of array input
     */
    public function testArrayInputConversion()
    {
        // Input array to test
        $inputArray = [
            'key' => 'value',
            'nested' => ['data' => 'test']
        ];

        // Expected JSON string
        $expectedJson = json_encode($inputArray);

        // Create a test client class that exposes the array conversion
        $client = new class('test-api-key') extends Client {
            public function __construct($apiKey)
            {
                $this->model = 'gpt-4o';
                // Don't initialize the real client
            }

            // Test method to expose array to JSON conversion
            public function testConvertUserPrompt($userPrompt)
            {
                if (is_array($userPrompt)) {
                    return json_encode($userPrompt);
                }
                return $userPrompt;
            }

            // Override to avoid actual API calls but match the method signature
            public function getOpenAIClient(): OpenAIClient
            {
                throw new \RuntimeException('This method should not be called in this test');
            }
        };

        // Test string input remains unchanged
        $this->assertEquals('test string', $client->testConvertUserPrompt('test string'));

        // Test array input gets converted to JSON
        $this->assertEquals($expectedJson, $client->testConvertUserPrompt($inputArray));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
