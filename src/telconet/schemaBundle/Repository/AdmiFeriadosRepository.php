<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AdmiFeriadosRepository extends EntityRepository
{
    /**
    * generarJson
    *
    * Esta funcion retorna en formato JSON la lista de feriados
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 10-12-2018 
    *
    * @param array  $arrayParametros
    *
    * @return array $arrayResultado
    *
    */
    public function generarJsonFeriados($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayRegistros   = array();

        $arrayRegistros     = $this->getRegistros($arrayParametros);
        $objRegistros       = $arrayRegistros['registros'];
        $intRegistrosTotal  = $arrayRegistros['total'];

        if ($intRegistrosTotal)
        {
            $intTotal = count($intRegistrosTotal);
            foreach ($objRegistros as $objRegistro)
            {
                switch($objRegistro->getMes())
                {   
                    case "1":
                        $strNombreMes = "Enero";
                        break;
                    case "2":
                        $strNombreMes = "Febrero";
                        break;
                    case "3":
                        $strNombreMes = "Marzo";
                        break;
                    case "4":
                        $strNombreMes = "Abril";
                        break;
                    case "5":
                        $strNombreMes = "Mayo";
                        break;
                    case "6":
                        $strNombreMes = "Junio";
                        break;
                    case "7":
                        $strNombreMes = "Julio";
                        break;
                    case "8":
                        $strNombreMes = "Agosto";
                        break;
                    case "9":
                        $strNombreMes = "Septiembre";
                        break;
                    case "10":
                        $strNombreMes = "Octubre";
                        break;
                    case "11":
                        $strNombreMes = "Noviembre";
                        break;
                    case "12":
                        $strNombreMes = "Diciembre";
                        break;
                    default:
                        $strNombreMes = "";
                        break;
                }
                $arrayEncontrados[] =array('idFeriados'  => $objRegistro->getId(),
                                           'descripcion' => trim($objRegistro->getDescripcion()),
                                           'tipo'        => trim($objRegistro->getTipo()),
                                           'mes'         => trim($objRegistro->getMes()),
                                           'nombreMes'   => $strNombreMes,                    
                                           'dia'         => trim($objRegistro->getDia()),
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
    * Esta funcion retorna la lista de Feriados a presentarse en el grid
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 10-12-2018 
    *
    * @param array  $arrayParametros
    *
    * @return array $arrayResultado
    *
    */
    public function getRegistros($arrayParametros)
    {
        try
        {
            $arrayDatos  = array();

            $strSql = "SELECT
                       pho
                       FROM
                       schemaBundle:AdmiFeriados pho";

            $objQuery = $this->_em->createQuery(null);


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
}
    
    

