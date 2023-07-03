<?php

namespace telconet\schemaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Clase base para todos los controllers que usan Doctrine,
 * EntityManager y Parameters.
 * @see \Symfony\Bundle\FrameworkBundle\Controller\Controller
 */
abstract class BaseController extends ContainerAware {
    
    /**
     * Shortcut to decode data into array.
     * @see \Symfony\Component\Serializer\Serializer -> decode()
     */
    public function decode($data, $format = 'json')
    {
        return $this->get('schema.Serializer')->decode($data, $format);
    }
    
    /**
     * Shortcut to encode data into array.
     * @see \Symfony\Component\Serializer\Serializer -> encode()
     */
    public function encode($data, $format = 'json')
    {
        return $this->get('schema.Serializer')->encode($data, $format);
    }
    
    /**
	 * Shortcut to deserialize data into the given type.
	 * @see \Symfony\Component\Serializer\Serializer -> deserialize()
	 */
    public function deserialize($data, $type, $format = 'json')
    {
        return $this->get('schema.Serializer')->deserialize($data, $type, $format);
    }
    
    /**
     * Shortcut to serialize data in the appropriate format.
     * @see \Symfony\Component\Serializer\Serializer -> serialize()
     */
    public function serialize($data, $format = 'json')
    {
        return $this->get('schema.Serializer')->serialize($data, $format);
    }
    
    /**
     * Shortcut to return the Doctrine Registry service from the service container.
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     * @throws \LogicException If DoctrineBundle is not available
     */
    protected function getDoctrine()
    {
        if (!$this->container->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not registered in your application.');
        }

        return $this->container->get('doctrine');
    }

    /**
     * Shortcut to return a named object manager from the Doctrine Registry service.
     * @param string $name The object manager name (null for the default one)
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    protected function getManager($name = null)
    {
        return $this->getDoctrine()->getManager($name);
    }

    /**
     * Shortcut to return a parameter from the service container.
     * @param string $name The parameter name
     * @return mixed  The parameter value
     * @throws InvalidArgumentException if the parameter is not defined
     */
    protected function getParameter($name)
    {
        return $this->container->getParameter($name);
    }

    /**
     * Shortcut to return a service by id from the service container.
     * @param string $id The service id
     * @return object The service
     */
    protected function get($id)
    {
        return $this->container->get($id);
    }

}
