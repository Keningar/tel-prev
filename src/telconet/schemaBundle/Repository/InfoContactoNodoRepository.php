<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoContactoNodoRepository extends EntityRepository
{
    /**
      * generarJsonContactoNodo
      *
      * Método que devuelve todos los registros en formato json de la informacion de contactos de NODOS
      * 
      * @param $idNodo            
      *                                                                             
      * @return json con resultado
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 25-02-2015
      * 
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.1 01-08-2016 - Se envia nombres y apellidos por separado para efecto de edicion de los mismos en nueva opcion,
      *                           Se devuelve el id del cada rol
      */       
    public function generarJsonContactoNodo($idNodo)
    {
        $arr_encontrados = array();
        
        $resultado = $this->getContactoNodo($idNodo);             
                
        if ($resultado) 
        {                                       
            $total = count($resultado);
            
            foreach ($resultado as $data)
            {                               
                $nombres   = $data['nombres']?$data['nombres']:"";
                $apellidos = $data['apellidos']?$data['apellidos']:"";
                
                $arr_encontrados[]=array('idRol'                  =>$data['idRol'],
                                         'descripcionRol'         =>$data['descripcionRol'],
                                         'tipoIdentificacion'     =>$data['tipoIdentificacion'],
                                         'identificacionCliente'  =>$data['identificacionCliente'],
                                         'idPersona'              =>$data['id'],
                                         'nombres'                =>$nombres,
                                         'apellidos'              =>$apellidos,
                                         'razonSocial'            =>$data['razonSocial'],
                                         'genero'                 =>$data['genero']=='M'?'Masculino':'Femenino',
                                         'tipoTributario'         =>$data['tipoTributario'],
                                         'nombres'                =>$nombres,
                                         'apellidos'              =>$apellidos
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
      * getContactoNodo
      *
      * Método que devuelve todos los registros en formato array de la informacion de contactos de NODOS
      * 
      * @param $idNodo            
      *                                                                             
      * @return json con resultado
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 25-02-2015
      * 
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.1 02-08-2016 - Se devuelve el id del rol en la consulta y se coloca estado como condicional a la consulta
      */     
    public function getContactoNodo($idNodo)
    {
        $query = $this->_em->createQuery();
        $dql ="
                        SELECT 
                        rol.id idRol,
                        rol.descripcionRol,
                        persona.id,
                        persona.tipoIdentificacion,
                        persona.identificacionCliente,
                        persona.nombres,
                        persona.apellidos,
                        persona.razonSocial,
                        persona.genero,
                        persona.tipoTributario
                        FROM                         
                        schemaBundle:InfoContactoNodo contacto,
                        schemaBundle:InfoPersona persona,
                        schemaBundle:InfoPersonaEmpresaRol personaRol,
                        schemaBundle:InfoEmpresaRol empresaRol,
                        schemaBundle:AdmiRol rol,
                        schemaBundle:AdmiTipoRol tipoRol
                        WHERE    
                        contacto.personaId        =  persona.id and
                        persona.id                =  personaRol.personaId and
                        personaRol.empresaRolId   =  empresaRol.id and
                        empresaRol.rolId          =  rol.id and
                        rol.tipoRolId             =  tipoRol.id and
                        tipoRol.descripcionTipoRol=  :tipoRol  and
                        contacto.nodoId           =  :nodo and
                        personaRol.estado         =  :estado
                        ";
        
        $query->setParameter('tipoRol', 'Contacto Nodo');
        $query->setParameter('nodo', $idNodo);
        $query->setParameter('estado', 'Activo');
        
        $query->setDQL($dql);            
              
        $datos = $query->getResult();        
                        
        return $datos;
    }
    
    /**
      * getContactoPrincipalNodo
      *
      * Método que devuelve la razon social del principal titular responsable del nodo solicitado
      * 
      * @param $idElemento    
      *                                                                             
      * @return array con resultado
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 17-03-2015
      */     
    public function getContactoPrincipalNodo($idElemento)
    {                
        $query = $this->_em->createQuery();
        $dql ="
                        SELECT 
                        persona                      
                        FROM                                
                        schemaBundle:InfoContactoNodo contacto,
                        schemaBundle:InfoPersona persona                                            
                        WHERE               
                        contacto.personaId  =  persona.id and                        
                        contacto.nodoId     =  :nodo and
                        contacto.id         = (select min(a.id) from schemaBundle:InfoContactoNodo a
                                               where a.nodoId = :nodo)                        
                        ";
        
        $query->setParameter('nodo', $idElemento);

        $query->setDQL($dql);

        $datos = $query->getResult();

        return $datos;
    }
}
