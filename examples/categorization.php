<?php

require __DIR__ . '/../vendor/autoload.php';

use Omarsabbagh\PhpOpenaiStructured\Client;
use Omarsabbagh\PhpOpenaiStructured\Schema\CategorizationSchema;

// Create a client with your API key
$apiKey = getenv('OPENAI_API_KEY') ?: 'your-api-key-here';
$client = new Client($apiKey);

// Create a categorization schema
$schema = new CategorizationSchema('contact_categorization', ['Real', 'Fake']);

// Define the system prompt
$systemPrompt = "
You are an AI model trained to categorize contact information into 'Real' or 'Fake' based on given examples.
Analyze the given JSON-structured contact details and classify them accordingly.

Guidelines for classification:
1. Real Contacts:
   - Typically contain a full first name and last name (e.g., John Smith).
   - Have professional-looking email addresses (e.g., name@companydomain.com).
   - Usually follow common naming conventions without excessive abbreviations.

2. Fake Contacts:
   - Contain generic placeholders like 'Info' or 'Support'.
   - Have non-personalized email addresses (e.g., info@company.com, support@company.com).
   - Use incomplete names or single initials.
";

// Input data to categorize
$input = [
    "first_name" => "John",
    "last_name" => "Smith",
    "email" => "jsmith@example.com"
];

try {
    // Get the categorization result
    $result = $client->completeWithSchema($schema, $systemPrompt, $input);

    // Output the result
    echo "Categorization Result:\n";
    echo "Category: " . $result['category'] . "\n";
    echo "Reason: " . $result['reason'] . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
