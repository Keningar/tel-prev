<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiPlantillaHorarioCabRepository extends EntityRepository
{

    /**
    * generarJson
    *
    * Esta funcion retorna en formato JSON la lista de Plantilla de horarios a presentarse en el grid
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 04-12-2017 Se realizan ajustes para presentar las hipotesis por tipo de caso
    *
    * @param array  $arrayParametros
    *
    * @return array $resultado
    *
    */
    public function generarJsonPlantillaHorario($arrayParametros,$intStart,$intLimit)
    {
        $arrayEncontrados = array();
        $arrayRegistros   = array();

        $arrayRegistros     = $this->getRegistros($arrayParametros, $intStart, $intLimit);
        $objRegistros       = $arrayRegistros['registros'];
        $intRegistrosTotal  = $arrayRegistros['total'];

        if ($intRegistrosTotal)
        {
            $intTotal = count($intRegistrosTotal);
            foreach ($objRegistros as $objRegistro)
            {
                $objJurisdiccion = $this->_em
                  ->getRepository('schemaBundle:AdmiJurisdiccion')
                  ->find($objRegistro->getJurisdiccionId());                
                $strNombreJurisdiccion = $objJurisdiccion->getNombreJurisdiccion();
                $arrayEncontrados[] =array('idPlantillaHorarioCab' => $objRegistro->getId(),
                                           'empresaCod'            => trim($objRegistro->getEmpresaCod()),
                                           'descripcion'           => trim($objRegistro->getDescripcion()),
                                           'esDefault'             => trim($objRegistro->getEsDefault()),
                                           'feCreacion'            => strval(date_format($objRegistro->getFeCreacion(),"d/m/Y G:i")) ,
                                           'usrCreacion'           => trim($objRegistro->getUsrCreacion()),
                                           'estado'                => (strtolower(trim($objRegistro->getEstado()))==strtolower('ELIMINADO') ?
                                                                                     'Eliminado':'Activo'),
                                           'strNombreJurisdiccion' => $strNombreJurisdiccion,
                                           'action1'               => 'button-grid-show',
                                           'action2'               => (strtolower(trim($objRegistro->getEstado()))==strtolower('ELIMINADO') ?
                                                                                     'icon-invisible':'button-grid-edit'),
                                           'action3'               => (strtolower(trim($objRegistro->getEstado()))==strtolower('ELIMINADO') ?
                                                                                     'icon-invisible':'button-grid-delete'));
            }

            if($intTotal == 0)
            {
                $arrayResultado = array('total' => 1 ,'encontrados' => array('idPlantillaHorarioCab' => 0 , 'empresaCod' => '',
                                                                        'descripcion' => 'Ninguno', 'esDefault'     => "N" , 'estado' => 'Ninguno'));
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
    * Esta funcion retorna la lista de Plantillas de Horarios a presentarse en el grid
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 04-12-2017 
    *
    * @param array  $arrayParametros
    *
    * @return array $resultado
    *
    */
    public function getRegistros($arrayParametros,$intStart,$intLimit)
    {
        try
        {
        $arrayDatos  = array();
        $strEmpresaCod   = $arrayParametros["empresaCod"];
        $strDescripcion  = $arrayParametros["descripcion"];
        $strEstado       = $arrayParametros["estado"];
        $arrayFechaDesde = $arrayParametros["fechaDesde"];
        $arrayFechaHasta = $arrayParametros["fechaHasta"];

        $strSql = "SELECT
                   pho
                   FROM
                   schemaBundle:AdmiPlantillaHorarioCab pho
                   WHERE";
        
        $objQuery = $this->_em->createQuery(null);

        if ($strEstado && $strEstado!="Todos")
        {
            if ($strEstado=="Activo")
            {
                $strSql .= " lower(pho.estado) not like lower(:estado) AND";
                $objQuery->setParameter('estado','Eliminado');
            }
            else
            {
                $strSql .= " lower(pho.estado) like lower(:estado) AND";
                $objQuery->setParameter('estado','%'.$strEstado.'%');
            }
        }
        if($strEmpresaCod && $strEmpresaCod!="")
        {
            $strSql .= " pho.empresaCod = :empresa ";
            $objQuery->setParameter('empresa',$strEmpresaCod);
        }

        if($strDescripcion && $strDescripcion!="")
        {
            $strSql .= " AND lower(pho.descripcion) like lower(:descripcion) ";
            $objQuery->setParameter('descripcion','%'.$strDescripcion.'%');
        }

        if($arrayFechaDesde && $arrayFechaDesde!="")
        {
            $strSql .= " AND pho.feCreacion >= :dateDesde ";
            $objQuery->setParameter('dateDesde',$arrayFechaDesde);
        }
        
        if($arrayFechaHasta && $arrayFechaHasta!="")
        {
            $strSql .= " AND pho.feCreacion <= :dateHasta ";
            $objQuery->setParameter('dateHasta',$arrayFechaHasta);
        }

        $strSql .= " order by pho.esDefault DESC, pho.feCreacion ASC";
        error_log("sql: " . $strSql);

        $objQuery->setDQL($strSql);

        $intRegistros = $objQuery->getResult();

        $arrayDatos['registros'] = $objQuery->setFirstResult($intStart)->setMaxResults($intLimit)->getResult();
        $arrayDatos['total']     = $intRegistros;   
        }        
        catch (\Exception $ex)
        {
            error_log("Error: " . $ex->getMessage());
        } 
        return $arrayDatos;

    }      
}
