<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;

class InfoPaqueteSoporteCabRepository extends EntityRepository
{
    public function generarJson($strEstado, $intStart, $intLimit)
    {
        $arrayEncontrados = array();
        
        $objRegistrosTotal = $this->getRegistros($strEstado, '', '');
        $objRegistros = $this->getRegistros($strEstado, $intStart, $intLimit);
 
        if ($objRegistros)
        {
            $intNum = count($objRegistrosTotal);
            foreach ($objRegistros as $data)
            {
                        
                $arrayEncontrados[]=array('UUID_PAQUETE_SOPORTE_CAB' =>$data->getId(),
                                        'UUID_PAQUETE_SOPORTE_CAB' =>$data->getId(),
                                         'PERSONA_EMPRESA_ROL_ID' =>$data->getPlantillaId(),
                                         'LOGIN_PUNTO_PAQUETE' =>$data->getProductoId(),
                                         'SERVICIO_ID' =>$data->getProcesoId(),
                                         'LOGIN_SERVICIO_PAQUETE' =>$data->getTareaId(),
                                         'EMPRESA_COD' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'));
            }

            if($intNum == 0)
            {
                $objResultado= array('total' => 1 ,
                                 'encontrados' => array('id_plantilla_producto_tarea' => 0 ,
                                 'UUID_PAQUETE_SOPORTE_CAB' => 0,
                                 'PERSONA_EMPRESA_ROL_ID' => 0,
                                 'LOGIN_PUNTO_PAQUETE' => 0,
                                 'SERVICIO_ID' => 0,
                                 'LOGIN_SERVICIO_PAQUETE' => 'Ninguno'));

                $objResultado = json_encode($objResultado);
                return $objResultado;
            }
            else
            {
                $objDataF =json_encode($arrayEncontrados);
                $objResultado= '{"total":"'.$intNum.'","encontrados":'.$objDataF.'}';
                return $objResultado;
            }
        }
        else
        {
            $objResultado= '{"total":"0","encontrados":[]}';
            return $objResultado;
        }
        
    }

     /*
     * Función que retorna el listado de procesos de getUuIdPaqueteSoporteCabx Servicio y Empresa_rol_id.
     *
     * @version Initial - 1.0
     * 
     * @param type $arrayTmpParametros
     * @return type
     */
    public function getUuIdPaqueteSoporteCabxServEmpre($arrayTmpParametros)
    {
        $intServicioId             = $arrayTmpParametros['servicioId'];
        $intPersonaEmpresaRolId    = $arrayTmpParametros['personaEmpresaRolId'];

        $objQuery = $this->_em->createQuery("SELECT pcab.uuidPaqueteSoporteCab 
                    FROM 
                schemaBundle:InfoPaqueteSoporteCab pcab
                WHERE 
                        pcab.servicioId          =   '".$intServicioId."'
                AND 
                        pcab.personaEmpresaRolId =  '".$intPersonaEmpresaRolId."'    
                    ");
        $objDatos = $objQuery->getResult();

        return $objDatos;
    }

    
    /*
     * Función que retorna el listado  InfoPaqueteSoporteCab por estado
     *
     * @version Initial - 1.0
     *
     * @param type $strEstado
     * @return type
    */


  public function findTodasPorEstado($strEstado)
  {
    $objQuery = $this->_em->createQuery("SELECT cab 
                FROM 
            schemaBundle:InfoPaqueteSoporteCab cab
        WHERE 
                        cab.estado='".$strEstado."'");
    $objDatos=$objQuery->getResult();
    return $objDatos;
  }   



    /*
     * Función que retorna la informaciòn de la tabla InfoPaqueteSoporteCabfiltrado por el cab_id
     *
     * @version Initial - 1.0
     *
     * @param type $strEstado
     * @return type
    */

    public function soporteCabPorCabId($arrayParametros)
    {
        $intIdPaqueteSoporteCab = $arrayParametros['idPaqueteSoporteCab'];
        try 
        {
            $objQuery   = $this->_em->createQuery();
            $strSelect  = "SELECT pcab.servicioId
                           FROM schemaBundle:InfoPaqueteSoporteCab pcab
                           WHERE pcab.id = :intIdPaqueteSoporteCab
                        ";
            $objQuery->setParameter("intIdPaqueteSoporteCab", $intIdPaqueteSoporteCab);
            $objQuery->setDQL($strSelect);

            $arrayResultado = $objQuery->getArrayResult(); 
        }
        catch (\Exception $e) 
        {
            $arrayResultado = array(
                'status' => 'ERROR',
                'result' => $e->getMessage()
            );
        }
        return $arrayResultado;
    }
}