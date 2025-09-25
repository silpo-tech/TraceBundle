# Trace Bundle

A Symfony bundle for distributed tracing and request correlation across microservices.

## Features

- **Request ID Generation**: Automatically generates unique trace IDs for incoming requests
- **Request ID Propagation**: Forwards trace IDs to outgoing HTTP requests via Guzzle middleware
- **Logging Integration**: Adds trace IDs to Monolog log entries
- **Console Support**: Generates trace IDs for console commands
- **Sentry Integration**: Enriches Sentry transactions with trace information
- **Message Bus Support**: Correlates messages with trace IDs (requires silpo-tech/message-bus-bundle)

## Installation

```bash
composer require silpo-tech/trace-bundle
```

## Configuration

Add the bundle to your `config/bundles.php`:

```php
return [
    // ...
    TraceBundle\TraceBundle::class => ['all' => true],
];
```

### Bundle Configuration

Create `config/packages/trace.yaml`:

```yaml
trace:
    id_header_name: 'X-Request-Id'        # Header name for trace ID (default)
    id_log_extra_name: 'requestId'        # Log field name for trace ID (default)
    autoconfigure_handlers: true          # Auto-configure Guzzle handlers (default)
```

## Usage

### HTTP Requests

The bundle automatically:
1. Extracts trace ID from incoming request headers (`X-Request-Id` by default)
2. Generates a new UUID if no trace ID is present
3. Adds the trace ID to response headers
4. Propagates trace ID to outgoing HTTP requests via Guzzle middleware

### Logging

Trace IDs are automatically added to all log entries:

```php
$logger->info('Processing user request'); 
// Log will include: {"message": "Processing user request", "extra": {"requestId": "uuid-here"}}
```

### Console Commands

For console commands, a trace ID is automatically generated and available throughout the command execution.

### Manual Access

You can access the current trace ID programmatically:

```php
use TraceBundle\Storage\TraceIdStorageInterface;

class YourService
{
    public function __construct(
        private TraceIdStorageInterface $traceIdStorage
    ) {}
    
    public function someMethod(): void
    {
        $traceId = $this->traceIdStorage->get();
        // Use trace ID as needed
    }
}
```

## Integration

### Sentry

When `sentry/sentry` is installed, the bundle automatically enriches Sentry transactions with trace information.

### Message Bus

When `silpo-tech/message-bus-bundle` is installed, trace IDs are automatically propagated through message bus operations.

## Requirements

- PHP ≥8.3
- Symfony ≥6.4|^7.0

## Dependencies

### Required
- `symfony/http-kernel`
- `symfony/dependency-injection`
- `symfony/config`
- `ramsey/uuid`
- `monolog/monolog`
- `guzzlehttp/guzzle`
- `symfony/yaml`

### Optional
- `sentry/sentry` - For Sentry integration
- `silpo-tech/message-bus-bundle` - For message bus correlation

## Testing

```bash
composer install
vendor/bin/phpunit
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contributing

This is a proprietary package. Please contact the maintainers for contribution guidelines.
