<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\DBALException;

class InfoAgendaCupoDetRepository extends EntityRepository
{

    /**
    * Repository de la entidad Info_Agenda_Cupo_Cab
    *
    * @author Juan Romero <jromero@telconet.ec>
    * @version 1.0 06-06-2018
    *
    */
    public function generarJsonAgendaCupo($intIdCabecera)
    {
        $arrayEncontrados = array();
        $arrayRegistros   = array();

        $arrayRegistros     = $this->getRegistrosPorIdCabecera($intIdCabecera);
        $objRegistros       = $arrayRegistros['registros'];
        $intRegistrosTotal  = $arrayRegistros['total'];

        if ($intRegistrosTotal)
        {
            $intTotal = count($intRegistrosTotal);
            foreach ($objRegistros as $objRegistro)
            {
                
                $intCeroHoraCreacion = (intval(date_format($objRegistro->getHoraDesde(), "G"))<10 ? "0" : "" );
                $strHoraDesde = strval(date_format($objRegistro->getHoraDesde(), "Y-m-d")). "T" . 
                              strval(date_format($objRegistro->getHoraDesde(), $intCeroHoraCreacion."G:i"));  

                $intCeroHoraFin = (intval(date_format($objRegistro->getHoraHasta(), "G"))<10 ? "0" : "" );
                $strHoraHasta = strval(date_format($objRegistro->getHoraHasta(), "Y-m-d")). "T" . 
                         strval(date_format($objRegistro->getHoraHasta(), $intCeroHoraFin."G:i"));
                



                $arrayEncontrados[] =array('idAgendaCupoDet'       => $objRegistro->getId(),
                                           'agendaCupoId'          => $objRegistro->getAgendaCupoId()->getId(),
                                           'horaDesde'             => $strHoraDesde,
                                           'horaHasta'             => $strHoraHasta,
                                           'cuposWeb'              => $objRegistro->getCuposWeb(),
                                           'cuposMovil'            => $objRegistro->getCuposMovil(),
                                           'totalCupos'            => $objRegistro->getTotalCupos(),
                                           'observacion'           => $objRegistro->getObservacion(),
                                           );
            }

            if($intTotal == 0)
            {
                $arrayResultado = array('total' => 1 ,'encontrados' => array('idAgendaCupoDet' => 0 , 'agendaCupoId' => '',
                                                                        'observacion' => 'Ninguno',  'estado' => 'Ninguno'));
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
    * Trae los registros del detalle de agenda por el id de la cabecera
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 06-06-2018
    *
    */
    public function getRegistrosPorIdCabecera($intPlantillaCabeceraId)
    {
        try
        {
        $arrayDatos  = array();
            
        $strSql = "SELECT
                   pho
                   FROM
                   schemaBundle:InfoAgendaCupoDet pho
                   WHERE ";

        $objQuery = $this->_em->createQuery(null);

        $strSql .= " pho.agendaCupoId = :id";
        $strSql .= " ORDER BY pho.horaDesde";
        $objQuery->setParameter('id', $intPlantillaCabeceraId);
        
        
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
    * Devuelve los registros de detalle de de agenda por los parametros especificados
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 07-06-2018
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.1 07-06-2018 - Bug - Se modifica error en join y campo phc.id 
    */    
    public function getDetalleAgenda($arrayParametros)
    {
        $strFechaDesde     = $arrayParametros['strFechaDesde'];
        $intJurisdiccionId = $arrayParametros['intJurisdiccionId'];
        try
        {
            $arrayDatos  = array();
            $strSql = "SELECT
                       pho
                       FROM
                       schemaBundle:InfoAgendaCupoDet pho join
                       schemaBundle:InfoAgendaCupoCab phc
                       with pho.agendaCupoId = phc.id
                       WHERE ";

            $objQuery = $this->_em->createQuery(null);
   
            $arrayFecha = explode(" ",$strFechaDesde);
            $arrayFecha2 = explode("/", $arrayFecha[0]);
            $strFechaSql = date("Y/m/d H:i", strtotime($arrayFecha2[0]."-".$arrayFecha2[1]."-".$arrayFecha2[2]." ".$arrayFecha[1])); 
            
            $strSql .= " pho.horaDesde = :now";
            $objQuery->setParameter('now',new \DateTime($strFechaSql));
            
            $strSql .= " and phc.jurisdiccionId = :intJurisdiccion";
            $objQuery->setParameter('intJurisdiccion', $intJurisdiccionId);            

            $objQuery->setDQL($strSql);

            $arrayDatos['registros'] = $objQuery->getResult();
        } 
        catch (\Exception $ex) 
        {
            error_log("Error: " . $ex->getMessage());
        }
        catch(\DBALException $ex)
        {
            error_log("Error: " . $ex->getMessage());
        }
        return $arrayDatos;        
    }  
}


