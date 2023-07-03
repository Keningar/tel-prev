<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * AdmiCanalPagoLineaRepository, Clase donde se crean metodos con consultas personalidas para obtener informacion 
 * de la entidad AdmiCanalPagoLinea que hace referencia a la estructura DB_FINANCIERO.ADMI_CANAL_PAGO_LINEA
 * 
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 09-09-2015
 */
class AdmiCanalPagoLineaRepository extends EntityRepository
{

     /**
     * getListaCanalPagosLinea, Crea la sentencia DQL según los parámetros enviados de la entidad AdmiCanalPagoLinea
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 09-09-2015
     * 
     * @param   array $arrayParametros['intIdCanalPagoLinea' => Id(primary key de la entidad),
     *                                 'strEstado'           => Estado del registro,
     *                                 'intStart'            => Parametro de inicio para obtener la data,
     *                                 'intLimit'            => Parametro de fin para obtener la data]
     *
     * @return array  Retorna un array con la información devuelta por la entidad AdmiCanalPagoLinea según los filtros enviados.
     *                $arrayResponse[
     *                                arrayDatos  => Retorna el resultado del query creado segun los parametros enviados
     *                                strStatus   => Retorna el estatus del metodo 
     *                                 ['000'  => 'No realizó la consulta', 
     *                                  '001'  => 'Existió algun error', 
     *                                  '100'  => 'Consulta realizada con éxito' ]
     *                                strMensaje  => Retorna un mensaje
     *                              ]
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.1 10-05-2023   Se agrega filtro por empresa en la consulta general.
     *                             Ahora solo muestra canales respectivos a la empresa en sesion.
     *                             Tambien se corrige consulta general, parametro de entrada intIdCanalPagoLinea.     * 
     */
    public function getListaCanalPagosLinea($arrayParametros)
    {
        $arrayResponse                  = array();
        $arrayResponse['strMensaje']    = 'No realizó la consulta';
        $arrayResponse['strStatus']     = '000';
        $arrayResponse['arrayDatos']    = '';
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT count(acpl.id) ";
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT acpl ";
                                
            $strFromQuery = "FROM schemaBundle:AdmiCanalPagoLinea acpl "
                            . " WHERE 1 = 1";

            //Pregunta si $arrayParametros['intEmpresaId'] es diferente de vacío para agregar la condición.
            if (!empty($arrayParametros['intEmpresaId']))
            {
                $strFromQuery = "FROM schemaBundle:AdmiCanalPagoLinea acpl "
                                . " WHERE 1 = 1"
                                . " AND acpl.id IN (SELECT det.valor1 FROM schemaBundle:AdmiParametroCab cab,"
                                . " schemaBundle:AdmiParametroDet det WHERE cab.id = det.parametroId"
                                . " AND UPPER(cab.nombreParametro) ="
                                . " 'LISTA CANALES PAGO LINEA PARA REPORTE' AND det.empresaCod = (:intEmpresaId))";
                $objQuery->setParameter('intEmpresaId', $arrayParametros['intEmpresaId']);
                $objQueryCount->setParameter('intEmpresaId', $arrayParametros['intEmpresaId']);
            }
            
            //Pregunta si $arrayParametros['intIdCanalPagoLinea'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['intIdCanalPagoLinea']))
            {
                $strFromQuery .= " AND acpl.id = (:intIdCanalPagoLinea)";
                $objQuery->setParameter('intIdCanalPagoLinea', $arrayParametros['intIdCanalPagoLinea']);
                $objQueryCount->setParameter('strNombreParametro', $arrayParametros['intIdCanalPagoLinea']);
            }

            //Pregunta si $arrayParametros['strEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strEstado']))
            {
                $strFromQuery .= " AND acpl.estadoPagoLinea IN (:strEstado)";
                $objQuery->setParameter('strEstado', $arrayParametros['strEstado']);
                $objQueryCount->setParameter('strEstado', $arrayParametros['strEstado']);
            }

            $strFromQuery .= " ORDER BY acpl.descripcionCanalPagoLinea";
            $objQuery->setDQL($strQuery . $strFromQuery);
            if(!empty($arrayParametros['intStart']))
            {
                $objQuery->setFirstResult($arrayParametros['intStart']);
            }
            if(!empty($arrayParametros['intStart']))
            {
                $objQuery->setMaxResults($arrayParametros['intLimit']);
            }
            $arrayResponse['arrayDatos'] = $objQuery->getResult();
            $arrayResponse['strMensaje'] = 'Consulta realizada con éxito';
            $arrayResponse['strStatus']  = '100';
            //Pregunta si $arrayResult['arrayDatos'] es diferente de vacio para realizar el count
            if(!empty($arrayResponse['arrayDatos']))
            {
                $strQueryCount = $strQueryCount . $strFromQuery;
                $objQueryCount->setDQL($strQueryCount);
                $arrayResponse['intTotal'] = $objQueryCount->getSingleScalarResult();
            }
        }
        catch(\Exception $ex)
        {
            $arrayResponse['strMensaje'] = 'Existion un error en findParametrosCab - ' . $ex->getMessage();
            $arrayResponse['strStatus']  = '001';
        }
        return $arrayResponse;
    }//getListaCanalPagosLinea
}