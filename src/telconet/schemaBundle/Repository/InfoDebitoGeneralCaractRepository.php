<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use telconet\schemaBundle\DependencyInjection\BaseRepository;
/**
 * InfoDebitoGeneralCaractRepository.
 *
 * Repositorio que se encarga de guardar los datos.
 *
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0 12-09-2022
 */
class InfoDebitoGeneralCaractRepository extends EntityRepository 
{

   /**
    * Documentación para función getRutaArchivoDebitoNfs
    * Función que obtiene el valor de la ruta nfs del archivo excel para el débito generado.
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.0 14-09-2022  
    */
    public function getRutaArchivoDebitoNfs($arrayParametros)
    {   
        try
        {
            $objRsm  = new ResultSetMappingBuilder($this->_em);          
            $strSql  = " SELECT IDGC.VALOR"
                     . " FROM DB_FINANCIERO.INFO_DEBITO_GENERAL_CARACT IDGC "
                     . " WHERE IDGC.DEBITO_GENERAL_ID = :intDebitoGeneralId "
                     . " AND IDGC.CARACTERISTICA_ID   = :intCaracteristicaId "
                     . " AND IDGC.ESTADO              = :strEstado";
            
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $objQuery->setParameter('intDebitoGeneralId',  $arrayParametros['intDebitoGeneralId']);
            $objQuery->setParameter('intCaracteristicaId', $arrayParametros['intCaracteristicaId']);
            $objQuery->setParameter('strEstado',           $arrayParametros['strEstado']);
            
            $objRsm  ->addScalarResult('VALOR', 'valor', 'string');     
            $objQuery->setSQL($strSql);
            
            $arrayResult = $objQuery->getResult();

        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
         return $arrayResult;
    }
    
}
