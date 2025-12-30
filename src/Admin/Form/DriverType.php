<?php

declare(strict_types=1);

namespace Admin\Form;

use Domain\Contract\DTO\TeamDTO;
use Domain\DomainFacadeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DriverType extends AbstractType
{
    public function __construct(
        private readonly DomainFacadeInterface $domainFacade,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $teams = $this->domainFacade->getAllTeams();
        $teamsChoiceList = $this->teamsToChoiceList($teams);

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Imię',
            ])
            ->add('surname', TextType::class, [
                'required' => true,
                'label' => 'Nazwisko',
            ])
            ->add('carNumber', NumberType::class, [
                'required' => true,
                'label' => 'Numer samochodu',
                'attr' => ['min' => 1, 'max' => 999],
            ])
            ->add('teamId', ChoiceType::class, [
                'choices' => $teamsChoiceList,
                'label' => 'Zespół',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DriverFormModel::class,
        ]);
    }

    /**
     * @param TeamDTO[] $teams
     * @return array<int, string>
     */
    private function teamsToChoiceList(array $teams): array
    {
        $result = [];

        foreach ($teams as $team) {
            $result[$team->getName()] = $team->getId();
        }

        return $result;
    }
}
