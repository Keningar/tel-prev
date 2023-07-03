<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiAliasRepository extends EntityRepository
{
    /**
     * Método que obtiene el json con la consulta de los alias de acuerdo a los parámetros enviados
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 12-09-2016 Se agrega el parámetro de estado para la obtención del campo es copia
     *
     * @param string $nombre
     * @param string $estado
     * @param integer $empresa
     * @param integer $ciudad
     * @param integer $departamento
     * @param string $start
     * @param string $limit
     * @param entityManager $em
     * @param entityManager $emI
     * @param integer $idPlantilla
     * @return json $resultado
     */
    public function generarJson($nombre,$estado,$empresa,$ciudad,$departamento,$start,$limit,$em, $emI, $idPlantilla='')
    {
        $arr_encontrados = array();

        if($idPlantilla == '')
        {
            $registrosTotal = $this->getRegistros($nombre, $estado, $empresa, $ciudad, $departamento, '', '');
            $registros = $this->getRegistros($nombre, $estado, $empresa, $ciudad, $departamento, $start, $limit);
        }
        else
        {
            $registrosTotal = $this->getRegistrosPorPlantilla($idPlantilla,$nombre, '', '');
            $registros = $this->getRegistrosPorPlantilla($idPlantilla,$nombre, $start, $limit);
        }

        if ($registros) 
        {
            $num = count($registrosTotal);

            foreach($registros as $data)
            {
                $empresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->find($data->getEmpresaCod());

                if($data->getCantonId())
                {
                    $juris = $emI->getRepository('schemaBundle:AdmiCanton')->find($data->getCantonId());
                    $jurisdiccion = $juris->getNombreCanton();
                }
                else
                {
                    $jurisdiccion = 'N/A';
                }

                if($data->getDepartamentoId())
                {
                    $juris = $emI->getRepository('schemaBundle:AdmiDepartamento')->find($data->getDepartamentoId());
                    $departamento = $juris->getNombreDepartamento();
                }
                else
                {
                    $departamento = 'N/A';
                }

                $esCopia = 'NO';
                if($idPlantilla != '')
                {
                    $objInfoAliasPlantilla = $this->_em->getRepository('schemaBundle:InfoAliasPlantilla')
                                                       ->findOneBy(array(   'plantillaId'   => $idPlantilla,
                                                                            'aliasId'       => $data->getId(),
                                                                            'estado'        => 'Activo'));

                    if($objInfoAliasPlantilla)
                    {
                        $esCopia = $objInfoAliasPlantilla->getEsCopia()?$objInfoAliasPlantilla->getEsCopia():'NO';
                    }
                }

                $arr_encontrados[] = array( 'id_alias'     => $data->getId(),
                                            'esCC'         => $esCopia,
                                            'valor'        => trim($data->getValor()),
                                            'empresa'      => trim($empresa->getNombreEmpresa()),
                                            'jurisdiccion' => $jurisdiccion,
                                            'departamento' => $departamento,
                                            'estado'       => (strtolower(trim($data->getEstado())) == strtolower('ELIMINADO') ? 
                                                              'Eliminado' : trim($data->getEstado())),
                                            'action1'      => 'button-grid-show',
                                            'action2'      => (strtolower(trim($data->getEstado())) == strtolower('ELIMINADO') ? 
                                                              'icon-invisible' : 'button-grid-edit'),
                                            'action3'      => (strtolower(trim($data->getEstado())) == strtolower('ELIMINADO') ? 
                                                              'icon-invisible' : 'button-grid-delete')
                                           );
            }

            if($num == 0)
            {
                $datos = array('total' => 1,
                    'encontrados' => array('id_alias' => 0, 'valor' => 'Ninguno', 'estado' => 'Ninguno', 'empresa' => 'Ninguno'));
                $resultado = json_encode($datos);
                return $resultado;
            }
            else
            {
                $datos = json_encode($arr_encontrados);
                $resultado = '{"total":"' . $num . '","encontrados":' . $datos . '}';
                return $resultado;
            }
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }
    
    public function getRegistros($nombre,$estado,$empresa='',$ciudad='',$departamento='',$start,$limit)
    {
        $qb = $this->_em->createQueryBuilder();
        
        $qb->select('sim')
           ->from('schemaBundle:AdmiAlias', 'sim');

        if($nombre != "")
        {
            $qb->where('LOWER(sim.valor) like LOWER(?1)');
            $qb->setParameter(1, '%' . $nombre . '%');
        }

        if($estado != "Todos")
        {
            if($estado == "Activo")
            {
                $qb->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else
            {
                $qb->andWhere('LOWER(sim.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }

        if($empresa != "")
        {
            $qb->andWhere('sim.empresaCod = ?3');
            $qb->setParameter(3, $empresa);
        }

        if($ciudad != "")
        {
            $qb->andWhere('sim.cantonId = ?4');
            $qb->setParameter(4, $ciudad);
        }

        if($departamento != "")
        {
            $qb->andWhere('sim.departamentoId = ?5');
            $qb->setParameter(5, $departamento);
        }

        if($start != '')
        {
            $qb->setFirstResult($start);
        }
        if($limit != '')
        {
            $qb->setMaxResults($limit);
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }
    
    /**
     * Método que obtiene los alias que se encuentran asociados a una plantilla
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 07-09-2016 Se agrega un parámetro para realizar la búsqueda por nombre del alias
     *
     * @param integer $idPlantilla
     * @param string $nombre
     * @param string $start
     * @param string $limit
     * @return array $arrayResultado
     */
    public function getRegistrosPorPlantilla($idPlantilla='',$nombre='',$start='',$limit='')
    {       
	  
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a')
           ->from('schemaBundle:AdmiAlias','a')
           ->from('schemaBundle:InfoAliasPlantilla','b')
           ->where('a = b.aliasId')
           ->andWhere("b.estado <> 'Eliminado' ");

        if($idPlantilla!="")
        {            
            $qb ->andWhere( 'b.plantillaId = ?1');
            $qb->setParameter(1, $idPlantilla);                                   
        }

        if($nombre!="")
        {
            $qb->andWhere('LOWER(a.valor) like LOWER(?2)');
            $qb->setParameter(2, '%' . $nombre . '%');     
        }

        if($start != '')
        {
            $qb->setFirstResult($start);
        }
        if($limit != '')
        {
            $qb->setMaxResults($limit);
        }

        $query = $qb->distinct()->getQuery();

        return $query->getResult();
      }

}
