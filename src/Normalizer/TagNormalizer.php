<?php

namespace App\Normalizer;

use App\Entity\Tag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class TagNormalizer extends ObjectNormalizer
{
    private $router;
    private $normalizer;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
        parent::__construct();
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        if(is_numeric($object->getId()))
        {
            $data['url'] = $this->router->generate('get_tag', [
                'id'=>$object->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Tag;
    }
}