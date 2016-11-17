<?php

namespace AppBundle\Form;

use AppBundle\Entity\Entry;
use AppBundle\Entity\Ratio;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
                'mapped' => false
            ])
            ->add('bgl')
            ->add('carbs')
            ->add('actualUnits', null, [
                'label' => 'Units',
                'required' => false
            ])
            ->add('notes', null, [
                'required' => false
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
