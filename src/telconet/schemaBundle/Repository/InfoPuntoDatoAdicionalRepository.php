<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoPuntoDatoAdicionalRepository extends EntityRepository
{
    /**
     * Devuelve lo mismo que el magic method findOneByPuntoId($idPunto)
     * @param integer $idPunto
     * @return \telconet\schemaBundle\Entity\InfoPuntoDatoAdicional
     */
    public function findPorIdPunto($idPunto){	
        $query = $this->_em->createQuery("SELECT a
                FROM schemaBundle:InfoPuntoDatoAdicional a
                WHERE a.puntoId = :idPunto");
        $query->setParameter('idPunto', $idPunto);
        $datos = $query->getOneOrNullResult();
        return $datos;
    }
    
    /**
     * getMailTelefonoByPunto, metodo obtiene las formas de contacto MAIL y FONO enviando como parametro el ID del punto.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 06-10-2015
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 20-10-2015
     * @since 1.0
     * 
     * @param array $arrayParametrosIn[
     *                                 intIdPunto  => Se refiere al Id del punto,
     *                                 strTipoDato => Se refiere al tipo de dato que se quiera obtener MAIL O FONO]
     * @return array $arrayResponse[
     *                              strStatus   => Retorna el estatus del metodo 
     *                              ['000'  => 'No se realizó la consulta', 
     *                               '001'  => 'Devuelve cuando existio un error', 
     *                               '100'  => 'Consulta realizada con éxito' ]
     *                              strMensaje   => Retorna un mensaje,
     *                              strMailFono  => Retorna los correos o telefonos concatenados por ;
     *                              ]
     * Se agrega la inicialización de la variable $strDatoFormaContacto ya que se cae al momento de llamarlo desde el Controller
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 1.2 09-12-2016
     */
    public function getMailTelefonoByPunto($arrayParametrosIn)
    {
        $arrayResponse['strMensaje']  = '';
        $arrayResponse['strStatus']   = '000';
        $strDatoFormaContacto         = '';
        $arrayResponse['strMailFono'] = '';
        try
        {
            $strDatoFormaContacto = str_pad($strDatoFormaContacto, 5000, " ");
            $strSql               = "BEGIN :strMailFono := DB_FINANCIERO.FNCK_COM_ELECTRONICO.GET_ADITIONAL_DATA_BYPUNTO(:intIdPunto, :strTipoDato); END;";
            $stmt                 = $this->_em->getConnection()->prepare($strSql);
            $stmt->bindParam('intIdPunto',  $arrayParametrosIn['intIdPunto']);
            $stmt->bindParam('strTipoDato', $arrayParametrosIn['strTipoDato']);
            $stmt->bindParam('strMailFono',  $strDatoFormaContacto);
            $stmt->execute();
            $arrayDatosFormaContacto      = explode(";", $strDatoFormaContacto);
            $arrayUniqueMailFono          = array_unique($arrayDatosFormaContacto);
            $arrayResponse['strMailFono'] = implode(";", $arrayUniqueMailFono);
            $arrayResponse['strMensaje']  = 'Consulta realizada con éxito';
            $arrayResponse['strStatus']   = '100';
        }
        catch(\Exception $ex)
        {
            $arrayResponse['strMensaje'] = 'Error ' . $ex->getMessage();
            $arrayResponse['strStatus']  = '001';
        }
        return $arrayResponse;
    }//getMailTelefonoByPunto
    
    /**
    * Esta función permite validar si el punto o el cliente tiene contactos de facturación.
    * 
    * @author Hector Ortega <haortega@telconet.ec>
    * @version 1.6 19-12-2016
    * 
    * @param array $arrayParametros[ strEmpresaCod  => codigo de la empresa a la que se le va a crear la factura,
    *                                 cliente => Cliente a que se le va a crear la factura]
    * @return array $arrayParametros[
    *                                 boolPuedeFacturar => bandera que indica si puede facturar o no
    *                                 booleanPresentarMensajeValidacion => bandera que indica si debe presentar o no el mensaje de validacion;
    *                                 strMensajeValidacion => mensaje que indica porque no se puede generar la factura para el cliente 
    *                              ]
    *
    *
    */
    public function validaContactoFacturacion($arrayParametros)
    {

        if (empty($arrayParametros["strEmpresaCod"]) || empty($arrayParametros["cliente"]["nombre_rol"])) {
             $arrayParametros["boolPuedeFacturar"]                 = false;
             $arrayParametros["booleanPresentarMensajeValidacion"] = true;
             $arrayParametros["strMensajeValidacion"]              = "Se debe especificar el rol del cliente y la empresa";
             return $arrayParametros;
        }
        
        if (!empty($arrayParametros) && isset($arrayParametros["strEmpresaCod"]) 
            && isset($arrayParametros["cliente"]) && isset($arrayParametros["punto_id"]) 
            && !empty($arrayParametros["strEmpresaCod"])&& !empty($arrayParametros["cliente"]["nombre_rol"]) )
        {
            $strEmpresaCod   = $arrayParametros["strEmpresaCod"];
            $arrayCliente    = $arrayParametros['cliente'];
            $arrayPtoCliente = $arrayParametros['punto_id'];
            //Consulta en la tabla detalle parametros validar el rol para la empresa
            // segun la empresa
            $arrayValidacionRol = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne("VALIDACION_ROL_FACTURACION", "FINANCIERO", "", "", 
                                                     "VALIDACION_ROL_FACTURACION", "", "", "", "", $strEmpresaCod);

            //se verifica si la validación esta activa para la empresa
            if(!empty($arrayValidacionRol) && isset($arrayValidacionRol["valor2"] ) && $arrayValidacionRol["valor2"] === 'S')
            {
                $arrayRolNoPermitido = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne("ROLES_NO_PERMITIDOS_A_FACTURAR", "FINANCIERO", "", "", 
                                                          "ROLES_NO_PERMITIDOS", strtolower($arrayCliente["nombre_rol"]), "", "S", 
                                                          "", $strEmpresaCod);

                //se verifica si se esta intentando crear una factura para un rol no permitido
                if(!empty($arrayRolNoPermitido) && isset($arrayRolNoPermitido["valor4"] ) && $arrayRolNoPermitido["valor4"] === 'S')
                {
                    $arrayParametros["boolPuedeFacturar"]                 = false;
                    $arrayParametros["booleanPresentarMensajeValidacion"] = true;
                    $arrayParametros["strMensajeValidacion"]              = $arrayRolNoPermitido["valor3"];
                }
                else
                {
                    //se verifica si esta activa la verificación del contato de facturacion para la empresa
                    $arrayVerificacionContacto = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                           ->getOne("VERIFICACION_CONTACTO_FACTURACION", "FINANCIERO", "", "", 
                                                                    "VERIFICACION_CONTACTO_FACTURACION", "", "", "", "", $strEmpresaCod);

                    if(!empty($arrayVerificacionContacto) && isset($arrayVerificacionContacto["valor2"] ) && $arrayVerificacionContacto["valor2"] === 'S')
                    {

                        $arrayParametroGetCorreo                = array();
                        $arrayParametroGetCorreo['intIdPunto']  = $arrayPtoCliente['id'];
                        $arrayParametroGetCorreo['strTipoDato'] = $arrayVerificacionContacto['valor4'];

                        //se verifica si el punto tiene contacto de facturación
                        $arrayMail = $this->getMailTelefonoByPunto($arrayParametroGetCorreo);

                        if ($arrayMail['strMailFono'] === '')
                        {
                            $arrayParametros["boolPuedeFacturar"]                 = false;
                            $arrayParametros["booleanPresentarMensajeValidacion"] = true;
                            $arrayParametros["strMensajeValidacion"]              = $arrayVerificacionContacto["valor3"];
                        }
                    }
                }
            }
        }

     return $arrayParametros;
    }//validaContactoFacturacion
    
    /**
    * Esta función permite obtener un punto que este habilitado para facturación a traves del id_empleado.
    * 
    * coste
    * @author David Leon <mdleon@telconet.ec>
    * @version 1.1 13-05-2020
    * 
    * @param array $arrayParametros[ intEmpleadoId  => id del empleado a consultar]
    * @return $arrayResultado  => datos del punto
    *
    */
    public function getPuntoByPersona($arrayParametros)
    {
        $intEmpleadoId    = $arrayParametros['intEmpleadoId'];
        $strEstado        = $arrayParametros['estado'];
        $intEmpresaCod    = $arrayParametros['codEmpresa'];  
        $objQuery = $this->_em->createQuery("SELECT ipu
                FROM 
                        schemaBundle:InfoPersona ip,schemaBundle:InfoPersonaEmpresaRol iper, 
                        schemaBundle:InfoPunto ipu, schemaBundle:InfoPuntoDatoAdicional ipfa,
                        schemaBundle:InfoEmpresaRol ier, schemaBundle:AdmiRol ar
                WHERE    
                        ip.id=iper.personaId AND
                        iper.id=ipu.personaEmpresaRolId AND
                        iper.empresaRolId=ier.id AND
                        ier.empresaCod=:empresaCod AND
                        ier.rolId=ar.id AND
                        ar.descripcionRol=:strRol AND
                        ipu.estado=:estado AND
                        ipu.id=ipfa.puntoId AND
                        ipfa.esPadreFacturacion=:padreFac AND
                        ip.id= :idPersona");
        $objQuery->setParameter('idPersona', $intEmpleadoId);
        $objQuery->setParameter('estado', $strEstado);
        $objQuery->setParameter('padreFac','S');
        $objQuery->setParameter('empresaCod',$intEmpresaCod);
        $objQuery->setParameter('strRol','Cliente');
        $intTotal = count($objQuery->getResult()); // TODO: usar count SQL
        $objDatos = $objQuery->setFirstResult($start)->setMaxResults($limit)->getResult();
        $arrayResultado = $objDatos;
        return $arrayResultado;
    }
    
}
