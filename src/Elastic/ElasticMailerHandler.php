<?php

declare(strict_types=1);

namespace Elastic;

use Elastic\Elasticsearch\Client;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

final class ElasticMailerHandler extends AbstractProcessingHandler
{
    private readonly Client $client;

    public function __construct(ElasticClientFactory $clientFactory)
    {
        parent::__construct();
        $this->client = $clientFactory->create();
    }

    protected function write(LogRecord $record): void
    {
        $index = 'mail-logs-' . date('Y.m.d');

        $params = [
            'index' => $index,
            'body' => [
                '@timestamp' => $record->datetime->format('c'),
                'level' => $record->level->getName(),
                'message' => $record->message,
                'context' => $record->context,
                'extra' => $record->extra,
                'env' => $_ENV['APP_ENV'] ?? 'dev',
                'app' => 'f1simulator',
            ],
        ];

        $this->client->index($params);
    }
}
