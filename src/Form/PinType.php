<?php

namespace App\Form;

use App\Entity\Pin;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
         ->add ('imageFile', VichImageType::class, [
             'label' => 'Image (JPG ou PNG)',
            'required' => false ,
            'allow_delete' => true ,
            'download_label' => "Télécharger l'image" ,
            'download_uri' => true ,
            'imagine_pattern' => 'square_thumbnail_small',
           
            
       ])
            ->add('title', TextType::class)
            ->add('description', CKEditorType::class)
            
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pin::class,
           
        ]);
    }
}
