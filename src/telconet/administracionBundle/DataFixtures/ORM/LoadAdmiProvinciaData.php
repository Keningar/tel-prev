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
use telconet\schemaBundle\Entity\AdmiProvincia;

class LoadAdmiProvinciaData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {       
        /*
        $region1 = $this->getReference("region_1") ? $this->getReference("region_1") : null;
        $arrayDatos[] = array("regionEntity"=>$region1, "nombreProvincia"=>"Prov200");
        */
        
        $arrayDatos = $this->retornaArreglo();   
        if($arrayDatos && count($arrayDatos)>0)
        {
            $i = 0;
            foreach($arrayDatos as $key1 => $arrayData)
            {
                $i++;

                $em->getConnection()->beginTransaction();

                $entity = new AdmiProvincia();
                $entity->setRegionId($arrayData["regionEntity"]);
                $entity->setNombreProvincia($arrayData["nombreProvincia"]);
                $entity->setEstado('Activo');
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion('rsaenz');
                $entity->setFeUltMod(new \DateTime('now'));
                $entity->setUsrUltMod('rsaenz');

                $em->persist($entity);
                $em->flush();
                $em->getConnection()->commit();

                $this->addReference("provincia_$i", $entity);
            }
        }//fin IFARRAYDATOS
    }
    

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3; // el orden en el cual serán cargados los accesorios
    }
    
    public function retornaArreglo()
    {
        $region1 = $this->getReference("region_1") ? $this->getReference("region_1") : null;
        $region2 = $this->getReference("region_2") ? $this->getReference("region_2") : null;
        $region3 = $this->getReference("region_3") ? $this->getReference("region_3") : null;
        $region4 = $this->getReference("region_4") ? $this->getReference("region_4") : null;
        
        $arrayDatos[] = array("regionEntity"=>$region2, "nombreProvincia"=>"AZUAY");
        $arrayDatos[] = array("regionEntity"=>$region2, "nombreProvincia"=>"BOLIVAR");
        $arrayDatos[] = array("regionEntity"=>$region2, "nombreProvincia"=>"CAÑAR");
        $arrayDatos[] = array("regionEntity"=>$region2, "nombreProvincia"=>"CARCHI");
        $arrayDatos[] = array("regionEntity"=>$region2, "nombreProvincia"=>"COTOPAXI");
        $arrayDatos[] = array("regionEntity"=>$region2, "nombreProvincia"=>"CHIMBORAZO");
        $arrayDatos[] = array("regionEntity"=>$region1, "nombreProvincia"=>"EL ORO");        
        $arrayDatos[] = array("regionEntity"=>$region1, "nombreProvincia"=>"ESMERALDAS");
        $arrayDatos[] = array("regionEntity"=>$region1, "nombreProvincia"=>"GUAYAS");
        $arrayDatos[] = array("regionEntity"=>$region2, "nombreProvincia"=>"IMBABURA");        
        $arrayDatos[] = array("regionEntity"=>$region2, "nombreProvincia"=>"LOJA");
        $arrayDatos[] = array("regionEntity"=>$region1, "nombreProvincia"=>"LOS RIOS");
        $arrayDatos[] = array("regionEntity"=>$region1, "nombreProvincia"=>"MANABI");
        $arrayDatos[] = array("regionEntity"=>$region3, "nombreProvincia"=>"MORONA SANTIAGO");
        $arrayDatos[] = array("regionEntity"=>$region3, "nombreProvincia"=>"NAPO");
        $arrayDatos[] = array("regionEntity"=>$region3, "nombreProvincia"=>"PASTAZA");
        $arrayDatos[] = array("regionEntity"=>$region2, "nombreProvincia"=>"PICHINCHA");        
        $arrayDatos[] = array("regionEntity"=>$region2, "nombreProvincia"=>"TUNGURAHUA");
        $arrayDatos[] = array("regionEntity"=>$region3, "nombreProvincia"=>"ZAMORA CHINCHIPE");
        $arrayDatos[] = array("regionEntity"=>$region4, "nombreProvincia"=>"GALAPAGOS");
        $arrayDatos[] = array("regionEntity"=>$region3, "nombreProvincia"=>"SUCUMBIOS");        
        $arrayDatos[] = array("regionEntity"=>$region3, "nombreProvincia"=>"ORELLANA");
        $arrayDatos[] = array("regionEntity"=>$region2, "nombreProvincia"=>"SANTO DOMINGO DE LOS TSACHILAS");
        $arrayDatos[] = array("regionEntity"=>$region1, "nombreProvincia"=>"SANTA ELENA");
        
        return $arrayDatos;
    }  
}
?>