<?php

namespace AppBundle\Form;

use AppBundle\Entity\EntryFood;
use AppBundle\Entity\Food;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryFoodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('food', EntityType::class, [
                'class' => Food::class,
                'placeholder' => 'Select a food',
                'required' => false,
                'attr' => [
                    'class' => 'currentFoods'
                ]
            ])
            ->add('newFood', null, [
                'required' => false,
                'attr' => [
                    'class' => 'newFood'
                ]
            ])
            ->add('carbs', null, [
                'attr' => [
                    'class' => 'foodCarbs'
                ]
            ])
            ->add('servings');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EntryFood::class
        ]);
    }
}
