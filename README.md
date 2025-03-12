# PHP OpenAI Structured Output

[![PHP Composer](https://github.com/omarsabbagh/php-openai-structured/actions/workflows/php.yml/badge.svg)](https://github.com/omarsabbagh/php-openai-structured/actions/workflows/php.yml)
[![Coding Standards](https://github.com/omarsabbagh/php-openai-structured/actions/workflows/coding-standards.yml/badge.svg)](https://github.com/omarsabbagh/php-openai-structured/actions/workflows/coding-standards.yml)
[![Static Analysis](https://github.com/omarsabbagh/php-openai-structured/actions/workflows/static-analysis.yml/badge.svg)](https://github.com/omarsabbagh/php-openai-structured/actions/workflows/static-analysis.yml)
[![PHP Compatibility](https://github.com/omarsabbagh/php-openai-structured/actions/workflows/php-compatibility.yml/badge.svg)](https://github.com/omarsabbagh/php-openai-structured/actions/workflows/php-compatibility.yml)
[![codecov](https://codecov.io/gh/omarsabbagh/php-openai-structured/branch/main/graph/badge.svg)](https://codecov.io/gh/omarsabbagh/php-openai-structured)

A PHP package for easily working with OpenAI's structured output feature. This package provides a simple and intuitive way to define JSON schemas for OpenAI API calls and process the structured responses.

## Features

- Simple client for making OpenAI API calls with structured output
- Fluent interface for building JSON schemas
- Pre-built schema types for common use cases
- Type-safe PHP classes with proper documentation

## Installation

Install via Composer:

```bash
composer require omarsabbagh/php-openai-structured
```

## Basic Usage

```php
<?php

require 'vendor/autoload.php';

use Omarsabbagh\PhpOpenaiStructured\Client;
use Omarsabbagh\PhpOpenaiStructured\Schema\CategorizationSchema;

// Create a client with your API key
$client = new Client('your-openai-api-key');

// Create a categorization schema
$schema = new CategorizationSchema('contact_categorization', ['Real', 'Fake']);

// Define the system prompt
$systemPrompt = "
You are an AI model trained to categorize contact information into 'Real' or 'Fake'.
Analyze the given JSON-structured contact details and classify them accordingly.
";

// Input data to categorize
$input = [
    "first_name" => "John",
    "last_name" => "Smith",
    "email" => "jsmith@example.com"
];

// Get the categorization result
$result = $client->completeWithSchema($schema, $systemPrompt, $input);

// Use the structured result
echo "Category: " . $result['category'] . "\n";
echo "Reason: " . $result['reason'] . "\n";
```

## Building Custom Schemas

You can build custom schemas for more complex use cases:

```php
<?php

use Omarsabbagh\PhpOpenaiStructured\Schema\ObjectSchema;

// Create a custom schema
$schema = new ObjectSchema('entity_extraction');

// Add properties to the schema
$schema->addArrayProperty(
    'people',
    [
        'type' => 'object',
        'properties' => [
            'name' => ['type' => 'string', 'description' => 'Full name'],
            'role' => ['type' => 'string', 'description' => 'Role or occupation'],
        ],
        'required' => ['name']
    ],
    'List of people mentioned in the text',
    true
);

// Add more properties as needed...
```

## Available Schema Types

### CategorizationSchema

For categorizing input into predefined categories:

```php
$schema = new CategorizationSchema('name', ['Category1', 'Category2']);
```

### ObjectSchema

For creating custom object schemas:

```php
$schema = new ObjectSchema('name');
$schema->addProperty('property', 'string', 'Description', true);
$schema->addEnumProperty('status', ['active', 'inactive'], 'Status description', true);
$schema->setAdditionalProperties(false);
```

## Creating Your Own Schema Types

You can create your own schema types by extending the `BaseSchema` class:

```php
class MyCustomSchema extends BaseSchema
{
    protected function getSchemaDefinition(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                // Your custom properties here
            ],
            'required' => ['property1', 'property2'],
            'additionalProperties' => false
        ];
    }
}
```

## Examples

Check the `examples` directory for more examples of how to use this package:

- `examples/categorization.php` - Basic categorization example
- `examples/custom_schema.php` - Custom schema for entity extraction

## Development

### Running Tests

This package includes a comprehensive test suite. To run the tests:

```bash
composer install
vendor/bin/phpunit
```

The test suite includes:

- **Unit Tests**: Tests for individual components
- **Integration Tests**: Tests for components working together

### Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Requirements

- PHP 8.1 or higher
- OpenAI API key
- Composer for dependency management

## License

MIT License

## Further Reading

- [OpenAI API Documentation](https://platform.openai.com/docs/introduction)
- [JSON Schema Standard](https://json-schema.org/) 