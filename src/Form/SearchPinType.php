<?php

namespace App\Form;

use App\Entity\Pin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchPinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mots', SearchType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control mt-5 searchbar ',
                    'placeholder' => 'Entrez un ou plusieurs mots-clés'
                ]
            ])
            ->add('Recherchez', SubmitType::class,[
                'attr' => [
                    'class' => ''
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
