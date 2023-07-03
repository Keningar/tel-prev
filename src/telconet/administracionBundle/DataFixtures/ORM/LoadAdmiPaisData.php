<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\administracionBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use telconet\schemaBundle\Entity\AdmiPais;

class LoadAdmiPaisData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {        
        //$arrayDatos[] = array("nombrePais"=>"PruebaPais300");
        
        $arrayDatos = $this->retornaArreglo();
        if($arrayDatos && count($arrayDatos)>0)
        {
            $i = 0;
            foreach($arrayDatos as $key => $arrayData)
            {
                $i++;
                
                $em->getConnection()->beginTransaction();

                $entity = new AdmiPais();
                $entity->setNombrePais($arrayData["nombrePais"]);
                $entity->setEstado('Activo');
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion('rsaenz');
                $entity->setFeUltMod(new \DateTime('now'));
                $entity->setUsrUltMod('rsaenz');

                $em->persist($entity);
                $em->flush();
                $em->getConnection()->commit();
                
                
                $this->addReference("pais_$i", $entity);
            }
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // el orden en el cual serán cargados los accesorios
    }
            
    public function retornaArreglo()
    {
        $arrayDatos[] = array("nombrePais"=>"Ecuador");
        $arrayDatos[] = array("nombrePais"=>"Venezuela");
        $arrayDatos[] = array("nombrePais"=>"Colombia");
        $arrayDatos[] = array("nombrePais"=>"Peru");
        $arrayDatos[] = array("nombrePais"=>"Brasil");
        $arrayDatos[] = array("nombrePais"=>"Argentina");
        $arrayDatos[] = array("nombrePais"=>"Chile");
        $arrayDatos[] = array("nombrePais"=>"Uruguay");
        $arrayDatos[] = array("nombrePais"=>"Bolivia");
        $arrayDatos[] = array("nombrePais"=>"Paraguay");
        
        return $arrayDatos;
    }
}
?>