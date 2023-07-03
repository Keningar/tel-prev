<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class SistItemMenuRepository extends EntityRepository
{
    public function findXModulo($item_padre="", $item_menu_nombre="")
    {
	if($item_padre && $item_padre!="")
	{
	    $wherePadre = "WHERE sim.itemMenuId = '$item_padre' ";
	    $selected = "SELECT DISTINCT sim.nombreItemMenu, sim.urlImagen, sim.id, sim.titleHtml, sim.descripcionHtml ";
	}
	else
	{
	    $wherePadre = "WHERE sim.itemMenuId is null ";
	    $selected = "SELECT DISTINCT sim.nombreItemMenu, sim.urlImagen, sim.id, sim.titleHtml, sim.descripcionHtml  ";
	}
	if($item_menu_nombre && $item_menu_nombre!="")
	{
	    $whereMenu = "AND sim.nombreItemMenu = '$item_menu_nombre' ";
	}
	else
	{
	    $whereMenu = "AND sim.nombreItemMenu = 'Inicio' ";
	}

	$query =  "$selected ".
		  "FROM schemaBundle:SistItemMenu sim ".
		  "$wherePadre $whereMenu ".
                  "AND LOWER(sim.estado) != LOWER('Eliminado') ".
		  "";
	
	return $this->_em->createQuery($query)->getSingleResult(); 
    }
    
    public function findListarItemsMenu($item_padre="", $arrayItems="")
    {
	//FILTRO POR ITEMS MENU --- SOLO PERMITIDOS
	$whereVar = "";
	if($arrayItems && count($arrayItems)>0)
	{
            //Se agregan cambios para soportar mas de 1000 perfiles por usuario
	    $arrayItemsIn = array_chunk($arrayItems, 1000);
            $whereVar .= "AND ( ";
            for($i=0;$i<count($arrayItemsIn);$i++){
              $string_items_implode = "";
              $string_items = "";
              if ($i > 0){
                  $whereVar .= " or ";
              }  
              $string_items_implode = implode("', '", $arrayItemsIn[$i]);
              $string_items = "'".$string_items_implode."'";
              $whereVar .= " sim.id IN ($string_items) ";  
            }
            $whereVar .= " ) ";
	}

	if($item_padre && $item_padre!="")
	{
	    $wherePadre = "WHERE sim.itemMenuId = '$item_padre' ";
	    $selected = "SELECT DISTINCT sim.nombreItemMenu, sim.urlImagen, sim.id, sim.titleHtml, sim.descripcionHtml ,sim.posicion";
	}
	else
	{
	    $wherePadre = "WHERE sim.itemMenuId is null ";
	    $selected = "SELECT DISTINCT sim.id , sim.nombreItemMenu, sim.urlImagen , sim.posicion ";
	}

	$query =  "$selected ".
		  "FROM schemaBundle:SistItemMenu sim ".
		  "$wherePadre  $whereVar".
                  "AND LOWER(sim.estado) != LOWER('Eliminado') ".
		  "order by sim.posicion";
	
	return $this->_em->createQuery($query)->getResult();
    }

    public function findDescripcionItem($item_padre="", $item_menu_nombre="")
    {
	if($item_menu_nombre && $item_menu_nombre!="")
	{
	    $where = "WHERE  m.itemMenuId = '$item_padre' AND LOWER(m.nombreItemMenu) = LOWER('$item_menu_nombre') ";
	}
	else
	{
	    $where = "WHERE m.itemMenuId = '$item_padre' AND LOWER(m.nombreItemMenu) = LOWER('Inicio') ";
	}

	$query =  "SELECT m.nombreItemMenu, m.urlImagen, m.id, m.titleHtml, m.descripcionHtml ".
		  "FROM schemaBundle:SistItemMenu m ".
		  "$where ".
                  "AND LOWER(m.estado) != LOWER('Eliminado') ".
		  "order by m.posicion";
	
	return $this->_em->createQuery($query)->setMaxResults(1)->getOneOrNullResult();
    }
    
    public function generarJsonItemMenu($nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $itemsTotal = $this->getItemMenus($nombre, $estado,'','');
        $items = $this->getItemMenus($nombre,$estado,$start,$limit);
 
        if ($items) {
            
            $num = count($itemsTotal);
            
            foreach ($items as $item_menu)
            {
                $item_padre = $item_menu->getItemMenuId();
                
                $arr_encontrados[]=array('id_item_menu' =>$item_menu->getId(),
                                         'nombre_item_menu' =>trim($item_menu->getNombreItemMenu()),
                                         'nombre_html' =>trim($item_menu->getTitleHtml()),
                                         'item_menu_nombre' =>trim($item_padre?$item_padre->getNombreItemMenu():'N/A'),
                                         'estado' =>(trim($item_menu->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($item_menu->getEstado())=='Eliminado' ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (trim($item_menu->getEstado())=='Eliminado' ? 'icon-invisible':'button-grid-delete'));
            }
            
            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_item_menu' => 0 , 'nombre_item_menu' => 'Ninguno','item_menu_id' => 0 , 'item_menu_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    public function getItemMenus($nombre,$estado,$start,$limit){       
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:SistItemMenu','sim');
               
            
        if($nombre!=""){
            $qb ->where( 'LOWER(sim.nombreItemMenu) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        if($estado!="Todos"){
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere('LOWER(sim.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }
        
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        $qb->orderBy('sim.id');
        $query = $qb->getQuery();
        return $query->getResult();
    }


}
