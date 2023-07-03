<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiCuentaContableRepository extends EntityRepository
{
	
    
    
    /**
    * Documentación para el método 'getResultadoNumeroCuentasBancosContables'.
    * Esta funcion obtiene las cuentas contables de los bancos de una empresa
    *
    * @param  Integer $empresaCod    Obtiene el Id de la empresa
    * @param  Integer $tipo     Obitne el Id del tipo de cuenta contable
    * @return Integer $out_Resultado Retorna arreglo con la informacion de los bancos
    * @author Andres Montero <amontero@telconet.ec>
    * @version 1.0 26-12-2015
    * costoQuery =7
    */    
    public function getResultadoNumeroCuentasBancosContables($strTipo,$strEmpresaCod,$strConsultaPara='PAGOS')
    {   
       $objQuery     = $this->_em->createQuery();
       $strCriterios = "";
       
       $strDqlCount="SELECT count(cc.id) ";

        $strDql="SELECT cc.id,cc.descripcion, cc.noCta ";
        
        $strCuerpo=" FROM  
            schemaBundle:AdmiCuentaContable cc,schemaBundle:AdmiTipoCuentaContable tcc
            WHERE 
            cc.tipoCuentaContableId=tcc.id
            AND tcc.descripcion=:tipo 
            AND cc.estado=:estado 
            AND cc.empresaCod=:empresaCod ";
        
        //EN EL CASO DE PAGOS SE MUESTRA SOLO LOS BANCOS
        //EN EL CASO DE DEPOSITOS SE MUESTRA TODOS
        if($strConsultaPara=='PAGOS')
        {
            $strCriterios.=" AND cc.descripcion <> :descripcion ";            
            $objQuery->setParameter('descripcion','OTROS');
        }    
        
        $strCriterios.=" ORDER BY  cc.descripcion ASC";
        
        $strCuerpo.=$strCriterios;

        $strDqlCount.=$strCuerpo;
        $strDql.= $strCuerpo;
        
        $objQuery->setParameter('tipo',$strTipo);
        $objQuery->setParameter('estado','Activo');
        $objQuery->setParameter('empresaCod',$strEmpresaCod);        
        $objQuery->setDQL($strDql);
        
        $arrayDatos= $objQuery->getResult();
        
        if ($arrayDatos)
        {
            $objQuery->setDQL($strDqlCount);
            $intTotal= $objQuery->getSingleScalarResult();
        }    
        else
        {
            $intTotal=0;
        }
        
        $resultado['registros']= $arrayDatos;
        $resultado['total']    = $intTotal;
            
        return $resultado;
    }	
    
    
    /**
     * Documentación para el método 'getJSONListadoCuentasBancariasEmpresa'.
     * Funcion que retorna las cuentas bancarias de la empresa en sesion'     
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.0
     * @since 07-01-2016  
     * @return objeto json
     */      
    public function getJSONListadoCuentasBancariasEmpresa($strTipo,$strEmpresaCod,$strConsultaPara='PAGOS')
    {   
        $arrayResultado = $this->getResultadoNumeroCuentasBancosContables($strTipo,$strEmpresaCod,$strConsultaPara);
        $arrayCuentas=$arrayResultado['registros'];
        if($arrayCuentas && count($arrayCuentas)>0)
        {
            $intNum = count($arrayCuentas);
            
            $arrayEncontrados[]=array('id_cuenta' =>0, 'descripcion_cuenta' =>"Seleccion una cuenta");
            foreach($arrayCuentas as $key => $arrayCuenta)
            {                
                $arrayEncontrados[]=array('id_cuenta'   => $arrayCuenta["id"],
                                         'descripcion_cuenta' => trim($arrayCuenta["descripcion"]."   Cta.".$arrayCuenta["noCta"]));
            }

            if($intNum == 0)
            {
                $arrayResultado= array('total' => 1 ,
                                 'encontrados' => array('id_cuenta' => 0 , 'descripcion_cuenta' => 'Ninguno')
                );
                $objJson = json_encode( $arrayResultado);
            }
            else
            {
                $arrayResultado=array('total'=>$intNum,'encontrados'=>$arrayEncontrados);
                $objJson=json_encode($arrayResultado);
            }
        }
        else
        {
            $arrayResultado=array('total'=>0,'encontrados'=>array());            
            $objJson=json_encode($arrayResultado);
        }
        
        return $objJson;
    }
    
    /**
     * Documentación para el método 'getCuentasBancariasEmpresaParametros'.
     * Funcion que retorna las cuentas bancarias de la empresa en sesion'     
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0
     * @since 27-08-2020  
     * @return objeto json
     */      
    public function getCuentasBancariasEmpresaParametros($arrayParametros)
    { 
        //$strTipo,$strEmpresaCod,$strConsultaPara='PAGOS'
        $arrayResultado = $this->getResultadoNumeroCuentasBancosContables($strTipo,$strEmpresaCod,$strConsultaPara);
        $arrayCuentas=$arrayResultado['registros'];
        if($arrayCuentas && count($arrayCuentas)>0)
        {
            $intNum = count($arrayCuentas);
            
            $arrayEncontrados[]=array('id_cuenta' =>0, 'descripcion_cuenta' =>"Seleccion una cuenta");
            foreach($arrayCuentas as $key => $arrayCuenta)
            {                
                $arrayEncontrados[]=array('id_cuenta'   => $arrayCuenta["id"],
                                         'descripcion_cuenta' => trim($arrayCuenta["descripcion"]."   Cta.".$arrayCuenta["noCta"]));
            }

            if($intNum == 0)
            {
                $arrayResultado= array('total' => 1 ,
                                 'encontrados' => array('id_cuenta' => 0 , 'descripcion_cuenta' => 'Ninguno')
                );
                $objJson = json_encode( $arrayResultado);
            }
            else
            {
                $arrayResultado=array('total'=>$intNum,'encontrados'=>$arrayEncontrados);
                $objJson=json_encode($arrayResultado);
            }
        }
        else
        {
            $arrayResultado=array('total'=>0,'encontrados'=>array());            
            $objJson=json_encode($arrayResultado);
        }
        
        return $objJson;
    }    
}