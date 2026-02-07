<?php

declare(strict_types=1);

namespace Elastic;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final readonly class ElasticClientFactory
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
    ) {
    }

    public function create(): Client
    {
        return ClientBuilder::create()
            ->setHosts([$this->parameterBag->get('elastic_host')])
            ->build();
    }
}
