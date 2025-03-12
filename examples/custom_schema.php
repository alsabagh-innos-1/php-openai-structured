<?php

require __DIR__ . '/../vendor/autoload.php';

use Omarsabbagh\PhpOpenaiStructured\Client;
use Omarsabbagh\PhpOpenaiStructured\Schema\ObjectSchema;

// Create a client with your API key
$apiKey = getenv('OPENAI_API_KEY') ?: 'your-api-key-here';
$client = new Client($apiKey);

// Create a custom schema for entity extraction
$schema = new ObjectSchema('entity_extraction');

// Add schema properties
$schema->addArrayProperty(
    'people',
    [
        'type' => 'object',
        'properties' => [
            'name' => ['type' => 'string', 'description' => 'The person\'s full name'],
            'role' => ['type' => 'string', 'description' => 'The person\'s role or occupation'],
        ],
        'required' => ['name']
    ],
    'List of people mentioned in the text',
    true
);

$schema->addArrayProperty(
    'organizations',
    [
        'type' => 'object',
        'properties' => [
            'name' => ['type' => 'string', 'description' => 'The organization\'s name'],
            'type' => ['type' => 'string', 'description' => 'The type of organization']
        ],
        'required' => ['name']
    ],
    'List of organizations mentioned in the text',
    true
);

$schema->addArrayProperty(
    'locations',
    [
        'type' => 'object',
        'properties' => [
            'name' => ['type' => 'string', 'description' => 'The location name'],
            'type' => ['type' => 'string', 'description' => 'The type of location (city, country, etc.)']
        ],
        'required' => ['name']
    ],
    'List of locations mentioned in the text',
    true
);

// Text to analyze
$text = "
Tim Cook, the CEO of Apple Inc., announced a new partnership with Microsoft Corporation yesterday 
at a tech conference in San Francisco, California. The partnership aims to improve cloud services 
integration between iOS and Windows platforms. Satya Nadella, Microsoft's CEO, expressed enthusiasm 
about working with Apple to enhance the user experience across devices.
";

// Define the system prompt
$systemPrompt = "
You are an entity extraction AI. Extract all people, organizations, and locations mentioned in the text.
For each entity, provide as much information as specified in the schema.
";

try {
    // Get the entities
    $result = $client->completeWithSchema($schema, $systemPrompt, $text);

    // Output the result
    echo "Entity Extraction Result:\n\n";

    echo "People:\n";
    foreach ($result['people'] as $person) {
        echo "- " . $person['name'];
        if (isset($person['role'])) {
            echo " (" . $person['role'] . ")";
        }
        echo "\n";
    }

    echo "\nOrganizations:\n";
    foreach ($result['organizations'] as $org) {
        echo "- " . $org['name'];
        if (isset($org['type'])) {
            echo " (" . $org['type'] . ")";
        }
        echo "\n";
    }

    echo "\nLocations:\n";
    foreach ($result['locations'] as $location) {
        echo "- " . $location['name'];
        if (isset($location['type'])) {
            echo " (" . $location['type'] . ")";
        }
        echo "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
