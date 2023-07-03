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
use telconet\schemaBundle\Entity\AdmiTipoParroquia;

class LoadAdmiTipoParroquiaData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        /*
        $arrayDatos[] = array("nombreTipoParroquia"=>"PruebaTipoParroquia1");
        $arrayDatos[] = array("nombreTipoParroquia"=>"PruebaTipoParroquia2");
         */  
        $arrayDatos = $this->retornaArreglo();     
        if($arrayDatos && count($arrayDatos)>0)
        {
            $i = 0;
            foreach($arrayDatos as $key => $arrayData)
            {
                $i++;
                
                $em->getConnection()->beginTransaction();

                $entity = new AdmiTipoParroquia();
                $entity->setNombreTipoParroquia($arrayData["nombreTipoParroquia"]);
                $entity->setEstado('Activo');
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion('rsaenz');
                $entity->setFeUltMod(new \DateTime('now'));
                $entity->setUsrUltMod('rsaenz');

                $em->persist($entity);
                $em->flush();
                $em->getConnection()->commit();

                $this->addReference("tipoparroquia_$i", $entity);                
            }
        }
    }
    

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4; // el orden en el cual serán cargados los accesorios
    }
            
    public function retornaArreglo()
    {
        $arrayDatos[] = array("nombreTipoParroquia"=>"Rural");
        $arrayDatos[] = array("nombreTipoParroquia"=>"Urbana");
        
        return $arrayDatos;
    }
}
?>