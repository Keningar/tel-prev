<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiBancoRepository extends EntityRepository
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
                        
                $arr_encontrados[]=array('id_banco' =>$data->getId(),
                                         'descripcion_banco' =>trim($data->getDescripcionBanco()),
                                         'requiere_numero_debito' =>($data->getRequiereNumeroDebito()=='S'?'SI':'NO'),
                                         'genera_debito_bancario' =>($data->getGeneraDebitoBancario()=='S'?'SI':'NO'),
                                         'numbero_cuenta_contable' =>trim($data->getNumeroCuentaContable()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_banco' => 0 , 'descripcion_banco' => 'Ninguno', 
                                                        'requiere_numero_debito'=>'NO', 'genera_debito_bancario'=>'NO', 'numbero_cuenta_contable'=>'Ninguno', 
                                                        'banco_id' => 0 , 'banco_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
     * Funcion que devuelve el nombre de bancos'
     * @author Steven Ruano <sruano@telconet.ec>
     * @version 1.0 12-01-2023.
     * @see \telconet\schemaBundle\Entity\AdmiBanco
     * @return object
     */
    public function getNombre($strNombre) 
    {
        
        $objQb = $this->_em->createQueryBuilder();
        
        $objQb->select('sim')
               ->from('schemaBundle:AdmiBanco','sim');

        $boolBusqueda = false;

        if($nombre!="")
        {

            $boolBusqueda = true;

            $objQb ->where( 'LOWER(sim.descripcionBanco) like LOWER(?1)');

            $objQb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");

            $objQb->setParameter(1, '%'.$strNombre.'%');
        }
        
        $objQuery = $objQb->getQuery();
        
        return $objQuery->getResult();
    }


    /**
     * Funcion que obtiene registros de banco'
     * @author Steven Ruano <sruano@telconet.ec>
     * @version 1.0 12-01-2023.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.2 25-02-2023 - Se modifica query para enviar todos los valores en el select.
     * 
     * @see \telconet\schemaBundle\Entity\AdmiBanco
     * @return object
     */
    public function getRegistros($strNombre,$strEstado,$intStart,$intLimit)
    {
        $strSelect  = "select bc ";
        $strFrom    = "from schemaBundle:AdmiBanco bc ";
        $strWhere   = "where bc.id IS NOT NULL ";
        $strOrderBy = "order by bc.descripcionBanco ";
        
        $boolBusqueda = false; 
            
        if($strNombre!="")
        {
            
            $boolBusqueda = true;

            $strWhere .= "AND LOWER(bc.descripcionBanco) like LOWER('%".$strNombre."%') ";

        }
        
        if($strEstado!="Todos")
        {
            
            $boolBusqueda = true;
            
            if($strEstado=="Activo")
            {
               
                $strWhere .= "AND LOWER(bc.estado) not like LOWER('Eliminado') ";

            }

            else{
                
                $strWhere .= "AND LOWER(bc.estado) like LOWER('".$strEstado."') ";
                
            }
        }
        
        
        $strSql = $strSelect.$strFrom.$strWhere.$strOrderBy;

        
        $objQuery = $this->_em->createQuery($strSql);
        
        if($intStart!='' && !$boolBusqueda)
        {

            $objQuery->setFirstResult($intStart); 
        }
        
        if($intLimit!='')
        {
        
            $objQuery->setMaxResults($intLimit);
        
        }
        
        $objDatos = $objQuery->getResult();
        
        return $objDatos;
    }

}
