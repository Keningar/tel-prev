<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiPlantillaHorarioDetRepository extends EntityRepository
{   
    
    /**
    * getRegistros
    *
    * Esta funcion retorna el detalle de la Plantilla de horario standard
    */
    public function generarJsonDetalleHorario($intIdCabecera)
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
                
                $intCeroHoraCreacion = (intval(date_format($objRegistro->getHoraInicio(), "G"))<10 ? "0" : "" );
                $arrayFeCreacion = strval(date_format($objRegistro->getHoraInicio(), "Y-m-d")). "T" . 
                              strval(date_format($objRegistro->getHoraInicio(), $intCeroHoraCreacion."G:i:s"));  

                $intCeroHoraFin = (intval(date_format($objRegistro->getHoraFin(), "G"))<10 ? "0" : "" );
                $arrayFeFin = strval(date_format($objRegistro->getHoraFin(), "Y-m-d")). "T" . 
                         strval(date_format($objRegistro->getHoraFin(), $intCeroHoraFin."G:i:s"));
                
                $boolAlmuerzo = $objRegistro->getAlmuerzo() == 'S';


                $arrayEncontrados[] =array('idPlantillaHorarioDet' => $objRegistro->getId(),
                                           'plantillaHorarioId'    => $objRegistro->getPlantillaHorarioId()->getId(),
                                           'horaInicio'            => $arrayFeCreacion,
                                           'horaFin'               => $arrayFeFin,
                                           'almuerzo'              => $boolAlmuerzo,
                                           'cupoWeb'               => $objRegistro->getCupoWeb(),
                                           'cupoMobile'            => $objRegistro->getCupoMobile(),
                                           'observacion'           => ''
                                           );
            }

            if($intTotal == 0)
            {
                $arrayResultado = array('total' => 1 ,'encontrados' => array('idPlantillaHorarioDet' => 0 , 'plantillaHorarioId' => '',
                                                                        'descripcion' => 'Ninguno', 'esDefault'     => "N" , 'estado' => 'Ninguno'));
                $arrayResultado = json_encode( $arrayResultado );
                return $arrayResultado;
            }
            else
            {
                $arrayFinal     = json_encode($arrayEncontrados);
                $arrayResultado = '{"total":"'.$intTotal.'","encontrados":'.$arrayFinal.'}';
                error_log(print_r($arrayResultado, 1));
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
    * Esta funcion retorna el detalle de las Plantillas de horarios por el cabeceraPlantillaId
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 04-12-2017 
    *
    * @param array  $parametros
    *
    * @return array $resultado
    *
    */
    
    public function getRegistros($arrayParametros)
    {
        $intJurisdiccionId = $arrayParametros['intJurisdiccionId'];
        try
        {
            $arrayDatos  = array();
            $objQuery = $this->_em->createQuery(null);

            $strSql = "SELECT
                       pho
                       FROM
                       schemaBundle:AdmiPlantillaHorarioDet pho
                       JOIN schemaBundle:AdmiPlantillaHorarioCab phc with pho.plantillaHorarioId = phc.id
                       where phc.jurisdiccionId = '$intJurisdiccionId'
                       order by pho.horaInicio";

            $objQuery = $this->_em->createQuery($strSql);

            $arrayResult = array();
            $objRegistros = $objQuery->getResult();
            foreach($objRegistros as $objRegistro)
            {
                if ($objRegistro->getAlmuerzo() == "N")
                {
                    $arrayResult[] = array("horaInicio" => $objRegistro->getHoraInicio(), "horaFin" => $objRegistro->getHoraFin());
                }
            }
            $arrayDatos['registros'] = $objQuery->getResult();
        }   
        catch (\Exception $ex)
        {
            error_log("Error: " . $ex->getMessage());
        }   
        return $arrayResult;
    }
    
    
    public function getRegistrosPorIdCabecera($intPlantillaCabeceraId)
    {
        try
        {
        error_log("Entro al try de registro Id Cabecera");
        $arrayDatos  = array();
            
        $strSql = "SELECT
                   pho
                   FROM
                   schemaBundle:AdmiPlantillaHorarioDet pho
                   WHERE ";

        $objQuery = $this->_em->createQuery(null);

        $strSql .= " pho.plantillaHorarioId = :plantillaHorarioId";
        $strSql .= " ORDER BY pho.horaInicio";
        $objQuery->setParameter('plantillaHorarioId', $intPlantillaCabeceraId);
        
        
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
    * getRegistrosParaMobile
    *
    * Esta funcion retorna el detalle de las Plantillas de horarios por el cabeceraPlantillaId para cupos moviles
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 04-06-2018 
    *
    * @param array  $parametros
    *
    * @return array $resultado
    *
    */
    
    public function getRegistrosParaMobile($arrayParametros)
    {
        $intJurisdiccionId = $arrayParametros['intJurisdiccionId'];
        try
        {
            $arrayDatos  = array();
            $objQuery = $this->_em->createQuery(null);

            $strSql = "SELECT
                       pho
                       FROM
                       schemaBundle:AdmiPlantillaHorarioDet pho
                       JOIN schemaBundle:AdmiPlantillaHorarioCab phc with pho.plantillaHorarioId = phc.id
                       where phc.jurisdiccionId = '$intJurisdiccionId'
                         and phc.cupoMobile > 0   
                       order by pho.horaInicio";

            $objQuery = $this->_em->createQuery($strSql);

            $arrayResult = array();
            $objRegistros = $objQuery->getResult();
            foreach($objRegistros as $objRegistro)
            {
                if ($objRegistro->getAlmuerzo() == "N")
                {
                    $arrayResult[] = array("horaInicio" => $objRegistro->getHoraInicio(), "horaFin" => $objRegistro->getHoraFin());
                }
            }
            $arrayDatos['registros'] = $objQuery->getResult();
        }   
        catch (\Exception $ex)
        {
            error_log("Error: " . $ex->getMessage());
        }   
        return $arrayResult;
    }
    
}

