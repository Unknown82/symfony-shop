<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('description')
            ->add('price')
            ->add('image', FileType::class,
                [
                    'mapped' => false,
                    'required' => false,
                    'attr' => [
                        'class' => 'w-full p-2 border rounded-lg text-black bg-white',
                        'accept' => 'image/*'
                    ],
                    'label_attr' => ['class' => 'text-gray-700 font-semibold'],
                    'constraints' => [
                        new File([
                            'maxSize' => '2M',
                            'mimeTypes' => ['image/jpeg', 'image/webp', 'image/png', 'image/svg', 'image/gif'],
                            'mimeTypesMessage' => 'Bitte laden Sie eine gültige Bilddatei hoch (JPEG, JPG, PNG, GIF, WEBP)',
                        ])
                    ]
                ]
            )
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
