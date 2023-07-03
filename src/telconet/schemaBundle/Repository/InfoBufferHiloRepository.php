<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use telconet\schemaBundle\DependencyInjection\BaseRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\schemaBundle\Entity\ReturnResponse;

/**
 * Clase repositorio para InfoBufferHilo
 * 
 * 
 */
class InfoBufferHiloRepository extends EntityRepository
{
    
    
     /**
     * getBufferHiloBy
     * Obtiene la relacion buffer hilo mediante los sgts parametros
     *
     * @param type $colorBuffer
     * @param type $colorHilo
     * @param type $nombreClaseTipoMedio
     * @param type $empresa
     *
     * @return string $data
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 29-06-2016
     * */
    
    public function getBufferHiloBy($colorBuffer, $colorHilo, $nombreClaseTipoMedio, $empresa)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);

        $sql = "SELECT IBH.ID_BUFFER_HILO,
                IBH.ESTADO ESTADO_BH,
                IBH.EMPRESA_COD,
                AB.COLOR_BUFFER,
                AB.ESTADO ESTADO_B,
                AH.COLOR_HILO,
                AH.NUMERO_HILO,
                AH.ESTADO ESTADO_H,
                IBH.USR_CREACION,
                ACTM.NOMBRE_CLASE_TIPO_MEDIO
              FROM DB_INFRAESTRUCTURA.ADMI_BUFFER AB ,
                DB_INFRAESTRUCTURA.ADMI_HILO AH ,
                DB_INFRAESTRUCTURA.ADMI_CLASE_TIPO_MEDIO ACTM,
                DB_INFRAESTRUCTURA.INFO_BUFFER_HILO IBH
              WHERE IBH.BUFFER_ID = AB.ID_BUFFER
              AND IBH.HILO_ID     = AH.ID_HILO
              AND ACTM.NOMBRE_CLASE_TIPO_MEDIO =  UPPER(:nombreClaseTipoMedio)
              AND AB.COLOR_BUFFER = UPPER(:colorBuffer)
              AND AH.COLOR_HILO   = UPPER(:colorHilo)
              AND IBH.EMPRESA_COD = :empresa ";
        
        $query->setParameter("colorBuffer", $colorBuffer);
        $query->setParameter("colorHilo", $colorHilo);
        $query->setParameter("nombreClaseTipoMedio", $nombreClaseTipoMedio);
        $query->setParameter("empresa", $empresa);

        $rsm->addScalarResult(strtoupper('ID_BUFFER_HILO'), 'idBufferHilo', 'integer');
        $rsm->addScalarResult(strtoupper('ESTADO_BH'), 'estadoBufferHilo', 'string');
        $rsm->addScalarResult(strtoupper('EMPRESA_COD'), 'empresaId', 'integer');
        $rsm->addScalarResult(strtoupper('COLOR_BUFFER'), 'colorBuffer', 'string');
        $rsm->addScalarResult(strtoupper('ESTADO_BUFFER'), 'estadoBuffer', 'string');
        $rsm->addScalarResult(strtoupper('COLOR_HILO'), 'colorHilo', 'string');
        $rsm->addScalarResult(strtoupper('NUMERO_HILO'), 'numeroHilo', 'integer');
        $rsm->addScalarResult(strtoupper('ESTADO_HILO'), 'estadoHilo', 'string');
        $rsm->addScalarResult(strtoupper('USR_CREACION'), 'userCreacion', 'string');
        $rsm->addScalarResult(strtoupper('NOMBRE_CLASE_TIPO_MEDIO'), 'claseTipoMedio', 'string');

        $query->setSQL($sql);
        $encontrados = $query->getResult();
        $totalSolicitudes = count($encontrados);
        $solicitudesArray = array();
        if($encontrados)
        {
            foreach($encontrados as $registro)
            {
                $solicitudesArray[] = array(
                    'idBufferHilo'      => $registro['idBufferHilo'],
                    'estadoBufferHilo'  => $registro['estadoBufferHilo'],
                    'empresaId'         => $registro['empresaId'],
                    'colorBuffer'       => $registro['colorBuffer'],
                    'estadoBuffer'      => $registro['estadoBuffer'],
                    'colorHilo'         => $registro['colorHilo'],
                    'numeroHilo'        => $registro['numeroHilo'],
                    'estadoHilo'        => $registro['estadoHilo'],
                    'userCreacion'      => $registro['userCreacion'],
                    'claseTipoMedio'    => $registro['claseTipoMedio']);
            }
        }

        $resultadoArray['registros'] = $solicitudesArray;
        $resultadoArray['total'] = $totalSolicitudes;
        return $resultadoArray;
    }
    
    
}
