<?php

declare(strict_types=1);

namespace Admin\Form;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

final class TrackFormModel
{
    #[Assert\NotBlank(message: 'Nazwa toru jest wymagana.')]
    #[Assert\Length(max: 86, maxMessage: 'Nazwa toru może mieć maksymalnie 86 znaków.')]
    public string $name;

    #[Assert\NotNull(message: 'Wgraj zdjęcie toru.')]
    #[Assert\Image(
        maxSize: '5M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
        mimeTypesMessage: 'Dozwolone są pliki JPG, PNG lub WEBP.',
        extensions: ['jpg', 'jpeg', 'png', 'webp']
    )]
    public UploadedFile $pictureFile;

    #[Assert\NotBlank(message: 'Szerokość geograficzna jest wymagana.')]
    #[Assert\Length(max: 64, maxMessage: 'Szerokość geograficzna może mieć maksymalnie 64 znaki.')]
    public string $latitude;

    #[Assert\NotBlank(message: 'Długość geograficzna jest wymagana.')]
    #[Assert\Length(max: 64, maxMessage: 'Długość geograficzna może mieć maksymalnie 64 znaki.')]
    public string $longitude;
}
