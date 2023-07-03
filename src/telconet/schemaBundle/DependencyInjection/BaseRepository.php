<?php

namespace telconet\schemaBundle\DependencyInjection;

use Doctrine\ORM\EntityRepository;

/**
 * Clase base para todos los repositorios  
 */
abstract class BaseRepository extends EntityRepository {
        
    /**
     * 
     * Metodo de acuerdo a los limites enviados por los componente js se trae la data según estos
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 23-12-2015
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 07-04-2016
     * Se corrige la segunda validación ($intLimit > 0) a ($intStart >0)
     * 
     * @param NativeQuery $objQuery
     * @param integer $intLimit
     * @param integer $intStart
     * @return $objQuery
     */
    public function setQueryLimit($objQuery, $intLimit, $intStart)
    {
        if($intLimit > 0)
        {
            $objQuery->setSQL('SELECT a.*, rownum AS doctrine_rownum FROM (' . $objQuery->getSQL() . ') a WHERE rownum <= :doctrine_limit');
            $objQuery->setParameter('doctrine_limit', $intLimit + $intStart);
            
            if($intStart > 0)
            {
                $objQuery->setSQL('SELECT * FROM (' . $objQuery->getSQL() . ') WHERE doctrine_rownum >= :doctrine_start');
                $objQuery->setParameter('doctrine_start', $intStart + 1);
            }
        }

        return $objQuery;
    }    
    
    /**
     * 
     * Metodo de acuerdo a los limites enviados por los componente js se trae la data según estos
     * 
     * @author Kenneth Jiménez <kjimenez@telconet.ec>
     * @version 1.0 29-06-2016
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-09-02 - Cambiar el # del parámetro para el startLimit de 100 a 101
     * 
     * @param NativeQuery $objQuery
     * @param integer $intLimit
     * @param integer $intStart
     * @return $objQuery
     */
    public function setQueryLimitWithBindVariables($objQuery, $intLimit, $intStart)
    {
        if($intLimit > 0)
        {
            $objQuery->setSQL('SELECT a.*, rownum AS doctrine_rownum FROM (' . $objQuery->getSQL() . ') a WHERE rownum <= ?');
            $objQuery->setParameter(100, $intLimit + $intStart);
            
            if($intStart > 0)
            {
                $objQuery->setSQL('SELECT * FROM (' . $objQuery->getSQL() . ') WHERE doctrine_rownum >= ?');
                $objQuery->setParameter(101, $intStart + 1);
            }
        }

        return $objQuery;
    }    

}
