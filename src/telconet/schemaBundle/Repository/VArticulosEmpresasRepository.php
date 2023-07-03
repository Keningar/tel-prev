<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class VArticulosEmpresasRepository extends EntityRepository
{
    public function getArticulosbyEmpresa($id_empresa)
    {   
        $query =    "SELECT a ".
                    "FROM schemaBundle:VArticulosEmpresas a ".
                    "WHERE a.noCia = '$id_empresa' ".
                    "ORDER BY a.descripcion ASC";

        return $this->_em->createQuery($query)->getResult();
    }
   
    public function getOneArticulobyEmpresabyCodigo($id_empresa, $id_articulo)
    {   
        $sql =  "SELECT a ".
                "FROM schemaBundle:VArticulosEmpresas a ".
                "WHERE a.noCia = '$id_empresa' AND a.id = '$id_articulo' ";

        $query = $this->_em->createQuery($sql)->setMaxResults(1);
        $datos = $query->getOneOrNullResult();
        return $datos;
    }
    
    /**
     * Documentación para el método 'getOneArticulobyCodigo'.
     *
     * Obtiene Articulo del NAF por articulo
     * @param $id_articulo identificador de articulo
     * @return object
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 07-11-2014
     */
    public function getOneArticulobyCodigo($id_articulo)
    {   
        $sql =  "SELECT a ".
                "FROM schemaBundle:VArticulosEmpresas a ".
                "WHERE a.id = :articulo";

        $query = $this->_em->createQuery($sql)->setMaxResults(1);
        $query->setParameter("articulo", $id_articulo);
        $datos = $query->getOneOrNullResult();
        return $datos;
    }
}