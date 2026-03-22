<?php

declare(strict_types=1);

namespace Admin\Form;

use Domain\Contract\DTO\TrackDTO;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

final class TrackEditFormModel
{
    #[Assert\NotBlank(message: 'Nazwa toru jest wymagana.')]
    #[Assert\Length(max: 86, maxMessage: 'Nazwa toru może mieć maksymalnie 86 znaków.')]
    public string $name;

    #[Assert\Image(
        maxSize: '5M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
        mimeTypesMessage: 'Dozwolone są pliki JPG, PNG lub WEBP.',
        extensions: ['jpg', 'jpeg', 'png', 'webp']
    )]
    public ?UploadedFile $pictureFile = null;

    public static function fromTrack(TrackDto $trackDto): self
    {
        $model = new self();
        $model->name = $trackDto->getName();
        $model->pictureFile = null;

        return $model;
    }
}
