<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;

class InfoEnlaceServicioBackboneRepository extends EntityRepository
{
    public function generarJson($strLoginAux, $strEstado, $intStart, $intLimit)
    {
        $arrayEncontrados = array();
        
        $objRegistrosTotal = $this->getRegistros($strLoginAux, $strDescripcion, $strEstado, '', '');
        $objRegistros = $this->getRegistros($strLoginAux, $strEstado, $intStart, $intLimit);
 
        if ($objRegistros)
        {
            $intNum = count($objRegistrosTotal);
            foreach ($objRegistros as $data)
            {
                        
                $arrayEncontrados[]=array('id_enlace_servicio_backbone' =>$data->getId(),
                                          'enlace_id' =>trim($data->getEnlaceId()),
                                          'servicio_id' =>trim($data->getServicioId()),
                                          'login_aux'   =>trim($data->getLoginAux()),
                                          'tipo_ruta'   =>trim($data->getTipoRuta()),
                                          'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'));
            }

            if($intNum == 0)
            {
                $objResultado= array('total' => 1 ,
                                    'encontrados' => array('id_enlace_servicio_backbone'    => 0 ,
                                                            'enlace_id'                     => 'Ninguno',
                                                            'servicio_id'                   => 'Ninguno',
                                                            'login_aux'                     => 'Ninguno'));

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
    
    /**
     * Función que retorna el listado de procesos de AdmiProcesosTelconet.
     *
     * @version Initial - 1.0
     *
     * @param type $strLoginAux
     * @param type $strEstado
     * @param type $intStart
     * @param type $intLimit
     * @return type
     */
    public function getRegistros($strLoginAux, $strEstado, $intStart, $intLimit)
    {
        $objQb = $this->_em->createQueryBuilder();
            $objQb->select('sim')
               ->from('schemaBundle:InfoEnlaceServicioBackbone', 'sim');
            
        if($strLoginAux!="")
        {
            $objQb ->where('LOWER(sim.login_aux) like LOWER(?1)');
            $objQb->setParameter(1, '%'.$strLoginAux.'%');
        }
        
        if($strEstado!="Todos")
        {
            if($strEstado=="Activo")
            {
                $objQb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else
            {
                $objQb ->andWhere('LOWER(sim.estado) = LOWER(?2)');
                $objQb->setParameter(2, $strEstado);
            }
        }
        
        if($intStart!='')
        {
            $objQb->setFirstResult($intStart);
        }
        if($intLimit!='')
        {
            $objQb->setMaxResults($intLimit);
        }
        
        $objDatos = $objQb->getQuery();
        
        return $objDatos->getResult();
    }

	public function findTodasPorstrEstado($strEstado)
	{
		$objDatos = $this->_em->createQuery("SELECT ap
				FROM 
						schemaBundle:InfoEnlaceServicioBackbone ap
				WHERE 
                        ap.estado='".$strEstado."'");

		return $objDatos->getResult();
	}   
}