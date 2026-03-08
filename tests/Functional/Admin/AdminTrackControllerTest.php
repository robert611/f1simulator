<?php

declare(strict_types=1);

namespace Tests\Functional\Admin;

use Admin\Service\TrackPictureService;
use Domain\Repository\TrackRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Path;
use Tests\Common\Fixtures;
use Tests\Common\TestFileHelper;

final class AdminTrackControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;
    private TestFileHelper $fileHelper;
    private TrackRepository $trackRepository;
    private TrackPictureService $trackPictureService;
    private ParameterBagInterface $parameterBag;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->fileHelper = self::getContainer()->get(TestFileHelper::class);
        $this->trackRepository = self::getContainer()->get(TrackRepository::class);
        $this->trackPictureService = self::getContainer()->get(TrackPictureService::class);
        $this->parameterBag = self::getContainer()->get(ParameterBagInterface::class);
    }

    #[Test]
    #[DataProvider('provideUrls')]
    public function only_admin_can_access_admin_driver_endpoints(string $method, string $url): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // when
        $this->client->request($method, $url);

        // then
        self::assertResponseStatusCodeSame(302);
    }

    #[Test]
    public function admin_track_page_is_successful(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/admin-track');

        // then
        self::assertResponseIsSuccessful();
    }

    #[Test]
    public function admin_track_page_displays_all_tracks(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $this->fixtures->aTrack('Silverstone', 'Silverstone.png');
        $this->fixtures->aTrack('Spain', 'Spain.png');
        $this->fixtures->aTrack('Belgium', 'Belgium.png');
        $this->fixtures->aTrack('Netherlands', 'Netherlands.png');

        // when
        $this->client->request('GET', '/admin-track');

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertSelectorTextContains('body', 'Silverstone');
        self::assertSelectorTextContains('body', 'Spain');
        self::assertSelectorTextContains('body', 'Belgium');
        self::assertSelectorTextContains('body', 'Netherlands');
    }

    #[Test]
    public function new_track_form_can_be_displayed(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', "/admin-track/new");

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertSelectorTextContains('body', 'Dodanie nowego toru');
        self::assertSelectorTextContains('body', 'Nazwa toru');
        self::assertSelectorTextContains('body', 'Zdjęcie toru');
    }

    #[Test]
    public function new_track_can_be_added(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $picture = $this->fileHelper->anImageFile();
        $picturePath = $picture->getPathname();

        // when
        $crawler = $this->client->request('GET', "/admin-track/new");
        $form = $crawler->selectButton('Zapisz')->form([
            'track[name]' => 'Silverstone',
        ]);
        $form['track[pictureFile]']->setValue($picturePath);
        $this->client->submit($form);

        // then
        self::assertResponseRedirects("/admin-track");

        // follow redirection
        $this->client->followRedirect();

        // and then
        self::assertSelectorTextContains('body', 'Dodano nowy tor');
        self::assertEquals(1, $this->trackRepository->count());

        // and then
        $track = $this->trackRepository->findOneBy([]);
        self::assertEquals('Silverstone', $track->getName());
        self::assertEquals($picture->getClientOriginalName(), $track->getPicture());

        // and then (remove added file)
        $this->trackPictureService->remove($track->getPicture());
    }

    #[Test]
    public function new_track_picture_must_have_unique_filename(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $picture = $this->fileHelper->anImageFile();
        $picturePath = $picture->getPathname();

        // and given (file will already exist, thus filename will be taken)
        $trackPicturesDirectory = $this->parameterBag->get('track_pictures_directory');
        copy($picturePath, Path::join($trackPicturesDirectory, $picture->getClientOriginalName()));

        // when
        $crawler = $this->client->request('GET', "/admin-track/new");
        $form = $crawler->selectButton('Zapisz')->form([
            'track[name]' => 'Silverstone',
        ]);
        $form['track[pictureFile]']->setValue($picturePath);
        $this->client->submit($form);

        // and then
        self::assertSelectorTextContains('body', 'Nazwa pliku jest już zajęta. Wybierz inną nazwę.');
        self::assertEquals(0, $this->trackRepository->count());

        // and then (remove added file)
        $this->trackPictureService->remove($picture->getClientOriginalName());
    }

    #[Test]
    public function admin_track_show_page_can_be_displayed(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $track = $this->fixtures->aTrack('Silverstone', 'Silverstone.png');

        // when
        $this->client->request('GET', "/admin-track/{$track->getId()}");

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertSelectorTextContains('body', (string) $track->getId());
        self::assertSelectorTextContains('body', $track->getName());
        self::assertSelectorTextContains('body', $track->getPicture());
    }

    #[Test]
    public function admin_track_edit_form_can_be_displayed(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $track = $this->fixtures->aTrack('Silverstone', 'Silverstone.png');

        // when
        $this->client->request('GET', "/admin-track/{$track->getId()}/edit");

        // then
        self::assertResponseIsSuccessful();
        $crawler = $this->client->getCrawler();

        // and then
        self::assertSame(
            $track->getName(),
            $crawler->filter('input[name="track_edit[name]"]')->attr('value'),
        );
        self::assertSelectorTextContains('body', $track->getPicture());
    }

    #[Test]
    public function admin_track_edition_works(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $track = $this->fixtures->aTrack('Silverstone', 'Silverstone.png');

        // and given
        $picture = $this->fileHelper->anImageFile();
        $picturePath = $picture->getPathname();

        // when
        $crawler = $this->client->request('GET', "/admin-track/{$track->getId()}/edit");
        $form = $crawler->selectButton('Zapisz')->form([
            'track_edit[name]' => 'Silverstone (edition)',
        ]);
        $form['track_edit[pictureFile]']->setValue($picturePath);
        $this->client->submit($form);

        // then
        self::assertResponseRedirects("/admin-track/{$track->getId()}/edit");

        // and then
        $track = $this->trackRepository->find($track->getId());
        self::assertSame('Silverstone (edition)', $track->getName());
        self::assertSame($picture->getClientOriginalName(), $track->getPicture());

        // and then (remove added file)
        $this->trackPictureService->remove($picture->getClientOriginalName());
    }

    #[Test]
    public function admin_track_edition_without_new_picture_works(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $track = $this->fixtures->aTrack('Silverstone', 'Silverstone.png');

        // when
        $crawler = $this->client->request('GET', "/admin-track/{$track->getId()}/edit");
        $form = $crawler->selectButton('Zapisz')->form([
            'track_edit[name]' => 'Silverstone (edition)',
        ]);
        $this->client->submit($form);

        // then
        self::assertResponseRedirects("/admin-track/{$track->getId()}/edit");

        // and then
        $track = $this->trackRepository->find($track->getId());
        self::assertSame('Silverstone (edition)', $track->getName());
        self::assertSame('Silverstone.png', $track->getPicture());
    }

    public static function provideUrls(): array
    {
        return [
            ['GET', '/admin-track'],
            ['GET', '/admin-track/new'],
            ['POST', '/admin-track/new'],
        ];
    }
}
