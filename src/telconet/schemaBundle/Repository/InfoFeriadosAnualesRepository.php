<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class InfoFeriadosAnualesRepository extends EntityRepository
{
    /**
    * generarJson
    *
    * Esta funcion retorna en formato JSON la lista de feriados
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 12-12-2018 
    *
    * @param array  $arrayParametros
    *
    * @return array $arrayResultado
    *
    */
    public function generarJsonFeriadosAnuales($arrayParametros)
    {
        $arrayEncontrados = array();

        $arrayRegistros     = $this->getRegistros($arrayParametros);
        $objRegistros       = $arrayRegistros['registros'];
        $intRegistrosTotal  = $arrayRegistros['total'];

        if ($intRegistrosTotal)
        {
            $intTotal = count($intRegistrosTotal);
            foreach ($objRegistros as $objRegistro)
            {
                $arrayEncontrados[] =array('idFeriados'  => $objRegistro->getId(),
                                           'feriadosId'  => trim($objRegistro->getFeriadosId()),
                                           'tipo'        => trim($objRegistro->getTipo()),
                                           'feDesde'     => $objRegistro->getFeDesde()->format('d/m/Y'),                  
                                           'feHasta'     => $objRegistro->getFeHasta()->format('d/m/Y'),
                                           'comentario'  => trim($objRegistro->getComentario()),
                                           'cantonId'    => trim($objRegistro->getCantonId()),
                                           'feCreacion'  => strval(date_format($objRegistro->getFeCreacion(),"d/m/Y G:i")) ,
                                           'usrCreacion' => trim($objRegistro->getUsrCreacion()),
                                           'action1'     => 'button-grid-show',
                                           'action2'     => (strtolower(trim($objRegistro->getEstado()))==strtolower('ELIMINADO') ?
                                                                                     'icon-invisible':'button-grid-edit'),
                                           'action3'     => (strtolower(trim($objRegistro->getEstado()))==strtolower('ELIMINADO') ?
                                                                                     'icon-invisible':'button-grid-delete'));
            }

            if($intTotal == 0)
            {
                $arrayResultado = array('total' => 1 ,'encontrados' => array('idFeriadosAnuales' => 0 , 'feriadosId' => 0));
                $arrayResultado = json_encode( $arrayResultado );
                return $arrayResultado;
            }
            else
            {
                $arrayFinal     = json_encode($arrayEncontrados);
                $arrayResultado = '{"total":"'.$intTotal.'","encontrados":'.$arrayFinal.'}';
                return $arrayResultado;
            }
        }
        else
        {
            $arrayResultado = '{"total":"0","encontrados":[]}';
            return $arrayResultado;
        }
    }
    
    /**
    * getRegistros
    *
    * Esta funcion retorna la lista de Feriados a presentarse en el grid
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 12-12-2018 
    *
    * @param array  $arrayParametros
    *
    * @return array $arrayResultado
    *
    */
    public function getRegistros($arrayParametros)
    {

        $intAnio  = $arrayParametros['intAnio'];
        try
        {
            $arrayDatos  = array();
            

            $strSql = "SELECT
                       fer
                       FROM
                       schemaBundle:InfoFeriadosAnuales fer
                       WHERE YEAR(fer.feDesde) = :anio";

         
            $objQuery = $this->_em->createQuery(null);
            $objQuery->setParameter('anio',$intAnio);  

            $objQuery->setDQL($strSql);

            $intRegistros = $objQuery->getResult();

            $arrayDatos['registros'] = $objQuery->getResult();
            $arrayDatos['total']     = $intRegistros;   
        }        
        catch (\Exception $ex)
        {
            error_log("Error: " . $ex->getMessage());
        } 
        return $arrayDatos;
    } 
    
    /**
    * generarJson
    *
    * Esta funcion retorna en formato JSON la lista de feriados con la plantilla de admiFeriados
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 13-12-2018 
    *
    * @param array  $arrayParametros
    *
    * @return array $arrayResultado
    *
    */
    public function generarJsonPlantillaFeriados($arrayParametros)
    {
        $arrayEncontrados = array();

        $arrayRegistros     = $this->getRegistrosPlantilla($arrayParametros);
        $objRegistros       = $arrayRegistros['registros'];
        $intRegistrosTotal  = $arrayRegistros['total'];

        if ($intRegistrosTotal)
        {
            $intTotal = count($intRegistrosTotal);
            foreach ($objRegistros as $objRegistro)
            {
                $arrayEncontrados[] =array('idFeriados'  => $objRegistro->getId(),
                                           'feriadosId'  => trim($objRegistro->getFeriadosId()),
                                           'tipo'        => trim($objRegistro->getTipo()),
                                           'feDesde'     => $objRegistro->getFeDesde()->format('d/m/Y'),                  
                                           'feHasta'     => $objRegistro->getFeHasta()->format('d/m/Y'),
                                           'comentario'  => trim($objRegistro->getComentario()),
                                           'cantonId'    => trim($objRegistro->getCantonId()),
                                           'feCreacion'  => strval(date_format($objRegistro->getFeCreacion(),"d/m/Y G:i")) ,
                                           'usrCreacion' => trim($objRegistro->getUsrCreacion()),
                                           'action1'     => 'button-grid-show',
                                           'action2'     => (strtolower(trim($objRegistro->getEstado()))==strtolower('ELIMINADO') ?
                                                                                     'icon-invisible':'button-grid-edit'),
                                           'action3'     => (strtolower(trim($objRegistro->getEstado()))==strtolower('ELIMINADO') ?
                                                                                     'icon-invisible':'button-grid-delete'));
            }

            if($intTotal == 0)
            {
                $arrayResultado = array('total' => 1 ,'encontrados' => array('idFeriadosAnuales' => 0 , 'feriadosId' => 0));
                $arrayResultado = json_encode( $arrayResultado );
                return $arrayResultado;
            }
            else
            {
                $arrayFinal     = json_encode($arrayEncontrados);
                $arrayResultado = '{"total":"'.$intTotal.'","encontrados":'.$arrayFinal.'}';
                return $arrayResultado;
            }
        }
        else
        {
            $arrayResultado = '{"total":"0","encontrados":[]}';
            return $arrayResultado;
        }
    }

    /**
    * getRegistrosPlantilla
    *
    * Esta funcion retorna la lista de la plantilla de Feriados a presentarse en el grid
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 13-12-2018 
    *
    * @param array  $arrayParametros
    *
    * @return array $arrayResultado
    *
    */
    public function getRegistrosPlantilla($arrayParametros)
    {
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQuery       = $this->_em->createNativeQuery(null,$objRsm);

        $strSelect = "SELECT ID_AREA, NOMBRE_AREA";
        $strFrom   = "  FROM DB_COMERCIAL.ADMI_AREA  ";        
        $strWhere  = " WHERE ESTADO      = :estado
                         AND EMPRESA_COD = :empresaCod ";
        
        $objRsm->addScalarResult('ID_AREA'    , 'intIdArea'    , 'integer');
        $objRsm->addScalarResult('NOMBRE_AREA', 'strNombreArea', 'string');
        
        if(isset($arrayParametros['intIdArea']) && !empty($arrayParametros['intIdArea']))
        {
            $strWhere .= " AND ID_AREA = :idArea ";
            $objQuery->setParameter('idArea',  $arrayParametros['intIdArea']);
        }
        $objQuery->setParameter('estado',      $arrayParametros['strEstado']);
        $objQuery->setParameter('empresaCod',  $arrayParametros['strIdEmpresa']);
        $strSql = $strSelect.$strFrom.$strWhere;
        $objQuery->setSQL($strSql);
        $arrayAreas = $objQuery->getArrayResult();

        return $arrayAreas;
    }     
    
}
    
    

