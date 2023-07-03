<?php

namespace telconet\schemaBundle\Service;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Servicio para realizar encode/decode y serialize/deserialize.
 */
class SerializerService {
    
    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    private $serializer;
    
    public function __construct()
    {
        $encoder = new JsonEncoder();
        $normalizer = new GetSetMethodNormalizer();
        $this->serializer = new Serializer(array($normalizer), array($encoder));
    }
    
    /**
     * Shortcut to decode data into array.
     * @see \Symfony\Component\Serializer\Serializer -> decode()
     */
    public function decode($data, $format = 'json')
    {
        return $this->serializer->decode($data, $format);
    }
    
    /**
     * Shortcut to encode data into array.
     * @see \Symfony\Component\Serializer\Serializer -> encode()
     */
    public function encode($data, $format = 'json')
    {
        return $this->serializer->encode($data, $format);
    }
    
    /**
     * Shortcut to deserialize data into the given type.
     * @see \Symfony\Component\Serializer\Serializer -> deserialize()
     */
    public function deserialize($data, $type, $format = 'json')
    {
        return $this->serializer->deserialize($data, $type, $format);
    }
    
    /**
     * Shortcut to serialize data in the appropriate format.
     * @see \Symfony\Component\Serializer\Serializer -> serialize()
     */
    public function serialize($data, $format = 'json')
    {
        return $this->serializer->serialize($data, $format);
    }
    
}
