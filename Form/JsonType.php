<?php
namespace Opera\AdminBundle\Form;

use Opera\AdminBundle\Form\DataTransformer\JsonToPrettyJsonTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class JsonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->addViewTransformer(new JsonToPrettyJsonTransformer());
    }

    public function getParent()
    {
        return TextareaType::class;
    }
}