<?php

namespace App\Normalizer;

use App\Entity\Todo;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class TodoNormalizer extends ObjectNormalizer
{
    private UrlGeneratorInterface $router;
    private ObjectNormalizer $normalizer;

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
            $data['url'] = $this->router->generate('get_todo', [
                'id'=>$object->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []) : bool
    {
        return $data instanceof Todo;
    }
}
