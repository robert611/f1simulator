<?php

namespace Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\Then;
use Exception;
use Kernel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private KernelBrowser $client;
    private ?string $responseContent = null;
    private Response $response;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        if (file_exists(__DIR__ . '/../../.env.test')) {
            new Dotenv()->usePutenv()->load(__DIR__ . '/../../.env.test');
        }

        if (file_exists(__DIR__ . '/../../.env.test.local')) {
            new Dotenv()->usePutenv()->load(__DIR__ . '/../../.env.test.local');
        }

        // Tworzymy klienta ręcznie
        $kernel = new Kernel('test', true); // environment 'test', debug true
        $kernel->boot();

        $this->client = new KernelBrowser($kernel);
    }

    #[Given('I am on :path')]
    public function iAmOn(string $path): void
    {
        $this->client->request(Request::METHOD_GET, $path);
        $this->responseContent = $this->client->getResponse()->getContent();
        $this->response = $this->client->getResponse();
    }

    /**
     * @throws Exception
     */
    #[Then('I should see :text')]
    public function iShouldSee(string $text): void
    {
        if (null === $this->responseContent) {
            throw new Exception('Response content is null');
        }

        if (str_contains($text, $this->responseContent)) {
            throw new Exception("Expected text not found: $text");
        }
    }

    /**
     * @throws Exception
     */
    #[Then('the response status should be :status')]
    public function theResponseStatusShouldBe(int $status): void
    {
        $responseStatus = $this->response->getStatusCode();

        if ($status !== $responseStatus) {
            throw new Exception("Response status is: $responseStatus");
        }
    }

    /**
     * @throws Exception
     */
    #[Then('the page title should be :title')]
    public function thePageTitleShouldBe(string $title): void
    {
        if (!preg_match('/<title>(.*?)<\/title>/', $this->responseContent, $matches)) {
            throw new Exception('No <title> tag found');
        }

        if (trim($matches[1]) !== $title) {
            throw new Exception(sprintf(
                'Expected title "%s" but got "%s"',
                $title,
                trim($matches[1]),
            ));
        }
    }
}
