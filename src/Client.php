<?php

namespace Omarsabbagh\PhpOpenaiStructured;

use OpenAI;
use OpenAI\Client as OpenAIClient;
use Omarsabbagh\PhpOpenaiStructured\Schema\SchemaInterface;

class Client
{
    /**
     * @var OpenAIClient The OpenAI client instance
     */
    protected OpenAIClient $client;

    /**
     * @var string The model to use for completions
     */
    protected string $model = 'gpt-4o';

    /**
     * Create a new Client instance
     *
     * @param string $apiKey Your OpenAI API key
     * @param string|null $model The model to use (defaults to gpt-4o)
     */
    public function __construct(string $apiKey, ?string $model = null)
    {
        $this->client = OpenAI::client($apiKey);

        if ($model) {
            $this->model = $model;
        }
    }

    /**
     * Set the model to use
     *
     * @param string $model The model name
     * @return self
     */
    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Get the current model being used
     *
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Get the OpenAI client instance
     *
     * @return OpenAIClient
     */
    public function getOpenAIClient(): OpenAIClient
    {
        return $this->client;
    }

    /**
     * Make a chat completion request with structured output
     *
     * @param SchemaInterface $schema The schema to use for structured output
     * @param string $systemPrompt The system prompt
     * @param string|array $userPrompt The user prompt (string or array data)
     * @param array $options Additional options for the request
     * @return array The parsed response as an array
     */
    public function completeWithSchema(
        SchemaInterface $schema,
        string $systemPrompt,
        $userPrompt,
        array $options = []
    ): array {
        // Convert array to JSON if necessary
        if (is_array($userPrompt)) {
            $userPrompt = json_encode($userPrompt);
        }

        // Prepare the messages
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ];

        // Add any additional messages from options
        if (isset($options['messages']) && is_array($options['messages'])) {
            $messages = array_merge($messages, $options['messages']);
            unset($options['messages']);
        }

        // Prepare request parameters
        $parameters = array_merge([
            'model' => $this->model,
            'messages' => $messages,
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => $schema->toArray()
            ]
        ], $options);

        // Make the request
        $result = $this->client->chat()->create($parameters);

        // Get the response content
        $response = $result->choices[0]->message->content;

        // Parse and return the JSON response
        return json_decode($response, true);
    }
}
