<?php

namespace Omarsabbagh\PhpOpenaiStructured\Tests\Unit;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use OpenAI\Client as OpenAIClient;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Responses\Chat\CreateResponseChoice;
use OpenAI\Responses\Chat\CreateResponseMessage;
use Omarsabbagh\PhpOpenaiStructured\Client;
use Omarsabbagh\PhpOpenaiStructured\Schema\SchemaInterface;

class ClientTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var MockInterface The mock OpenAI client
     */
    private $mockOpenAI;

    /**
     * @var MockInterface The mock chat interface
     */
    private $mockChat;

    /**
     * @var MockInterface The mock schema
     */
    private $mockSchema;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup OpenAI client mocks
        $this->mockChat = Mockery::mock();
        $this->mockOpenAI = Mockery::mock('overload:OpenAI');
        $this->mockOpenAI->shouldReceive('client')->andReturn(Mockery::mock(OpenAIClient::class));

        // Setup schema mock
        $this->mockSchema = Mockery::mock(SchemaInterface::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testConstructorSetsApiKeyAndDefaultModel()
    {
        $client = new Client('test-api-key');
        $this->assertEquals('gpt-4o', $client->getModel());
    }

    public function testConstructorSetsCustomModel()
    {
        $client = new Client('test-api-key', 'gpt-4');
        $this->assertEquals('gpt-4', $client->getModel());
    }

    public function testSetModelChangesModel()
    {
        $client = new Client('test-api-key');
        $this->assertEquals('gpt-4o', $client->getModel());

        $client->setModel('gpt-4');
        $this->assertEquals('gpt-4', $client->getModel());

        $client->setModel('gpt-3.5-turbo');
        $this->assertEquals('gpt-3.5-turbo', $client->getModel());
    }

    public function testGetOpenAIClientReturnsClient()
    {
        $client = new Client('test-api-key');
        $this->assertInstanceOf(OpenAIClient::class, $client->getOpenAIClient());
    }

    public function testCompleteWithSchemaReturnsCorrectResponse()
    {
        // Setup OpenAI response mocks
        $jsonResponse = json_encode(['result' => 'test result']);

        $mockResponseMessage = Mockery::mock(CreateResponseMessage::class);
        $mockResponseMessage->content = $jsonResponse;

        $mockChoice = Mockery::mock(CreateResponseChoice::class);
        $mockChoice->message = $mockResponseMessage;

        $mockResponse = Mockery::mock(CreateResponse::class);
        $mockResponse->choices = [$mockChoice];

        // Configure the chat mock to return our response
        $this->mockChat->shouldReceive('create')->andReturn($mockResponse);

        // Configure the OpenAI client mock to return the chat mock
        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockOpenAIClient->shouldReceive('chat')->andReturn($this->mockChat);

        // Configure the schema mock
        $this->mockSchema->shouldReceive('toArray')->andReturn([
            'name' => 'test_schema',
            'strict' => true,
            'schema' => ['type' => 'object']
        ]);

        // Create a partial mock of our client to use the mocked OpenAI client
        $client = Mockery::mock(Client::class, ['test-api-key'])->makePartial();
        $client->shouldReceive('getOpenAIClient')->andReturn($mockOpenAIClient);

        // Define test data
        $systemPrompt = 'This is a system prompt';
        $userPrompt = 'This is a user prompt';

        // Call the method
        $expectedResult = ['result' => 'test result'];
        $result = $client->completeWithSchema($this->mockSchema, $systemPrompt, $userPrompt);

        // Assert the result matches our expectation
        $this->assertEquals($expectedResult, $result);
    }

    public function testCompleteWithSchemaConvertsArrayUserPromptToJson()
    {
        // Setup OpenAI response mocks
        $jsonResponse = json_encode(['result' => 'test result']);

        $mockResponseMessage = Mockery::mock(CreateResponseMessage::class);
        $mockResponseMessage->content = $jsonResponse;

        $mockChoice = Mockery::mock(CreateResponseChoice::class);
        $mockChoice->message = $mockResponseMessage;

        $mockResponse = Mockery::mock(CreateResponse::class);
        $mockResponse->choices = [$mockChoice];

        // Configure the chat mock to expect a JSON string user prompt
        $this->mockChat->shouldReceive('create')
            ->with(Mockery::on(function ($params) {
                $userMessage = $params['messages'][1];
                return $userMessage['role'] === 'user' && is_string($userMessage['content']);
            }))
            ->andReturn($mockResponse);

        // Configure the OpenAI client mock to return the chat mock
        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockOpenAIClient->shouldReceive('chat')->andReturn($this->mockChat);

        // Configure the schema mock
        $this->mockSchema->shouldReceive('toArray')->andReturn([
            'name' => 'test_schema',
            'strict' => true,
            'schema' => ['type' => 'object']
        ]);

        // Create a partial mock of our client to use the mocked OpenAI client
        $client = Mockery::mock(Client::class, ['test-api-key'])->makePartial();
        $client->shouldReceive('getOpenAIClient')->andReturn($mockOpenAIClient);

        // Define test data with an array user prompt
        $systemPrompt = 'This is a system prompt';
        $userPrompt = ['key' => 'value', 'nested' => ['data' => 'test']];

        // Call the method
        $expectedResult = ['result' => 'test result'];
        $result = $client->completeWithSchema($this->mockSchema, $systemPrompt, $userPrompt);

        // Assert the result matches our expectation
        $this->assertEquals($expectedResult, $result);
    }
}
