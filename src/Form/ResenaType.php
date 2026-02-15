<?php

namespace App\Form;

use App\Entity\Resena;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ResenaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Puntuación obligatoria entre 1 y 5
            ->add('puntuacion', IntegerType::class, [
                'label' => 'Puntuación (1-5)',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range([
                        'min' => 1,
                        'max' => 5,
                        'notInRangeMessage' => 'La puntuación debe estar entre {{ min }} y {{ max }}',
                    ])
                ]
            ])

            // Comentario opcional
            ->add('comentario', TextareaType::class, [
                'label' => 'Comentario',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resena::class,
        ]);
    }
}
