<?php

namespace AppBundle\Form;

use AppBundle\Entity\Entry;
use AppBundle\Entity\Ratio;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ratio', EntityType::class, [
                'class' => Ratio::class,
                'mapped' => false,
                'required' => false
            ])
            ->add('bgl')
            ->add('entryFoods', CollectionType::class, [
                'entry_type' => EntryFoodType::class,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true
            ])
            ->add('actualUnits', null, [
                'label' => 'Units',
                'required' => false
            ])
            ->add('notes', null, [
                'required' => false
            ])
            ->add('createdAt', DateTimeType::class, [
                'label' => 'Date',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'input' => 'datetime',
                'view_timezone' => 'America/Chicago'
            ])
            ->add('calculate', SubmitType::class, [
                'validation_groups' => false,
                'attr' => ['formnovalidate' => true]
            ])
            ->add('save', SubmitType::class, [
                'validation_groups' => ['Default', 'save'],
                'attr' => ['formnovalidate' => true]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Entry::class
        ]);
    }

    public function getName()
    {
        return 'app_bundle_entry_type';
    }
}
