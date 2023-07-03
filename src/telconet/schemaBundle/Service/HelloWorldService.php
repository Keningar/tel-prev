<?php

namespace telconet\schemaBundle\Service;

class HelloWorldService {

    private $helloPrefix;

    public function setDependencies($helloPrefix)
    {
        // parametro declarado e inyectado mediante services.yml
        $this->helloPrefix = $helloPrefix;
    }
    
    public function obtenerSaludo($message) {
        // se usa el parametro como parte del valor a retornar
        return $this->helloPrefix . ": Hola $message!";
    }

}

