<?php

namespace App\Form;

use App\Entity\Etudiant;
use App\Entity\Section;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('section',EntityType::class, [
                    'class' => Section::class,
                    'required'=>false,
                    'expanded'=>false,
                    'multiple'=>false,
                    'query_builder'=>function(EntityRepository $er){
                        return $er->createQueryBuilder('s')
                            ->orderBy('s.designation', 'ASC');
                    },
                    'choice_label'=>'designation',
                    'attr'=>[
                        'class'=>'select2'
                    ]
                ])
            ->add('Editer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }
}
