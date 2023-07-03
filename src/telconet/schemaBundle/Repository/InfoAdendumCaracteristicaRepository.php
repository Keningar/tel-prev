<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
/**
 * InfoAdendumCaracteristicaRepository.
 *
 * Repositorio que se encargará de administrar las funcionalidades adicionales que se relacionen con la entidad InfoAdendumCaracteristica
 *
 * @author Joel Broncano <jbroncano@telconet.ec>
 * @version 1.0 29-09-2022
 */
class InfoAdendumCaracteristicaRepository extends EntityRepository 
{



    public function getObtenerCarateristicaDocumento($intAdendum)
    {            
        $objQuery = $this->_em->createQuery("SELECT iAdec.valor1 from        
                                schemaBundle:InfoAdendumCaracteristica iAdec,
                                schemaBundle:AdmiCaracteristica carac                               
                                where iAdec.adendumId=:intAdendumId
                                and iAdec.caracteristicaId =carac.id
                                and carac.descripcionCaracteristica =:descripcionCaracteristica
                                and iAdec.estado =:strEstado ");
               
        $objQuery->setParameters(array('strEstado'            => 'Activo',
                                    'intAdendumId'           => $intAdendum,
                                    'descripcionCaracteristica'=> 'docFisicoCargado' ));                
        $strCantidadContactos = $objQuery->getOneOrNullResult();
        if(!$strCantidadContactos)
        {
            $strCantidadContactos ='N';
        }
        $strCantidadContactos ='S';
        return $strCantidadContactos;   
    }
}
