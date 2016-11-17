<?php

namespace AppBundle\Form;

use AppBundle\Entity\CorrectionFactor;
use AppBundle\Entity\SlidingScale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CorrectionFactorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('units')
            ->add('threshold');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CorrectionFactor::class
        ]);
    }

    public function getName()
    {
        return 'app_bundle_correction_factor_type';
    }
}
