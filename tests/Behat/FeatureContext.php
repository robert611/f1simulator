<?php

namespace Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\Then;
use Kernel;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private KernelBrowser $client;
    private ?string $responseContent = null;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
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
    }

    #[Then('I should see :text')]
    public function iShouldSee(string $text): void
    {
        Assert::assertNotNull($this->responseContent, 'Response content is null');
        Assert::assertStringContainsString($text, $this->responseContent);
    }

    #[Then('the response status should be :status')]
    public function theResponseStatusShouldBe(int $status): void
    {
        Assert::assertSame(
            $status,
            $this->client->getResponse()->getStatusCode(),
        );
    }

    #[Then('the page title should be :title')]
    public function thePageTitleShouldBe(string $title): void
    {
        preg_match('/<title>(.*?)<\/title>/', $this->responseContent, $matches);

        Assert::assertNotEmpty($matches, 'No <title> tag found');
        Assert::assertSame($title, trim($matches[1]));
    }
}
