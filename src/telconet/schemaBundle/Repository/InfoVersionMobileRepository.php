<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\schemaBundle\DependencyInjection\BaseRepository;

class InfoVersionMobileRepository extends BaseRepository
{
    
    /**
      * Obtener ultima version del mobile
      * Funcion que regresa la ultima version del mobile operaciones
      *
      * @return json con resultado
      * @author Nestor Naula Lopez <nnaulal@telconet.ec>
      * @version 1.1 04/07/2018
     */
    public function ObtenerUltimaVersionMobile(){
        $objRsmb       = new ResultSetMappingBuilder($this->_em);
        $objQuery      = $this->_em->createNativeQuery(null, $objRsmb);
        $objEstado     = 'ACTIVO';
        $objAppVersion = 'EC.TELCONET.MOBILE.TELCOS.OPERACIONES';
        
        $strSql = " SELECT VERSION
                    FROM DB_MOBILEVERSION.mobile_version
                    WHERE estado = :estado AND APP_MOBILE= :appMobile ";
   
        $objQuery->setParameter("estado", $objEstado);
        $objQuery->setParameter("appMobile", $objAppVersion);
        
        $objRsmb->addScalarResult('VERSION', 'version', 'string');

        $objQuery->setSQL($strSql);
        $objServicio      = $objQuery->getOneOrNullResult();
        $strUltimaVersion = $objServicio[version];
        
        return $strUltimaVersion;
    }
    
}

 