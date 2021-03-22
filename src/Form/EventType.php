<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre de l'évênement",
                'attr' => [
                    'placeholder' => "Titre de l'évênement"
                ],
                'required' => false
            ])
            ->add('startedAt', DateTimeType::class, [
                'label' => "Date et heure de l'évênement",
                'date_widget'=> 'single_text',
                'time_widget'=> 'single_text',
                'required' => false
            ])
            ->add('description', CKEditorType::class, [
                'label' => "Description de l'évênement",
                'required' => false
            ])
            ->add('picture', FileType::class, [
                'label' => "Image de l'évênement",
                'mapped' => false,
                'required' => false
            ])
        ;
    }
    

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
