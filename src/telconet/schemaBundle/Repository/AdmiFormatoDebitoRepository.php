<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiFormatoDebitoRepository extends EntityRepository
{
    
	public function findFormatosDebitoParaGrid($limit,$page,$start){	
		$query = $this->_em->createQuery("SELECT DISTINCT a
		FROM 
                schemaBundle:AdmiBancoTipoCuenta a, schemaBundle:AdmiFormatoDebito b
                WHERE a.id=b.bancoTipoCuentaId AND a.estado in ('Activo','Activo-debitos') order by a.feCreacion DESC"
                        );
		$datos = $query->getResult();
                //echo $query->getSQL();
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                $resultado['registros']=$datos;
                $resultado['total']=$total;
		return $resultado;
	}
	public function findFormatosDebitoPorEstadoPorIdPorEmpresa($id,$estado,$empresaCod){	
		$query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:AdmiFormatoDebito a
                WHERE a.estado='$estado' AND a.bancoTipoCuentaId=$id AND a.empresaCod=$empresaCod
                    order by a.posicion ASC"
                        );
		$datos = $query->getResult();
		return $datos;
	}        
	public function findValidacionesPorFormatoDebitoId($id){	
		$query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:AdmiValidacionFormato a
                WHERE a.formatoDebitoId=$id 
                    order by a.feCreacion DESC"
                        );
		//echo $query->getSQL(); die;
		$datos = $query->getResult();
		return $datos;
	} 		

    /**
     * Documentación para obtieneValorCaracteristicaAdicional
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0 06-09-2017 Función que devuelve el valor de la característica adicional
     */
    public function obtieneValorCaracteristicaAdicional($arrayParametros)
    {
        $intBancoTipoCuentaId = $arrayParametros["intBancoTipoCuentaId"];
        $strProceso           = $arrayParametros["strProceso"];
        $strCodEmpresa        = $arrayParametros["strCodEmpresa"];
        $strCaracteristica    = $arrayParametros["strCaracteristica"];
        $objResultSet         = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('VALOR', 'valor', 'string');
        $strQuery             = $this->_em->createNativeQuery(
                                               "SELECT DEB.VALOR \n"
                                             . "FROM ADMI_FORMATO_DEBITO_CARACT DEB\n"
                                             . "WHERE DEB.BANCO_TIPO_CUENTA_ID = ? \n"
                                             . "AND DEB.EMPRESA_COD            = ? \n"
                                             . "AND DEB.PROCESO                = ? \n"
                                             . "AND DEB.ESTADO                 = 'Activo'\n"
                                             . "AND DEB.CARACTERISTICA_ID      = \n"
                                             . "  (SELECT CAR.ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA CAR\n"
                                             . "  WHERE CAR.DESCRIPCION_CARACTERISTICA = ? \n"
                                             . "  AND CAR.ESTADO = 'Activo'\n"
                                             . "  AND CAR.TIPO = 'FINANCIERO'\n"
                                             . "  )",$objResultSet
                                );
        $strQuery->setParameter(1, $intBancoTipoCuentaId);
        $strQuery->setParameter(2, strval($strCodEmpresa));
        $strQuery->setParameter(3, $strProceso);
        $strQuery->setParameter(4, $strCaracteristica);
        $arrayRespuesta = $strQuery->getResult();
        return $arrayRespuesta;
    }

}
