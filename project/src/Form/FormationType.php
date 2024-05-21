<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Formation;
use App\Entity\Section;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            #->add('date')

            ->add('level', ChoiceType::class, [
                'choices' => ['Debutant' => 'Debutant','Intermédiare' => 'Intermediare','Avancé' => 'Aavancé'],
            ])
            // ->add('hours')
           
            
            // ->add('rate')
            // ->add('popular')

            ->add('category')

            ->add('thumbnail', FileType::class);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
