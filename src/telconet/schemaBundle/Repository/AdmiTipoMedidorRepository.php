<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiTipoMedidorRepository extends EntityRepository
{
    public function generarJsonTiposMedidores($nombre,$estado,$start,$limit){
        $arr_encontrados = array();
        
        $tiposMedidoresTotal = $this->getTiposMedidores($nombre,$estado,'','');
        
        $tiposMedidores = $this->getTiposMedidores($nombre,$estado,$start,$limit);

        if ($tiposMedidores) {
            
            $num = count($tiposMedidoresTotal);
            
            foreach ($tiposMedidores as $tipoMedidor)
            {
                $arr_encontrados[]=array('idTipoMedidor' =>$tipoMedidor->getId(),
                                         'nombreTipoMedidor' =>trim($tipoMedidor->getNombreTipoMedidor()),
                                         'estado' =>(trim($tipoMedidor->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($tipoMedidor->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($tipoMedidor->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno','idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);

                return $resultado;
            }
            else
            {
                $data=json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }
   
    public function getTiposMedidores($nombre,$estado,$start,$limit)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb ->select('e')
            ->from('schemaBundle:AdmiTipoMedidor','e');
               
            
        if($nombre!=""){
            $qb ->where( 'e.nombreTipoMedidor like ?1');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        if($estado!="Todos"){
            $qb ->andWhere('e.estado = ?2');
            $qb->setParameter(2, $estado);
        }
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }
    
    /**
     * Método que obtiene el json con la consulta de los medidores eléctricos de acuerdo
     * a los parámetros enviados
     *
     * @version 1.0
     * @author Antonio Ayala <afayala@telconet.ec>
     *
     * @param string $strNombre
     * @param string $strEstado
     * @param string $strParametro
     * @return json $resultado
     */
    public function generarJsonMedidoresElectricos($strNombre,$strEstado,$strParametro)
    {
        $arrayEncontrados = array();
        
        $arrayMedidoresElectricosTotal = $this->getMedidoresElectricos($strNombre,$strEstado,$strParametro);
        
        $arrayMedidoresElectricos = $this->getMedidoresElectricos($strNombre,$strEstado,$strParametro);

        if ($arrayMedidoresElectricos) 
        {
            $intNum = count($arrayMedidoresElectricosTotal);
            
            foreach ($arrayMedidoresElectricos as $arrayMedidorElectrico)
            {
                $arrayEncontrados[] =
                        array('valor1' =>trim($arrayMedidorElectrico->getValor1()),
                              'estado'  =>(trim($arrayMedidorElectrico->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                              'action1' => 'button-grid-show',
                              'action2' => (trim($arrayMedidorElectrico->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                              'action3' => (trim($arrayMedidorElectrico->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
            }

            if($intNum == 0)
            {
               $arrayResultado= array('total' => 1 ,
                                 'encontrados' => array('idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno','idConectorInterface' => 0
                                                       ,'nombreConectorInterface' => 'Ninguno', 'estado' => 'Ninguno'));
                $objResultado = json_encode( $arrayResultado);

                return $objResultado;
            }
            else
            {
                $objData=json_encode($arrayEncontrados);
                $arrayResultado= '{"total":"'.$intNum.'","encontrados":'.$objData.'}';

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
     * Método que obtiene los valores que se encuentran asociados a un medidor eléctrico
     * a los parámetros enviados
     *
     * @version 1.0
     * @author Antonio Ayala <afayala@telconet.ec>
     *
     * @param string $strNombre
     * @param string $strEstado
     * @param string $strParametro
     * @return $query
     */
    public function getMedidoresElectricos($strNombre,$strEstado,$strParametro)
    {
        $objSelect = $this->_em->createQueryBuilder();
        $objSelect->select('b')
           ->from('schemaBundle:AdmiParametroCab','a')
           ->from('schemaBundle:AdmiParametroDet','b')
           ->Where('a.nombreParametro = ?1 ')
           ->setParameter(1, $strNombre);
        
        if($strParametro!="")
        {
            $objSelect->andWhere('b.valor1 = ?2');
            $objSelect->setParameter(2, $strParametro);
        } 
        $objSelect->andWhere('a.id = b.parametroId ')
           ->andWhere("a.estado = 'Activo' ")
           ->andWhere("b.estado = 'Activo' ");
            
        $objSelect->orderBy('b.valor1', 'ASC');

        $objQuery = $objSelect->getQuery();

        return $objQuery->getResult();
    }

    /**
     * Método que obtiene los parámetros relaciones a una categoría en específico.
     *
     * @version 1.0 2023-03-03
     * @author Geovanny Cudco <acudco@telconet.ec>
     * 
     * @param array $arrayParametro arreglo de parámetros
     *                          ['strParametro','strEstado']
     * 
     * @return array con resultado $resultado
     */

    public function getElementosConClase($arrayParametros)
    {
        $objRsm       = new ResultSetMappingBuilder($this->_em);
        $objQuery     = $this->_em->createNativeQuery(null, $objRsm);

        $strParametro = $arrayParametros['strParametro'];        
        $strEstado    = $arrayParametros['strEstado'];

        $strSql       = "SELECT PRM.NOMBRE_PARAMETRO,
                                PRM.ID_PARAMETRO,
                                DET.PARAMETRO_ID,
                                DET.DESCRIPCION,
                                DET.VALOR1,
                                DET.ESTADO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB PRM       
                        INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET
                            ON PRM.ID_PARAMETRO=DET.PARAMETRO_ID
                        WHERE PRM.NOMBRE_PARAMETRO = :strInParametro
                            AND DET.ESTADO         = :strInEstado
                        ORDER BY DET.VALOR1";

        $objQuery->setParameter("strInParametro", $strParametro);        
        $objQuery->setParameter("strInEstado", $strEstado);

        $objRsm->addScalarResult('VALOR1', 'tipoElemento', 'string');
        $objRsm->addScalarResult('DESCRIPCION', 'descripcion', 'string');
    
        $objQuery->setSQL($strSql);
        $arrayDatos = $objQuery->getScalarResult();
        
        return $arrayDatos;
    }
}
