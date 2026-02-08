<?php

declare(strict_types=1);

namespace Tests\Integration\Elastic;

use Elastic\ElasticClientFactory;
use Elastic\Elasticsearch\Client;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ElasticClientFactoryTest extends KernelTestCase
{
    private ElasticClientFactory $elasticClientFactory;

    protected function setUp(): void
    {
        $this->elasticClientFactory = self::getContainer()->get(ElasticClientFactory::class);
    }

    #[Test]
    public function client_will_be_created(): void
    {
        $client = $this->elasticClientFactory->create();

        self::assertInstanceOf(Client::class, $client);
    }
}
