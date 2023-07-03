<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoEspacioFisicoRepository extends EntityRepository
{
    /**
      * generarJsonElementoNodo
      *
      * Método que devuelve todos los registros en formato json de la informacion de espacio fisico de NODOS
      * 
      * @param $idNodo            
      *                                                                             
      * @return json con resultado
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 25-02-2015
      */       
    public function generarJsonEspacioFisico($idNodo)
    {
        $arr_encontrados = array();
        
        $resultado = $this->getElementoNodo($idNodo);             
                
        if ($resultado) 
        {                                       
            $total = count($resultado);
            
            foreach ($resultado as $data)
            {                               
                $arr_encontrados[]=array('id'                 =>$data['id'],
                                         'idTipoEspacio'      =>$data['idTipoEspacio'],                
                                         'nombreTipoEspacio'  =>$data['nombreTipoEspacio'],
                                         'largo'              =>$data['largo'],
                                         'ancho'              =>$data['ancho'],
                                         'alto'               =>$data['alto'],
                                         'valor'              =>$data['valor']                                        
                                        );            
            }

            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$total.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
    }
    
    /**
      * getElementoNodo
      *
      * Método que devuelve todos los registros en formato array de la informacion de espacio fisico de NODOS
      * 
      * @param $idNodo            
      *                                                                             
      * @return json con resultado
      *
      * @author Gabriela Mora <gmora@telconet.ec>
      * @version 1.1 11-10-2022 - Se añadió validación para no incluir espacios fisicos eliminados
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 25-02-2015
      */     
    public function getElementoNodo($idNodo)
    {
        $query = $this->_em->createQuery();
        $dql ="
                        SELECT 
                        espacio.id,
                        tipoEspacio.id as idTipoEspacio,
                        tipoEspacio.nombreTipoEspacio,
                        espacio.largo,
                        espacio.ancho,
                        espacio.alto,
                        espacio.valor                        
                        FROM                         
                        schemaBundle:InfoEspacioFisico espacio,
                        schemaBundle:AdmiTipoEspacio tipoEspacio                                        
                        WHERE    
                        espacio.tipoEspacioFisicoId   =  tipoEspacio.id AND
                        espacio.nodoId                =  :nodo          AND
                        espacio.estado                !=  'Eliminado'                                                         
                        ";
        
        $query->setParameter('nodo', $idNodo);
        
        $query->setDQL($dql);            
              
        $datos = $query->getResult();        
                        
        return $datos;
    }

    /**
      * getEspacioFisicoPendiente
      *
      * Método que devuelve todos los registros en formato array de la informacion de espacio fisico en estado PENDIENTE de un NODO
      * 
      * @param $intIdNodo            
      *                                                                             
      * @return array con resultado
      *
      * @author Gabriela Mora <gmora@telconet.ec>
      * @version 1.0 14-10-2022
      */ 
    public function getEspacioFisicoPendiente($intIdNodo)
    {
        $objQuery = $this->_em->createQuery();
        $strDql ="
                        SELECT 
                        espacio.id                       
                        FROM                         
                        schemaBundle:InfoEspacioFisico espacio                                       
                        WHERE
                        espacio.nodoId                =  :nodo          AND
                        espacio.estado                =  'Pendiente'                                                         
                        ";
        
        $objQuery->setParameter('nodo', $intIdNodo);
        
        $objQuery->setDQL($strDql);            
              
        $arrayDatos = $objQuery->getArrayResult();        
                        
        return $arrayDatos;
    }
}
