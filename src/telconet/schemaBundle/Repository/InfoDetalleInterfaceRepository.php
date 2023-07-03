<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoDetalleInterfaceRepository extends EntityRepository
{
     /**
     * Costo: 6
     * getServiciosPorInterfaceElemento
     *
     * Función utilizada para obtener los servicios relacionados a una interface elemento especifica
     *
     * @param array arrayParametros [ strDetalleNombre       => tipo del valor a guardar, 'servicio',
     *                                arrayEstados           => array de estados,
     *                                intInterfaceElementoId => id de interface elemento
     *                              ]
     *
     * @return array arrayServicios retorna los servicios relacionados
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 20-09-2017
     */
    public function getServiciosPorInterfaceElemento($arrayParametros)
    {
        $arrayServicios = array();
        $objRsmb        = new ResultSetMappingBuilder($this->_em);
        $objQuery       = $this->_em->createNativeQuery(null,$objRsmb);

        $strSql = " SELECT IDE.DETALLE_VALOR FROM INFO_DETALLE_INTERFACE IDE,INFO_SERVICIO ISE
                    WHERE IDE.DETALLE_VALOR = TO_CHAR(ISE.ID_SERVICIO)
                    AND IDE.DETALLE_NOMBRE = :strDetalleNombre
                    AND ISE.ESTADO IN ( :arrayEstados )
                    AND IDE.INTERFACE_ELEMENTO_ID = :intInterfaceElemento ";

        $objQuery->setParameter('strDetalleNombre',$arrayParametros["strDetalleNombre"]);
        $objQuery->setParameter('arrayEstados',$arrayParametros["arrayEstados"]);
        $objQuery->setParameter('intInterfaceElemento',$arrayParametros["intInterfaceElementoId"]);

        $objRsmb->addScalarResult('DETALLE_VALOR', 'idServicio', 'integer');

        $objQuery->setSQL($strSql);

        $arrayServicios = $objQuery->getResult();

        return $arrayServicios;
    }
    
    /**
     * Costo: 8
     * getIdInterfaceElemento
     *
     * Función utilizada para obtener la interface elemento id
     *
     * @param array arrayParametros [ strDetalleValor       => tipo del valor a consultar, 'interfaceElemenmtoId']
     *
     * @return array $arrayInterface retorna el interface_elemento_id relacionado
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 23-07-2021
     */
    public function getIdInterfaceElemento($arrayParametros)
    {
        $arrayInterface = array();
        $objRsmb        = new ResultSetMappingBuilder($this->_em);
        $objQuery       = $this->_em->createNativeQuery(null,$objRsmb);

        $strSql = " SELECT *
                    FROM (
                         SELECT DB_INFRAESTRUCTURA.INFO_detalle_INTERFACE.INTERFACE_ELEMENTO_ID
                           FROM DB_INFRAESTRUCTURA.INFO_detalle_INTERFACE where Detalle_Valor = :strDetalleValor
                          ORDER BY fe_creacion DESC
                         )
                    WHERE rownum = 1 ";

        $objQuery->setParameter('strDetalleValor',$arrayParametros["strDetalleValor"]);
        
        $objRsmb->addScalarResult('INTERFACE_ELEMENTO_ID', 'idInterfaceElemento', 'integer');

        $objQuery->setSQL($strSql);

        $arrayInterface = $objQuery->getResult();

        return $arrayInterface;
    }
    
    /**
     * Costo: 5
     * getSerieFisica
     *
     * Función utilizada para obtener la serie física del cpe
     *
     * @param array arrayParametros [ intInterfaceElementoId    => tipo del valor a consultar, 'serie_fisica']
     *
     * @return array arrayServicios retorna el interface_elemento_id relacionado
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 23-07-2021
     */
    public function getSerieCpe($arrayParametros)
    {
        $arraySerieCpe  = array();
        $objRsmb        = new ResultSetMappingBuilder($this->_em);
        $objQuery       = $this->_em->createNativeQuery(null,$objRsmb);

        $strSql = " SELECT ele.id_elemento
                    FROM DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO inter,DB_INFRAESTRUCTURA.INFO_ELEMENTO ele   
                    WHERE ID_INTERFACE_ELEMENTO = :intInterfaceElementoId
                    AND inter.elemento_id = ele.id_elemento ";

        $objQuery->setParameter('intInterfaceElementoId',$arrayParametros["intInterfaceElementoId"]);
        
        $objRsmb->addScalarResult('ID_ELEMENTO', 'intIdElemento', 'integer');

        $objQuery->setSQL($strSql);

        $arraySerieCpe = $objQuery->getResult();

        return $arraySerieCpe;
    }
}
