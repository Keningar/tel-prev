<?php

namespace telconet\schemaBundle\Service;

use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Clase para envolver un ValidatorInterface.
 * @see \Symfony\Component\Validator\ValidatorInterface
 * @author ltama
 */
class ValidatorService {
    
    /**
     * @var ValidatorInterface
     */
    public $validator;
    
    public function setDependencies(ContainerInterface $container) {
        $this->validator = $container->get('validator');
    }
    
    /**
     * Valida el entity dado
     * @return ConstraintViolationListInterface si hay errores de validacion
     */
    public function validate($entity)
    {
        return $this->validator->validate($entity);
    }
    
    /**
     * Valida el entity dado, devuelve el mensaje de error
     * @return string mensaje si hay errores de validacion
     */
    public function validateAndGetMessage($entity)
    {
        $errors = $this->validator->validate($entity);
        $count = $errors->count();
        if ($count > 0)
        {
            // concatenar los mensajes de validacion
            $message = '';
            for ($i = 0; $i < $count ; $i++)
            {
                $message .= $errors->get($i)->getMessage() . '. ';
            }
            return $message;
        }
        return null;
    }
    
    /**
     * Valida el entity dado, lanza excepcion en caso de error
     * @throws \Exception si hay errores de validacion
     */
    public function validateAndThrowException($entity)
    {
        $message = $this->validateAndGetMessage($entity);
        if (!empty($message))
        {
            throw new \Exception($message);
        }
    }
    
}
