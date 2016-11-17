<?php

namespace AppBundle\Form;

use AppBundle\Entity\Ratio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RatioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('carbs', null, [
                'label' => 'Carbs'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ratio::class
        ]);
    }

    public function getName()
    {
        return 'app_bundle_ratio_type';
    }
}
