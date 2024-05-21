<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Formation;
use App\Entity\Inscription;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('learner',EntityType::class, [
                'class' => User::class,
                'choice_label' => 'getFullName'
            ])
            ->add('formation',EntityType::class, [
                'class' => Formation::class,
                'choice_label' => 'getId'
            ])

            ->add('validated')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inscription::class,
        ]);
    }
}
