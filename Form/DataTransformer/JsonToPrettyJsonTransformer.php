<?php
namespace Opera\AdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class JsonToPrettyJsonTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        $result = json_encode($value);

        if ($result === 'null') {
            return "";
        }

        return $result;
    }

    public function reverseTransform($value)
    {
        return json_decode($value);
    }
}