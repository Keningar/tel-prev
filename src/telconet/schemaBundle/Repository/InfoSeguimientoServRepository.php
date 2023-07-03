<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use telconet\schemaBundle\DependencyInjection\BaseRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\ResultSetMapping;

class InfoSeguimientoServRepository extends BaseRepository
{
    /**
     * Función que retorna el Mapeo entre el Producto SDWAN con el de seguridad Security NG Firewall.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 04-01-2020
     * Costo = 12
     *
     * @param array $arrayParametros [ "strCodEmpresa"      => código empresa en sesión
     *                                 "intIdProducto"      => id del producto que se desea verificar,
     *                                 "strObtieneCaract"   => si se desea filtrar para obtener la característica asociada
     *                               ]
     * @return array $arrayResultado [ *  ]
     */
    public function historialSeguimiento($arrayParametros)
    {
        try
        {
        $objQuery      = $this->_em->createQuery();

        $strQuery       = " SELECT ISS
                                         FROM schemaBundle:InfoSeguimientoServicio ISS
                                         WHERE ISS.servicioId  = :intServicioId
                                         ORDER BY ISS.id ";

        $objQuery->setParameter('intServicioId', $arrayParametros['intServicioId']);
        
        $objQuery->setDQL($strQuery);              
        $arrayDatos = $objQuery->getResult();
        
        if(!empty($arrayDatos))
        {
            $arrayResultado = array(
                                "strStatus"  => "OK" ,
                                "arrayData"  => $arrayDatos,
                                "strMensaje" => "Información recuperada existosamente");
        }
        else
        {
            $arrayResultado = array(
                                "strStatus"  => "ERROR" ,
                                "arrayData"  => $arrayDatos,
                                "strMensaje" => "No se Encontraron Datos");
        }
        }
        catch (\Exception $e) 
        {
            $arrayResultado = array(
                                    "strStatus"  => "ERROR" ,
                                    "arrayData"  => array(),
                                    "strMensaje" => $e->getMessage()
                                   );
        }
        return $arrayResultado;
    }
}
