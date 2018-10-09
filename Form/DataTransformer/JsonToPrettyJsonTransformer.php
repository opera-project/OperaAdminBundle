<?php
namespace Opera\AdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class JsonToPrettyJsonTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return json_encode($value);
    }

    public function reverseTransform($value)
    {
        return json_decode($value);
    }
}