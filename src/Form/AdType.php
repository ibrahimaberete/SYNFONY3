<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends ApplicationType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("titre", " Tapez un super titre pour votre annonce"))
            ->add('slug', TextType::class, $this->getConfiguration("Adresse web", "Tapez l'adresse web(automatique)",['required' => false]))
            ->add('coverImage', UrlType::class, $this->getConfiguration("url de l'image principale", "Donnez l'adresse d'une image qui donne envie de venir"))
            ->add('introduction', TextType::class, $this->getConfiguration("Introduction", "donnez une description globale de l'annonce"))
            ->add('content',TextareaType::class, $this->getConfiguration("Description détaillé","tapez une description qui donne vraiment envie de venir chez vous"))
            ->add('rooms', IntegerType::class, $this->getConfiguration("Nombre de chambre", "Le nombre de chambres disponibles"))
            ->add('price', MoneyType::class, $this->getConfiguration("prix par nuit", "Indiquer le prix que vous voulez pour une nuit"))
            ->add(
                'images',
                CollectionType::class ,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true ,
                    'allow_delete' => true
                    
                ]

            )
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
