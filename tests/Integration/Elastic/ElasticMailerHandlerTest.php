<?php

declare(strict_types=1);

namespace Tests\Integration\Elastic;

use DateTimeImmutable;
use Elastic\ElasticMailerHandler;
use Elastic\Transport\Exception\NoNodeAvailableException;
use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ElasticMailerHandlerTest extends KernelTestCase
{
    private ElasticMailerHandler $elasticMailerHandler;

    public function setUp(): void
    {
        $this->elasticMailerHandler = self::getContainer()->get(ElasticMailerHandler::class);
    }

    #[Test]
    public function log_is_written_to_elasticsearch(): void
    {
        // expected
        $this->expectException(NoNodeAvailableException::class);

        // given
        $logRecord = new LogRecord(
            datetime: new DateTimeImmutable(),
            channel: 'test',
            level: Level::Error,
            message: 'Test message',
            context: ['foo' => 'bar'],
            extra: [],
        );

        // and given
        $this->elasticMailerHandler->handle($logRecord);
    }
}
