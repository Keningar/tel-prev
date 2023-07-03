<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiCantonJurisdiccionRepository extends EntityRepository
{
    
    /**
     * Documentación para el método 'generarJsonCantonesJurisdicciones'.
     * 
     * Retorna la cadena Json de los cantones por Jurisdicción.
     *
     * @param Array $arrayParametros['JURISDICCIONID'] id de la jurisdicción 
     *                              ['ESTADO']         descripción del estado de búsqueda
     *                              ['CANTONES']       índice del final de la paginación
     * 
     * @return Response Lista de Cantones.
     * 
     * @author Unknow
     * @version 1.0 Unknow
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.1 11-04-2016
     * Se ajusta el consumo del método getCantonesJurisdicciones y ya no se cosulta de manera individual cada cantón
     * Se modifica los parámetros a un solo arrayParamenters.
     * Se elimina el parámetro $em en la definición del método.
     * Renombrado de variables.
     */
    public function generarJsonCantonesJurisdicciones($intJurisdiccionId, $strEstado, $intStart, $intLimit)
    {
        $arrayCantones = array();
        
        $arrayParametros['CANTONES']       = true;
        $arrayParametros['JURISDICCIONID'] = $intJurisdiccionId;
        $arrayParametros['ESTADO']         = $strEstado;

        $listCantonesTotal = $this->getCantonesJurisdicciones($arrayParametros);

        if ($listCantonesTotal) 
        {
            $intTotal = count($listCantonesTotal);
            
            $arrayParametros['START'] = $intStart;
            $arrayParametros['LIMIT'] = $intLimit;

            $listCantones = $this->getCantonesJurisdicciones($arrayParametros);
            
            foreach ($listCantones as $entityCanton)
            {
                $strAccion2 = (trim($entityCanton['estado']) == 'Eliminado' ? 'button-grid-invisible' : 'button-grid-edit');
                $strAccion3 = (trim($entityCanton['estado']) == 'Eliminado' ? 'button-grid-invisible' : 'button-grid-delete');
                
                $arrayCantones[] = array('idCantonJurisdiccion' =>       $entityCanton['id'],
                                         'canton_id'            =>  trim($entityCanton['cantonId']),
                                         'nombreCanton'         =>  trim($entityCanton['nombreCanton']),
                                         'mailTecnico'          =>  trim($entityCanton['mailTecnico']),
                                         'ipReserva'            =>  trim($entityCanton['ipReserva']),
                                         'estado'               => (trim($entityCanton['estado']) == 'Eliminado' ? 'Eliminado' : 'Activo'),
                                         'action1'              => 'button-grid-show',
                                         'action2'              => $strAccion2,
                                         'action3'              => $strAccion3);
            }

            if($intTotal == 0)
            {
               $resultado= array('total' => 1 , 'encontrados' => array('idConectorInterface'     => 0 , 
                                                                       'nombreConectorInterface' => 'Ninguno',
                                                                       'idConectorInterface'     => 0 , 
                                                                       'nombreConectorInterface' => 'Ninguno', 
                                                                       'estado'                  => 'Ninguno'));
                return json_encode( $resultado);
            }
            else
            {
                return '{"total":"' . $intTotal . '","encontrados":' . json_encode($arrayCantones) . '}';
            }
        }
        else
        {
            return '{"total":"0","encontrados":[]}';
        }
    }
   
    /**
     * Documentación para el método 'getCantonesJurisdicciones'.
     * 
     * Método para obtener la lista de cantones por Jurisdicción.
     *
     * @param Array $arrayParametros['JURISDICCIONID'] id de la jurisdicción 
     *                              ['ESTADO']         descripción del estado de búsqueda
     *                              ['START']          índice del inicio de la paginación
     *                              ['CANTONES']       índice del final de la paginación
     * 
     * @return Response Lista de Cantones/CantonesJuridiscción.
     * 
     * @author Unknow
     * @version 1.0 Unknow
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.1 11-04-2016
     * Se cambia listado de resultado de AdmiCantonJurisdiccion a arreglo [id-nombreCanton] de la entidad AdmiCanton.
     * Se modifica los parámetros a un solo arrayParamenters.
     * Renombrado de variables.
     */
    public function getCantonesJurisdicciones($arrayParametros)
    {
        $strValue     = isset($arrayParametros['VALUE'])    ? $arrayParametros['VALUE']    : 'id';
        $strDisplay   = isset($arrayParametros['DISPLAY'])  ? $arrayParametros['DISPLAY']  : 'nombre';
        $intStart     = isset($arrayParametros['START'])    ? $arrayParametros['START']    : '';
        $intLimit     = isset($arrayParametros['LIMIT'])    ? $arrayParametros['LIMIT']    : '';
        $boolCantones = isset($arrayParametros['CANTONES']) ? $arrayParametros['CANTONES'] : false;
        
        $objQueryBuilder = $this->_em->createQueryBuilder();
        
        if($boolCantones)
        {
            $objQueryBuilder->select('e.id, e.cantonId, c.nombreCanton, e.mailTecnico, e.ipReserva, e.estado'); 
        }
        else
        {
            // Se devuelve en modo de array el id y el nombreCanton del Cantón.
            // Se establece los identificadores en el arreglo de resultado, no se requiere el seteo como parámetro.
            $objQueryBuilder->select("c.id as $strValue, c.nombreCanton as $strDisplay ");
        }
        
        $objQueryBuilder->from('schemaBundle:AdmiCanton', 'c')
                        ->innerJoin('schemaBundle:AdmiCantonJurisdiccion', 'e', 'WITH', 'e.cantonId = c.id') // Se agrega un join a AdmiCanton
                        ->orderBy('c.nombreCanton', 'ASC'); // Se ordena por nombreCanton en sentido Ascendente.

        if($arrayParametros['JURISDICCIONID'] != "")
        {
            $objQueryBuilder->where('e.jurisdiccionId = :JURISDICCIONID');
            $objQueryBuilder->setParameter('JURISDICCIONID', $arrayParametros['JURISDICCIONID']);
        }
        
        if($arrayParametros['ESTADO'] != "Todos")
        {
            $objQueryBuilder->andWhere('e.estado = :ESTADO');
            $objQueryBuilder->setParameter('ESTADO', $arrayParametros['ESTADO']);
        }
        
        if(isset($arrayParametros['NOMBRE']) && $arrayParametros['NOMBRE'] != "")
        {
            $objQueryBuilder->andWhere('c.nombreCanton like :NOMBRE');
            $objQueryBuilder->setParameter('NOMBRE', '%' . $arrayParametros['NOMBRE'] . '%');
        }

        // Se anida la paginación
        if($intLimit != '')
        {
            $objQueryBuilder->setMaxResults($intLimit);

            if($intStart != '')
            {
                $objQueryBuilder->setFirstResult($intStart);
            }
        }

        return $objQueryBuilder->getQuery()->getResult();
    }

    /**
     * Funcion que sirve para actualizar la jurisdiccion
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 08-07-2016
     * @since   1.0
     * No se borra registro de relación Cantón-Jurisdicción de la BD, se cambia a estado Eliminado.
     */
    public function borrarDistintosEleccion($arreglo_relaciones, $jurisdiccionId, $strUsuario)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('srs')
            ->from('schemaBundle:AdmiCantonJurisdiccion', 'srs')
            ->where('srs.jurisdiccionId = ?1')
            ->setParameter(1, $jurisdiccionId)
            ->andWhere($qb->expr()->NotIn('srs.id', $arreglo_relaciones));

        $query     = $qb->getQuery();
        $distintos = $query->getResult();

        foreach($distintos as $entityCantonJurisdiccion)
        {
            $entityCantonJurisdiccion->setEstado('Eliminado');
            $entityCantonJurisdiccion->setUsrUltMod($strUsuario);
            $entityCantonJurisdiccion->setFeUltMod(new \DateTime('now'));
            $this->_em->persist($entityCantonJurisdiccion);
            $this->_em->flush();
        }
    }

}
