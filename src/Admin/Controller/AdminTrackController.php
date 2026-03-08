<?php

declare(strict_types=1);

namespace Admin\Controller;

use Admin\Exception\TrackFilenameTakenException;
use Admin\Form\TrackEditFormModel;
use Admin\Form\TrackEditType;
use Admin\Form\TrackFormModel;
use Admin\Form\TrackType;
use Admin\Service\TrackPictureService;
use Domain\Contract\Exception\TrackCannotBeDeletedException;
use Domain\Contract\TrackServiceFacadeInterface;
use Domain\DomainFacadeInterface;
use Shared\Controller\BaseController;
use Shared\Service\FilenameSanitizer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin-track')]
class AdminTrackController extends BaseController
{
    public function __construct(
        private readonly DomainFacadeInterface $domainFacade,
        private readonly TrackServiceFacadeInterface $trackServiceFacade,
        private readonly ParameterBagInterface $parameterBag,
        private readonly TrackPictureService $trackPictureService,
    ) {
    }

    #[Route('', name: 'admin_track_index', methods: ['GET'])]
    public function index(): Response
    {
        $tracks = $this->domainFacade->getAllTracks();

        return $this->render('@admin/admin_track/index.html.twig', [
            'tracks' => $tracks,
        ]);
    }

    #[Route('/new', name: 'admin_track_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $trackFormModel = new TrackFormModel();
        $form = $this->createForm(TrackType::class, $trackFormModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TrackFormModel $trackFormModel */
            $trackFormModel = $form->getData();
            $uploadedFile = $trackFormModel->pictureFile;

            try {
                $pictureFilename = FilenameSanitizer::sanitize($uploadedFile);

                if ($this->trackPictureService->isFilenameTaken($pictureFilename)) {
                    throw new TrackFilenameTakenException();
                }

                $trackPicturesDirectory = $this->parameterBag->get('track_pictures_directory');

                $uploadedFile->move($trackPicturesDirectory, $pictureFilename);

                $this->trackServiceFacade->add(
                    $trackFormModel->name,
                    $pictureFilename,
                );

                $this->addFlash('admin_success', 'Dodano nowy tor');

                return $this->redirectToRoute('admin_track_index');
            } catch (TrackFilenameTakenException) {
                $form->addError(new FormError('Nazwa pliku jest już zajęta. Wybierz inną nazwę.'));
            } catch (FileException) {
                $this->addFlash('admin_error', 'Nie udało się zapisać pliku na serwerze. Spróbuj ponownie.');
            }
        }

        return $this->render('@admin/admin_track/new.html.twig', [
            'form' => $form->createView(),
            'trackFormModel' => $trackFormModel,
        ]);
    }

    #[Route('/{id}', name: 'admin_track_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $track = $this->domainFacade->getTrackById($id);

        return $this->render('@admin/admin_track/show.html.twig', [
            'track' => $track,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_track_edit', methods: ["GET", "POST"])]
    public function edit(Request $request, int $id): Response
    {
        $track = $this->domainFacade->getTrackById($id);

        $form = $this->createForm(TrackEditType::class, TrackEditFormModel::fromTrack($track));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TrackEditFormModel $trackFormModel */
            $trackFormModel = $form->getData();
            $uploadedFile = $trackFormModel->pictureFile;
            $pictureFilename = null;

            try {
                if ($uploadedFile) {
                    $pictureFilename = FilenameSanitizer::sanitize($uploadedFile);

                    $oldPictureTemporaryFilename = $this->trackPictureService->temporaryRename($track->getPicture());

                    if ($this->trackPictureService->isFilenameTaken($pictureFilename)) {
                        throw new TrackFilenameTakenException();
                    }

                    $trackPicturesDirectory = $this->parameterBag->get('track_pictures_directory');

                    $uploadedFile->move($trackPicturesDirectory, $pictureFilename);

                    $this->trackPictureService->remove($oldPictureTemporaryFilename);
                }

                $this->trackServiceFacade->update(
                    $track->getId(),
                    $trackFormModel->name,
                    $pictureFilename,
                );
            } catch (TrackFilenameTakenException) {
                $form->addError(new FormError('Nazwa pliku jest już zajęta. Wybierz inną nazwę.'));
                $this->trackPictureService->revertTemporaryRename($track->getPicture());
            } catch (FileException) {
                $this->addFlash('admin_error', 'Nie udało się zapisać pliku na serwerze. Spróbuj ponownie.');
                $this->trackPictureService->revertTemporaryRename($track->getPicture());
            }

            return $this->redirectToRoute('admin_track_edit', ['id' => $id]);
        }

        return $this->render('@admin/admin_track/edit.html.twig', [
            'track' => $track,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_track_delete', methods: ["POST", "DELETE"])]
    public function delete(Request $request, int $id): Response
    {
        if ($this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            try {
                $track = $this->domainFacade->getTrackById($id);
                $this->trackServiceFacade->delete($id);
                $this->trackPictureService->remove($track->getPicture());
                $this->addFlash('admin_success', 'Tor został usunięty');
            } catch (TrackCannotBeDeletedException) {
                $this->addFlash(
                    'admin_error',
                    'Tor nie może zostać usunięty ponieważ był użyty w istniejących sezonach',
                );
            }
        }

        return $this->redirectToRoute('admin_track_index');
    }
}
