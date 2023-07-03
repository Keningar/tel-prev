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
use telconet\schemaBundle\Entity\AdmiCanton;

class LoadAdmiCantonData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {    
        //$provincia1 = $this->getReference("provincia_1") ? $this->getReference("provincia_1") : null;
        // $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=>"canton200", "esCabecera"=> 'NO', "esCapital"=> 'NO');
            
        $arrayDatos = $this->retornaArreglo();        
        if($arrayDatos && count($arrayDatos)>0)
        {
            $i = 0;
            foreach($arrayDatos as $key1 => $arrayData)
            {
                $i++;

                $em->getConnection()->beginTransaction();

                $entity = new AdmiCanton();
                $entity->setProvinciaId($arrayData["provinciaEntity"]);
                $entity->setNombreCanton($arrayData["nombreCanton"]);
                $entity->setEsCabecera($arrayData["esCabecera"]);
                $entity->setEsCapital($arrayData["esCapital"]);
                $entity->setEstado('Activo');
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion('rsaenz');

                $em->persist($entity);
                $em->flush();
                $em->getConnection()->commit();

                $this->addReference("canton_$i", $entity);
            }
        }//fin IFARRAYDATOS
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
        $provincia1 = $this->getReference("provincia_1") ? $this->getReference("provincia_1") : null;
        $provincia2 = $this->getReference("provincia_2") ? $this->getReference("provincia_2") : null;
        $provincia3 = $this->getReference("provincia_3") ? $this->getReference("provincia_3") : null;
        $provincia4 = $this->getReference("provincia_4") ? $this->getReference("provincia_4") : null;
        $provincia5 = $this->getReference("provincia_5") ? $this->getReference("provincia_5") : null;
        $provincia6 = $this->getReference("provincia_6") ? $this->getReference("provincia_6") : null;
        $provincia7 = $this->getReference("provincia_7") ? $this->getReference("provincia_7") : null;
        $provincia8 = $this->getReference("provincia_8") ? $this->getReference("provincia_8") : null;
        $provincia9 = $this->getReference("provincia_9") ? $this->getReference("provincia_9") : null;
        $provincia10 = $this->getReference("provincia_10") ? $this->getReference("provincia_10") : null;
        $provincia11 = $this->getReference("provincia_11") ? $this->getReference("provincia_11") : null;
        $provincia12 = $this->getReference("provincia_12") ? $this->getReference("provincia_12") : null;
        $provincia13 = $this->getReference("provincia_13") ? $this->getReference("provincia_13") : null;
        $provincia14 = $this->getReference("provincia_14") ? $this->getReference("provincia_14") : null;
        $provincia15 = $this->getReference("provincia_15") ? $this->getReference("provincia_15") : null;
        $provincia16 = $this->getReference("provincia_16") ? $this->getReference("provincia_16") : null;
        $provincia17 = $this->getReference("provincia_17") ? $this->getReference("provincia_17") : null;
        $provincia18 = $this->getReference("provincia_18") ? $this->getReference("provincia_18") : null;
        $provincia19 = $this->getReference("provincia_19") ? $this->getReference("provincia_19") : null;
        $provincia20 = $this->getReference("provincia_20") ? $this->getReference("provincia_20") : null;
        $provincia21 = $this->getReference("provincia_21") ? $this->getReference("provincia_21") : null;
        $provincia22 = $this->getReference("provincia_22") ? $this->getReference("provincia_22") : null;
        $provincia23 = $this->getReference("provincia_23") ? $this->getReference("provincia_23") : null;
        $provincia24 = $this->getReference("provincia_24") ? $this->getReference("provincia_24") : null;
         
        $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=> 'CUENCA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=> 'GIRÓN', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=> 'GUALACEO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=> 'NABÓN', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=> 'PAUTE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=> 'PUCARÁ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=> 'SAN FERNANDO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=> 'SANTA ISABEL', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=> 'SIGSIG', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=> 'OÑA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=> 'CHORDELEG', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=> 'EL PAN', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=> 'SEVILLA DE ORO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=> 'GUACHAPALA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia1, "nombreCanton"=> 'CAMILO PONCE ENRÍQUEZ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia2, "nombreCanton"=> 'GUARANDA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia2, "nombreCanton"=> 'CHILLANES', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia2, "nombreCanton"=> 'CHIMBO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia2, "nombreCanton"=> 'ECHEANDÍA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia2, "nombreCanton"=> 'SAN MIGUEL', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia2, "nombreCanton"=> 'CALUMA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia2, "nombreCanton"=> 'LAS NAVES', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia3, "nombreCanton"=> 'AZOGUES', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia3, "nombreCanton"=> 'BIBLIÁN', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia3, "nombreCanton"=> 'CAÑAR', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia3, "nombreCanton"=> 'LA TRONCAL', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia3, "nombreCanton"=> 'EL TAMBO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia3, "nombreCanton"=> 'DÉELEG', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia3, "nombreCanton"=> 'SUSCAL', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia4, "nombreCanton"=> 'TULCÁN', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia4, "nombreCanton"=> 'BOLÍVAR', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia4, "nombreCanton"=> 'ESPEJO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia4, "nombreCanton"=> 'MIRA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia4, "nombreCanton"=> 'MONTÚFAR', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia4, "nombreCanton"=> 'SAN PEDRO DE HUACA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia5, "nombreCanton"=> 'LATACUNGA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia5, "nombreCanton"=> 'LA MANÁ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia5, "nombreCanton"=> 'PANGUA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia5, "nombreCanton"=> 'PUJILÍ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia5, "nombreCanton"=> 'SALCEDO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia5, "nombreCanton"=> 'SAQUISILÍ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia5, "nombreCanton"=> 'SIGCHOS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia6, "nombreCanton"=> 'RIOBAMBA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia6, "nombreCanton"=> 'ALAUSÍ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia6, "nombreCanton"=> 'COLTA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia6, "nombreCanton"=> 'CHAMBO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia6, "nombreCanton"=> 'CHUNCHI', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia6, "nombreCanton"=> 'GUAMOTE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia6, "nombreCanton"=> 'GUANO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia6, "nombreCanton"=> 'PALLATANGA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia6, "nombreCanton"=> 'PENIPE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia6, "nombreCanton"=> 'CUMANDÁ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia7, "nombreCanton"=> 'MACHALA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia7, "nombreCanton"=> 'ARENILLAS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia7, "nombreCanton"=> 'ATAHUALPA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia7, "nombreCanton"=> 'BALSAS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia7, "nombreCanton"=> 'CHILLA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia7, "nombreCanton"=> 'EL GUABO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia7, "nombreCanton"=> 'HUAQUILLAS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia7, "nombreCanton"=> 'MARCABELÍ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia7, "nombreCanton"=> 'PASAJE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia7, "nombreCanton"=> 'PIÑAS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia7, "nombreCanton"=> 'PORTOVELO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia7, "nombreCanton"=> 'SANTA ROSA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia7, "nombreCanton"=> 'ZARUMA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia7, "nombreCanton"=> 'LAS LAJAS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia8, "nombreCanton"=> 'ESMERALDAS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia8, "nombreCanton"=> 'ELOY ALFARO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia8, "nombreCanton"=> 'MUISNE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia8, "nombreCanton"=> 'QUININDÉ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia8, "nombreCanton"=> 'SAN LORENZO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia8, "nombreCanton"=> 'ATACAMES', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia8, "nombreCanton"=> 'RIOVERDE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia8, "nombreCanton"=> 'LA CONCORDIA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'GUAYAQUIL', "esCabecera"=> 'SI', "esCapital"=> 'SI');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'ALFREDO BAQUERIZO MORENO (JUJÁN)', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'BALAO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'BALZAR', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'COLIMES', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'DAULE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'DURÁN', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'EL EMPALME', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'EL TRIUNFO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'MILAGRO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'NARANJAL', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'NARANJITO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'PALESTINA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'PEDRO CARBO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'SAMBORONDÓN', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'SANTA LUCÍA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'SALITRE (URBINA JADO)', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'SAN JACINTO DE YAGUACHI', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'PLAYAS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'SIMÓN BOLÍVAR', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'CORONEL MARCELINO MARIDUEÑA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'LOMAS DE SARGENTILLO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'NOBOL', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'GENERAL ANTONIO ELIZALDE (BUCAY)', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia9, "nombreCanton"=> 'ISIDRO AYORA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia10, "nombreCanton"=> 'IBARRA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia10, "nombreCanton"=> 'ANTONIO ANTE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia10, "nombreCanton"=> 'COTACACHI', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia10, "nombreCanton"=> 'OTAVALO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia10, "nombreCanton"=> 'PIMAMPIRO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia10, "nombreCanton"=> 'SAN MIGUEL DE URCUQUÍ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'LOJA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'CALVAS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'CATAMAYO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'CELICA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'CHAGUARPAMBA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'ESPÍNDOLA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'GONZANAMÁ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'MACARÁ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'PALTAS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'PUYANGO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'SARAGURO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'SOZORANGA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'ZAPOTILLO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'PINDAL', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'QUILANGA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia11, "nombreCanton"=> 'OLMEDO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia12, "nombreCanton"=> 'BABAHOYO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia12, "nombreCanton"=> 'BABA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia12, "nombreCanton"=> 'MONTALVO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia12, "nombreCanton"=> 'PUEBLOVIEJO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia12, "nombreCanton"=> 'QUEVEDO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia12, "nombreCanton"=> 'URDANETA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia12, "nombreCanton"=> 'VENTANAS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia12, "nombreCanton"=> 'VINCES', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia12, "nombreCanton"=> 'PALENQUE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia12, "nombreCanton"=> 'BUENA FÉ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia12, "nombreCanton"=> 'VALENCIA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia12, "nombreCanton"=> 'MOCACHE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia12, "nombreCanton"=> 'QUINSALOMA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'PORTOVIEJO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'BOLÍVAR', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'CHONE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'EL CARMEN', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'FLAVIO ALFARO ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'JIPIJAPA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'JUNÍN', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'MANTA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'MONTECRISTI', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'PAJÁN', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'PICHINCHA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'ROCAFUERTE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'SANTA ANA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'SUCRE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'TOSAGUA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> '24 DE MAYO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'PEDERNALES', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'OLMEDO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'PUERTO LÓPEZ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'JAMA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'JARAMIJÓ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia13, "nombreCanton"=> 'SAN VICENTE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia14, "nombreCanton"=> 'MORONA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia14, "nombreCanton"=> 'GUALAQUIZA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia14, "nombreCanton"=> 'LIMÓN INDANZA ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia14, "nombreCanton"=> 'PALORA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia14, "nombreCanton"=> 'SANTIAGO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia14, "nombreCanton"=> 'SUCÚA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia14, "nombreCanton"=> 'HUAMBOYA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia14, "nombreCanton"=> 'SAN JUAN BOSCO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia14, "nombreCanton"=> 'TAISHA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia14, "nombreCanton"=> 'LOGROÑO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia14, "nombreCanton"=> 'PABLO SEXTO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia14, "nombreCanton"=> 'TIWINTZA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia15, "nombreCanton"=> 'TENA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia15, "nombreCanton"=> 'ARCHIDONA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia15, "nombreCanton"=> 'EL CHACO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia15, "nombreCanton"=> 'QUIJOS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia15, "nombreCanton"=> 'CARLOS JULIO AROSEMENA TOLA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia16, "nombreCanton"=> 'PASTAZA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia16, "nombreCanton"=> 'MERA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia16, "nombreCanton"=> 'SANTA CLARA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia16, "nombreCanton"=> 'ARAJUNO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia17, "nombreCanton"=> 'QUITO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia17, "nombreCanton"=> 'CAYAMBE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia17, "nombreCanton"=> 'MEJÍA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia17, "nombreCanton"=> 'PEDRO MONCAYO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia17, "nombreCanton"=> 'RUMIÑAHUI', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia17, "nombreCanton"=> 'SAN MIGUEL DE LOS BANCOS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia17, "nombreCanton"=> 'PEDRO VICENTE MALDONADO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia17, "nombreCanton"=> 'PUERTO QUITO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia18, "nombreCanton"=> 'AMBATO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia18, "nombreCanton"=> 'BAÑOS DE AGUA SANTA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia18, "nombreCanton"=> 'CEVALLOS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia18, "nombreCanton"=> 'MOCHA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia18, "nombreCanton"=> 'PATATE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia18, "nombreCanton"=> 'QUERO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia18, "nombreCanton"=> 'SAN PEDRO DE PELILEO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia18, "nombreCanton"=> 'SANTIAGO DE PÍLLARO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia18, "nombreCanton"=> 'TISALEO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia19, "nombreCanton"=> 'ZAMORA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia19, "nombreCanton"=> 'CHINCHIPE', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia19, "nombreCanton"=> 'NANGARITZA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia19, "nombreCanton"=> 'YACUAMBÍ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia19, "nombreCanton"=> 'YANTZAZA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia19, "nombreCanton"=> 'EL PANGUI', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia19, "nombreCanton"=> 'CENTINELA DEL CÓNDOR', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia19, "nombreCanton"=> 'PALANDA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia19, "nombreCanton"=> 'PAQUISHA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia20, "nombreCanton"=> 'SAN CRISTÓBAL', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia20, "nombreCanton"=> 'ISABELA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia20, "nombreCanton"=> 'SANTA CRUZ', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia21, "nombreCanton"=> 'LAGO AGRIO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia21, "nombreCanton"=> 'GONZALO PIZARRO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia21, "nombreCanton"=> 'PUTUMAYO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia21, "nombreCanton"=> 'SHUSHUFINDI', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia21, "nombreCanton"=> 'SUCUMBÍOS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia21, "nombreCanton"=> 'CASCALES', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia21, "nombreCanton"=> 'CUYABENO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia22, "nombreCanton"=> 'ORELLANA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia22, "nombreCanton"=> 'AGUARICO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia22, "nombreCanton"=> 'LA JOYA DE LOS SACHAS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia22, "nombreCanton"=> 'LORETO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia23, "nombreCanton"=> 'SANTO DOMINGO', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia24, "nombreCanton"=> 'SANTA ELENA', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia24, "nombreCanton"=> 'LA LIBERTAD', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        $arrayDatos[] = array("provinciaEntity"=>$provincia24, "nombreCanton"=> 'SALINAS', "esCabecera"=> 'NO', "esCapital"=> 'NO');
        
        return $arrayDatos;
    }
}
?>