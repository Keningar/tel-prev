<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiTipoCasoRepository extends EntityRepository
{
    public function generarJson($nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '');
        $registros = $this->getRegistros($nombre, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_tipo_caso' =>$data->getId(),
                                         'nombre_tipo_caso' =>trim($data->getNombreTipoCaso()),
                                         'descripcion_tipo_caso' =>trim($data->getDescripcionTipoCaso()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_tipo_caso' => 0 , 'nombre_tipo_caso' => 'Ninguno', 'descripcion_tipo_caso' => 'Ninguno', 'hipotesis_id' => 0 , 'hipotesis_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF =json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        
    }

    /**
    * getArrayTipoCaso
    *
    * Esta funcion retorna la lista de los tipos de casos activos
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-12-2015
    *
    * @return array $arrayTiposCasos
    *
    */
    public function getArrayTipoCaso()
    {
        $tiposCasos      = $this->_em->getRepository('schemaBundle:AdmiTipoCaso')->findByEstado("Activo");
        $arrayTiposCasos = false;
        if($tiposCasos && count($tiposCasos)>0)
        {
            foreach($tiposCasos as $key => $valueCaso)
            {
                $arrayCaso["id"]     = $valueCaso->getId();
                $arrayCaso["nombre"] = $valueCaso->getNombreTipoCaso();
                $arrayTiposCasos[]   = $arrayCaso;
            }
        }
        return $arrayTiposCasos;
    }

    /**
    * getTipoCasosXDepartamento
    *
    * Esta funcion retorna los tipos de casos que se deben presentar segun el departamento del usuario que esta conectado
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-12-2015
    *
    * @param  String  $departamento
    *
    * @return array $arraytipoCaso
    *
    */
    public function getTipoCasosXDepartamento($departamento)
    {
        $arraytipoCaso  = '';

        //Se consultan los parametros para obtener el departamento y tipo de caso a mostrarse
        $bandera = $this->_em->getRepository('schemaBundle:AdmiParametroDet')->get("DEPARTAMENTO_TIPO_CASO", "", "", "",$departamento, "", "", "");

        if ($bandera)
        {
            if($bandera[0]['valor2'])
            {
                $arraytipoCaso = $bandera[0]['valor2'];
                if($bandera[0]['valor3'])
                {
                    $arraytipoCaso = $arraytipoCaso . "','" . $bandera[0]['valor3'];
                    if($bandera[0]['valor4'])
                    {
                        $arraytipoCaso = $arraytipoCaso . "','" . $bandera[0]['valor4'];
                    }
                }
            }
        }
        else
        {
            $tipoCaso       = $this->_em->getRepository('schemaBundle:AdmiTipoCaso')->findAll();

            foreach($tipoCaso as $reg):
                $arraytipoCaso = $arraytipoCaso . $reg->getNombreTipoCaso() . "','";
            endforeach;
            $arraytipoCaso     = substr($arraytipoCaso,0,strlen($arraytipoCaso)-3);
        }
        return $arraytipoCaso;
    }

    public function getRegistros($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiTipoCaso','sim');
            
        $boolBusqueda = false; 
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.nombreTipoCaso) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere('LOWER(sim.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }
        
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
}
