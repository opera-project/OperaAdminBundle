<?php

namespace Opera\AdminBundle\Form;

use Opera\CoreBundle\Cms\BlockManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Opera\CoreBundle\Entity\Block;

class NewBlockType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name')
        ->add('type', ChoiceType::class, [
            'choices' => array_combine(
                $options['block_manager']->getKindsOfBlocks(),
                $options['block_manager']->getKindsOfBlocks()
            )
        ])
        ->add('area', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Block::class,
        ]);

        $resolver->setRequired('block_manager');
    }


}