<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\administracionBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use telconet\schemaBundle\Entity\AdmiTipoRol;

class LoadAdmiTipoRolData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        /*        
        $arrayDatos[] = array("nombreTipoRol"=>"PruebaTioRpol300");
        */
        $arrayDatos = $this->retornaArreglo();
        if($arrayDatos && count($arrayDatos)>0)
        {
            foreach($arrayDatos as $key => $arrayData)
            {
                $em->getConnection()->beginTransaction();

                $entity = new AdmiTipoRol();
                $entity->setDescripcionTipoRol($arrayData["nombreTipoRol"]);
                $entity->setEstado('Activo');
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion('rsaenz');
                $entity->setFeUltMod(new \DateTime('now'));
                $entity->setUsrUltMod('rsaenz');

                $em->persist($entity);
                $em->flush();
                $em->getConnection()->commit();
                
            }
        }
    }
          
    public function retornaArreglo()
    {
        $arrayDatos[] = array("nombreTipoRol"=>"Empleado");
        $arrayDatos[] = array("nombreTipoRol"=>"Cliente");
        $arrayDatos[] = array("nombreTipoRol"=>"Pre-Cliente");
        $arrayDatos[] = array("nombreTipoRol"=>"Proveedor");
        $arrayDatos[] = array("nombreTipoRol"=>"Contacto");
        
        return $arrayDatos;
    }
}
?>