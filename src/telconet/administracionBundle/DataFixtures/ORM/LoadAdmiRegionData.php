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
use telconet\schemaBundle\Entity\AdmiRegion;

class LoadAdmiRegionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        /*
        $pais1 = $this->getReference("pais_1") ? $this->getReference("pais_1") : null;
        $arrayDatos[] = array("paisEntity"=>$pais1, "nombreRegion"=>"PruebaRegion200");
           */ 
        $arrayDatos = $this->retornaArreglo();    
        if($arrayDatos && count($arrayDatos)>0)
        {
            $i = 0;
            foreach($arrayDatos as $key1 => $arrayData)
            {
                $i++;

                $em->getConnection()->beginTransaction();

                $entity = new AdmiRegion();
                $entity->setPaisId($arrayData["paisEntity"]);
                $entity->setNombreRegion($arrayData["nombreRegion"]);
                $entity->setEstado('Activo');
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion('rsaenz');
                $entity->setFeUltMod(new \DateTime('now'));
                $entity->setUsrUltMod('rsaenz');

                $em->persist($entity);
                $em->flush();
                $em->getConnection()->commit();

                $this->addReference("region_$i", $entity);
            }
        }//fin IFARRAYDATOS
    }
    

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // el orden en el cual serán cargados los accesorios
    }
    
    public function retornaArreglo()
    {
        $pais1 = $this->getReference("pais_1") ? $this->getReference("pais_1") : null;
        
        $arrayDatos[] = array("paisEntity"=>$pais1, "nombreRegion"=>"Costa");
        $arrayDatos[] = array("paisEntity"=>$pais1, "nombreRegion"=>"Sierra");
        $arrayDatos[] = array("paisEntity"=>$pais1, "nombreRegion"=>"Oriental");
        $arrayDatos[] = array("paisEntity"=>$pais1, "nombreRegion"=>"Insular");
        
        return $arrayDatos;
    }  
}
?>