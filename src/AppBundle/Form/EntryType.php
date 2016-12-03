<?php

namespace AppBundle\Form;

use AppBundle\Entity\Entry;
use AppBundle\Entity\Food;
use AppBundle\Entity\Ratio;
use AppBundle\Entity\Tag;
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
use Symfony\Component\Validator\Constraints\Valid;

class EntryType extends AbstractType
{

    private $em;
    private $user;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->em = $options['em'];
        $this->user = $options['user'];

        $builder
            ->add('ratio', EntityType::class, [
                'class' => Ratio::class,
//                'mapped' => false,
                'required' => false
            ])
            ->add('bgl')
            ->add('entryFoods', CollectionType::class, [
                'entry_type' => EntryFoodType::class,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'constraints' => [new Valid()]
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
                'validation_groups' => ['Default', 'calculate'],
                'attr' => ['formnovalidate' => true]
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => ['class' => 'select2']
            ])
            ->add('save', SubmitType::class, [
                'validation_groups' => ['Default', 'save'],
                'attr' => ['formnovalidate' => true]
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Entry::class,
        ]);

        $resolver->setRequired('em');
        $resolver->setRequired('user');
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (!empty($data['tags'])) {
            $tagRepo = $this->em->getRepository(Tag::class);
            foreach ($data['tags'] as $key=>$tag) {
                if (!$tagRepo->find($tag)) {
                    $newTag = new Tag();
                    $newTag->setName($tag);
                    $newTag->setUser($this->user);
                    $this->em->persist($newTag);
                    $this->em->flush();
                    $data['tags'][$key] = $newTag->getId();
                }
            }
        }

        if (!empty($data['entryFoods'])) {
            foreach ($data['entryFoods'] as $key=>$food) {
                if (!empty($food['newFood'])) {
                    $newFood = new Food();
                    $newFood->setCarbs($food['carbs']);
                    $newFood->setName($food['newFood']);
                    $newFood->setUser($this->user);
                    $this->em->persist($newFood);
                    $this->em->flush();

                    $data['entryFoods'][$key]['food'] = $newFood->getId();
                }
            }
        }

        $event->setData($data);
    }
}
