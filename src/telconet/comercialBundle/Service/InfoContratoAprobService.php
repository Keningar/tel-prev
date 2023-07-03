<?php
namespace telconet\comercialBundle\Service;

use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\InfoContrato;
use telconet\schemaBundle\Entity\AdmiNumeracion;
use telconet\schemaBundle\Entity\InfoContratoDatoAdicional;
use telconet\schemaBundle\Entity\InfoContratoClausula;
use telconet\schemaBundle\Entity\InfoContratoFormaPago;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaReferido;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoEmpresaRol;
use telconet\schemaBundle\Entity\AdmiFormaPago;
use telconet\schemaBundle\Entity\AdmiTipoCuenta;
use telconet\schemaBundle\Entity\AdmiBancoTipoCuenta;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoOrdenTrabajo;
use telconet\schemaBundle\Entity\InfoPersonaRepresentante;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoPunto;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\InfoPuntoCaracteristica;
use telconet\schemaBundle\Entity\InfoPuntoDatoAdicional;
use telconet\schemaBundle\Entity\InfoPersonaContacto;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoContratoCaracteristica;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\schemaBundle\Service\UtilService;
use Symfony\Component\Console\Output\OutputInterface;
use telconet\schemaBundle\Entity\InfoContratoFormaPagoHist;
use telconet\schemaBundle\Entity\InfoPuntoHistorial;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\InfoServicioComision;
use telconet\schemaBundle\Entity\InfoServicioComisionHisto;
use telconet\comercialBundle\Service\ComercialService;


class InfoContratoAprobService {

    private $container;
    private $serviceEnvioPlantilla;
    private $serviceNotaCredito;
    private $servicePreCliente;
    private $serviceCrypt;    
    private $serviceInfoContrato;    
    private $emcom;
    private $serviceInfoPersonaFormaContacto;
    private $emInfraestructura;
    private $emGeneral;
    private $servicePersona;
    private $servicePersonaFormaContacto;
    private $serviceServicioTecnico;
    private $serviceInfoServicio;
    private $serviceUtil;
    private $emFinanciero;
    private $serviceInfoPunto;
    private $serviceComercial;
    private $servicePlanificar;
    private $serviceLicenciasOffice365;
    private $templating;
    private $serviceInternetProtegido;
    private $serviceLicenciasKaspersky;
    private $serviceFoxPremium;


    
    /** 
    * Documentación para el método 'setDependencies'.
    * @param \Symfony\Component\DependencyInjection\ContainerInterface $container 
    * @author  telcos
    * @version 1.0 
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.1 08-11-2016 Se agrega uso de entity manager financiero.
    * @author Edson Franco <efranco@telconet.ec>
    * @version 1.2 19-12-2016 - Se agregan las variables 'serviceEnvioPlantilla', 'serviceNotaCredito' y 'container'
    * @author Edson Franco <efranco@telconet.ec>
    * @version 1.3 06-03-2017 - Se agrega la variable 'serviceInfoServicio'
    * @author Andres Montero <amontero@telconet.ec>
    * @version 1.4 20-03-2017 - Se agregan las variables 'serviceUtil'
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.5 20-06-2017 - Se agrega la variable 'serviceInfoPunto'
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.6 17/04/2020 - Se agrega variable 'templating' para el envío de correo y 'serviceUtil' para el envio de errores a info_log
    */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emcom                           = $container->get('doctrine.orm.telconet_entity_manager');        
        $this->serviceCrypt                    = $container->get('seguridad.Crypt');     
        $this->serviceInfoContrato             = $container->get('comercial.InfoContrato');     
        $this->servicePreCliente               = $container->get('comercial.PreCliente');   
        $this->serviceInfoPersonaFormaContacto = $container->get('comercial.InfoPersonaFormaContacto'); 
        $this->emInfraestructura               = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->emGeneral                       = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->emFinanciero                    = $container->get('doctrine.orm.telconet_financiero_entity_manager');
        //...
        $this->servicePersona                  = $container->get('comercial.InfoPersona');   
        $this->servicePersonaFormaContacto     = $container->get('comercial.InfoPersonaFormaContacto');   
        $this->serviceServicioTecnico          = $container->get('tecnico.InfoServicioTecnico'); 
        $this->serviceUtil                     = $container->get('schema.Util');
        $this->serviceEnvioPlantilla           = $container->get('soporte.EnvioPlantilla');
        $this->serviceNotaCredito              = $container->get('financiero.InfoNotaCredito');
        $this->container                       = $container;
        $this->serviceInfoServicio             = $container->get('comercial.InfoServicio');
        $this->serviceInfoPunto                = $container->get('comercial.InfoPunto');
        $this->serviceComercial                = $container->get('comercial.Comercial');
        $this->servicePlanificar               = $container->get('planificacion.Planificar');
        $this->serviceLicenciasOffice365       = $container->get('tecnico.LicenciasOffice365');
        $this->templating                      = $container->get('templating');  
        $this->serviceInternetProtegido        = $container->get('tecnico.InternetProtegido');
        $this->serviceLicenciasKaspersky       = $container->get('tecnico.LicenciasKaspersky');
        $this->serviceFoxPremium               = $container->get("tecnico.FoxPremium");
    }
    
    /**
     * Documentación para el método 'guardarDocumentoEntregable'.
     *
     * Método que guarda los documentos entregables seleccionados desde la vista.
     * 
     * @param String $strJsonEntregables Cadena Json con el listado de documentos entregables del contrato.
     * @param String $strUsrCreacion     Login del usuario en sesión.
     *
     * @return String Respuesta de ejecución del guardado de los entregables.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 24-03-2016
     */
    public function guardarDocumentoEntregable($strJsonEntregables, $strUsrCreacion) 
    {
        
        $strEntregables  = 'DOCUMENTOS_ENTREGABLES_CONTRATO';
        $objEntityCaract = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                      ->getCaracteristicaPorDescripcionPorEstado($strEntregables, 'Activo');
        if(!is_object($objEntityCaract))
        {
            throw new \Exception("No se ha definido la característica $strEntregables");
        }
        
        $objICCRepository = $this->emcom->getRepository('schemaBundle:InfoContratoCaracteristica');
        $arrayEntregables = json_decode($strJsonEntregables);// Se decodifica la cadena Json de los documentos entregables entregados o no.
        
        $this->emcom->getConnection()->beginTransaction();
        $arrayResultado['ESTADO'] = 'OK';
        try
        {
            foreach($arrayEntregables as $objEntregable)
            {
                $intIdContrato  = $objEntregable->{'idContrato'};
                $strDocumento   = $objEntregable->{'codEntregable'};
                $booleanEntrego = $objEntregable->{'valEntregable'};
                $intIdCarac     = $objEntityCaract->getId();
                $objContrato    = $this->emcom->getRepository('schemaBundle:InfoContrato')->find($intIdContrato);

                if(is_object($objContrato))
                {
                    $entityEntregable = $objICCRepository->getResultadoDocumentoEntregableContrato($intIdContrato, $intIdCarac, $strDocumento);

                    if(!is_object($entityEntregable))
                    {
                        $entityEntregable = new InfoContratoCaracteristica();
                        $entityEntregable->setCaracteristicaId($objEntityCaract);
                        $entityEntregable->setContratoId($objContrato);
                        $entityEntregable->setEstado('Activo');
                        $entityEntregable->setFeCreacion(new \DateTime('now'));
                        $entityEntregable->setUsrCreacion($strUsrCreacion);
                        $entityEntregable->setIpCreacion('127.0.0.1');
                        $entityEntregable->setValor1($strDocumento);
                    }
                    else
                    {
                        $entityEntregable->setFeUltMod(new \DateTime('now'));
                        $entityEntregable->setUsrUltMod($strUsrCreacion);
                    }

                    $entityEntregable->setValor2($booleanEntrego ? 'S' : 'N');
                    $this->emcom->persist($entityEntregable);
                }
                else
                {
                    throw new \Exception("No se encontró el contrato.");
                }
            }
            
            $this->emcom->flush();
            $this->emcom->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
            }
            $this->emcom->getConnection()->close();
            error_log('DOCUMENTOS_ENTREGABLES_ERROR: ' . $e->getMessage());
            $arrayResultado['ESTADO'] = 'ERROR';
            $arrayResultado['ERROR']  = "No se pudieron guardar los documentos entregables, por favor verificar con el departamento de sistemas";
        }

        return $arrayResultado;
    }
    
    /**
     * Documentación para el método 'listarContratosPorCriterios'
     * 
     * Saca información de los contratos por empresa según criterios
     * 
     * @param Array $arrayParams['estado']     String  Estado del contrato.
     *                          ['idEmpresa']  String  Código empresa.
     *                          ['fechaDesde'] String  Fecha mínima de ingreso del contrato.
     *                          ['fechaHasta'] String  Fecha máxima de ingreso del contrato.
     *                          ['idper']      Integer IdPersonaEmpresaRol
     *                          ['oficinaId']  Integer Id de la oficina.
     *                          ['nombre']     String  Nombre del cliente.
     *                          ['intLimit']   Integer Indice final de la paginación
     *                          ['page']       Integer Número de página a obtener
     *                          ['intStart']   Integer Indice incial de la paginación
     *                          ['origen']     String  Origen del contrato: WEB o MOVIL.
     *                          ['documento']  String  Situación de recepción de documentos del contrato (Pendientes/Entregados)
     * 
     * @return $arrayResultado['total']     Integer Cantitad de registros obtenidos.
    *                         ['registros'] Array   Listado de Contratos.
    *                         ['error']     String  Mensaje de error.
     * 
     * @author apenaherrera
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 01-02-2016
     * @since 1.0
     * Se condensan los parámetros en el arreglo $arrayParams
     * Se agrega el parámetro "$strOrigen"    que define el origen del contrato físico(WEB) o digital(MOVIL).
     * Se agrega el parámetro "$strDocumento" indica los contratos con documentos pendiente de recepción o entregados en su totalidad
     */
    public function listarContratosPorCriterios($arrayParams)
    {
        return  $this->emcom->getRepository('schemaBundle:InfoContrato')
                            ->findContratosPorCriteriosConServFact($arrayParams);
    }

    /**
 * Saca los datos de la Persona Empresa Rol
 *  @param integer $id_persona_empresa_rol
 */
function getDatosPersonaEmpresaRolId($id_persona_empresa_rol){
	
     $persona_empresa_rol=$this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($id_persona_empresa_rol);
     return $persona_empresa_rol;
  }			

/**
 * Saca los datos de la Persona 
 *  @param integer $id_persona
 */
function getDatosPersonaId($id_persona){
    
     $persona=$this->emcom->getRepository('schemaBundle:InfoPersona')->find($id_persona);
      return $persona;
}			
/**
 * Saca los datos del Contrato 
 *  @param integer $id_contrato
 */
function getDatosContratoId($id_contrato){
    
     $contrato=$this->emcom->getRepository('schemaBundle:InfoContrato')->find($id_contrato);
      return $contrato;
}			
/**
 * Funcion que saca los datos de la forma de pago por id de contrato y por estado
 * @param integer $id_contrato
  */
function getDatosContratoFormaPagoId($id_contrato){
         
     $formFormaPago = $this->emcom->getRepository('schemaBundle:InfoContratoFormaPago')->findPorContratoId($id_contrato);            
      return $formFormaPago;
}

/**
 * Funcion que saca todos los servicios X Estado "Factible" de todos los puntos clientes
 * que tenga una persona_empresa_rol_id
 * @param integer $id_per_emp_rol 
 * @param string $start
 * @param string $limit
 * @param string $estado
 */
function getTodosServiciosXEstado($id_per_emp_rol,$start,$limit,$estado){
 
        $resultado= $this->emcom->getRepository('schemaBundle:InfoServicio')->findTodosServiciosXEstado($id_per_emp_rol,$start,$limit,$estado);
        return $resultado;
}
/**
 * Funcion que saca todos los servicios X Estado para TN de todos los puntos clientes
 * que tenga una persona_empresa_rol_id
 * @param integer $id_per_emp_rol 
 * @param string $start
 * @param string $limit
 * @param string $estado
 */
function getTodosServiciosXEstadoTn($id_per_emp_rol,$start,$limit,$estado){
 
        $resultado= $this->emcom->getRepository('schemaBundle:InfoServicio')->findTodosServiciosXEstadoTn($id_per_emp_rol,$start,$limit,$estado);
        return $resultado;
}

/**
 * Saca Datos de la Empresa Rol
 *  @param integer $id_empresa_rol
 */
function getDatosEmpresaRolId($id_empresa_rol){
    
     $empresa_rol=$this->emcom->getRepository('schemaBundle:InfoEmpresaRol')->find($id_empresa_rol);
     return $empresa_rol;
}
/**
 * Saca Datos de la Empresa Rol
 *  @param integer $id_empresa_rol
 */
function getClientesPorIdentificacion($identificacion,$empresaCod){
    
     $cliente = $this->emcom->getRepository('schemaBundle:InfoPersona')->findClientesPorIdentificacion($identificacion,$empresaCod);     
     return $cliente;
}
/**
 * Funcion que saca el servicio
 * @param integer $id_servicio
  */
function getDatosServicioId($id_servicio){
         
     $entityInfoServicio=$this->emcom->getRepository('schemaBundle:InfoServicio')->find($id_servicio);
     return $entityInfoServicio;
}
/**
 * Funcion que saca el servicio
 * @param integer $id_servicio
 * @param string  $estado
  */
function getSolicitudPrePlanifId($id_servicio,$estado){
         
     $solicitudPrePlanificacion = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneBy(array("servicioId"=>$id_servicio, "estado"=>$estado));			
     return $solicitudPrePlanificacion;
}	
/**
 * Funcion que saca el servicio
 * @param string $usrVendedor
 * @param string  $correo
  */
function getContactosByLoginPersonaAndFormaContacto($usrVendedor,$correo){
         
     $formasContacto = $this->emcom->getRepository('schemaBundle:InfoPersona')->getContactosByLoginPersonaAndFormaContacto($usrVendedor,$correo);
     return $formasContacto;
}

    /**
     *  Funcion que guarda el proceso de aprobacion de contrato
     * Guarda informacion del prospecto, convierte Prospecto a Cliente
     * Genera Orden de Trabajo de los servicios Factibles que pasarán al proceso de Planificacion
     * Actualiza la informacion de la forma de Pago : numero de cuenta o tarjeta , anio y mes de vencimiento de la tarjeta etc.
     * Actualiza estado de contrato a Aprobado  
     * Se llama a service que realiza la encriptacion del numero de cuenta tarjeta
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.1 modificado 02-12-2014    
     * @version 1.2 modificado 13-02-2015             
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.3 modificado 22/03/2016
     * 
     * Se agrega Control para TN que solo los Productos marcados que requieran_planificacion pasen a estado PrePlanificada
     * y generen Solicitud de planificacion a PyL
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.4 modificado 29-06-2016
     * 
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.5 modificado 07-09-2016
     * Si Producto no requiere flujo. Se realiza Activación automática en la Aprobación del Contrato.
     * 
     * 
     * Se ha movido la obtencion de la forma de pago fuera de un bloque if, 
     * con lo que estara disponible en el resto del método
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.6 modificado 04-09-2016
     * 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.7 15-09-2016 - Se corrige que retorne la variable $arrayData cuando exista una excepción al procesar la aprobación de contrato.
     *                           Adicional se valida que exista la forma de pago del contrato mediante la variable $arrayParametros['intIdFormaPago']
     * 
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.8 modificado 09-11-2016
     * Se agrega creación de característica para clientes que deben ser compensados previa verificación (aplica sólo para TN).
     * 
     * @param array $arrayParametros    - intIdContrato
     *                                  - arrayPersona
     *                                  - arrayPersonaExtra            
     *                                  - arrayFormaContacto
     *                                  - arrayFormaPago
     *                                  - intIdFormaPago
     *                                  - intIdTipoCuenta
     *                                  - arrayServicios
     *                                  - strUsrCreacion
     *                                  - strIpCreacion
     *                                  - strPrefijoEmpresa
     *                                  - strEmpresaCod
     * 
     * @param array $arrayData
     * 
     *  
     * Actualización: Se agrega parametro recibido strOrigen y 
     * se actualiza el campo origen del contrato con el nuevo parametro recibido 
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.9 09-02-2017
     * 
     * Actualización: 
     * - Se corrige mensajes de error que retorna al usuario, eliminando informacion sensible
     * - Se envia ip y usuario creacion al metodo editarPersona
     * - Se corrige en el catch mensaje que se retorna al usuario que sea mas entendible
     * - Se agrega en el catch insertando error en BD e imprimiendo con error_log
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.10 14-03-2017
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.11 10-04-2017 - Se verifica si existe el parámetro 'strOrigen' dentro de la variable '$arrayParametros'. Adicional se verifica si
     *                            la variable '$strOrigen' no es null le actualice el campo de origen enviado.
     *
     * Actualización: Se modifica envio de parametros en $arrayParametrosValidaCtaTarj a la función validarNumeroTarjetaCta
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.12 11-07-2017
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.13 08-09-2017   Se agregan validaciones para generar nuevo flujo de servicios UM RADIO TN factibilibles anticipadamente, luego de 
     *                            aprobar el contrato se debe asignar la factibilidad real del servicio para poder coordinar la activación del 
     *                            servicio mediante la planificación respectiva
     * @since 1.12
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.14 10-04-2017 - Se cambia el usuario de creacion para el historial del servicio para el caso de PrePlanificacion
     *                            Se añade historial a los servicios de la validacion del PIN
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 1.15 02-10-2017
     * Se agrega que al momento de la Aprobación del Contrato para empresa MD se herede el CICLO_FACTURACION del Pre-cliente al nuevo
     * Cliente que es creado en el proceso de Aprobación del Contrato.
     * 
     * @author Jorge Guerrero<jguerrerop@telconet.ec>
     * @version 1.16 01-12-2017
     * Se agrega el parametro por empresa configurado en la admi_parametro
     *      
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.17
     * @since 08-05-2018
     * Se inactivan los ciclos activos en caso de existir para evitar inconsistencia.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.15 30-03-2018 - Se añade validacion para planificacion en Linea, si el servicio queda como solicitud PrePlanificada
     *                            se envia array con lista de cupos disponibles para agendar
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.18 - Se realizan modificaciones en la Pantalla de Aprobacion de Contratos para Telconet Panamá
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.19 - Se modifica proceso para que al traer la numeracion de la orden de trabajo si es una oficina que no tenga asignada numeración
     *                 se escoja la numeración de oficina virtual
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.20 23-09-2020 Se modifica para que el commit, close y rollback esten a nivel de transacción y no de la conexión
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.19 - Cambios referentes al representante legal de persona jurídica debido al cambio de rol (Megadatos)
     * 
     * @author David Leon<mdleon@telconet.ec>
     * @version 1.21 30-10-2020 Se agrega validación para generar OT adicional para planes con cableado Ethernet.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.22 14-04-2021 Se cambia posicion de generacion de orden para que entre solo por servicio con CE.
     *
     * @author Carlos Caguana <ccaguana@telconet.ec>
     * @version 1.23 17-02-2023 Se valida el parametro del puntoId.
     * 
     * @author Alex Gómez <algomez@telconet.ec>
     * @version 1.24 25-10-2022 Se invoca procedimientos para preplanificación de productos adicionales CIH.
     *                          Se recibe nuevo parámetro para definir el origen de la ejeución (strOrigenCIH)
     *
     */
    public function guardarProcesoAprobContrato($arrayParametros)
    { 
        $intIdContrato        = $arrayParametros['intIdContrato'];
        //...
        $arrayPersona         = $arrayParametros['arrayPersona'];
        $arrayPersonaExtra    = $arrayParametros['arrayPersonaExtra'];
        $arrayFormasContacto  = $arrayParametros['arrayFormasContacto'];
        //...
        $arrayFormaPago       = $arrayParametros['arrayFormaPago'];
        $intIdFormaPago       = $arrayParametros['intIdFormaPago'];
        $intIdTipoCuenta      = $arrayParametros['intIdTipoCuenta'];
        //...
        $arrayServicios       = $arrayParametros['arrayServicios'];
        //...
        $strUsrCreacion       = $arrayParametros['strUsrCreacion'];
        $strIpCreacion        = $arrayParametros['strIpCreacion'];
        $strPrefijoEmpresa    = $arrayParametros['strPrefijoEmpresa'];
        $strEmpresaCod        = $arrayParametros['strEmpresaCod'];
        $intIdPunto           = isset($arrayParametros['intIdPunto']) ? $arrayParametros['intIdPunto'] : null;
        $strOrigen            = ( isset($arrayParametros['strOrigen']) && !empty($arrayParametros['strOrigen']) ) ? $arrayParametros['strOrigen']
                                : '';
        $strOrigenCIH         = $arrayParametros['strOrigenCIH'];
        $strObservacionHistorial      = "";
        $strEstadoSolPlanificacion    = "";
        $objSolFactibilidadAnticipada = null;
        $boolHayServicio              = false;
        $intIdJurisdiccion            = 0;
        
        $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
        $arrayParametros['strEmpresaCod']     = $strEmpresaCod;

        $strAplicaCiclosFac = $this->serviceComercial->aplicaCicloFacturacion($arrayParametros);
        
        $strObservacionHistorial = isset($arrayParametros['strObservacionHistorial']) ? $arrayParametros['strObservacionHistorial'] : '';

        $arrayData = array();
        
        if(empty($intIdFormaPago))
        {
            $arrayData['status']  = 'ERROR_SERVICE';
            $arrayData['mensaje'] = 'Información incompleta, no se está enviando la forma de pago del contrato';
            return $arrayData;
        }
        if(empty($intIdContrato))
        {
            $arrayData['status']  = 'ERROR_SERVICE';
            $arrayData['mensaje'] = 'Información incompleta por favor verifique la informacion del contrato';
            return $arrayData;
        }
        if(empty($arrayServicios))
        {
            $arrayData['status']  = 'ERROR_SERVICE';
            $arrayData['mensaje'] = 'Información incompleta por favor seleccione los servicios';
            return $arrayData;
        }
        if(empty($strUsrCreacion) && empty($strIpCreacion))
        {
            $arrayData['status']  = 'ERROR_SERVICE';
            $arrayData['mensaje'] = 'Información incompleta para registrar su petición.';
            return $arrayData;
        }
        // ========================================================================
        // [ Inicio de la transaccion ]
        // ========================================================================
        $this->emcom->getConnection()->beginTransaction();
        
        $objContrato          = $this->emcom->getRepository('schemaBundle:InfoContrato')
                                            ->find($intIdContrato);
        $objPersonaEmpresaRol = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->find($objContrato->getPersonaEmpresaRolId()->getId());
        $objEmpresaRol        = $this->emcom->getRepository('schemaBundle:InfoEmpresaRol')
                                            ->find($objPersonaEmpresaRol->getEmpresaRolId()->getId());
        $objPersona           = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                            ->find($objPersonaEmpresaRol->getPersonaId()->getId());                 
        $objOficina           = $this->emcom->getRepository('schemaBundle:InfoOficinaGrupo')
                                            ->find($objPersonaEmpresaRol->getOficinaId()->getId());
        $objEmpresaRolCliente = $this->emcom->getRepository('schemaBundle:InfoEmpresaRol')
                                                ->findPorNombreTipoRolPorEmpresa('Cliente', $objEmpresaRol->getEmpresaCod()->getId());
        
        // ========================================================================
        // [VALIDATION] - Se valida la existencia de la persona como cliente.
        // ========================================================================
        if( $objPersona != null && $objPersona->getIdentificacionCliente() )
        {
            $objCliente = $this->getClientesPorIdentificacion($objPersona->getIdentificacionCliente(), $objEmpresaRol->getEmpresaCod()->getId());
        }
        else
        {
            $arrayData['status']  = 'ERROR_SERVICE';
            $arrayData['mensaje'] = 'No se encontro la información del cliente, Favor Revisar!"';
            return $arrayData;
        }
        
        if(isset($objCliente) )
        {
            try
            {
                $objFormaPago = $this->emcom->getRepository('schemaBundle:AdmiFormaPago')->find($intIdFormaPago);
                    
                if(!empty($arrayFormaPago))
                {
                    $objContratoFormaPago = $this->emcom->getRepository('schemaBundle:InfoContratoFormaPago')
                                                 ->findOneBy(array("contratoId" => $objContrato->getId()));

                    // =================================================================================
                    // [FORMA DE PAGO EN PANTALLA] DEBITO BANCARIO 
                    // =================================================================================
                    if(strtoupper($objFormaPago->getDescripcionFormaPago()) == "DEBITO BANCARIO")
                    {
                        $objTipoCuenta      = $this->emcom->getRepository('schemaBundle:AdmiTipoCuenta')->find($intIdTipoCuenta);
                        $objBancoTipoCuenta = $this->emcom->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                                                          ->find($arrayFormaPago['bancoTipoCuentaId']);

                        // =================================================================================
                        // [VALIDATION] - Se validan los Campos Requeridos
                        // =================================================================================
                        if(!$arrayFormaPago['numeroCtaTarjeta'])
                        {
                            $arrayData['status']  = 'ERROR_SERVICE';
                            $arrayData['mensaje'] = 'No fue posible aprobar el contrato - El Numero de Cuenta / Tarjeta es un campo obligatorio';
                            return $arrayData;
                        }

                        if($objBancoTipoCuenta->getEsTarjeta() == 'S')
                        {
                            if(!$arrayFormaPago['mesVencimiento'] || !$arrayFormaPago['anioVencimiento'])
                            {
                                $arrayData['status']  = 'ERROR_SERVICE';
                                $arrayData['mensaje'] = 'No fue posible aprobar el contrato - '
                                                      . 'El Anio y mes de Vencimiento de la tarjeta es un campo obligatorio';
                                return $arrayData;
                            } 
                        }
                        // =================================================================================
                        // [VALIDATION] - Se valida Numero de Cuenta / Tarjeta, si es DEBITO BANCARIO 
                        // =================================================================================
                        //Llamo a funcion para validar numero de cuenta/tarjeta
                        $arrayParametrosValidaCtaTarj                          = array();
                        $arrayParametrosValidaCtaTarj['intTipoCuentaId']       = $intIdTipoCuenta;
                        $arrayParametrosValidaCtaTarj['intBancoTipoCuentaId']  = $arrayFormaPago['bancoTipoCuentaId'];
                        $arrayParametrosValidaCtaTarj['strNumeroCtaTarjeta']   = $arrayFormaPago['numeroCtaTarjeta'];
                        $arrayParametrosValidaCtaTarj['strCodigoVerificacion'] = $arrayFormaPago['codigoVerificacion'];
                        $arrayParametrosValidaCtaTarj['strCodEmpresa']         = $strEmpresaCod;
                        $arrayParametrosValidaCtaTarj['intFormaPagoId']        = $objContrato->getFormaPagoId()->getId();

                        $arrayValidaciones   = $this->serviceInfoContrato->validarNumeroTarjetaCtaAprobarContrato($arrayParametrosValidaCtaTarj);
                        if($arrayValidaciones)
                        {    
                            $strError = "";
                            foreach($arrayValidaciones as $key => $mensaje_validaciones)
                            {
                                foreach($mensaje_validaciones as $key_msj => $value)
                                {                      
                                    $strError = $strError.$value.".\n";  
                                }
                            }
                            $arrayData['status']  = 'ERROR_SERVICE';
                            $arrayData['mensaje'] = 'No fue posible aprobar el contrato - numero cuenta/tarjeta invalida';
                            return $arrayData;
                        }
                        // ===========================================================================================
                        // [ENCRYPT] - Encriptacion del numero del Numero de Cuenta / Tarjeta
                        // ===========================================================================================
                        //Llamo a funcion que realiza encriptado del numero de cuenta
                        $strNumeroCtaTarjeta = $this->serviceCrypt->encriptar($arrayFormaPago['numeroCtaTarjeta']);
                        if( !isset($strNumeroCtaTarjeta) )
                        {
                            $arrayData['status']  = 'ERROR_SERVICE';
                            $arrayData['mensaje'] = 'No fue posible aprobar el contrato, error al encriptar numero de cuenta/tarjeta';
                            return $arrayData;
                        }
                        // ==================================================================================================
                        // [VALIDATION] - Si existe un Contrato Forma Pago, actualiza la informacion al momento de aprobacion
                        // ==================================================================================================
                        if(isset($objContratoFormaPago))
                        {
                            $objContratoFormaPago->setNumeroCtaTarjeta($strNumeroCtaTarjeta);  
                            // ...    
                            $objContratoFormaPago->setTipoCuentaId($objTipoCuenta);      
                            $objContratoFormaPago->setBancoTipoCuentaId($objBancoTipoCuenta);
                            // ...
                            if($objBancoTipoCuenta->getEsTarjeta() == 'S')
                            {
                                $objContratoFormaPago->setTitularCuenta($arrayFormaPago['titularCuenta']);
                                $objContratoFormaPago->setMesVencimiento($arrayFormaPago['mesVencimiento']);
                                $objContratoFormaPago->setAnioVencimiento($arrayFormaPago['anioVencimiento']);
                                $objContratoFormaPago->setCodigoVerificacion($arrayFormaPago['codigoVerificacion']);
                            }
                            $objContratoFormaPago->setEstado("Activo");
                            $objContratoFormaPago->setUsrUltMod($strUsrCreacion);
                            $objContratoFormaPago->setFeUltMod(new \DateTime('now'));
                            $this->emcom->persist($objContratoFormaPago);
                            $this->emcom->flush();
                        }
                        // ===========================================================================================
                        // [CASO CONTRARIO] - Como no existe un Contrato Forma Pago, se genera uno en la aprobación
                        // ===========================================================================================
                        else
                        {
                            $objContratoFormaPagoNuevo = new InfoContratoFormaPago();

                            $objContratoFormaPagoNuevo->setContratoId($objContrato);
                            $objContratoFormaPagoNuevo->setTipoCuentaId($objTipoCuenta);
                            $objContratoFormaPagoNuevo->setBancoTipoCuentaId($objBancoTipoCuenta);
                            //...
                            $objContratoFormaPagoNuevo->setNumeroCtaTarjeta($strNumeroCtaTarjeta);  
                            //...
                            if($objBancoTipoCuenta->getEsTarjeta() == 'S')
                            {
                                $objContratoFormaPagoNuevo->setTitularCuenta($arrayFormaPago['titularCuenta']);
                                $objContratoFormaPagoNuevo->setMesVencimiento($arrayFormaPago['mesVencimiento']);
                                $objContratoFormaPagoNuevo->setAnioVencimiento($arrayFormaPago['anioVencimiento']);
                                $objContratoFormaPagoNuevo->setCodigoVerificacion($arrayFormaPago['codigoVerificacion']);
                            }
                            $objContratoFormaPagoNuevo->setEstado("Activo");
                            $objContratoFormaPagoNuevo->setUsrCreacion($strUsrCreacion);
                            $objContratoFormaPagoNuevo->setFeCreacion(new \DateTime('now'));
                            $this->emcom->persist($objContratoFormaPagoNuevo);
                            $this->emcom->flush();

                        }
                    }
                    // =====================================================================================================
                    // [UPDATE] Cambio de CUENTA BANCARIA > EFECTIVO, CHEQUE, RECAUDACION y se inactiva Contrato Forma Pago
                    // =====================================================================================================
                    elseif(isset($objContratoFormaPago) && ((strtoupper($objFormaPago->getDescripcionFormaPago()) == "EFECTIVO") ||
                                 (strtoupper($objFormaPago->getDescripcionFormaPago()) == "CHEQUE") ||
                                 (strtoupper($objFormaPago->getDescripcionFormaPago()) == "RECAUDACION")))
                    {
                        $objContratoFormaPago->setEstado("Inactivo");
                        $objContratoFormaPago->setUsrUltMod($strUsrCreacion);
                        $objContratoFormaPago->setFeUltMod(new \DateTime('now'));
                        $this->emcom->persist($objContratoFormaPago);
                        $this->emcom->flush();
                    }
                }
                
                /**********************************************************************
                 * GENERO ORDEN DE TRABAJO DE TODOS LOS SERVICIOS FACTIBLES MARCADOS  
                 * EN EL LISTADO
                 * ********************************************************************* */
                // =================================================================================
                // Generacion de Ordenes de Trabajo, Solicitudes e Historiales para cada servicio 
                // =================================================================================
                
                $strMensajeHist = "";
                if ($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
                {
                    $arrayValorParametros =  $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getOne('PRODUCTOS QUE NO SE PLANIFICAN',
                                                '',
                                                '',
                                                'PRODUCTOS QUE NO SE PLANIFICAN',
                                                '',
                                                '',
                                                '',
                                                '',
                                                '',
                                                '');

                    $arrayValorParam =  $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('PRODUCTOS ADICIONALES MANUALES',
                                                            '',
                                                            '',
                                                            'Productos adicionales manuales para activar',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '18');
                    $arrayValorP =  $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                        '',
                                                        '',
                                                        'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
                                                        'GESTION_PYL_SIMULTANEA',
                                                        '',
                                                        'PLANIFICAR',
                                                        '',
                                                        '',
                                                        '18');

                    $strValores = $arrayValorParam['valor1'] . ',' . $arrayValorParam['valor2'] . ',';                   

                    foreach( $arrayValorP as $arrayP )
                    {
                        $strValores .= $arrayP['valor5'] . ',';
                    }
                    
                    $arrayProdNolanif = explode(",",$strValores);
                    $strMensajeHist = $arrayValorParametros['valor4'] ;                    
                    foreach( $arrayServicios as $idServicio )
                    {
                        $objServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($idServicio);
                        $intIdPunto = ($objServicio) ? $objServicio->getPuntoId()->getId() : $intIdPunto;
                        $objAdendum = $this->emcom->getRepository('schemaBundle:InfoAdendum')
                                                  ->findOneBy(array("servicioId" => $objServicio->getId()));
                        if ($objAdendum)
                        {
                            //con el servicio obtengo el adendum completo a autorizar
                            $objAdendums = $this->emcom->getRepository('schemaBundle:InfoAdendum')
                                                ->findBy(array("puntoId" => $objServicio->getPuntoId()));
                            if ($objAdendums)
                            {
                                foreach ($objAdendums as $entityAdendum)
                                {                 
                                    $objServAdendum = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                                  ->find($entityAdendum->getServicioId());                                    
                                    if (($objServAdendum->getProductoId()) 
                                        && !in_array($objServAdendum->getProductoId()->getId(),$arrayProdNolanif))
                                    {   
                                        $objAdmiProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')
                                                                       ->find($objServAdendum->getProductoId());
                                        if((is_object($objAdmiProducto) && $objAdmiProducto->getRequierePlanificacion() == 'SI') || 
                                           $objAdendum->getTipo() == "AS") 
                                        {
                                            $strMensajeHist = $arrayValorParametros['valor3'] ; 
                                        }                                           
                                        break;
                                    } 
                                }
                            }       
                        }
                    }
                
                }
                // =====================================================================================================
                // [UPDATE] La caracteristica del adendum
                // =====================================================================================================
                $objAdendum = $this->emcom->getRepository('schemaBundle:InfoAdendum')
                                          ->findOneBy(array('puntoId'        => $intIdPunto,
                                                            'contratoId'     => $intIdContrato
                                                     ));
                if (is_object($objAdendum) && $objAdendum->getFormaContrato() == "FISICO")
                {
                    $objAdendum->setFeModifica(new \DateTime('now'));
                    $this->emcom->persist($objAdendum);
                    $this->emcom->flush();

                    if($objAdendum->getTipo() == "C")
                    {
                        $objContratoCaract      = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                              ->findOneBy(
                                                                array("descripcionCaracteristica"   => 'FORMA_REALIZACION_CONTRATO',
                                                                      "estado"                      => 'Activo'));
                        $arrayCaractContrato    = $this->emcom->getRepository('schemaBundle:InfoContratoCaracteristica')
                                                       ->findBy(
                                                            array("caracteristicaId"  => $objContratoCaract,
                                                                  "contratoId"        => $objAdendum->getContratoId(),
                                                                  "estado"            => "Activo",
                                                                  "valor2"            => "I"
                                                            ));
                        foreach ($arrayCaractContrato as $objCaractContrato)
                        {
                            $objCaractContrato->setFeUltMod(new \DateTime('now'));
                            $objCaractContrato->setUsrUltMod($strUsrCreacion);
                            $objCaractContrato->setValor2('C');
                            $this->emcom->persist($objCaractContrato);
                            $this->emcom->flush();
                        }
                    }
                    else
                    {
                        $objAdendumCaract    = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                           ->findOneBy(
                                                                    array("descripcionCaracteristica"   => 'FORMA_REALIZACION_ADENDUM',
                                                                          "estado"                      => 'Activo'));

                        $arrayCaractContrato = $this->emcom->getRepository('schemaBundle:InfoContratoCaracteristica')
                                                           ->findBy(
                                                                array("caracteristicaId"  => $objAdendumCaract,
                                                                      "contratoId"        => $objAdendum->getContratoId(),
                                                                      "estado"            => "Activo",
                                                                      "valor2"            => "I"
                                                                ));
                        foreach ($arrayCaractContrato as $objCaractContrato)
                        {
                            $objCaractContrato->setFeUltMod(new \DateTime('now'));
                            $objCaractContrato->setUsrUltMod($strUsrCreacion);
                            $objCaractContrato->setValor2('C');
                            $this->emcom->persist($objCaractContrato);
                            $this->emcom->flush();
                        }
                    }
                }
                foreach( $arrayServicios as $idServicio )
                {
                                                
                    $objServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($idServicio);
                    $objPunto    = $this->emcom->getRepository('schemaBundle:InfoPunto')->find($objServicio->getPuntoId()->getId());
                

                    // =================================================================================
                    // [CREATE] - Se crea la Orden de Trabajo
                    // =================================================================================
                    $objOrdenTrabajo = new InfoOrdenTrabajo();
                    
                    // [LEGACY] Se genera el numero de la Orden de Trabajo
                    $objNumeracion = $this->emcom->getRepository('schemaBundle:AdmiNumeracion')
                                            ->findByEmpresaYOficina($objEmpresaRol->getEmpresaCod()->getId(), 
                                                                    $objOficina->getId(), 
                                                                    "ORD");
                    
                    if( $objNumeracion )
                    {
                        $strSecuenciaAsignada  = str_pad($objNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
                        $strNumeroOrdenTrabajo = $objNumeracion->getNumeracionUno() 
                                               . "-" . $objNumeracion->getNumeracionDos() 
                                               . "-" . $strSecuenciaAsignada;
                        $objOrdenTrabajo->setNumeroOrdenTrabajo($strNumeroOrdenTrabajo);
                    }
                    else
                    {
                        $objAdmiParametroCab  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                                    ->findOneBy( array('nombreParametro' => 'OFICINA VIRTUAL', 
                                                                                       'estado'          => 'Activo') );

                        if(is_object($objAdmiParametroCab))
                        {        
                            $objOficinaVirtual = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                     ->findOneBy( array ("parametroId" => $objAdmiParametroCab->getId(),
                                                                                         "estado"      => "Activo" ));

                        }

                        $objNumeracion = $this->emcom->getRepository('schemaBundle:AdmiNumeracion')
                                              ->findByEmpresaYOficina($objEmpresaRol->getEmpresaCod()->getId(), 
                                                                      $objOficinaVirtual->getValor1(),
                                                                      "ORD");

                    }
                    
                    $objOrdenTrabajo->setPuntoId($objPunto);
                    $objOrdenTrabajo->setTipoOrden('N');                    
                    $objOrdenTrabajo->setFeCreacion(new \DateTime('now'));
                    $objOrdenTrabajo->setUsrCreacion($strUsrCreacion);
                    $objOrdenTrabajo->setIpCreacion($strIpCreacion);
                    $objOrdenTrabajo->setOficinaId($objOficina->getId());
                    $objOrdenTrabajo->setEstado("Activa");
                    $this->emcom->persist($objOrdenTrabajo);
                    $this->emcom->flush();
                    
                    // =================================================================================
                    // [UPDATE] - Se actualiza la numeracion de las Ordenes de Trabajo
                    // =================================================================================
                    if( $objOrdenTrabajo )
                    {
                        $numero_act = ($objNumeracion->getSecuencia() + 1);
                        $objNumeracion->setSecuencia($numero_act);
                        $this->emcom->persist($objNumeracion);
                        $this->emcom->flush();
                    }
                    
                     
                    if(isset($objServicio))
                    {
                        $strObservacionHistorialServ  = "Se solicito planificacion";
                        $strEstadoSolPlanificacion    = "PrePlanificada";
                        $objSolFactibilidadAnticipada = null;
                        $boolRequierePlanificacion    = false;
                        // =================================================================================
                        // [UPDATE] - Se actualiza el Servicio con la Orden de Trabajo generada
                        // =================================================================================
                        $objServicio->setOrdenTrabajoId($objOrdenTrabajo);
                        
                        // Si se trata de empresa MD los servicios pasan a estado PrePlanificada
                        if($arrayParametros['strPrefijoEmpresa']=='MD' || $arrayParametros['strPrefijoEmpresa']=='EN')
                        {
                            $objServicio->setEstado('PrePlanificada');   
                            $boolRequierePlanificacion = true;
                            $boolHayServicio           = true;
                            $intIdJurisdiccion         = $objServicio->getPuntoId()->getPuntoCoberturaId()->getId();
                        }
                        elseif($arrayParametros['strPrefijoEmpresa']=='TNP')
                        {
                            if($objServicio->getProductoId())
                            {
                                $objAdmiProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')->find($objServicio->getProductoId());
                                if(is_object($objAdmiProducto) && $objAdmiProducto->getRequierePlanificacion()=='SI' 
                                   && $objServicio->getEstado()=='Factible')
                                {
                                    $objServicio->setEstado('PrePlanificada');   
                                    $boolRequierePlanificacion = true;                                
                                }
                                elseif(is_object($objAdmiProducto) && $objAdmiProducto->getEstadoInicial()=='Activo' 
                                     && $objServicio->getEstado()=='Pendiente')
                                {
                                    //Se realizará Activación automática del servicio en la Aprobación del Contrato
                                    // para productos que no requieren flujo.
                                    $objServicio->setEstado($objAdmiProducto->getEstadoInicial());                                  
                                    $objServicioHist = new InfoServicioHistorial();
                                    $objServicioHist->setServicioId($objServicio);
                                    $objServicioHist->setObservacion('Se Confirmo el Servicio');
                                    $objServicioHist->setIpCreacion($strIpCreacion);
                                    $objServicioHist->setUsrCreacion($strUsrCreacion);
                                    $objServicioHist->setFeCreacion(new \DateTime('now'));
                                    $objServicioHist->setAccion('confirmarServicio');
                                    $objServicioHist->setEstado($objAdmiProducto->getEstadoInicial());
                                    $this->emcom->persist($objServicioHist);
                                    $this->emcom->flush();
                                }                                  
                            }
                            else
                            {
                                if($objServicio->getEstado()=='Factible')
                                {
                                    $objServicio->setEstado('PrePlanificada');   
                                    $boolRequierePlanificacion = true;
                                }
                            }     
                        }
                        else
                        {
                            // Si no es MD debo verificar si el producto esta marcado que requiere Planificacion y que sea Factible.
                            $objAdmiProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')->find($objServicio->getProductoId());
                            if(isset($objAdmiProducto) && $objAdmiProducto->getRequierePlanificacion()=='SI' 
                                && $objServicio->getEstado()=='Factible')
                            {
                                $objServicio->setEstado('PrePlanificada');   
                                $boolRequierePlanificacion = true;
                                
                            }
                            elseif($objServicio->getEstado()=='Factibilidad-anticipada')
                            {
                                if($arrayParametros['strPrefijoEmpresa'] == 'TN')
                                {
                                    $objServicioTecnico = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                                      ->findOneByServicioId($objServicio->getId());
                                    if (is_object($objServicioTecnico) && ($objServicioTecnico->getUltimaMillaId() > 0))
                                    {
                                        $objUltimaMilla = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                                  ->find($objServicioTecnico->getUltimaMillaId());
                                        if(is_object($objUltimaMilla))
                                        {
                                            $strUltimaMilla = $objUltimaMilla->getNombreTipoMedio();
                                            if ($strUltimaMilla == 'Radio' && $objServicio->getEstado() == "Factibilidad-anticipada")
                                            {
                                                $strEstadoSolPlanificacion = "Asignar-factibilidad";
                                                $boolRequierePlanificacion = true;
                                                $strObservacionHistorial   = "Se solicita asignar factibilidad de servicio Radio";
                                                $objServicio->setEstado($strEstadoSolPlanificacion);   
                                                $objSolFactibilidadAnticipada = $this->emcom
                                                                                     ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                                     ->findOneBy(array(
                                                                                                "servicioId" => $objServicio->getId(), 
                                                                                                "estado"     => "Factibilidad-anticipada")
                                                                                                );
                                                if(is_object($objSolFactibilidadAnticipada))
                                                {
                                                    $objSolFactibilidadAnticipada->setEstado($strEstadoSolPlanificacion);
                                                    $this->emcom->persist($objSolFactibilidadAnticipada);
                                                    $this->emcom->flush();

                                                    $objDetalleSolHist = new InfoDetalleSolHist();
                                                    $objDetalleSolHist->setDetalleSolicitudId($objSolFactibilidadAnticipada);
                                                    $objDetalleSolHist->setIpCreacion($strIpCreacion);
                                                    $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                                                    $objDetalleSolHist->setUsrCreacion($strUsrCreacion);
                                                    $objDetalleSolHist->setEstado($strEstadoSolPlanificacion);
                                                    $this->emcom->persist($objDetalleSolHist);
                                                    $this->emcom->flush();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            elseif(isset($objAdmiProducto) && $objAdmiProducto->getEstadoInicial()=='Activo' 
                                && $objServicio->getEstado()=='Pendiente')
                            {
                                 //Si Producto no requiere flujo. Se realizará Activación automática en la Aprobación del Contrato.
                                 $objServicio->setEstado($objAdmiProducto->getEstadoInicial());  
                                
                                 $objServicioHist = new InfoServicioHistorial();
                                 $objServicioHist->setServicioId($objServicio);
                                 $objServicioHist->setObservacion('Se Confirmo el Servicio');
                                 $objServicioHist->setIpCreacion($strIpCreacion);
                                 $objServicioHist->setUsrCreacion($strUsrCreacion);
                                 $objServicioHist->setFeCreacion(new \DateTime('now'));
                                 $objServicioHist->setAccion('confirmarServicio');
                                 $objServicioHist->setEstado($objAdmiProducto->getEstadoInicial());
                                 $this->emcom->persist($objServicioHist);
                                 $this->emcom->flush();
                            }
                        }
                        $this->emcom->persist($objServicio);
                        $this->emcom->flush();
                        if( $objServicio->getTipoOrden() )
                        {
                            $objOrdenTrabajo->setTipoOrden($objServicio->getTipoOrden());
                            $this->emcom->persist($objOrdenTrabajo);
                            $this->emcom->flush();
                        }
                        $this->emcom->flush();
                        if($boolRequierePlanificacion)
                        {
                            $strUsrCreacionHistorial = $strUsrCreacion;
                            if('MOVIL' === strtoupper($strOrigen))
                            {
                                $strUsrCreacionHistorial = 'telcos_contrato';
                            }
                            
                            // =================================================================================
                            // [CREATE] - Se crea el Historial del Servicio
                            // =================================================================================
                            $objServicioHist = new InfoServicioHistorial();
                            $objServicioHist->setServicioId($objServicio);
                            $objServicioHist->setObservacion($strObservacionHistorialServ);
                            $objServicioHist->setIpCreacion($strIpCreacion);
                            $objServicioHist->setUsrCreacion($strUsrCreacionHistorial);
                            $objServicioHist->setFeCreacion(new \DateTime('now'));
                            $objServicioHist->setEstado($objServicio->getEstado());
                            $this->emcom->persist($objServicioHist);
                            $this->emcom->flush();

                            // =================================================================================
                            // [CREATE] - Se crea la solicitud de planificacion para el servicio
                            // =================================================================================
                            $objTipoSolicitud    = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                        ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");

                            $objDetalleSolicitud = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                        ->findCountDetalleSolicitudByIds($objServicio->getId(), $objTipoSolicitud->getId());

                            // Se valida que no exista otra solicitud de Planificacion 
                            if(!$objDetalleSolicitud || $objDetalleSolicitud["cont"] <= 0 || is_object($objSolFactibilidadAnticipada))
                            {
                                $objSolicitudNueva = new InfoDetalleSolicitud();
                                $objSolicitudNueva->setServicioId($objServicio);
                                $objSolicitudNueva->setTipoSolicitudId($objTipoSolicitud);
                                $objSolicitudNueva->setEstado($strEstadoSolPlanificacion);
                                $objSolicitudNueva->setUsrCreacion($strUsrCreacion);
                                $objSolicitudNueva->setFeCreacion(new \DateTime('now'));
                                $this->emcom->persist($objSolicitudNueva);
                                $this->emcom->flush();

                                // =================================================================================
                                // [CREATE] - Se crea el Historial del Servicio
                                // =================================================================================
                                $entityDetalleSolHist = new InfoDetalleSolHist();
                                $entityDetalleSolHist->setDetalleSolicitudId($objSolicitudNueva);
                                $entityDetalleSolHist->setIpCreacion($strIpCreacion);
                                $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                                $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                                $entityDetalleSolHist->setEstado($strEstadoSolPlanificacion);
                                $this->emcom->persist($entityDetalleSolHist);
                                $this->emcom->flush();

                                // =====================================================================================
                                // [CREATE] - Se crea la solicitud de Cableado Ethernet si lo tiene incluido en el plan
                                // =====================================================================================
                                $arrayParametrosValor = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->get('VALIDA_PROD_ADICIONAL', 
                                                                             'COMERCIAL', 
                                                                             '',
                                                                             '',
                                                                             'PROD_ADIC_PLANIFICA',
                                                                             '',
                                                                             '',
                                                                             '',
                                                                             '',
                                                                             $strEmpresaCod);
                                if (is_array($arrayParametrosValor) && !empty($arrayParametrosValor))
                                {
                                    foreach($arrayParametrosValor as $arrayParametro)
                                    {
                                        $arrayParametros    =    array("Punto"      => $objServicio->getPuntoId()->getId(),
                                                                    "Producto"   => $arrayParametro['valor2'],
                                                                    "Servicio"   => $objServicio->getId(),
                                                                    "Plan"       => "",
                                                                    "Estado"     => 'Todos');
                                        $arrayResultado = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                            ->getProductoByPlanes($arrayParametros);
                                        if($arrayResultado['total'] > 0)
                                        {
                                            $arrayDatos     =   array("Punto"           => $objServicio->getPuntoId()->getId(),
                                                                    "Producto"       => $arrayParametro['valor2'],
                                                                    "Servicio"       => $objServicio->getId(),
                                                                    "Observacion"    => $arrayParametro['valor3'],
                                                                    "Caracteristica" => $arrayParametro['valor4'],
                                                                    "Usuario"        => $objServicio->getUsrCreacion(),
                                                                    "Ip"             => $strIpCreacion,
                                                                    "EmpresaId"      => $strEmpresaCod,
                                                                    "OficinaId"      => $objPersonaEmpresaRol->getOficinaId()->getId(),
                                                                    "EstadoServicio" => 'PrePlanificada',
                                                                    "Solicitud"      => '',
                                                                    "NuevoServicio"  => '');
                                            
                                            $this->serviceServicioTecnico->generarOtServiciosAdicional($arrayDatos);
                                        }
                                    }
                                }
                            }
                        }
                    }// if(isset($objServicio))
                } // foreach( $arrayServicios as $idServicio )
                
                //Verificación y generación de solictudes por preplanificación de servicios CIH
                if($strPrefijoEmpresa == "MD")
                {
                    $arrayParamsPreplanificaCIH = array('intIdServicioInternet'  => $objServicio->getId(),
                                                        'intIdPunto'             => $objServicio->getPuntoId()->getId(),
                                                        'strUsuarioCreacion'     => $strUsrCreacion,
                                                        'strIpCreacion'          => $strIpCreacion,
                                                        'strOrigen'              => $strOrigenCIH,
                                                        'strPrefijoEmpresa'      => $strPrefijoEmpresa,
                                                        'strCodEmpresa'          => $strEmpresaCod);

                    $arrayResponseCIH = $this->serviceInfoContrato->preplanificaProductosCIH($arrayParamsPreplanificaCIH);

                    if ($arrayResponseCIH['status'] != 'OK')
                    {
                        $arrayData['status']  = 'ERROR_SERVICE';
                        $arrayData['mensaje'] = $arrayResponseCIH['mensaje'];
                        return $arrayData;
                    }
                }


                /************************************************************************
                 * ACTUALIZO LA INFORMACION DEL PROSPECTO QUE SERÁ CONVERTIDO A CLIENTE Y
                 * ACTUALIZO LAS FORMAS DE CONTACTO
                 * *********************************************************************** */
                // =================================================================================
                // [UPDATE] - Se actualiza la informacion de la Persona y se activa
                // =================================================================================
                if (!empty($arrayPersona))
                {
                    $arrayPersona['intIdPersona']        = $objPersona->getId();
                    $arrayPersona['estado']              = 'Activo';
                    $arrayPersona['origenProspecto']     = 'S';
                    $arrayPersona['direccionTributaria'] = $arrayPersonaExtra['direccionTributaria'];
                    $arrayPersona['strUsrCreacion']      = $strUsrCreacion;
                    $arrayPersona['strIpCreacion']       = $strIpCreacion;
                                 
                    $arrayService = $this->servicePersona->editarPersona($arrayPersona);
                    if($arrayService && array_key_exists('status',$arrayService) && $arrayService['status']=='ERROR_SERVICE'){
                        $arrayData['status']  = $arrayService['status'];
                        $arrayData['mensaje'] = $arrayService['mensaje'];
                        return $arrayData;
                    }
                }
                
                // =================================================================================
                // [UPDATE] - Se actualizan las formas de contacto de la persona
                // =================================================================================   
                if (!empty($arrayFormasContacto))
                {
                    $arrayFormasContactoUpdate['objPersona']          = $objPersona;
                    $arrayFormasContactoUpdate['strUsuario']          = $strUsrCreacion;
                    $arrayFormasContactoUpdate['strIpCreacion']       = $strIpCreacion;
                    $arrayFormasContactoUpdate['arrayFormasContacto'] = $arrayFormasContacto;

                    $arrayService2 = $this->servicePersonaFormaContacto->actualizarFormasContacto($arrayFormasContactoUpdate);
                    if($arrayService2 && array_key_exists('status',$arrayService2) && $arrayService2['status']=='ERROR_SERVICE'){
                        $arrayData['status']  = $arrayService2['status'];
                        $arrayData['mensaje'] = $arrayService2['mensaje'];
                        return $arrayData;
                    }
                }
                
                // =================================================================================
                // [UPDATE] - Se crea  el nuevo Info Persona Empresa Rol con ROL: Cliente
                // =================================================================================                           
                $objPersonaEmpresaRolCliente = new InfoPersonaEmpresaRol();
                $objPersonaEmpresaRolCliente->setEmpresaRolId($objEmpresaRolCliente);
                $objPersonaEmpresaRolCliente->setPersonaId($objPersona);
                $objPersonaEmpresaRolCliente->setOficinaId($objOficina);
                $objPersonaEmpresaRolCliente->setFeCreacion(new \DateTime('now'));
                $objPersonaEmpresaRolCliente->setUsrCreacion($strUsrCreacion);                
                $objPersonaEmpresaRolCliente->setEsPrepago($objPersonaEmpresaRol->getEsPrepago());
                $objPersonaEmpresaRolCliente->setEstado('Activo');
                $this->emcom->persist($objPersonaEmpresaRolCliente);
                $this->emcom->flush();
                
                // ================================================================================================================
                // [UPDATE] - Se actualiza id de InfoPersonaEmpresaRol de tabla Info_Persona_Representante (Megadatos - TM Comercial)
                // ================================================================================================================
                
                if(($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN') && $objPersona->getTipoTributario() == 'JUR')
                {
                    $objInfoPersonaRepresentante = $this->emcom->getRepository('schemaBundle:InfoPersonaRepresentante')
                        ->findOneBy(array('personaEmpresaRolId' => $objPersonaEmpresaRol->getId(),
                                          'estado'              => 'Activo'));
                    
                    if(!is_null($objInfoPersonaRepresentante))
                    {
                        $objInfoPersonaRepresentanteNuevo = new InfoPersonaRepresentante();
                        $objInfoPersonaRepresentanteNuevo->setPersonaEmpresaRolId($objPersonaEmpresaRolCliente);
                        $objInfoPersonaRepresentanteNuevo->setRepresentanteEmpresaRolId($objInfoPersonaRepresentante->getRepresentanteEmpresaRolId());
                        $objInfoPersonaRepresentanteNuevo->setRazonComercial($objInfoPersonaRepresentante->getRazonComercial());
                        $objInfoPersonaRepresentanteNuevo->setFeRegistroMercantil($objInfoPersonaRepresentante->getFeRegistroMercantil());
                        $objInfoPersonaRepresentanteNuevo->setFeExpiracionNombramiento($objInfoPersonaRepresentante->getFeExpiracionNombramiento());
                        $objInfoPersonaRepresentanteNuevo->setEstado('Activo');
                        $objInfoPersonaRepresentanteNuevo->setUsrCreacion($strUsrCreacion);
                        $objInfoPersonaRepresentanteNuevo->setFeCreacion(new \DateTime('now'));
                        $objInfoPersonaRepresentanteNuevo->setIpCreacion($strIpCreacion);
                        $objInfoPersonaRepresentanteNuevo->setObservacion("Actualización de rol precliente a cliente");

                        $this->emcom->persist($objInfoPersonaRepresentanteNuevo);

                        $objInfoPersonaRepresentante->setEstado('Eliminado');
                        $objInfoPersonaRepresentante->setFeUltMod(new \DateTime('now'));
                        $objInfoPersonaRepresentante->setUsrUltMod($strUsrCreacion);
                        $objInfoPersonaRepresentante->setIpUltMod($strIpCreacion);
                        
                         $this->emcom->persist($objInfoPersonaRepresentante);
                    }
                }
                
                // SE ASIGNA EL CICLO DE FACTURACION DEL PRE-CLIENTE AL CLIENTE
                //if($arrayParametros['strPrefijoEmpresa'] == 'MD')
                if ($strAplicaCiclosFac == 'S' )
                {                                   
                    $arrayParam                    = array();
                    $arrayParam['intIdPersonaRol'] = $objPersonaEmpresaRol->getId();
                    $arrayPersEmpRolCaracCicloPreCliente = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                       ->getCaractCicloFacturacion($arrayParam);
                    if( !isset($arrayPersEmpRolCaracCicloPreCliente['intIdPersonaEmpresaRolCaract']) 
                        && empty($arrayPersEmpRolCaracCicloPreCliente['intIdPersonaEmpresaRolCaract']) )
                    {
                        $arrayData['status']  = 'ERROR_SERVICE';
                        $arrayData['mensaje'] = 'No fue posible aprobar el contrato - El Pre-Cliente no posee Ciclo de Facturación asignado';
                        return $arrayData;
                    }
                    else
                    {   
                        $objCaracteristicaCiclo = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                       ->find($arrayPersEmpRolCaracCicloPreCliente['intCaracteristicaId']);
                        if(!is_object($objCaracteristicaCiclo))
                        {                           
                            $arrayData['status']  = 'ERROR_SERVICE';
                            $arrayData['mensaje'] = 'No fue posible aprobar el contrato - No existe Caracteristica CICLO_FACTURACION';
                            return $arrayData;
                        }

                        //Se busca si el cliente tiene un ciclo asignado anteriormente ya sea por recontratación o inconsistencia de migración.
                        $arrayParametrosBuscaCiclo = array("estado"              => "Activo",
                                                           "personaEmpresaRolId" => $objPersonaEmpresaRolCliente->getId(),
                                                           "caracteristicaId"    => $objCaracteristicaCiclo->getId());
                        $arrayListPersEmpRolCarac  = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                 ->findBy($arrayParametrosBuscaCiclo);
                        foreach($arrayListPersEmpRolCarac as $objInfoPerEmpRolCarac)
                        {
                            //Se actualiza el estado del registro
                            $objInfoPerEmpRolCarac->setEstado("Inactivo");
                            $objInfoPerEmpRolCarac->setFeUltMod(new \DateTime("now"));
                            $objInfoPerEmpRolCarac->setUsrUltMod("telcos_ciclo");
                            $this->emcom->persist($objInfoPerEmpRolCarac);

                            //Se crea el historial por cambio de estado en caracteristica del ciclo.
                            $objInfoPerEmpRolHisto = new InfoPersonaEmpresaRolHisto();
                            $objInfoPerEmpRolHisto->setUsrCreacion("telcos_ciclo");
                            $objInfoPerEmpRolHisto->setFeCreacion(new \DateTime('now'));
                            $objInfoPerEmpRolHisto->setIpCreacion('127.0.0.1');
                            $objInfoPerEmpRolHisto->setEstado("Inactivo");
                            $objInfoPerEmpRolHisto->setPersonaEmpresaRolId($objPersonaEmpresaRolCliente);
                            $objInfoPerEmpRolHisto->setObservacion('Se inactiva el ciclo anteriormente asignado');
                            $this->emcom->persist($objInfoPerEmpRolHisto);
                        }

                        //Inserto CICLO_FACTURACION del Pre_cliente en el nuevo Cliente
                        $objPersEmpRolCaracCicloCliente = new InfoPersonaEmpresaRolCarac();                        
                        $objPersEmpRolCaracCicloCliente->setPersonaEmpresaRolId($objPersonaEmpresaRolCliente);
                        $objPersEmpRolCaracCicloCliente->setCaracteristicaId($objCaracteristicaCiclo);
                        $objPersEmpRolCaracCicloCliente->setValor($arrayPersEmpRolCaracCicloPreCliente['strValor']);
                        $objPersEmpRolCaracCicloCliente->setFeCreacion(new \DateTime('now'));
                        $objPersEmpRolCaracCicloCliente->setUsrCreacion($strUsrCreacion);     
                        $objPersEmpRolCaracCicloCliente->setIpCreacion($strIpCreacion);
                        $objPersEmpRolCaracCicloCliente->setEstado('Activo');
                        $this->emcom->persist($objPersEmpRolCaracCicloCliente);
                                                
                        //Inserto Historial de creacion de caracteristica de CICLO_FACTURACION en el CLIENTE                
                        $objPersEmpRolCaracCicloHisto = new InfoPersonaEmpresaRolHisto();
                        $objPersEmpRolCaracCicloHisto->setUsrCreacion($strUsrCreacion);
                        $objPersEmpRolCaracCicloHisto->setFeCreacion(new \DateTime('now'));
                        $objPersEmpRolCaracCicloHisto->setIpCreacion($strIpCreacion);
                        $objPersEmpRolCaracCicloHisto->setEstado('Activo');
                        $objPersEmpRolCaracCicloHisto->setPersonaEmpresaRolId($objPersonaEmpresaRolCliente);
                        $objPersEmpRolCaracCicloHisto->setObservacion('Se creo Cliente con Ciclo de Facturación: '
                            . $arrayPersEmpRolCaracCicloPreCliente['strNombreCiclo']);
                        $this->emcom->persist($objPersEmpRolCaracCicloHisto);
                    }
                }
                
                // =================================================================================
                // [UPDATE] - Se actualiza el Info Persona Empresa Rol del Prospecto a Inactivo
                // =================================================================================
                $objPersonaEmpresaRol->setEstado('Inactivo');
                $this->emcom->persist($objPersonaEmpresaRol);
                $this->emcom->flush();                
                
                // =================================================================================
                // [UPDATE] - Se actualiza el Info Persona Referido con el Rol de Cliente
                // =================================================================================
                $objPersonaReferido = $this->emcom->getRepository('schemaBundle:InfoPersonaReferido')
                                            ->findOneBy(array("personaEmpresaRolId" => $objPersonaEmpresaRol->getId(), 
                                                              "estado" => "Activo"));
                if( $objPersonaReferido )
                {
                    $objPersonaReferido->setPersonaEmpresaRolId($objPersonaEmpresaRolCliente);
                    $this->emcom->persist($objPersonaReferido);
                    $this->emcom->flush();
                }
                // =============================================================================================
                // [UPDATE] - Se actualiza los Contactos de Pre-Cliente con el Info Persona Empresa Rol Cliente
                // =============================================================================================
                $arrayContactos = $this->emcom->getRepository('schemaBundle:InfoPersonaContacto')
                                    ->findByPersonaEmpresaRolId($objPersonaEmpresaRol->getId());                
                if( $arrayContactos )
                {
                    foreach( $arrayContactos as $contacto )
                    {
                        $contacto->setPersonaEmpresaRolId($objPersonaEmpresaRolCliente);
                        $this->emcom->persist($contacto);
                        $this->emcom->flush();
                    }
                }
                // =================================================================================
                // [UPDATE] - Se actualiza los Puntos con el Info Persona Empresa Rol Cliente
                // =================================================================================
                $arrayPuntos = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                    ->findByPersonaEmpresaRolId($objPersonaEmpresaRol->getId());
            
        
                $strBoolContribucionSolidaria = 'N'; 
                
                if( $arrayPuntos )
                {
                    foreach( $arrayPuntos as $punto )
                    {
                        $punto->setPersonaEmpresaRolId($objPersonaEmpresaRolCliente);
                        $punto->setUsrUltMod($strUsrCreacion);
                        $punto->setFeUltMod(new \DateTime('now'));
                        $this->emcom->persist($punto);
                        $this->emcom->flush();
                       
                        if($strPrefijoEmpresa=='TN')
                        {
                            $objInfoPuntoDatoAdicional = $this->emcom->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                                     ->findOneBy(array("puntoId" => $punto));
                            if(is_object($objInfoPuntoDatoAdicional))
                            {
                               if($objInfoPuntoDatoAdicional->getEsPadreFacturacion()=='S' && $strBoolContribucionSolidaria=='N')
                               {
                               
                                    $arrayParametros        = array('intIdPersonaEmpresaRol'=> $objPersonaEmpresaRolCliente->getId(),
                                                                    'intIdOficina'          => $objOficina->getId(),
                                                                    'strEmpresaCod'         => $strEmpresaCod,
                                                                    'intIdSectorPunto'      => $punto->getSectorId()->getId(),
                                                                    'intIdPuntoFacturacion' => $punto->getId()
                                                                   );
                                                                                              
                                    $strRptContribucionSolidaria = $this->emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                                      ->getClienteCompensado($arrayParametros);
                                   

                                    if($strRptContribucionSolidaria)    
                                    {
                                        $strBoolContribucionSolidaria = $strRptContribucionSolidaria;
                                    }
                               
                               }
                            }
                        }  
                    }
                }

                
                // Compensación Solidaria
                
                if($strPrefijoEmpresa == 'TN')
                {                    
                    $objContrSolidaria = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                     ->getOneByCaracteristica($objPersonaEmpresaRolCliente->getId(),'CONTRIBUCION_SOLIDARIA');

                    $objAdmiCaracteris = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy(array('descripcionCaracteristica' => 'CONTRIBUCION_SOLIDARIA'));

                    $objMotivo         = $this->emcom->getRepository('schemaBundle:AdmiMotivo')
                                              ->findOneBy(array('nombreMotivo' => 'CAMBIO DATOS FACTURACION'));
                    
                    if(!is_object($objAdmiCaracteris))
                    { 
                        throw new \Exception("No existe Caracteristica CONTRIBUCION_SOLIDARIA");
                    }

                    if(is_object($objContrSolidaria))
                    { 
                        $objContrSolidaria->setValor($strBoolContribucionSolidaria);
                        $this->emcom->persist($objContrSolidaria);
                    }
                    else
                    { 
                        if(is_object($objAdmiCaracteris))
                        {
                            $objInfoPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                            $objInfoPersonaEmpresaRolCarac->setPersonaEmpresaRolId($objPersonaEmpresaRolCliente);
                            $objInfoPersonaEmpresaRolCarac->setCaracteristicaId($objAdmiCaracteris);
                            $objInfoPersonaEmpresaRolCarac->setValor($strBoolContribucionSolidaria);
                            $objInfoPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                            $objInfoPersonaEmpresaRolCarac->setUsrCreacion($strUsrCreacion);
                            $objInfoPersonaEmpresaRolCarac->setIpCreacion($strIpCreacion);
                            $objInfoPersonaEmpresaRolCarac->setEstado('Activo');
                            $this->emcom->persist($objInfoPersonaEmpresaRolCarac);
                        }
                    }
                    $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
                    $objInfoPersonaEmpresaRolHisto->setEstado('Activo');
                    $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
                    $objInfoPersonaEmpresaRolHisto->setUsrCreacion($strUsrCreacion);
                    $objInfoPersonaEmpresaRolHisto->setIpCreacion($strIpCreacion);
                    $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objPersonaEmpresaRolCliente);
                    if(is_object($objMotivo))
                    {    
                        $objInfoPersonaEmpresaRolHisto->setMotivoId($objMotivo->getId()); 
                    }
                    
                    if($strBoolContribucionSolidaria == 'S')
                    { 
                        $objInfoPersonaEmpresaRolHisto->setObservacion("El cliente se marco como CONTRIBUCION_SOLIDARIA en Si");                       
                    }
                    else
                    { 
                        $objInfoPersonaEmpresaRolHisto->setObservacion("El cliente se marco como CONTRIBUCION_SOLIDARIA en No");
                    }                                                
                    $this->emcom->persist($objInfoPersonaEmpresaRolHisto); 
                    $this->emcom->flush();
                }   
                
                // =================================================================================
                // [UPDATE] - Se actualiza y se ACTIVA el contrato
                // =================================================================================
                $objContrato->setPersonaEmpresaRolId($objPersonaEmpresaRolCliente);
                $objContrato->setEstado('Activo');
                $objContrato->setFormaPagoId($objFormaPago);
                $objContrato->setUsrAprobacion($strUsrCreacion);
                $objContrato->setFeAprobacion(new \DateTime('now'));
                
                if( !empty($strOrigen) )
                {
                    $objContrato->setOrigen($strOrigen);
                }
                    
                $this->emcom->persist($objContrato);
                $this->emcom->flush();

                // =================================================================================
                // [CREATE] - Se crea el historial del Info Persona Empresa Rol Pre-Cliente
                // =================================================================================
                $objPersonaEmpresaRolHist = new InfoPersonaEmpresaRolHisto();
                $objPersonaEmpresaRolHist->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $objPersonaEmpresaRolHist->setEstado('Convertido');
                $objPersonaEmpresaRolHist->setIpCreacion($strIpCreacion);
                $objPersonaEmpresaRolHist->setUsrCreacion($strUsrCreacion);
                $objPersonaEmpresaRolHist->setFeCreacion(new \DateTime('now'));
                $this->emcom->persist($objPersonaEmpresaRolHist);
                $this->emcom->flush();

                // =================================================================================
                // [CREATE] - Se crea el historial del Info Persona Empresa Rol Cliente
                // =================================================================================
                $objPersonaEmpresaRolHistCliente = new InfoPersonaEmpresaRolHisto();
                $objPersonaEmpresaRolHistCliente->setEstado($objPersonaEmpresaRolCliente->getEstado());
                $objPersonaEmpresaRolHistCliente->setFeCreacion(new \DateTime('now'));
                $objPersonaEmpresaRolHistCliente->setIpCreacion($strIpCreacion);
                $objPersonaEmpresaRolHistCliente->setPersonaEmpresaRolId($objPersonaEmpresaRolCliente);
                $objPersonaEmpresaRolHistCliente->setUsrCreacion($strUsrCreacion);
                $this->emcom->persist($objPersonaEmpresaRolHistCliente);             
                
                // =================================================================================
                // [CREATE] - Se crea el historial del Info Persona Empresa Rol Cliente con el pin y
                // el telefono de autorizacion con su numero de contrato
                // =================================================================================
                if(isset($strObservacionHistorial) && !empty($strObservacionHistorial))
                {
                    $objPersonaEmpresaRolHistCliente = new InfoPersonaEmpresaRolHisto();
                    $objPersonaEmpresaRolHistCliente->setEstado($objPersonaEmpresaRolCliente->getEstado());
                    $objPersonaEmpresaRolHistCliente->setFeCreacion(new \DateTime('now'));
                    $objPersonaEmpresaRolHistCliente->setIpCreacion($strIpCreacion);
                    $objPersonaEmpresaRolHistCliente->setPersonaEmpresaRolId($objPersonaEmpresaRolCliente);
                    $objPersonaEmpresaRolHistCliente->setUsrCreacion('telcos_contrato');
                    $objPersonaEmpresaRolHistCliente->setObservacion($strObservacionHistorial);
                    $this->emcom->persist($objPersonaEmpresaRolHistCliente);
                }
                else
                {
                    $arrayHistorial = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')
                                                  ->findByPersonaEmpresaRolId($objPersonaEmpresaRol->getId());
                    if($arrayHistorial)
                    {
                        
                        foreach( $arrayHistorial as $objHistorial )
                        {
                            $objPersonaEmpresaRolHistCliente = new InfoPersonaEmpresaRolHisto();
                            $objPersonaEmpresaRolHistCliente->setEstado($objPersonaEmpresaRolCliente->getEstado());
                            $objPersonaEmpresaRolHistCliente->setFeCreacion(new \DateTime('now'));
                            $objPersonaEmpresaRolHistCliente->setIpCreacion($strIpCreacion);
                            $objPersonaEmpresaRolHistCliente->setPersonaEmpresaRolId($objPersonaEmpresaRolCliente);
                            $objPersonaEmpresaRolHistCliente->setUsrCreacion($objHistorial->getUsrCreacion());
                            $objPersonaEmpresaRolHistCliente->setObservacion($objHistorial->getObservacion());
                            $this->emcom->persist($objPersonaEmpresaRolHistCliente);
                        }
                    }
                }
                
                if(($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN') && is_object($objServicio) && $objServicio != null)
                {
                    $objAdendum = $this->emcom->getRepository('schemaBundle:InfoAdendum')
                                                    ->findOneBy(array("servicioId" => $objServicio->getId()));
                    if ($objAdendum)
                    {
                            
                        $objAdendums = $this->emcom->getRepository('schemaBundle:InfoAdendum')
                                                ->findBy(array("puntoId" => $objAdendum->getPuntoId()));

                        if ($objAdendums)
                        {
                            foreach ($objAdendums as $entityAdendum)
                            {
                                $entityAdendum->setTipo("C");
                                $entityAdendum->setContratoId($objContrato->getId());
                                $entityAdendum->setEstado("Activo");
                                $entityAdendum->setFeModifica(new \DateTime('now'));
                                $entityAdendum->setUsrModifica($strUsrCreacion);
                                $this->emcom->persist($entityAdendum);
                                $this->emcom->flush(); 
                                
                                if ($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN' )
                                {
                                    
                                    $objServ = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($entityAdendum->getServicioId());
                                    $objServHist = $this->emcom->getRepository('schemaBundle:InfoServicioHistorial')
                                                        ->findOneBy(array("servicioId" => $entityAdendum->getServicioId(),
                                                                          "usrCreacion" => 'PLANIF_COMERCIAL'));
                                    if (($objServ->getPlanId()) || 
                                         ($objServ->getProductoId()->getRequierePlanificacion() == "SI" || 
                                         $objServ->getProductoId()->getNombreTecnico() == "EXTENDER_DUAL_BAND") &&
                                         !($objServHist) )
                                    {
                                        $objServicioHistS = new InfoServicioHistorial();
                                        $objServicioHistS->setServicioId($objServ);
                                        $objServicioHistS->setObservacion($strMensajeHist);
                                        $objServicioHistS->setIpCreacion($strIpCreacion);
                                        $objServicioHistS->setUsrCreacion($strUsrCreacion);
                                        $objServicioHistS->setFeCreacion(new \DateTime('now'));
                                        $objServicioHistS->setAccion('Planificacion Comercial');
                                        $objServicioHistS->setEstado($objServ->getEstado());
                                        $this->emcom->persist($objServicioHistS);
                                        $this->emcom->flush();
    
                                    }
                                }                                
                            }
                        }                                                                 
                    }                              
                    else
                    {
                        throw new \Exception("No se encuentra adendum para activar");
                    } 
                }
                if($boolHayServicio)
                {
                    $arrayPlanificacion = $this->servicePlanificar->getCuposMobil(array(
                        "intJurisdiccionId" => $intIdJurisdiccion));
                }
                $arrayData['arrayPlanificacion'] = $arrayPlanificacion;
                $this->emcom->flush();
                $this->emcom->getConnection()->commit();
                $this->emcom->getConnection()->close();
                return $arrayData;
            }
            catch(\Exception $e)
            {
                error_log("error " . json_encode($e));
                // Rollback the failed transaction attempt
                if ($this->emcom->getConnection()->isTransactionActive())
                {
                    $this->emcom->getConnection()->rollback();
                    $this->emcom->getConnection()->close();
                }
                // ...
                $arrayData['status']  = 'ERROR_SERVICE';
                $arrayData['mensaje'] = 'No fue posible aprobar el contrato - error inesperado';
                $this->serviceUtil->insertError(
                                                "Telcos+",
                                                "InfoContratoAprobService->guardarProcesoAprobContrato", 
                                                $e->getMessage(), 
                                                $strUsrCreacion, 
                                                $strIpCreacion
                                               );

                //Reverso en caso de error durante el proceso
                if($strPrefijoEmpresa == "MD" && is_object($arrayParamsPreplanificaCIH))
                {
                    $this->serviceInfoContrato->reversaPreplanificacionCIH($arrayParamsPreplanificaCIH);
                }

                return $arrayData;
            }
        }
    }

    /**
     * Documentación para el método 'listarContratosPorCambioRazonSocialCriterios'.
     * Saca informacion de los contratos  segun criterios de busqueda por empresa que se encuentran pendientes de Aprobacion y que 
     * corresponden a contratos creados por cambio de Razon Social por Punto.
     * @param array  $arrayParams
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 02-10-2015   
     * @return object       
     */
    function listarContratosPorCambioRazonSocialCriterios($arrayParams)
    {

        $datos = $this->emcom->getRepository('schemaBundle:InfoContrato')
                 ->listarContratosPorCambioRazonSocialCriterios($arrayParams);
        return $datos;
    }

    /**
     * Documentación para el método 'listarContratosTnPorCriterios'.
     * Saca informacion de los contratos  segun criterios de busqueda por empresa que se encuentran pendientes de Aprobacion y que 
     * corresponden a contratos nuevos para la empresa TN
     * @param array  $arrayParams
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 19-06-2016
     * @return object       
     */
    function listarContratosTnPorCriterios($arrayParams)
    {

        $datos = $this->emcom->getRepository('schemaBundle:InfoContrato')
                 ->listarContratosTnPorCriterios($arrayParams);
        return $datos;
    }
    
    /**   
     * Documentación para el método 'procesaAprobContratoCambioRazonSocial'.
     * Funcion que procesa la aprobacion del contrato para el caso de un contrato generado por un Cambio de Razon
     * Social por Punto o Login.    
     * Consideraciones:
     * 1) Se le asigna nuevo Rol de "Cliente".
     * 2) Se crean los nuevos Logines en base a los Logines Origen del Cambio de Razon Social y sus servicios en estado Activo.
     * 3) Los Logines origen del "Cambio de Razon Social por Punto" pasaran a Cancelados asi como toda la data relacionada a estos en el momento
     *    de la Aprobacion del Nuevo Contrato.
     * 4) Actualiza la informacion de la forma de Pago : numero de cuenta o tarjeta , anio y mes de vencimiento de la tarjeta etc.
     * 5) Actualiza estado de contrato a Aprobado
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 02-10-2015    
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 16-06-2015    Se agrega cancelación de LDAP de servicios antiguos y creación de LDAP nuevos servicios
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 27-06-2015    Se corrige cancelación de LDAP de servicios antiguos y creación de LDAP nuevos servicios
     *  
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.3 20-06-2016  
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4 06-07-2016    Se agrega registro de motivo en cancelacion de servicios
     *      
     * Se agrega informacion de Contactos a Clonarse en el Proceso de Cambio de Razon Social:
     * Clono Contactos a nivel de Cliente hacia la nueva razon social
     * Clono Contactos a nivel de Punto hacia los Logines de la nueva razon social
     *     
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.5 08-11-2016
     * Se envia array de Parametros $arrayParamFormasContac a la funcion "validarFormasContactos" se agrega strOpcionPermitida y strPrefijoEmpresa, 
     * Prefijo de empresa en sesion para validar que para empresa MD no se obligue el ingreso de al menos 1 correo.  
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 06-03-2017 - Se agrega historial a nivel del servicio marcando como fecha de creación la fecha con la cual se realiza el cálculo
     *                           de los meses restantes para facturar el servicio.
     *  
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>
     * @version 1.7 04-05-2017 -Se guarda la Plantilla de Comisionistas asociada a la antigua Razon social en la nueva razon social.
     * Se Cancela Plantilla de comisionistas en la antigua Razon Social
     * Se guarda Historial con fecha y observacion 'Se Confirmo el Servicio' en los servicios de la nueva Razon Social en base a la Fecha 
     * de Activacion o Confirmacion de Servicio de los servicios antiguos. 
     * Se Cancelan las Plantillas de Comisionistas asociadas a los servicios del cliente origen del Cambio de Razon Social
     * Se genera Historial de Cancelacion de Plantilla.     
     *      
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>
     * @version 1.8 20-06-2017 - Se modifica el ingreso de Datos de Envio por Punto.
     * Se debe obtener la informacion de correos y telefonos del contacto de Facturacion del Punto o del cliente en ese orden. 
     * Funcion a llamar es la existente para la generacion del XML (DB_FINANCIERO.FNCK_COM_ELECTRONICO.GET_ADITIONAL_DATA_BYPUNTO)
     * Se debe considerar eliminar duplicidad de registros y solo se registrara un maximo de 2 correos y 2 telefonos separados por ;
     * La informacion del nombre_envio, direccion_envio sera tomados de la nueva Razon Social.
     * La informacion del Sector_id será tomado del Punto Clonado.
     * 
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.9 07-07-2017
     * Actualización: Se modifica envio de parametros en $arrayParametrosValidaCtaTarj a la función validarNumeroTarjetaCta
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 2.0 03-10-2017
     * Se agrega que cuando se realice la aprobación de un contrato por CRS por Login hacia cliente nuevo se asigne la caracteristrica 
     * CICLO_FACTURACION en el nuevo Cliente o cliente destino del cambio de razón social.
     * 
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>
     * @version 2.1 25-06-2018- Se agrega que se generen las caracteristicas de los servicios en estado activo, y se considera para el Producto
     *                          Fox_Primium que al clonar dichas caracteristicas se marque la caracteristica 'MIGRADO_FOX' en S.
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 2.2
     * @since 12-06-2018
     * Se heredan las características de servicios por cambio de razón social.
     *
     * @param array $arrayParams  [estado, idEmpresa, fechaDesde, fechaHasta, idper, oficinaId, nombre, limit, page, start ]
     * @throws \telconet\comercialBundle\Service\Exception
     * @throws \Exception
     * 
     * @author Hector Lozano<hlozano@telconet.ec>
     * @version 2.2
     * @since  13-07-2018
     * Se agrega validación para que no clone las caracteristicas del servicio Netlifecloud cuando se realiza 
     * el Cambio de Razón Social por login. En lugar de clonar se guardan nuevas caracteristicas del servicio
     * Netlifecloud invocando al WebService de Intcomex. 
     * 
     * @author Hector Lozano<hlozano@telconet.ec>
     * @version 3.7
     * @since  25-07-2018
     * Se agrega validación para facturar a los servicios NetlifeCloud cancelados cuando se realiza cambio de Razón Social por login. 
     * 
     * @author Hector Lozano<hlozano@telconet.ec>
     * @version 3.8
     * @since  31-08-2018
     * Se agrega validación para no clonar los descuentos al nuevo cliente cuando se realiza el Cambio de Razón Social.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 3.9 27-12-2018 - Se envia al array que valida las formas de contacto el Nombre y Id Pais.                                
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 4.0
     * @since 21-01-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 4.1 17-02-2019 Se agrega actualización de ldap para servicios Small Business y TelcoHome al aprobar el contrato 
     *                          por cambio de razón social
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 4.2 19-06-2019 Se agrega clonación de solicitudes de Agregar Equipo y Cambio de equipo por soporte en proceso de cambio
     *                         de razón social de puntos de un cliente
     * @since 4.1
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 4.3 09-11-2020 Se modifica el orden de invocación de funciones para no alterar la ejecución del proceso principal y se devuelve
     *                         el mensaje obtenido por los procesos secundarios ya que no debe alterar el funcionamiento de la aprobación de 
     *                         contrato por cambio de razón social
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 4.4 21-04-2021 - Se adapta programación ya usada para W+AP para permitir clonación de solicitudes Extender Dual Band
     *
     * Se obtiene segun el tipo de caracteristica  ENLACE_DATOS o ES_BACKUP  los enlaces (Extremo-Concentrador) o (Backup- Principal) 
     * existentes para un ID_SERVICIO Concentrador o Principal.       
     * Clona la informacion del enlace para asignar el ID_SERVICIO del nuevo CONCENTRADOR  o nuevo PRINCIPAL que fue generado por el cambio de 
     * Razón Social tradicional o por Login segun el caso.
     * Se genera Historial en el servicio Extremo o en servicio BACKUP indicando que se actualiza el enlace por el cambio de Razon Social    
     * Se cancela la caracteristica ENLACE_DATOS o ES_BACKUP que contiene la referencia al servicio CONCENTRADOR  o PRINCIPAL que fue Cancelado
     * por Cambio de Razon Social.
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 4.3 11-11-2020 Se omite el proceso de activar el contrato por la web para megadatos y se lo pasa a microservicio
     * @since 4.2
     * 
     * @author Alex Gomez <algomez@telconet.ec>
     * @version 4.5 10-08-2022 Se modifica estado de los puntos y servicios clonados por CRS tradicional y por punto 
     *                          cuando el contrato aun no ha sido autorizado. Aplica para MD y contrato digital.
     * 
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 4.3 23-10-2022 - Se actualiza el proceso para agregar validaciones para el flujo de cambio de razon social 
     *                             de NetlifeCam Outdoor
     * 
     */
     function procesaAprobContratoCambioRazonSocial($arrayParams)
     {
        $strFechaCreacion    = new \DateTime('now');
        $strNumeroCtaTarjeta = "";
        $strError            = "";  
        $strLogin            = "";
        $strEstadoInactivo   = "Inactivo";
        $strEstadoActivo     = "Activo";
        $this->emcom->getConnection()->beginTransaction();        
        $this->emInfraestructura->getConnection()->beginTransaction();
        $objInfoContrato            = $this->emcom->getRepository('schemaBundle:InfoContrato')->find($arrayParams['id_contrato']);
        $objInfoPersEmpRolProspecto = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                      ->find($objInfoContrato->getPersonaEmpresaRolId()->getId());
        $objInfoPersona             = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                      ->find($objInfoPersEmpRolProspecto->getPersonaId()->getId());             
        $objFormaPago               = $this->emcom->getRepository('schemaBundle:AdmiFormaPago')->find($arrayParams['id_forma_pago']);
        $objContratoFormaPago       = $this->emcom->getRepository('schemaBundle:InfoContratoFormaPago')
                                      ->findOneBy(array("contratoId" => $objInfoContrato->getId()));
        $strTipoMensaje                         = "";
        $arrayServiciosLdap                     = array();
        $arrayServiciosNetlifeCloud             = array();
        $arrayPuntosCRSActivar                  = array();
        $arrayServiciosPreActivo                = array();

        $boolAsignaEstadoPreactivo    = false;
        $strEstadoServicioPreactivo   = '';

        $strMensaje                             = "";
        $arrayParametros['strPrefijoEmpresa']   = $arrayParams ['strPrefijoEmpresa'];
        $arrayParametros['strEmpresaCod']       = $arrayParams['strCodEmpresa'];
        $strAplicaCiclosFac                     = $this->serviceComercial->aplicaCicloFacturacion($arrayParametros);
        $strMensajeCorreoECDF       = "";
        $strTieneCorreoElectronico  = "NO";
        if(isset($arrayParams["tieneCorreoElectronico"]) && !empty($arrayParams["tieneCorreoElectronico"]))
        {
          $strTieneCorreoElectronico = $arrayParams["tieneCorreoElectronico"];
        }

        $arrayPersonaEmpresaRol = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                       ->getPersonaEmpresaRolPorPersonaPorEmpresa($objInfoPersona->getId(), 
                                                                                  $arrayParams['strCodEmpresa']);
            
        foreach($arrayPersonaEmpresaRol as $objPersonaEmpresaRol)
        {
            $objInfoPersonaRepresLegal = $this->emcom->getRepository('schemaBundle:InfoPersonaRepresentante')
                                            ->findOneBy(array('personaEmpresaRolId'       => $objPersonaEmpresaRol->getId(),
                                                              'estado'                    => 'Activo'));

            if(is_object($objInfoPersonaRepresLegal))
            {
                $objInfoPersonaRepresentante = $objInfoPersonaRepresLegal;
            }
        }
         
        try
        {
            //se recupera motivo de cancelacion de servicios
            $objMotivoCambioRs     = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo('Cambio de Razon Social');
            
            // Valido que exista la Caracteristica necesaria que relaciona los logines origen y destino del cambio de razon social 
            // para ejecutar el Proceso
            $objAdmiCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                     ->findOneBy(array("descripcionCaracteristica" => "PUNTO CAMBIO RAZON SOCIAL", "estado" => "Activo"));
            if(!$objAdmiCaracteristica)
            {
                throw new \Exception('No fue posible aprobar el contrato - '
                                   . 'No se encontro registro de la Caracteristica [PUNTO CAMBIO RAZON SOCIAL] requerida para ejecutar el proceso.');
            }                                                                       
           
            if(strtoupper($objFormaPago->getDescripcionFormaPago()) == "DEBITO BANCARIO")
            {
                // Llamo a funcion para validar numero de cuenta/tarjeta
                $arrayParametrosValidaCtaTarj                          = array();
                $arrayParametrosValidaCtaTarj['intTipoCuentaId']       = $arrayParams['id_tipo_cuenta'];
                $arrayParametrosValidaCtaTarj['intBancoTipoCuentaId']  = $arrayParams['bancoTipoCuentaId'];
                $arrayParametrosValidaCtaTarj['strNumeroCtaTarjeta']   = $arrayParams['numeroCtaTarjeta'];
                $arrayParametrosValidaCtaTarj['strCodigoVerificacion'] = $arrayParams['codigoVerificacion'];
                $arrayParametrosValidaCtaTarj['strCodEmpresa']         = $arrayParams['strCodEmpresa'];
                $arrayParametrosValidaCtaTarj['intFormaPagoId']        = $objInfoContrato->getFormaPagoId()->getId();
                $arrayValidaciones = $this->serviceInfoContrato->validarNumeroTarjetaCta($arrayParametrosValidaCtaTarj);
                if($arrayValidaciones)
                {
                    foreach($arrayValidaciones as $key => $mensaje_validaciones)
                    {
                        foreach($mensaje_validaciones as $key_msj => $value)
                        {
                            $strError = $strError . $value . ".\n";
                        }
                    }
                    throw new \Exception("No fue posible aprobar el contrato - " . $strError);
                }
            }
                                   
            // Actualizo informacion de la Persona, Asigno Rol de Cliente a la Persona y Actualizo las formas de Contacto
            if(!$arrayParams['tipoIdentificacion'])
            {
                throw new \Exception('El tipo de Identificacion es un campo obligatorio - No se pudo Aprobar el contrato');
            }
                        
            // Valido Formas de Contacto Ingresadas                
            $arrayFormasContacto = array();
            if($arrayParams['formas_contacto'])
            {
                $array_formas_contacto = explode(',', $arrayParams['formas_contacto']);
                for($i = 0; $i < count($array_formas_contacto); $i+=3)
                {
                    $arrayFormasContacto[] = array('formaContacto' => $array_formas_contacto[$i + 1],
                                                   'valor' => $array_formas_contacto[$i + 2]);
                }
            }
            /* Se envia array de Parametros y se agrega strOpcionPermitida y strPrefijoEmpresa, Prefijo de empresa en sesion para validar
             * que para empresa MD no se obligue el ingreso de al menos 1 correo */
            $arrayParamFormasContac                        = array ();
            $arrayParamFormasContac['strPrefijoEmpresa']   = $arrayParams ['strPrefijoEmpresa'];
            $arrayParamFormasContac['arrayFormasContacto'] = $arrayFormasContacto;
            $arrayParamFormasContac['strOpcionPermitida']  = 'NO';
            $arrayParamFormasContac['strNombrePais']       = $arrayParams['strNombrePais'];
            $arrayParamFormasContac['intIdPais']           = $arrayParams['intIdPais'];
            
            $arrayValidaciones = $this->serviceInfoPersonaFormaContacto->validarFormasContactos($arrayParamFormasContac);
            if($arrayValidaciones)
            {
                foreach($arrayValidaciones as $key => $mensaje_validaciones)
                {
                    foreach($mensaje_validaciones as $key_msj => $value)
                    {
                        $strError = $strError . $value . ".\n";
                    }
                }
                throw new \Exception("No se pudo Aprobar el contrato - " . $strError);
            }
            // Pone en estado Inactivo a todas las formas de Contacto de la Persona que tenga en estado Activo                
            $this->serviceInfoPersonaFormaContacto->inactivarPersonaFormaContactoActivasPorPersona($objInfoPersona->getId(), 
                                                                                                   $arrayParams['strUsrCreacion']);
            // Registra las formas de Contacto del Cliente
            for($i = 0; $i < count($arrayFormasContacto); $i++)
            {
                $objInfoPersonaFormaContacto = new InfoPersonaFormaContacto();
                $objInfoPersonaFormaContacto->setValor($arrayFormasContacto[$i]["valor"]);
                $objInfoPersonaFormaContacto->setEstado($strEstadoActivo);
                $objInfoPersonaFormaContacto->setFeCreacion($strFechaCreacion);

                if(isset($arrayFormasContacto[$i]['idFormaContacto']))
                {
                    $objAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                            ->find($arrayFormasContacto[$i]['idFormaContacto']);
                }
                else
                {
                    $objAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                            ->findPorDescripcionFormaContacto($arrayFormasContacto [$i] ['formaContacto']);
                }

                $objInfoPersonaFormaContacto->setFormaContactoId($objAdmiFormaContacto);
                $objInfoPersonaFormaContacto->setIpCreacion($arrayParams['strClientIp']);
                $objInfoPersonaFormaContacto->setPersonaId($objInfoPersona);
                $objInfoPersonaFormaContacto->setUsrCreacion($arrayParams['strUsrCreacion']);
                $this->emcom->persist($objInfoPersonaFormaContacto);
                $this->emcom->flush();
            }

            
            
            // Asigna nuevo rol de CLIENTE a la persona
            $objInfoPersonaEmpresaRol = new InfoPersonaEmpresaRol();
            $objInfoEmpresaRol        = $this->emcom->getRepository('schemaBundle:InfoEmpresaRol')
                                        ->findPorNombreTipoRolPorEmpresa('Cliente', $arrayParams['strCodEmpresa']);
            if(!$objInfoEmpresaRol)
            {
                throw new \Exception('No encontro Rol de Cliente, para la empresa [' . $arrayParams['strPrefijoEmpresa'] . '] - '
                                     . 'No se pudo Aprobar el contrato');
            }
            $objInfoPersonaEmpresaRol->setEmpresaRolId($objInfoEmpresaRol);
            $objInfoPersonaEmpresaRol->setPersonaId($objInfoPersona);
            $objInfoOficinaGrupo = $this->emcom->getRepository('schemaBundle:InfoOficinaGrupo')->find($arrayParams['intIdOficina']);
            if(!$objInfoOficinaGrupo)
            {
                throw new \Exception('No encontro Oficina [' . $arrayParams['intIdOficina'] . '] para la creacion del Rol del Cliente - '
                                      . 'No se pudo Aprobar el contrato');
            }
            $objInfoPersonaEmpresaRol->setOficinaId($objInfoOficinaGrupo);            
            $objInfoPersonaEmpresaRol->setFeCreacion($strFechaCreacion);
            $objInfoPersonaEmpresaRol->setUsrCreacion($arrayParams['strUsrCreacion']);
            $objInfoPersonaEmpresaRol->setIpCreacion($arrayParams['strClientIp']);
            $objInfoPersonaEmpresaRol->setEstado($strEstadoActivo);
            $objInfoPersonaEmpresaRol->setEsPrepago($objInfoPersEmpRolProspecto->getEsPrepago());
            $this->emcom->persist($objInfoPersonaEmpresaRol);
            $this->emcom->flush();
            
            // SE ASIGNA EL CICLO DE FACTURACION DEL PRE-CLIENTE AL CLIENTE
            if($arrayParams['strPrefijoEmpresa'] == 'MD' || $arrayParams['strPrefijoEmpresa'] == 'EN')
            {
                $arrayParamCiclo                     = array();
                $arrayParamCiclo['intIdPersonaRol']  = $objInfoPersEmpRolProspecto->getId();
                $arrayPersEmpRolCaracCicloPreCliente = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                   ->getCaractCicloFacturacion($arrayParamCiclo);
                if(!isset($arrayPersEmpRolCaracCicloPreCliente['intIdPersonaEmpresaRolCaract']) 
                    && empty($arrayPersEmpRolCaracCicloPreCliente['intIdPersonaEmpresaRolCaract']))
                {
                    throw new \Exception('El Pre-Cliente no posee Ciclo de Facturación asignado - '
                                       . 'No se pudo Aprobar el contrato');                   
                }
                else
                {
                    $objCaracteristicaCiclo = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->find($arrayPersEmpRolCaracCicloPreCliente['intCaracteristicaId']);
                    if(!is_object($objCaracteristicaCiclo))
                    {
                         throw new \Exception('No existe Caracteristica CICLO_FACTURACION - '
                                            . 'No fue posible aprobar el contrato');                         
                    }
                    //Inserto CICLO_FACTURACION del Pre_cliente en el nuevo Cliente
                    $objPersEmpRolCaracCicloCliente = new InfoPersonaEmpresaRolCarac();
                    $objPersEmpRolCaracCicloCliente->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                    $objPersEmpRolCaracCicloCliente->setCaracteristicaId($objCaracteristicaCiclo);
                    $objPersEmpRolCaracCicloCliente->setValor($arrayPersEmpRolCaracCicloPreCliente['strValor']);
                    $objPersEmpRolCaracCicloCliente->setFeCreacion($strFechaCreacion);
                    $objPersEmpRolCaracCicloCliente->setUsrCreacion($arrayParams['strUsrCreacion']);
                    $objPersEmpRolCaracCicloCliente->setIpCreacion($arrayParams['strClientIp']);
                    $objPersEmpRolCaracCicloCliente->setEstado($strEstadoActivo);
                    $this->emcom->persist($objPersEmpRolCaracCicloCliente);

                    //Inserto Historial de creacion de caracteristica de CICLO_FACTURACION en el CLIENTE                
                    $objPersEmpRolCaracCicloHisto = new InfoPersonaEmpresaRolHisto();
                    $objPersEmpRolCaracCicloHisto->setUsrCreacion($arrayParams['strUsrCreacion']);
                    $objPersEmpRolCaracCicloHisto->setFeCreacion($strFechaCreacion);
                    $objPersEmpRolCaracCicloHisto->setIpCreacion($arrayParams['strClientIp']);
                    $objPersEmpRolCaracCicloHisto->setEstado($strEstadoActivo);
                    $objPersEmpRolCaracCicloHisto->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                    $objPersEmpRolCaracCicloHisto->setObservacion('Se generó cambio de Razón Social por Login y se asignó Ciclo de Facturación: '
                        . $arrayPersEmpRolCaracCicloPreCliente['strNombreCiclo']);
                    $this->emcom->persist($objPersEmpRolCaracCicloHisto);
                }
            }
            // Cambia a estado INACTIVO el rol de PROSPECTO $objInfoPersEmpRolProspecto            
            $objInfoPersEmpRolProspecto->setEstado($strEstadoInactivo);
            $this->emcom->persist($objInfoPersEmpRolProspecto);
            $this->emcom->flush();
           
            // Actualizo Informacion de la persona de ser el caso
            $objInfoPersona->setTipoIdentificacion($arrayParams['tipoIdentificacion']);
            
            if($objInfoPersona->getIdentificacionCliente() != $arrayParams['identificacionCliente'])
            {
                $objInfoPersona->setIdentificacionCliente($arrayParams['identificacionCliente']);
            }
            $objInfoPersona->setTipoEmpresa($arrayParams['tipoEmpresa']);
            $objInfoPersona->setTipoTributario($arrayParams['tipoTributario']);
            if($arrayParams['tipoEmpresa'])
            {
                $objInfoPersona->setRazonSocial($arrayParams['razonSocial']);
            }
            else
            {
                $objInfoPersona->setNombres($arrayParams['nombres']);
                $objInfoPersona->setApellidos($arrayParams['apellidos']);
                $objAdmiTitulo = $this->emcom->getRepository('schemaBundle:AdmiTitulo')->find($arrayParams['tituloId']);
                if($objAdmiTitulo)
                {
                    $objInfoPersona->setTituloId($objAdmiTitulo);
                }
                $objInfoPersona->setGenero($arrayParams['genero']);
                $objInfoPersona->setEstadoCivil($arrayParams['estadoCivil']);                
                $objInfoPersona->setOrigenIngresos($arrayParams['origenIngresos']);
                
                // conversion desde el mobile DATE -> ARRAY
                if(!is_array($arrayParams['fechaNacimiento']))
                {
                    $fechaNacimientoObj             = $arrayParams['fechaNacimiento']->format('Y-m-d');
                    $fechaNacimientoArray           = explode('-', $fechaNacimientoObj);
                    $arrayParams['fechaNacimiento'] = array('year'  => $fechaNacimientoArray[0],
                                                            'month' => $fechaNacimientoArray[1],
                                                            'day'   => $fechaNacimientoArray[2]);
                }
                if(!$arrayParams['fechaNacimiento'] 
                    || (!$arrayParams ['fechaNacimiento'] ['year'] 
                        && !$arrayParams ['fechaNacimiento'] ['month'] 
                        && !$arrayParams ['fechaNacimiento'] ['day']))
                {
                    throw new \Exception('La Fecha de Nacimiento es un campo obligatorio -  No se pudo Aprobar el contrato'); 
                }                                                
                else
                {
                    if(is_array($arrayParams ['fechaNacimiento']) &&
                        ($arrayParams ['fechaNacimiento'] ['year'] && 
                        $arrayParams ['fechaNacimiento'] ['month'] && 
                        $arrayParams ['fechaNacimiento'] ['day']))
                    {
                         $intEdad =  $this->servicePreCliente->devuelveEdadPorFecha($arrayParams ['fechaNacimiento'] ['year'] .
                                    '-' . $arrayParams ['fechaNacimiento'] ['month'] .
                                    '-' . $arrayParams ['fechaNacimiento'] ['day']);       
                         if($intEdad<18)
                         {
                             throw new \Exception('La Fecha de Nacimiento ingresada corresponde a un menor de edad - '
                                 . 'No se pudo Aprobar el contrato : '.$arrayParams ['fechaNacimiento'] ['year'] .
                                    '-' . $arrayParams ['fechaNacimiento'] ['month'] .
                                    '-' . $arrayParams ['fechaNacimiento'] ['day']); 
                         }
                    }
                }
                
                if($arrayParams ['fechaNacimiento'] instanceof \DateTime)
                {
                    $objInfoPersona->setFechaNacimiento($arrayParams['fechaNacimiento']);
                }
                else if(is_array($arrayParams ['fechaNacimiento']))
                {
                    if($arrayParams ['fechaNacimiento'] ['year'] &&
                        $arrayParams ['fechaNacimiento'] ['month'] &&
                        $arrayParams ['fechaNacimiento'] ['day'])
                    {
                        $objInfoPersona->setFechaNacimiento(date_create($arrayParams ['fechaNacimiento'] ['year'] . '-' .
                                $arrayParams ['fechaNacimiento'] ['month'] . '-' .
                                $arrayParams ['fechaNacimiento'] ['day']));
                    }
                }
            }
            $objInfoPersona->setRepresentanteLegal($arrayParams['representanteLegal']);
            $objInfoPersona->setNacionalidad($arrayParams['nacionalidad']);
            $objInfoPersona->setDireccionTributaria($arrayParams['direccionTributaria']);
            $objInfoPersona->setOrigenProspecto('S');
            $objInfoPersona->setEstado($strEstadoActivo);
            if($arrayParams ['strPrefijoEmpresa'] == 'TN')
            {             
                $objInfoPersona->setContribuyenteEspecial($arrayParams ['contribuyenteEspecial']);
                $objInfoPersona->setPagaIva($arrayParams ['pagaIva']);   
                if( $arrayParams ['tieneCarnetConadis'] == 'S')
                {
                    $objInfoPersona->setNumeroConadis($arrayParams ['numeroConadis']); 
                }
                else
                {
                    $objInfoPersona->setNumeroConadis(null); 
                }
            }
       
            $this->emcom->persist($objInfoPersona);
            $this->emcom->flush();
            
            // Actualiza el personaEmpresaRol de Cliente 
            $objContrato = $this->emcom->getRepository('schemaBundle:InfoContrato')
                           ->findByPersonaEmpresaRolId($objInfoPersEmpRolProspecto->getId());
            foreach($objContrato as $objContrato)
            {
                $objContrato->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                $this->emcom->persist($objContrato);
                $this->emcom->flush();
            }   
            
            if($objInfoContrato->getEstado() != 'PorAutorizar')
            {
            // Activa el contrato            
                $objInfoContrato->setEstado($strEstadoActivo);
                $objInfoContrato->setUsrAprobacion($arrayParams['strUsrCreacion']);
                $objInfoContrato->setFeAprobacion($strFechaCreacion);
                $this->emcom->persist($objInfoContrato);
                $this->emcom->flush();   
                
                // Actualizo la forma de pago de ser el caso
                if(($objContratoFormaPago && strtoupper($objFormaPago->getDescripcionFormaPago()) == "DEBITO BANCARIO") ||
                (!$objContratoFormaPago && strtoupper($objFormaPago->getDescripcionFormaPago()) == "DEBITO BANCARIO"))
                {
                    if($objContratoFormaPago)
                    {                    
                        $objContratoFormaPago->setUsrUltMod($arrayParams['strUsrCreacion']);
                        $objContratoFormaPago->setFeUltMod($strFechaCreacion);
                    }
                    else
                    {
                        $objContratoFormaPago = new InfoContratoFormaPago();
                        $objContratoFormaPago->setContratoId($objInfoContrato);                      
                        $objContratoFormaPago->setUsrCreacion($arrayParams['strUsrCreacion']);
                        $objContratoFormaPago->setFeCreacion($strFechaCreacion);
                    }
                    $objContratoFormaPago->setEstado($strEstadoActivo);
                    $objAdmiBancoTipoCuenta = $this->emcom->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                                            ->find($arrayParams['bancoTipoCuentaId']);

                    if($objAdmiBancoTipoCuenta->getEsTarjeta() == 'S')
                    {
                        if(!$arrayParams['mesVencimiento'] || !$arrayParams['anioVencimiento'])
                        {
                            throw new \Exception('No fue posible aprobar el contrato - '
                            . 'El Anio y mes de Vencimiento de la tarjeta es un campo obligatorio');
                        }
                        if(!$arrayParams['codigoVerificacion'])
                        {
                            throw new \Exception('No fue posible aprobar el contrato - '
                            . 'El codigo de verificacion de la tarjeta es un campo obligatorio');
                        }
                    }

                    $objContratoFormaPago->setBancoTipoCuentaId($objAdmiBancoTipoCuenta);
                    $objAdmiTipoCuenta = $this->emcom->getRepository('schemaBundle:AdmiTipoCuenta')->find($arrayParams['id_tipo_cuenta']);
                    $objContratoFormaPago->setTipoCuentaId($objAdmiTipoCuenta);

                    if(!$arrayParams['numeroCtaTarjeta'])
                    {
                        throw new \Exception('El Numero de Cuenta / Tarjeta es un campo obligatorio - No se pudo Aprobar el contrato');
                    }
                    // Llamo a funcion que realiza encriptado del numero de cuenta
                    $strNumeroCtaTarjeta = $this->serviceCrypt->encriptar($arrayParams['numeroCtaTarjeta']);
                    if($strNumeroCtaTarjeta)
                    {
                        $objContratoFormaPago->setNumeroCtaTarjeta($strNumeroCtaTarjeta);
                    }
                    else
                    {
                        throw new \Exception('No se pudo Aprobar el contrato, no fue posible guardar el numero de cuenta/tarjeta : ' 
                                            . $arrayParams['numeroCtaTarjeta']);
                    }
                    if(!$arrayParams['titularCuenta'])
                    {
                        throw new \Exception('No fue posible aprobar el contrato - El Titular de Cuenta es un campo obligatorio');
                    }
                    $objContratoFormaPago->setTitularCuenta($arrayParams['titularCuenta']);
                    if($objAdmiBancoTipoCuenta->getEsTarjeta() == 'S')
                    {
                        $objContratoFormaPago->setMesVencimiento($arrayParams['mesVencimiento']);
                        $objContratoFormaPago->setAnioVencimiento($arrayParams['anioVencimiento']);
                        $objContratoFormaPago->setCodigoVerificacion($arrayParams['codigoVerificacion']);
                    }               
                    $this->emcom->persist($objContratoFormaPago);
                    $this->emcom->flush();
                }
                elseif(($objContratoFormaPago) && ((strtoupper($objFormaPago->getDescripcionFormaPago()) == "EFECTIVO") ||
                    (strtoupper($objFormaPago->getDescripcionFormaPago()) == "CHEQUE") ||
                    (strtoupper($objFormaPago->getDescripcionFormaPago()) == "RECAUDACION")))
                {
                    $objContratoFormaPago->setEstado("Inactivo");
                    $objContratoFormaPago->setUsrUltMod($arrayParams['strUsrCreacion']);
                    $objContratoFormaPago->setFeUltMod($strFechaCreacion);
                    $this->emcom->persist($objContratoFormaPago);
                    $this->emcom->flush();
                }
            }
            // Registra en el Historial el estado convertido del registro del Prospecto
            $objInfoPersEmpRolHistoProsp = new InfoPersonaEmpresaRolHisto();
            $objInfoPersEmpRolHistoProsp->setEstado('Convertido');
            $objInfoPersEmpRolHistoProsp->setFeCreacion($strFechaCreacion);
            $objInfoPersEmpRolHistoProsp->setIpCreacion($arrayParams['strClientIp']);
            $objInfoPersEmpRolHistoProsp->setPersonaEmpresaRolId($objInfoPersEmpRolProspecto);
            $objInfoPersEmpRolHistoProsp->setUsrCreacion($arrayParams['strUsrCreacion']);
            $this->emcom->persist($objInfoPersEmpRolHistoProsp);
            $this->emcom->flush();

            // Registra en el Historial el estado Activo del registro del Cliente 
            $objInfoPersEmpRolHistoCli = new InfoPersonaEmpresaRolHisto();
            $objInfoPersEmpRolHistoCli->setEstado($strEstadoActivo);
            $objInfoPersEmpRolHistoCli->setFeCreacion($strFechaCreacion);
            $objInfoPersEmpRolHistoCli->setIpCreacion($arrayParams['strClientIp']);
            $objInfoPersEmpRolHistoCli->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
            $objInfoPersEmpRolHistoCli->setUsrCreacion($arrayParams['strUsrCreacion']);
            $this->emcom->persist($objInfoPersEmpRolHistoCli);
            $this->emcom->flush();
            
            //Clono Contactos Activos a nivel de Cliente hacia la nueva razon social
            $arrayContactos = $this->emcom->getRepository('schemaBundle:InfoPersonaContacto')
                                   ->findByPersonaEmpresaRolIdYEstado($objInfoPersEmpRolProspecto->getId(),$strEstadoActivo);
            if($arrayContactos) 
            {
                foreach($arrayContactos as $contacto)
                {
                    $objContactoClonado = clone $contacto;
                    $objContactoClonado->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                    $objContactoClonado->setFeCreacion($strFechaCreacion);
                    $objContactoClonado->setIpCreacion($arrayParams['strClientIp']);
                    $objContactoClonado->setUsrCreacion($arrayParams['strUsrCreacion']);
                    $this->emcom->persist($objContactoClonado);
                    $this->emcom->flush();
                }
            }
            
            // Obtengo Los Puntos Origenes del Cambio de Razon Social que seran Clonados con sus servicios
            if(($arrayParams['strCodEmpresa'] == '18' || $arrayParams['strCodEmpresa'] == '33')
                && $objInfoContrato->getEstado() == 'PorAutorizar')
            {
                $objDatosPuntos = array();
                $objPuntosClie  = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                              ->findBy(
                                                  array('personaEmpresaRolId' => $objInfoPersEmpRolProspecto->getId(),
                                                        'estado'              => array('Pendiente', 'Activo')
                                                ));   
                
                foreach($objPuntosClie as $objPunto)
                {
                    array_push($objDatosPuntos,array('id' => $objPunto->getId()));
                }
            }
            else
            {
                $arrayResultado = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                              ->getPuntosAprobCambioRazonSocial($objInfoPersEmpRolProspecto->getId(), 
                                                                                0, 9999999, $strEstadoActivo);   
                                              
                // Recorro arreglo, Clono los nuevos puntos y sus servicios                   
                $objDatosPuntos = $arrayResultado['registros'];
            }
            
            // Valido que existan las referencias de los logines que ejecutaran el cambio de Razon social
            if(count($objDatosPuntos)==0)
            {
                throw new \Exception('No se encontro los Logines Origen del Cambio de Razon Social, - '
                                     . 'No se pudo Aprobar el contrato');
            }
            
            //Consulta nuevo estado para servicios creados por Cambio de Razón Social por Login
            //previo a la autorizacion del contrato
            if($arrayParams['strPrefijoEmpresa'] === 'MD' || $arrayParams['strPrefijoEmpresa'] === 'EN')
            {
                $boolAsignaEstadoPreactivo = true;

                $arrayEstadosServicios = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne(
                                                            'ESTADOS_CAMBIO_RAZON_SOCIALXPUNTO',
                                                            'COMERCIAL',
                                                            'CAMBIO_RAZON_SOCIAL_POR_PUNTO',
                                                            '','','','','','',
                                                            $arrayParams['strCodEmpresa']);
                
                if(isset($arrayEstadosServicios) && !empty($arrayEstadosServicios))
                {
                    $strEstadoServicioPreactivo = $arrayEstadosServicios["valor1"];
                }
                else
                {
                    $boolAsignaEstadoPreactivo = false;
                }
            }

            foreach($objDatosPuntos as $objDatosPuntos)
            {                
                
                
                if(($arrayParams['strCodEmpresa'] == '18' || $arrayParams['strCodEmpresa'] == '33') 
                    && $objInfoContrato->getEstado() == 'PorAutorizar')
                {
                    $objInfoPuntoClonado  = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                                        ->find($objDatosPuntos['id']);
                    $strObservacionPuntoClonado = $objInfoPuntoClonado->getObservacion();
                    if(!empty($strObservacionPuntoClonado))
                    {
                        $objInfoPuntoOrigen = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                                       ->findOneById($strObservacionPuntoClonado);

                        array_push($arrayPuntosCRSActivar,$objInfoPuntoClonado->getId());

                    }
                    else
                    {
                        throw new \Exception("Error no tiene asociado el anterior punto");
                    }
                    
                }
                else
                {
                    $strLogin            = "";
                    $objInfoPuntoOrigen  = $this->emcom->getRepository('schemaBundle:InfoPunto')->find($objDatosPuntos['id']);
                    $objInfoPuntoClonado = new InfoPunto();
                    $objInfoPuntoClonado = clone $objInfoPuntoOrigen;
                    $objInfoPuntoClonado->setFeCreacion($strFechaCreacion);
                    $objInfoPuntoClonado->setUsrCreacion($arrayParams['strUsrCreacion']);
                    $objInfoPuntoClonado->setObservacion('');
                    // Obtengo Login con secuencia
                    $arrayPuntos = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                ->findPtosPorEmpresaPorCanton($arrayParams['strCodEmpresa'], $objInfoPuntoClonado->getLogin(),
                                    $objInfoPuntoClonado->getSectorId()->getParroquiaId()->getCantonId()->getId(), 9999999, 1, 0);

                    $strLogin    = $objInfoPuntoClonado->getLogin() . ($arrayPuntos['total'] + 1);
                    $objInfoPuntoClonado->setLogin($strLogin);
                    $this->emcom->persist($objInfoPuntoClonado);
                    $this->emcom->flush();
                }
                
                /*El cambio se realizaria desde el MS?
                //Cambio de estado Pendiente por Activo por CRS por login
                if($boolAsignaEstadoPreactivo == true && $objInfoPuntoClonado->getEstado() === 'Pendiente')
                {
                    $objInfoPuntoClonado->setEstado('Activo');
                }
                */
                $objInfoPuntoClonado->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                


                
                //Clono Contactos a nivel de Punto hacia los Logines de la nueva razon social
                $arrayPuntoContactos = $this->emcom->getRepository('schemaBundle:InfoPuntoContacto')
                                            ->findByPuntoIdYEstado($objInfoPuntoOrigen->getId(),$strEstadoActivo);
                if($arrayPuntoContactos)
                {
                    foreach($arrayPuntoContactos as $contactoPto)
                    {
                        $objContactoPtoClonado = clone $contactoPto;
                        $objContactoPtoClonado->setPuntoId($objInfoPuntoClonado);
                        $objContactoPtoClonado->setFeCreacion($strFechaCreacion);
                        $objContactoPtoClonado->setIpCreacion($arrayParams['strClientIp']);
                        $objContactoPtoClonado->setUsrCreacion($arrayParams['strUsrCreacion']);
                        $this->emcom->persist($objContactoPtoClonado);
                        $this->emcom->flush();
                    }
                }

                $arrayParamDatoAdic                        = array();
                $arrayParamDatoAdic['objInfoPuntoClonado'] = $objInfoPuntoClonado;
                $arrayParamDatoAdic['objInfoPersona']      = $objInfoPersona;
                $arrayParamDatoAdic['strUsrCreacion']      = $arrayParams['strUsrCreacion'];
                $arrayParamDatoAdic['intIdPunto']          = $objInfoPuntoOrigen->getId();
                $arrayParamDatoAdic['strTipoCrs']          = 'Cambio_Razon_Social_Por_Login';                
                $arrayParamDatoAdic['arrayFormasContacto'] = $arrayFormasContacto;
                $objInfoPuntoDatoAdicionalClonado = $this->serviceInfoPunto->generarInfoPuntoDatoAdicional($arrayParamDatoAdic);

                // Obtengo los servicios Ligados al Punto que seran trasladados
                if(($arrayParams['strCodEmpresa'] == '18' || $arrayParams['strCodEmpresa'] == '33') 
                    && $objInfoContrato->getEstado() == 'PorAutorizar')
                {

                    ////Aqui va elnuevo estado
                    $objInfoServicio  = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                             ->findBy(array(
                                                        'puntoId' => $objInfoPuntoClonado->getId(),
                                                        'estado'  => array("Activo",$strEstadoServicioPreactivo)
                                                    ));
                }
                else
                {
                    // Obtengo los servicios Ligados al Punto que seran trasladados
                    $arrayInfoServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                        ->findServiciosPorEmpresaPorPunto($arrayParams['strCodEmpresa'], $objDatosPuntos['id'], 99999999, 1, 0);
                    $objInfoServicio   = $arrayInfoServicio['registros'];
                }
                
                foreach($objInfoServicio as $objServ)
                {
                    $strEstadoServicioAnterior      = "Cancel";
                    $strObservacionServicioAnterior = "Cancelado por cambio de razon social por login";
                    $strEstadoSpcAnterior           = "Cancelado";
                    $strContinuaFlujoWyAp           = "NO";
                    $strEjecutaCreacionSolWyAp      = "NO";
                    $strContinuaFlujoEdb            = "NO";
                    $strEjecutaCreacionSolEdb       = "NO";
                    $strEjecutaFlujoNormal          = "SI";

                    //La activación se realizaría desde el ms
                    ///Los Servicios PreActivo se pasan a Activo para que el flujo continuen sin ninguna novedad
                    if($boolAsignaEstadoPreactivo && $objServ->getEstado() == $strEstadoServicioPreactivo)
                    {
                        array_push($arrayServiciosPreActivo, $objServ->getId());
                        $objServ->setEstado("Activo");
                        $this->emcom->persist($objServ);
                        $this->emcom->flush();
                    }
                    

                    $strEjecutaCreacionSolPlan      = "NO";
                    $strContinuaFlujoCAM            = "NO";
                    $boolProductoNetlifeCam         = false;
                    if( isset($arrayParams['strPrefijoEmpresa']) && !empty($arrayParams['strPrefijoEmpresa'])
                        && $arrayParams ['strPrefijoEmpresa'] == 'MD' && is_object($objServ->getProductoId()))
                    {
                        if($objServ->getProductoId()->getNombreTecnico() === "WDB_Y_EDB")
                        {
                            $strContinuaFlujoWyAp           = "SI";
                            $arrayEstadoPermitidoCRSWdbyEdb = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                              ->getOne( 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD', 
                                                                                        '', 
                                                                                        '', 
                                                                                        '',
                                                                                        'CAMBIO_RAZON_SOCIAL',
                                                                                        'ESTADOS_SERVICIOS_X_PROD_FLUJO_PERSONALIZADO',
                                                                                        'WDB_Y_EDB',
                                                                                        $objServ->getEstado(),
                                                                                        '',
                                                                                        $arrayParams['strCodEmpresa']);
                            if(isset($arrayEstadoPermitidoCRSWdbyEdb) && !empty($arrayEstadoPermitidoCRSWdbyEdb))
                            {
                                $strEjecutaFlujoNormal          = "NO";
                                $strEjecutaCreacionSolWyAp      = "SI";
                                $strEstadoServicioPorCRS        = $arrayEstadoPermitidoCRSWdbyEdb['valor5'];
                                $strEstadoServicioAnterior      = "Eliminado";
                                $strObservacionServicioAnterior = "Eliminado por cambio de razón social";
                                $strEstadoSpcAnterior           = "Eliminado";
                            }
                            else
                            {
                                $strEjecutaFlujoNormal      = "SI";
                                $strEstadoServicioPorCRS    = $objServ->getEstado();
                            }
                        }
                        else if($objServ->getProductoId()->getNombreTecnico() === "EXTENDER_DUAL_BAND")
                        {
                            $strContinuaFlujoEdb        = "SI";
                            $arrayEstadoPermitidoCRSEdb = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                              ->getOne( 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD', 
                                                                                        '', 
                                                                                        '', 
                                                                                        '',
                                                                                        'CAMBIO_RAZON_SOCIAL',
                                                                                        'ESTADOS_SERVICIOS_X_PROD_FLUJO_PERSONALIZADO',
                                                                                        'EXTENDER_DUAL_BAND',
                                                                                        $objServ->getEstado(),
                                                                                        '',
                                                                                        $arrayParams['strCodEmpresa']);
                            if(isset($arrayEstadoPermitidoCRSEdb) && !empty($arrayEstadoPermitidoCRSEdb))
                            {
                                $strEjecutaFlujoNormal          = "NO";
                                $strEjecutaCreacionSolEdb       = "SI";
                                $strEstadoServicioPorCRS        = $arrayEstadoPermitidoCRSEdb['valor5'];
                                $strEstadoServicioAnterior      = "Eliminado";
                                $strObservacionServicioAnterior = "Eliminado por cambio de razón social";
                                $strEstadoSpcAnterior           = "Eliminado";
                            }
                            else
                            {
                                $strEjecutaFlujoNormal      = "SI";
                                $strEstadoServicioPorCRS    = $objServ->getEstado();
                            }
                        }
                        else if($objServ->getProductoId()->getNombreTecnico() === "ECDF")
                        {
                            $strEjecutaFlujoNormal      = "SI";
                            $strEstadoServicioPorCRS    = $objServ->getEstado();
                            if($arrayParams['strCodEmpresa'] == '18'  && $objInfoContrato->getEstado() == 'PorAutorizar')
                            {
                                $intIdServicioAnteriorECDF = $objServ->getObservacion();
                                $objServECDF       = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                              ->findOneById($intIdServicioAnteriorECDF);
                                                
                            }
                            else 
                            {
                                $objServECDF = $objServ;
                            }
                            $objServProdCaractCorreoPendienteCRS = $this->serviceServicioTecnico
                                                                        ->getServicioProductoCaracteristica(
                                                                            $objServECDF,
                                                                            'CORREO ELECTRONICO',
                                                                            $objServECDF->getProductoId(),
                                                                            array('strEstadoSpc' => 'tmpPendienteCRS'));
                            if(!is_object($objServProdCaractCorreoPendienteCRS))
                            {
                                $strTieneCorreoElectronico  = "NO";
                                $strEstadoServicioPorCRS    = "Pendiente";
                            }
                            else
                            {
                                $strTieneCorreoElectronico  = "SI";
                                $objServProdCaractCorreo = $this->serviceServicioTecnico
                                                                ->getServicioProductoCaracteristica($objServECDF,
                                                                    'CORREO ELECTRONICO',
                                                                    $objServECDF->getProductoId(),
                                                                    array('strEstadoSpc' => 'Activo'));
                                if(is_object($objServProdCaractCorreo))
                                {
                                    $strCorreoAnterior = $objServProdCaractCorreo->getValor();
                                    $strCorreoNuevo    = $objServProdCaractCorreoPendienteCRS->getValor();
                                    if((!isset($strCorreoAnterior) || empty($strCorreoAnterior)))
                                    {
                                        throw new \Exception("El cliente anterior no cuenta un correo eléctrónico de suscripción");
                                    }
                                    // Ejecutar WS del canal del futbol para actualizar el correo antiguo
                                    $objInfoPersonaAsigna = $this->emcom->getRepository("schemaBundle:InfoPersona")
                                    ->findOneByLogin($arrayParams["strUsrCreacion"]);
    
                                    if(!is_object($objInfoPersonaAsigna) 
                                    || !in_array($objInfoPersonaAsigna->getEstado(), array('Activo','Pendiente','Modificado')))
                                     {
                                          throw new \Exception('El usuario de creación no existe en telcos o no se encuentra Activo.');
                                     }
                                    $strUsuarioAsigna  = $objInfoPersonaAsigna->getNombres()." ".$objInfoPersonaAsigna->getApellidos();
    
                                    $arrayParametrosECDF["email_old"]              = $strCorreoAnterior;
                                    $arrayParametrosECDF["email_new"]              = $strCorreoNuevo;
                                    $arrayParametrosECDF["usrCreacion"]            = $arrayParams["strUsrCreacion"];
                                    $arrayParametrosECDF["ipCreacion"]             = $arrayParams["strClientIp"];
                                    $arrayParametrosECDF['strLoginOrigen']         = $objInfoPuntoOrigen->getLogin();
                                    $arrayParametrosECDF['strLoginDestino']        = $strLogin;
                                    $arrayParametrosECDF['intIdEmpresa']           = $arrayParams["strCodEmpresa"];
                                    $arrayParametrosECDF['strPrefijoEmpresa']      = $arrayParams['strPrefijoEmpresa'];
                                    $arrayParametrosECDF['strUsuarioAsigna']       = $strUsuarioAsigna;
                                    $arrayParametrosECDF['intIdPersonaEmpresaRol'] = $arrayParams['intIdPersonEmpRolEmpl'];
                                    $arrayParametrosECDF['intPuntoId']             = $objInfoPuntoClonado->getId();
                                    $arrayParametrosECDF['boolCrearTarea']         = true;
                                    $arrayParametrosECDF['objServicio']            = $objServ;
                                    $arrayParametrosECDF['identificacionCliente']  = $arrayParams['identificacionCliente'];
    
                                    $arrayResultado  = $this->serviceFoxPremium->actualizarCorreoECDF($arrayParametrosECDF);
                                    if($arrayResultado['mensaje'] != 'ok')
                                    {
                                          $strMensajeCorreoECDF     = "<br />".$arrayResultado['mensaje'];
                                          $strEstadoServicioPorCRS  = "Pendiente";
                                    }
                                }
                                else 
                                {
                                    throw new \Exception("El cliente anterior no cuenta un correo eléctrónico de suscripción");
                                }
                            }
                        }
                        else if($objServ->getProductoId()->getNombreTecnico() === "NETLIFECAM OUTDOOR")
                        {   
                            $boolProductoNetlifeCam = true;
                            $strContinuaFlujoCAM = "SI";
                            $arrayEstadoPermitidoCRSCAM = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne(   'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    'CAMBIO_RAZON_SOCIAL',
                                                                                    'ESTADOS_SERVICIOS_X_PROD_FLUJO_PERSONALIZADO',
                                                                                    'NETLIFECAM OUTDOOR',
                                                                                    $objServ->getEstado(),
                                                                                    '',
                                                                                    $arrayParams['strCodEmpresa']);
                            
                            
                            
                            
                            if(isset($arrayEstadoPermitidoCRSCAM) && !empty($arrayEstadoPermitidoCRSCAM)) 
                            {
                                $strEjecutaFlujoNormal      = "NO";
                                $strEjecutaCreacionSolPlan  = "SI";
                                $strEstadoServicioPorCRS    = $arrayEstadoPermitidoCRSCAM['valor5'];
                                $strEstadoServicioAnterior      = "Eliminado";
                                $strObservacionServicioAnterior = "Eliminado por cambio de razón social";
                                $strEstadoSpcAnterior           = "Eliminado";
                            }
                            else
                            {
                                $strEjecutaFlujoNormal  = "SI";
                                $strEstadoServicioPorCRS = $objServ->getEstado();
                            }
                        }
                        else
                        {
                            $strEjecutaFlujoNormal      = "SI";
                            $strEstadoServicioPorCRS    = $objServ->getEstado();
                        }
                    }
                    else
                    {
                        $strEjecutaFlujoNormal      = "SI";
                        $strEstadoServicioPorCRS    = $objServ->getEstado();
                    }
                            
                            
                    if($objServ->getEstado() == 'Activo' || $strContinuaFlujoWyAp === "SI"  || $strContinuaFlujoEdb === "SI"
                        || $strContinuaFlujoCAM === "SI")
                    {
                        if(($arrayParams['strCodEmpresa'] == '18' || $arrayParams['strCodEmpresa'] == '33') 
                            && $objInfoContrato->getEstado() == 'PorAutorizar')
                        {
                            $objInfoServicioClonado  = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                                   ->findOneById($objServ->getId());

                            $intIdServicioAnterior = $objServ->getObservacion();
                            $objServ               = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                          ->findOneById($intIdServicioAnterior);
                            if (is_object($objServ->getProductoId())
                            && $objServ->getProductoId()->getNombreTecnico() === "ECDF")
                            {
                                $objInfoServicioClonado->setEstado($strEstadoServicioPorCRS);
                                $this->emcom->persist($objInfoServicioClonado);
                                $this->emcom->flush();
                            }
                        }
                        else
                        {
                            $objInfoServicioClonado = new InfoServicio();
                            $objInfoServicioClonado = clone $objServ;
                            $objInfoServicioClonado->setFeCreacion($strFechaCreacion);
                            $objInfoServicioClonado->setUsrCreacion($arrayParams['strUsrCreacion']);
                            $objInfoServicioClonado->setPuntoId($objInfoPuntoClonado);
                            $objInfoServicioClonado->setPuntoFacturacionId($objInfoPuntoClonado);
                            if( $arrayParams['strPrefijoEmpresa'] != 'TN')
                            {   
                                $objInfoServicioClonado->setPorcentajeDescuento(0);
                                $objInfoServicioClonado->setValorDescuento(null);
                                $objInfoServicioClonado->setDescuentoUnitario(null);
                            }
                            $objInfoServicioClonado->setEstado($strEstadoServicioPorCRS);
                            $this->emcom->persist($objInfoServicioClonado);
                            $this->emcom->flush();
                        }
                    
                        if($arrayParams['strPrefijoEmpresa'] == 'MD' && $strEjecutaFlujoNormal === "SI")
                        {
                            $objAdmiCicloOrigen = $this->emcom->getRepository("schemaBundle:AdmiCiclo")
                                                              ->find($arrayPersEmpRolCaracCicloPreCliente['strValor']);

                            $arrayParametros = array("strAplicaCiclosFac" => $strAplicaCiclosFac,
                                                     "objServicioOrigen"  => $objServ,
                                                     "objServicioDestino" => $objInfoServicioClonado,
                                                     "objAdmiCicloOrigen" => $objAdmiCicloOrigen,
                                                     "strUsrCreacion"     => $arrayParams['strUsrCreacion'],
                                                     "strIpCreacion"      => $arrayParams['strClientIp']);
                            $arrayRespuesta = $this->serviceInfoServicio->crearServicioCaracteristicaPorCRS($arrayParametros);
                            if($arrayRespuesta["strEstado"] != "OK")
                            {
                                throw new \Exception("Error al procesar el Cambio de Razón Social: " . $arrayRespuesta["strMensaje"]);
                            }
                        }
                        /**
                         * Bloque que genera un historial en el servicio con la fecha con la cual se realiza el cálculo de meses restantes
                         */
                        $intFrecuenciaProducto = $objServ->getFrecuenciaProducto() ? $objServ->getFrecuenciaProducto() : 0;

                        if( isset($arrayParams['strPrefijoEmpresa']) && $arrayParams['strPrefijoEmpresa'] == 'TN' && $intFrecuenciaProducto > 1 
                            && is_object($objInfoServicioClonado) )
                        {
                            $intIdServicioAntiguo = $objServ->getId() ? $objServ->getId() : 0;
                            $intMesesRestantes    = $objServ->getMesesRestantes() ? $objServ->getMesesRestantes() : 0;

                            $arrayParametrosGenerarHistorialReinicioConteo = array('intIdServicioAntiguo' => $intIdServicioAntiguo,
                                                                                   'objServicioNuevo'     => $objInfoServicioClonado,
                                                                                   'strPrefijoEmpresa'    => $arrayParams['strPrefijoEmpresa'],
                                                                                   'strUsrCreacion'       => $arrayParams['strUsrCreacion'],
                                                                                   'intMesesRestantes'    => $intMesesRestantes);

                            $this->serviceInfoServicio->generarHistorialReinicioConteo($arrayParametrosGenerarHistorialReinicioConteo);
                        }//( isset($arrayParams['strPrefijoEmpresa']) && $arrayParams['strPrefijoEmpresa'] == 'TN' && $intFrecuenciaProducto > 1...
                        
                        if($strEjecutaFlujoNormal === "SI")
                        {
                            $arrayParametrosFechaAct = array('emFinanciero'  => $this->emFinanciero,
                                                             'intIdServicio' => $objServ->getId()
                            );
                            // Obtengo la fecha de confirmacion del servicio del cliente origen del cambio de razon social
                            $strFechaActivacion = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                              ->getFechaActivacionServicio($arrayParametrosFechaAct);

                            if(isset($strFechaActivacion) && !empty($strFechaActivacion))
                            {
                                // Guardo Historial con fecha y observacion 'Se Confirmo el Servicio' en los servicios origenes del Cambio de 
                                // Razon Social
                                $objServicioHistorial = new InfoServicioHistorial();
                                $objServicioHistorial->setServicioId($objInfoServicioClonado);
                                $objServicioHistorial->setFeCreacion(new \DateTime($strFechaActivacion));
                                $objServicioHistorial->setUsrCreacion($arrayParams['strUsrCreacion']);
                                $objServicioHistorial->setEstado($objInfoServicioClonado->getEstado());
                                $objServicioHistorial->setAccion('confirmarServicio');
                                $objServicioHistorial->setObservacion('Se Confirmó el Servicio por Cambio de razón social por login');
                                $this->emcom->persist($objServicioHistorial);

                                if ($boolProductoNetlifeCam)
                                {   
                                    $objAdmiParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy(array('nombreParametro' => 'PROYECTO NETLIFECAM', 
                                                                    'estado'          => 'Activo'));
                                    if(is_object($objAdmiParametroCab))
                                    {
                                        $objAdmiParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                        'descripcion' => 'PARAMETROS NETLIFECAM OUTDOOR',
                                                                        'valor1'      => 'CAMBIO RAZON SOCIAL',
                                                                        'empresaCod'  => $arrayParams["strCodEmpresa"],
                                                                        'estado'      => 'Activo'));
                                        $strAccionHistOrigen = $objAdmiParametroDet->getValor2();
                                        $strObserHistOrigen = $objAdmiParametroDet->getValor3();   
                                    }
                                    $objServicioHistorial = new InfoServicioHistorial();
                                    $objServicioHistorial->setServicioId($objInfoServicioClonado);
                                    $objServicioHistorial->setFeCreacion(new \DateTime($strFechaActivacion));
                                    $objServicioHistorial->setUsrCreacion($arrayParams['strUsrCreacion']);
                                    $objServicioHistorial->setEstado($objInfoServicioClonado->getEstado()); 
                                    $objServicioHistorial->setAccion($strAccionHistOrigen);
                                    $objServicioHistorial->setObservacion($strObserHistOrigen); 
                                    $this->emcom->persist($objServicioHistorial);
                                }   
                                
                            }
                        }
                        $objInfoServicioHistorial = new InfoServicioHistorial();
                        $objInfoServicioHistorial->setServicioId($objInfoServicioClonado);
                        $objInfoServicioHistorial->setFeCreacion($strFechaCreacion);
                        $objInfoServicioHistorial->setUsrCreacion($arrayParams['strUsrCreacion']);
                        $objInfoServicioHistorial->setEstado($objInfoServicioClonado->getEstado());
                        $objInfoServicioHistorial->setObservacion('Creado por Cambio de razon social por login, Login Origen:' .
                                                                  $objInfoPuntoOrigen->getLogin());
                        $this->emcom->persist($objInfoServicioHistorial);
                        $this->emcom->flush();
                        if ($boolProductoNetlifeCam && $strEjecutaFlujoNormal === "NO")
                        {   
                            $objAdmiParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneBy(array('nombreParametro' => 'PROYECTO NETLIFECAM', 
                                                            'estado'          => 'Activo'));
                            if(is_object($objAdmiParametroCab))
                            {
                                $objAdmiParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                'descripcion' => 'PARAMETROS NETLIFECAM OUTDOOR',
                                                                'valor1'      => 'CAMBIO RAZON SOCIAL',
                                                                'empresaCod'  => $arrayParams["strCodEmpresa"],
                                                                'estado'      => 'Activo'));
                                $strAccionHistOrigen = $objAdmiParametroDet->getValor2();
                                $strObserHistOrigen = $objAdmiParametroDet->getValor3();   
                            }
    
                            $arrayParametrosFechaAct = array('emFinanciero'  => $this->emFinanciero,
                                                            'intIdServicio' => $objServ->getId());
                            $strFechaActivacionOrigen = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->getFechaActivacionServicioOrigen($arrayParametrosFechaAct);
                            $objInfoServicioHistorial = new InfoServicioHistorial();
                            $objInfoServicioHistorial->setServicioId($objInfoServicioClonado);
                            $objInfoServicioHistorial->setFeCreacion(new \DateTime($strFechaActivacionOrigen));
                            $objInfoServicioHistorial->setUsrCreacion($arrayParams['strUsrCreacion']);
                            $objInfoServicioHistorial->setEstado($objInfoServicioClonado->getEstado()); 
                            $objInfoServicioHistorial->setAccion($strAccionHistOrigen);
                            $objInfoServicioHistorial->setObservacion($strObserHistOrigen);
                            $this->emcom->persist($objInfoServicioHistorial);
                            $this->emcom->flush(); 
                        }                                           
                        
                               
                        //Funcion que verifica si existen servicios extremos para un servicio concentrador, actualiza a todos los enlaces extremos
                        //existentes (servicios con caracteristica ENLACE_DATOS) el nuevo servicio Concentrador generado en el cambio de razon Social
                        $arrayParametroEnlaceDatos = array ('strFechaCreacion'       => $strFechaCreacion,
                                                            'strUsrCreacion'         => $arrayParams['strUsrCreacion'],
                                                            'strIpCreacion'          => $arrayParams['strClientIp'],                                                                                                              
                                                            'objInfoServicioOrigen'  => $objServ,
                                                            'objInfoServicioDestino' => $objInfoServicioClonado,
                                                            'strTipoCaracteristica'  => 'ENLACE_DATOS'); 
                    
                        $strMsjActualizaConcentradorEnExtremos = $this->serviceInfoContrato
                                                                      ->actualizaConcentradorEnExtremos($arrayParametroEnlaceDatos);
                        if($strMsjActualizaConcentradorEnExtremos)
                        {
                            throw new \Exception($strMsjActualizaConcentradorEnExtremos);
                        }
                        
                        // Funcion que verifica si existen servicios BACKUP para un servicio PRINCIPAL, actualiza a todos los enlaces BACKUPS
                        // existentes (servicios con caracteristica ES_BACKUP) el nuevo ID servicio PRINCIPAL generado en el cambio de razon Social
                        $arrayParametroEnlacesBackup = array ('strFechaCreacion'       => $strFechaCreacion,
                                                              'strUsrCreacion'         => $arrayParams['strUsrCreacion'],
                                                              'strIpCreacion'          => $arrayParams['strClientIp'],                                                                                                              
                                                              'objInfoServicioOrigen'  => $objServ,
                                                              'objInfoServicioDestino' => $objInfoServicioClonado,
                                                              'strTipoCaracteristica'  => 'ES_BACKUP'); 
                    
                        $strMsjActualizaPrincipalEnBackups = $this->serviceInfoContrato
                                                                  ->actualizaConcentradorEnExtremos($arrayParametroEnlacesBackup);
                        if($strMsjActualizaPrincipalEnBackups)
                        {
                            throw new \Exception($strMsjActualizaPrincipalEnBackups);
                        }
                    
                        if($objInfoContrato->getEstado() != 'PorAutorizar')
                        {
                            $objInfoServicioTecnico = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                  ->findByServicioId($objServ);       
                       
                            foreach($objInfoServicioTecnico as $servT)
                            {                                                          
                                $objInfoServicioTecnicoClonado = new InfoServicioTecnico();
                                $objInfoServicioTecnicoClonado = clone $servT;
                                $objInfoServicioTecnicoClonado->setServicioId($objInfoServicioClonado);                            
                                $this->emcom->persist($objInfoServicioTecnicoClonado);
                                $this->emcom->flush();                            
                            }
                        }
                        
                        $objInfoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->findByServicioId($objServ->getId());
                        foreach($objInfoIp as $ip)
                        {
                            $objInfoIpClonado = new InfoIp();
                            $objInfoIpClonado = clone $ip;
                            $objInfoIpClonado->setServicioId($objInfoServicioClonado->getId());
                            $this->emInfraestructura->persist($objInfoIpClonado);
                            $this->emInfraestructura->flush();

                            // Se procede a Cancelar la informacion de las IPS asociadas al servicio origen del Cambio de Razon Social
                            $ip->setEstado('Cancelado');
                            $this->emInfraestructura->persist($ip);
                            $this->emInfraestructura->flush();
                        }
                        
                       // Se obtienen el producto IPMP
                        $objProductoIPMP = $this->emcom->getRepository('schemaBundle:AdmiProducto')
                                                            ->findOneBy(array("descripcionProducto"
                                                                                => 'I. PROTEGIDO MULTI PAID',
                                                                                    "estado" => "Activo"));

                        $arrayProCaract  = array( "objServicio"       => $objServ,
                                                  "objProducto"       => $objProductoIPMP,
                                                  "strUsrCreacion"    => $arrayParams['strUsrCreacion'],
                                                  "strCaracteristica" => "ANTIVIRUS");

                        $strRespuestaCaract = $this->serviceLicenciasKaspersky->obtenerValorServicioProductoCaracteristica($arrayProCaract);
                        
                        
                        if(is_object($strRespuestaCaract['objServicioProdCaract']) &&
                           $strRespuestaCaract['objServicioProdCaract']->getValor() == "KASPERSKY")
                        {
                            $objInfoServicioProdCaract = $this->emcom->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                        ->findBy(array("servicioId" => $objServ->getId()));

                            $arrayEstadosCaract = array('Activo','Pendiente','Suspendido');
                        } 
                        else
                        {
                            $objInfoServicioProdCaract = $this->emcom->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                        ->findBy(array("servicioId" => $objServ->getId()));

                            $arrayEstadosCaract = array('Activo');
                        }                                     
                        
                        //Seteamos la caracteristica a buscar
                        $strCaractProducto='NETLIFECLOUD';
                        
                        //Seteamos los parametros para enviar a la función getInfoCaractProducto
                        $arrayParamProdCaract = array(
                                                        'intServicioId'        => $objServ->getId(),
                                                        'strCaracteristica'    => $strCaractProducto
                                                    );
        
                        // Se obtienen las características del servicio asociado
                        $objCaractProducto = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                        ->getInfoCaractProducto($arrayParamProdCaract);

                        if(is_object($strRespuestaCaract['objServicioProdCaract']) && 
                           $strRespuestaCaract['objServicioProdCaract']->getValor() == "KASPERSKY")
                        {
                            $arrayProCaractAntivirus   = array( "objServicio"       => $objServ,
                                                                "objProducto"       => $objProductoIPMP, 
                                                                "strUsrCreacion"    => $arrayParams['strUsrCreacion']);


                            $arrayProCaractAntivirus["strCaracteristica"] = "SUSCRIBER_ID";
                            $arrayRespuestaCaract = $this->serviceLicenciasKaspersky
                                                ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);

                            if(is_object($arrayRespuestaCaract['objServicioProdCaract']))
                            {
                                $intSuscriberId  = $arrayRespuestaCaract["objServicioProdCaract"]->getValor();
                            }
                            else if($arrayRespuestaCaract["status"] == 'ERROR')
                            {  
                                throw new \Exception('No se obtuvo suscriber ID');
                            }
                            
                        
                            $arrayProCaractAntivirus["strCaracteristica"] = 'CORREO ELECTRONICO';
                            $arrayRespuestaGetSpc  = $this->serviceLicenciasKaspersky
                                                    ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);
                            
                            if(is_object($arrayRespuestaGetSpc['objServicioProdCaract']))
                            {
                                $strCorreoSuscripcion  = $arrayRespuestaGetSpc["objServicioProdCaract"]->getValor();
                            }
                            else if($arrayRespuestaGetSpc["status"] == 'ERROR')
                            {  
                                throw new \Exception('No se obtuvo correo electrónico del cliente');
                            }

                            $strMsjErrorAdicHtml        = "No se pudo Realizar la cancelacion del suscriberID";
                        
                            $arrayParamsLicencias       = array("strProceso"                => "CANCELACION_ANTIVIRUS",
                                                                "strEscenario"              => "CANCELACION_POR_CAMBIO_RAZON_SOCIAL_LOGIN",
                                                                "objServicio"               => $objServ,
                                                                "objPunto"                  => $objServ->getPuntoId(),
                                                                "strCodEmpresa"             => $arrayParams['strCodEmpresa'],
                                                                "objProductoIPMP"           => $objProductoIPMP,
                                                                "strUsrCreacion"            => $arrayParams['strUsrCreacion'],
                                                                "strIpCreacion"             => $arrayParametros['strIpCreacion'],
                                                                "strEstadoServicioInicial"  => $objServ->getEstado(),
                                                                "intSuscriberId"            => $intSuscriberId,
                                                                "strCorreoSuscripcion"      => $strCorreoSuscripcion,
                                                                "strMsjErrorAdicHtml"       => $strMsjErrorAdicHtml
                                                                );                                

                            $arrayRespuestaGestionLicencias = $this->serviceLicenciasKaspersky->gestionarLicencias($arrayParamsLicencias);
                            $strStatusGestionLicencias      = $arrayRespuestaGestionLicencias["status"];
                            $strMensajeGestionLicencias     = $arrayRespuestaGestionLicencias["mensaje"];
                            $arrayRespuestaWs               = $arrayRespuestaGestionLicencias["arrayRespuestaWs"];
                            if($strStatusGestionLicencias === "ERROR")
                            {
                                $strMostrarError = "SI";
                                throw new \Exception('Fallo del envió de solicitud Cancelación de licencia kaspersky');
                            }
                        }
                        //NOMBRE TECNICO DE PRODUCTOS DE TVS PERMITIDOS
                        $arrayNombreTecnicoPermitido = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('NOMBRE_TECNICO_PRODUCTOSTV_CRS',//nombre parametro cab
                                                            'COMERCIAL', //modulo cab
                                                            'OBTENER_NOMBRE_PRODUCTO',//proceso cab
                                                            'FLUJO_CRS', //descripcion det
                                                            '','','','','',
                                                            '18'); //empresa
                        foreach($arrayNombreTecnicoPermitido as $arrayNombreTecnico)
                        {
                            $arrayProdTvNombreTecnico[]   =   $arrayNombreTecnico['valor1'];
                        }
                        if(is_object($objInfoServicioClonado->getProductoId()))
                        {
                            $strDescripcionProducto = $objInfoServicioClonado->getProductoId()->getDescripcionProducto();
                            $objProductoServicio    = $objInfoServicioClonado->getProductoId();
                            $strNombreTecnicoProdTv = $objInfoServicioClonado->getProductoId()->getNombreTecnico();
                            if(in_array($strNombreTecnicoProdTv,$arrayProdTvNombreTecnico ))
                            {
                                $arrayProducto  = $this->serviceFoxPremium->determinarProducto(
                                    array('strNombreTecnico'=>$strNombreTecnicoProdTv));
                            }
                        }
                        else
                        {
                            $arrayProducto = null;
                        }
                        $strBanderaCredenciales    = "N";
                        $strBanderaNotifica        = "N";
                        $strCaracteristicaUsuario  = null;
                        $strCaracteristicaPassword = null;
                        $strNombreTecnico          = null;
                        $strPlantillaCorreo        = null;
                        $strAsuntoNuevoServicio    = null;
                        $strPlantillaSms           = null;
                        //Se obtiene el nombre de las caracteristicas: usuario y password para los productos configurados
                        if(isset($arrayProducto) && !empty($arrayProducto))
                        {
                            //se agrega Parametro para validar si se generan o no credenciales de productos
                            $arrayNombreTecnicoGeneraCredenciales = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                ->get('NO_GENERA_CREDENCIALES_CRS',//nombre parametro cab
                                                                                    'COMERCIAL', //modulo cab
                                                                                    'NO_GENERA_CREDENCIALES',//proceso cab
                                                                                    'PRODUCTO_TV', //descripcion det
                                                                                    '','','','','',
                                                                                    '18'); //empresa
                            foreach($arrayNombreTecnicoGeneraCredenciales as $arrayNTGeneraCredenciales)
                            {
                                $arrayProdTvNombreTecGeneraCred[]   =   $arrayNTGeneraCredenciales['valor1'];
                            }
                            if($arrayProducto["strNombreTecnico"] = "ECDF")
                            {
                                $strBanderaCredenciales    = "N";
                                $strBanderaNotifica        = "S";
                                $strNombreTecnico          = $arrayProducto["strNombreTecnico"];
                                $strPlantillaCorreo        = $arrayProducto["strCodPlantNuevo"];
                                $strAsuntoNuevoServicio    = $arrayProducto['strAsuntoNuevo'];
                                $strPlantillaSms           = $arrayProducto['strSmsNuevo'];
    
                                $arrayEstadosCaract[]       = "tmpPendienteCRS";
                                $strCaracteristicaUsuario   = $arrayProducto["strUser"];
                                $strCaracteristicaPassword  = $arrayProducto["strPass"];
    
                                if($strTieneCorreoElectronico === "SI"
                                  && $objInfoServicioClonado->getEstado() === "Activo")
                                {
                                    $strBanderaCredenciales    = "S";
                                }
                                else
                                {
                                    $strBanderaNotifica        = "N";
                                }
                                
                            }
                        }
                        else
                        {
                            $strCaracteristicaUsuario  = null;
                            $strCaracteristicaPassword = null;
                            $strNombreTecnico          = null;
                            $strPlantillaCorreo        = null;
                            $strAsuntoNuevoServicio    = null;
                            $strPlantillaSms           = null;
                            $strBanderaCredenciales    = "N";
                            $strBanderaNotifica        = "N";
                        }

                        // Si la caracteristica del servicio es diferente de Office, se clonan sus caracteristicas al nuevo punto.            
                        if($objCaractProducto['caracteristica']!='NETLIFECLOUD')
                        {
                            foreach($objInfoServicioProdCaract as $servpc)
                            {
                                if(in_array($servpc->getEstado(),$arrayEstadosCaract))
                                {
                                    $intProductoCaracteristica = $servpc->getProductoCaracterisiticaId();

                                    $objInfoServicioProdCaractClonado = new InfoServicioProdCaract();
                                    $objInfoServicioProdCaractClonado = clone $servpc;
                                    $objInfoServicioProdCaractClonado->setServicioId($objInfoServicioClonado->getId());
                                    $objInfoServicioProdCaractClonado->setFeCreacion($strFechaCreacion);
                                    $objInfoServicioProdCaractClonado->setUsrCreacion($arrayParams['strUsrCreacion']);
                                    $this->emcom->persist($objInfoServicioProdCaractClonado);
                                    $this->emcom->flush();
                                    
                                    $objAdmiProductoCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                                       ->find($intProductoCaracteristica);

                                    $objAdmiCaracteristica = $objAdmiProductoCaracteristica->getCaracteristicaId();
                                    
                                    if($objAdmiCaracteristica->getDescripcionCaracteristica() == "SUSCRIBER_ID")
                                    {

                                        if(is_object($objServ) && $objServ->getPlanId() !== null)
                                        {
                                            $strEsenario = "ACTIVACION_PROD_EN_PLAN";
                                        }
                                        else
                                        {
                                            $strEsenario = "ACTIVACION_PROD_ADICIONAL";
                                        }

                                        $strMsjErrorAdicHtml            = "No se pudo Realizar la activacion suscriberID";

                                        $arrayParamsLicencias           = array("strProceso"                => "ACTIVACION_ANTIVIRUS",
                                                                                "boolEsCRS"                 => true,
                                                                                "strEscenario"              => $strEsenario,
                                                                                "objServicio"               => $objServ,
                                                                                "objPunto"                  => $objServ->getPuntoId(),
                                                                                "strCodEmpresa"             => $arrayParams['strCodEmpresa'],
                                                                                "objProductoIPMP"           => $objProductoIPMP,
                                                                                "strUsrCreacion"            => $arrayParams['strUsrCreacion'],
                                                                                "strIpCreacion"             => $arrayParametros['strIpCreacion'],
                                                                                "strEstadoServicioInicial"  => $objServ->getEstado(),
                                                                                "intIdOficina"              => $arrayParams['intIdOficina'],
                                                                                "strMsjErrorAdicHtml"       => $strMsjErrorAdicHtml);

                                        $arrayRespuestaGestionLicencias = $this->serviceLicenciasKaspersky->gestionarLicencias($arrayParamsLicencias);
                                        $strStatusGestionLicencias      = $arrayRespuestaGestionLicencias["status"];
                                        $strMensajeGestionLicencias     = $arrayRespuestaGestionLicencias["mensaje"];
                                        $arrayRespuestaWs               = $arrayRespuestaGestionLicencias["arrayRespuestaWs"];

                                        if($strStatusGestionLicencias === "ERROR")
                                        {
                                            $strMostrarError = "SI";
                                            throw new \Exception('Fallo del envió de solicitud Activación de licencia kaspersky');
                                        }
                                        
                                        $strSuscriberId = $arrayRespuestaWs["SuscriberId"];
                                        $objInfoServicioProdCaractClonado->setValor($strSuscriberId);
                                        $objInfoServicioProdCaractClonado->setEstado("Pendiente"); 

                                        $this->emcom->persist($objInfoServicioProdCaractClonado);
                                        $this->emcom->flush();     
                                    }
                                    else if($objAdmiCaracteristica->getDescripcionCaracteristica() == "CORREO ELECTRONICO")
                                    {
                                        $strEstadoServProdC = $objInfoServicioProdCaractClonado->getEstado();
                                        if ($arrayProducto["strNombreTecnico"] === "ECDF" && 
                                            $strEstadoServProdC === "tmpPendienteCRS" && 
                                            $strTieneCorreoElectronico === "SI")
                                        {
                                            $objInfoServicioProdCaractClonado->setEstado("Activo");
                                            $objInfoServicioProdCaractClonado->setFeUltMod(new \DateTime('now'));
                                            $objInfoServicioProdCaractClonado->setUsrUltMod($arrayParams['strUsrCreacion']);
                                            $this->emcom->persist($objInfoServicioProdCaractClonado);
                                            $this->emcom->flush();
                                        }
                                        if ($arrayProducto["strNombreTecnico"] === "ECDF" && 
                                            $strEstadoServProdC === "Activo")
                                        {
                                            $objInfoServicioProdCaractClonado->setEstado("Cancel");
                                            $objInfoServicioProdCaractClonado->setFeUltMod(new \DateTime('now'));
                                            $objInfoServicioProdCaractClonado->setUsrUltMod($arrayParams['strUsrCreacion']);
                                            $this->emcom->persist($objInfoServicioProdCaractClonado);
                                            $this->emcom->flush();
                                        }
                                        
                                    }
                                    else if($objAdmiCaracteristica->getDescripcionCaracteristica() == "FECHA_ACTIVACION"
                                            && $arrayProducto["strNombreTecnico"] === "ECDF")
                                    {
                                      $entityServicioHistorialOrig = new InfoServicioHistorial();
                                      $entityServicioHistorialOrig->setServicioId($objInfoServicioClonado);
                                      $entityServicioHistorialOrig->setFeCreacion(new \DateTime($objInfoServicioProdCaractClonado->getValor()));
                                      $entityServicioHistorialOrig->setUsrCreacion($arrayParams['strUsrCreacion']);
                                      $entityServicioHistorialOrig->setEstado($strEstadoServicioPorCRS);
                                      $entityServicioHistorialOrig->setIpCreacion($arrayParams['strClientIp']);
                                      $entityServicioHistorialOrig->setAccion('feOrigenCambioRazonSocial');
                                      $entityServicioHistorialOrig->setObservacion('Fecha inicial de servicio por Cambio de razón social.');
                                      $this->emcom->persist($entityServicioHistorialOrig);
                                    }

                                }
                            }
                            //cancelacion de Caracteristicas del servicio
                            foreach($objInfoServicioProdCaract as $servpc)
                            {
                            
                                // paso el valor de la caracteristica 'MIGRADO_FOX' a S, ya que el servicio fue clonado o migrado 
                                // por el cambio de razon social
                                $arrayParametrosFox = array();
                                $objRespuestaValidacion = null;
                                $arrayParametrosFox["strDescripcionCaracteristica"] = "MIGRADO_FOX";
                                $arrayParametrosFox["strNombreTecnico"]             = "FOXPREMIUM";
                                $arrayParametrosFox["intIdServicio"]                = $servpc->getId();
                                $arrayParametrosFox["intIdServProdCaract"]          = $servpc->getId();
                                $arrayParametrosFox["strEstadoSpc"]                 = 'Activo';

                                $objRespuestaServProdCarac = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                                        ->getCaracteristicaServicio($arrayParametrosFox);

                                if (is_object($objRespuestaServProdCarac))
                                {
                                    $servpc->setValor('S');
                                }
                                // Se procede a Cancelar las caracteristicas de los Servicios Origen del Cambio de Razon Social
                                $servpc->setEstado($strEstadoSpcAnterior);
                                $servpc->setFeUltMod($strFechaCreacion);
                                $servpc->setUsrUltMod($arrayParams['strUsrCreacion']);
                                $this->emcom->persist($servpc);
                                $this->emcom->flush();
                                
                            } 
                        }    
                        else
                        {
                            // Se crean nuevas caracteristicas para el servicio si su caracteristica anterior es Office   
                            $strAccion = 'cambioRazonSocial';
                            $arrayParametrosWs = array(
                                                      'strPrefijoEmpresa'    => $arrayParams['strPrefijoEmpresa'],
                                                      'strEmpresaCod'        => $arrayParams['strCodEmpresa'],
                                                      'strUsuarioCreacion'   => $arrayParams['strUsrCreacion'],
                                                      'strIp'                => $arrayParams['strClientIp'], 
                                                      'intServicioId'        => $objInfoServicioClonado->getId(),
                                                      'strAccion'            => $strAccion
                                                    );

                            $arrayRespuestaLicencia=$this->serviceLicenciasOffice365->renovarLicenciaOffice365($arrayParametrosWs);

                            if($arrayRespuestaLicencia["status"] == 'ERROR')
                            {
                                throw new \Exception($arrayRespuestaLicencia["mensaje"]);
                            }
                            
                            foreach($objInfoServicioProdCaract as $servpc)
                            {
                                // Se procede a Cancelar las caracteristicas de los Servicios Origen del Cambio de Razon Social
                                $servpc->setEstado('Cancelado');
                                $servpc->setFeUltMod($strFechaCreacion);
                                $servpc->setUsrUltMod($arrayParams['strUsrCreacion']);
                                $this->emcom->persist($servpc);
                                $this->emcom->flush();                                
                            }   
                        }
                        if($strBanderaCredenciales === "S" && is_object($objInfoPersona))
                        {
                            //Para servicios Paramount y Noggin se generan nuevo usuario y contrasenia
                            $arrayParametrosGenerarUsuario["intIdPersona"]     = $objInfoPersona->getId();
                            $arrayParametrosGenerarUsuario["strCaracUsuario"]  = $strCaracteristicaUsuario;
                            $arrayParametrosGenerarUsuario["strNombreTecnico"] = $strNombreTecnico;
    
                            $strUsuario  = $this->serviceFoxPremium->generaUsuarioFox($arrayParametrosGenerarUsuario);
    
                            if(empty($strUsuario))
                            {
                                throw new \Exception("No se pudo obtener Usuario para el servicio ".$strDescripcionProducto);
                            }
    
                            $strPassword           = $this->serviceFoxPremium->generaContraseniaFox();
                            $strPasswordEncriptado = $this->serviceCrypt->encriptar($strPassword);
                            if(empty($strPassword))
                            {
                                throw new \Exception("No se pudo generar Password para el servicio ".$strDescripcionProducto);
                            }
    
                            //Insertar nuevas caracteristicas: usuario y password
                            $this->serviceServicioTecnico->ingresarServicioProductoCaracteristica($objInfoServicioClonado,
                                                                                                  $objProductoServicio,
                                                                                                  $strCaracteristicaUsuario,
                                                                                                  $strUsuario,
                                                                                                  $arrayParams['strUsrCreacion']);
    
                            $this->serviceServicioTecnico->ingresarServicioProductoCaracteristica($objInfoServicioClonado,
                                                                                                  $objProductoServicio,
                                                                                                  $strCaracteristicaPassword,
                                                                                                  $strPasswordEncriptado,
                                                                                                  $arrayParams['strUsrCreacion']);
                            //Cambiar estado ELiminado de la caracteristica del correo del producto
                            $arrayNombreTecnicoEliminaCaracCorreo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->get('NOMBRE_PRODUCTOSTV_ELIMINA_CARAC_CORREO',//nombre parametro cab
                                                                            'COMERCIAL', //modulo cab
                                                                            'ELIMINA_CARAC_CORREO',//proceso cab
                                                                            'CRS_ELIMINA_CARAC_CORREO', //descripcion det
                                                                            '','','','','',
                                                                            '18'); //empresa
                            foreach($arrayNombreTecnicoEliminaCaracCorreo as $arrayNombreTecnicoProd)
                            {
                                $arrayProdTvPermitido[]   =   $arrayNombreTecnicoProd['valor1'];
                            }
                            if(in_array($strNombreTecnico,$arrayProdTvPermitido))
                            {
                                $arrayParameter =   array(
                                                            "strNombreTecnico"  =>  $strNombreTecnico,
                                                            "strUsrCreacion"    =>  $arrayParams['strUsrCreacion'],
                                                            "intIdServicio"     =>  $objInfoServicioClonado->getId()
                                                         );
                                $this->serviceFoxPremium->eliminarCaractCorreo($arrayParameter);
                            }
                        }
                                              //Se valida si se notifica por correo y sms productos de tv
                        if ($strBanderaNotifica === "S")
                        {
                            //Coger las credenciales de la info_servicio_pro_caract clonadas
                            if(empty($strPassword) && empty($strUsuario))
                            {
                                $arrayParamServProdCarac= array('intIdServicio' =>  $objInfoServicioClonado->getId());
                                $arrayCaracteristicasTv  =   $this->serviceFoxPremium->obtieneArrayCaracteristicas($arrayParamServProdCarac);

                                if(is_array($arrayCaracteristicasTv) && !empty($arrayCaracteristicasTv))
                                {
                                    $objServProdCaracContrasenia = $arrayCaracteristicasTv[$arrayProducto['strPass']];
                                    $objServProdCaracUsuario     = $arrayCaracteristicasTv[$arrayProducto['strUser']];
                                    $strUsuario                  = $objServProdCaracUsuario->getValor();
                                    $strPassword                 = $this->serviceCrypt->descencriptar($objServProdCaracContrasenia->getValor());
                                }
                                else
                                {
                                    throw new \Exception('No se encontraron características del Servicio '. $arrayProducto['strMensaje']);
                                }
                            }
                            //Guarda Historial de Notificacion de correo y sms
                            $arrayParamHistorial        = array('strUsrCreacion'  => $arrayParams['strUsrCreacion'], 
                                                                'strClientIp'     => $arrayParams['strClientIp'], 
                                                                'objInfoServicio' => $objInfoServicioClonado,
                                                                'strTipoAccion'   => $arrayProducto['strAccionActivo'],
                                                                'strMensaje'      => $arrayProducto['strMensaje']);

                            //Notifico al cliente por Correo y SMS
                            $this->serviceFoxPremium->notificaCorreoServicioFox(
                                                    array("strDescripcionAsunto"   => $strAsuntoNuevoServicio,
                                                          "strCodigoPlantilla"     => $strPlantillaCorreo,
                                                          "strEmpresaCod"          => $arrayParams["strCodEmpresa"],
                                                          "intPuntoId"             => $objInfoPuntoClonado->getId(),
                                                          "intIdServicio"          => $objInfoServicioClonado->getId(),
                                                          "strNombreTecnico"       => $strNombreTecnico,
                                                          "intPersonaEmpresaRolId" => $objInfoPuntoClonado->getPersonaEmpresaRolId()
                                                                                                        ->getId(),
                                                          "arrayParametros"        => array("contrasenia" => $strPassword,
                                                                                            "usuario"     => $strUsuario),
                                                          "arrayParamHistorial"    => $arrayParamHistorial
                                                        ));

                            //Se reemplaza la contraseña del mensaje del parámetro
                            $strMensajeSMS = str_replace("{{USUARIO}}",
                                                        $strUsuario,
                                                        str_replace("{{CONTRASENIA}}",
                                                                    $strPassword,
                                                                    $strPlantillaSms));

                            $this->serviceFoxPremium->notificaSMSServicioFox(
                                    array("strMensaje"             => $strMensajeSMS,
                                          "strTipoEvento"          => "enviar_infobip",
                                          "strEmpresaCod"          => $arrayParams["strCodEmpresa"],
                                          "intPuntoId"             => $objInfoPuntoClonado->getId(),
                                          "intPersonaEmpresaRolId" => $objInfoPuntoClonado->getPersonaEmpresaRolId()->getId(),
                                          "strNombreTecnico"       => $strNombreTecnico,
                                          "arrayParamHistorial"    => $arrayParamHistorial
                                        )
                                  );
                        }
                        if($strEjecutaCreacionSolWyAp === "SI")
                        {
                            $arrayParamsWyApTrasladoyCRS    = array("objServicioOrigen"     => $objServ,
                                                                    "objServicioDestino"    => $objInfoServicioClonado,
                                                                    "strCodEmpresa"         => $arrayParams["strCodEmpresa"],
                                                                    "strUsrCreacion"        => $arrayParams['strUsrCreacion'],
                                                                    "strIpCreacion"         => $arrayParams['strClientIp'],
                                                                    "strOpcion"             => "aprobación de contrato por cambio de razón social");
                            $arrayRespuestaWyApTrasladoyCrs = $this->serviceInfoServicio->creaSolicitudWyApTrasladoyCRS($arrayParamsWyApTrasladoyCRS);
                            if($arrayRespuestaWyApTrasladoyCrs["status"] === "ERROR")
                            {
                                throw new \Exception($arrayRespuestaWyApTrasladoyCrs["mensaje"]);
                            }
                        }
                        else if($strEjecutaCreacionSolEdb === "SI")
                        {
                            $arrayParamsEdbTrasladoyCRS     = array("objServicioOrigen"     => $objServ,
                                                                    "objServicioDestino"    => $objInfoServicioClonado,
                                                                    "strCodEmpresa"         => $arrayParams["strCodEmpresa"],
                                                                    "strUsrCreacion"        => $arrayParams['strUsrCreacion'],
                                                                    "strIpCreacion"         => $arrayParams['strClientIp'],
                                                                    "strOpcion"             => "aprobación de contrato por cambio de razón social");
                            $arrayRespuestaEdbTrasladoyCrs  = $this->serviceInfoServicio->creaSolicitudEdbTrasladoyCRS($arrayParamsEdbTrasladoyCRS);
                            if($arrayRespuestaEdbTrasladoyCrs["status"] === "ERROR")
                            {
                                throw new \Exception($arrayRespuestaEdbTrasladoyCrs["mensaje"]);
                            }
                        }
                        else if ($strEjecutaCreacionSolPlan === "SI")
                        {   
                            $arrayParamsCamCRS = array("objServicioOrigen"      => $objServ,
                                                        "objServicioDestino"    => $objInfoServicioClonado,
                                                        "strCodEmpresa"         => $arrayParams["strCodEmpresa"],
                                                        "strUsrCreacion"        => $arrayParams['strUsrCreacion'],
                                                        "strIpCreacion"         => $arrayParams['strClientIp'],
                                                        "strOpcion"             => "cambio de razón social con cliente existente");
                            $arrayRespuestaCamCrs  = $this->serviceInfoServicio->creaSolicitudNetLifeCAM($arrayParamsCamCRS);
                            if($arrayRespuestaCamCrs["status"] === "ERROR")
                            {
                                throw new \Exception($arrayRespuestaCamCrs["mensaje"]);
                            }
                        }
                        
                        // Se procede a Cancelar los Servicios Origen del Cambio de Razon Social
                        $objServ->setEstado($strEstadoServicioAnterior);
                        $this->emcom->persist($objServ);
                        $this->emcom->flush();
                        
                        // Creo registro en el Historial del Servicio Origen del Cambio de Razon Social
                        $objInfoServicioHistorialOrigen = new InfoServicioHistorial();
                        $objInfoServicioHistorialOrigen->setServicioId($objServ);
                        $objInfoServicioHistorialOrigen->setFeCreacion(new \DateTime('now'));
                        $objInfoServicioHistorialOrigen->setUsrCreacion($arrayParams['strUsrCreacion']);
                        if ($objMotivoCambioRs)
                        {
                            $objInfoServicioHistorialOrigen->setMotivoId($objMotivoCambioRs->getId());
                        }
                        $objInfoServicioHistorialOrigen->setEstado($strEstadoServicioAnterior);
                        $objInfoServicioHistorialOrigen->setObservacion($strObservacionServicioAnterior);
                        $this->emcom->persist($objInfoServicioHistorialOrigen);
                        $this->emcom->flush();   
                        
                        if($arrayParams ['strPrefijoEmpresa'] == 'MD' 
                            || ($arrayParams ['strPrefijoEmpresa'] == 'TN' && is_object($objServ->getProductoId())
                                && ($objServ->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS"
                                    || $objServ->getProductoId()->getNombreTecnico() === "TELCOHOME" )))
                        {
                            $arrayServiciosLdap[] = array(
                                                            'servicioAnterior' => $objServ,
                                                            'servicioNuevo'    => $objInfoServicioClonado
                                                         );
                        }
                        
                        // Se guarda la Plantilla de Comisionistas a la nueva Razon social
                        $arrayServicioComision = $this->emcom->getRepository('schemaBundle:InfoServicioComision')
                                                             ->findBy(array("servicioId" => $objServ->getId(), "estado" => "Activo"));

                        foreach($arrayServicioComision as $objServicioComision)
                        {
                            $objInfoServicioComision = clone $objServicioComision;
                            $objInfoServicioComision->setServicioId($objInfoServicioClonado);
                            $objInfoServicioComision->setFeCreacion($strFechaCreacion);
                            $objInfoServicioComision->setIpCreacion($arrayParams['strClientIp']);
                            $objInfoServicioComision->setUsrCreacion($arrayParams['strUsrCreacion']);
                            $this->emcom->persist($objInfoServicioComision);

                            //Cancelo estado de la plantilla del cliente origen del cambio de razon social, guardo usuario, ip y fecha.
                            $objServicioComision->setEstado('Cancelado');
                            $objServicioComision->setFeUltMod($strFechaCreacion);
                            $objServicioComision->setIpUltMod($arrayParams['strClientIp']);
                            $objServicioComision->setUsrUltMod($arrayParams['strUsrCreacion']);
                            $this->emcom->persist($objServicioComision);

                            /* Guardo un registro en el Historico en la plantilla del cliente origen del cambio de razon social 
                              que se Cancela */
                            $objInfoServicioComisionHisto = new InfoServicioComisionHisto();
                            $objInfoServicioComisionHisto->setServicioComisionId($objServicioComision);
                            $objInfoServicioComisionHisto->setServicioId($objServicioComision->getServicioId());
                            $objInfoServicioComisionHisto->setComisionDetId($objServicioComision->getComisionDetId());
                            $objInfoServicioComisionHisto->setPersonaEmpresaRolId($objServicioComision->getPersonaEmpresaRolId());
                            $objInfoServicioComisionHisto->setComisionVenta($objServicioComision->getComisionVenta());
                            $objInfoServicioComisionHisto->setComisionMantenimiento($objServicioComision->getComisionMantenimiento());
                            $objInfoServicioComisionHisto->setEstado($objServicioComision->getEstado());
                            $objInfoServicioComisionHisto->setObservacion('Plantilla de Comisionistas cancelada por cambio de razón social'
                                                                        . ' por login');
                            $objInfoServicioComisionHisto->setFeCreacion($strFechaCreacion);
                            $objInfoServicioComisionHisto->setIpCreacion($arrayParams['strClientIp']);
                            $objInfoServicioComisionHisto->setUsrCreacion($arrayParams['strUsrCreacion']);
                            $this->emcom->persist($objInfoServicioComisionHisto);
                        }

                        //se clonan las solicitudes de agregar equipo y cambio de equipo por soporte que se encuentren en estado permitidos
                        if($arrayParams['strPrefijoEmpresa'] == 'MD' && 
                           is_object($objInfoServicioClonado->getPlanId()) &&
                           ($objInfoServicioClonado->getEstado() == 'Activo' ||
                            $objInfoServicioClonado->getEstado() == 'In-Corte'
                           ))
                        {
                            $arrayParametrosClonarSolCrs = array(
                                                                 'objServicioOrigen'  => $objServ,
                                                                 'objServicioDestino' => $objInfoServicioClonado,
                                                                 'strUsrCreacion'     => $arrayParams['strUsrCreacion'],
                                                                 'strIpCreacion'      => $arrayParams['strClientIp'],
                                                                 'strEmpresaCod'      => $arrayParams["strCodEmpresa"]
                                                                );
                            $this->serviceInfoServicio->clonarSolicitudesPorCrs($arrayParametrosClonarSolCrs);
                        }
                    }
                    //Se verifica si servicio está atado a un producto con la característica NETLIFECLOUD
                    $arrayCaractProdNetlifeCloud    = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                                  ->getInfoCaractProducto(array(
                                                                                                    'intServicioId'        => $objServ->getId(),
                                                                                                    'strCaracteristica'    => 'NETLIFECLOUD'
                                                                                                ));
                    if($arrayCaractProdNetlifeCloud['caracteristica'] === 'NETLIFECLOUD')
                    {
                        $arrayServiciosNetlifeCloud[]   = array('intIdServicio' => $objServ->getId());
                    }
                }
                                                             
                //Guardo relacion de los Puntos Logines destinos del Cambio de Razon Social con sus Puntos Logines origen
                $objInfoPuntoCaracteristica = new InfoPuntoCaracteristica();
                $objInfoPuntoCaracteristica->setPuntoId($objInfoPuntoClonado);                   
                $objInfoPuntoCaracteristica->setCaracteristicaId($objAdmiCaracteristica);
                $objInfoPuntoCaracteristica->setValor($objInfoPuntoOrigen->getId());
                $objInfoPuntoCaracteristica->setFeCreacion($strFechaCreacion);
                $objInfoPuntoCaracteristica->setUsrCreacion($arrayParams['strUsrCreacion']);
                $objInfoPuntoCaracteristica->setIpCreacion($arrayParams['strClientIp']);
                $objInfoPuntoCaracteristica->setEstado($strEstadoActivo);
                $this->emcom->persist($objInfoPuntoCaracteristica);
                $this->emcom->flush();
                
                // Se procede a Cancelar los Logines Origen del Cambio de Razon Social
                $objInfoPuntoOrigen->setEstado('Cancelado');
                $this->emcom->persist($objInfoPuntoOrigen);
                $this->emcom->flush();
                
            }// fin foreach($objDatosPuntos as $objDatosPuntos)
            
            if(is_object($objInfoPersonaRepresentante))
            {
                $objInfoPersonaRepresentante->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                $this->emcom->persist($objInfoPersonaRepresentante);
                $this->emcom->flush();
            }
            
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->commit();
            }
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->commit();
            }           
            $this->emcom->getConnection()->close();
            $this->emInfraestructura->getConnection()->close();
            $strStatus  = "OK";
        }
        catch(\Exception $e)
        {
            $strStatus      = "ERROR";
            $strMensaje     = $e->getMessage();
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
            }            
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
            $this->emcom->getConnection()->close();            
            $this->emInfraestructura->getConnection()->close();  
            $this->serviceUtil->insertError(
                                                "Telcos+",
                                                "InfoContratoAprobService->procesaAprobContratoCambioRazonSocial", 
                                                $e->getMessage(), 
                                                $arrayParams['strUsrCreacion'], 
                                                $arrayParams['strClientIp']
                                            );
        }
        
        if($strStatus === "OK")
        {
            $strMuestraErrorAdicionalCRS    = "NO";
            $strMsjUsrErrorAdicionalCRS     = "";
            //eliminación de Ldap de antiguo servicio y creación de Ldap de nuevo servicio
            if(isset($arrayServiciosLdap) && !empty($arrayServiciosLdap))
            {
                foreach($arrayServiciosLdap as $arrayServicioLdap)
                {
                    $arrayRespuestaLdap = $this->serviceServicioTecnico
                                               ->configurarLdapCambioRazonSocial(array( "servicioAnterior"  => 
                                                                                        $arrayServicioLdap['servicioAnterior'],
                                                                                        "servicioNuevo"     => 
                                                                                        $arrayServicioLdap['servicioNuevo'],
                                                                                        "usrCreacion"       => $arrayParams['strUsrCreacion'],
                                                                                        "ipCreacion"        => $arrayParams['strClientIp'],
                                                                                        "prefijoEmpresa"    => $arrayParams['strPrefijoEmpresa']
                                                                                    ));

                    if($arrayRespuestaLdap["status"] === "ERROR" || !empty($arrayRespuestaLdap["mensaje"]))
                    {
                        $strMuestraErrorAdicionalCRS    = "SI";
                        $strMsjUsrErrorAdicionalCRS     .= $arrayRespuestaLdap["mensaje"] . ". ";
                    }
                }
            }
            
            if(isset($arrayServiciosNetlifeCloud) && !empty($arrayServiciosNetlifeCloud))
            {
                //FACTURACIÓN DE LOS SERVICIOS CANCELADOS NETLIFECLOUD   
                foreach ($arrayServiciosNetlifeCloud as $arrayServicioNetlifeCloud)
                {
                    //Se invoca a la función generarFacturaServicioCancelado para generar factura a los servicios NetlifeCloud cancelados
                    $arrayRespuestaFacturaNetlifeCloud  = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                                      ->generarFacturaServicioCancelado(
                                                                          array(
                                                                                'strPrefijoEmpresa' => $arrayParams['strPrefijoEmpresa'],
                                                                                'strEmpresaCod'     => $arrayParams['strCodEmpresa'],
                                                                                'strIp'             => $arrayParams['strClientIp'], 
                                                                                'intServicioId'     => $arrayServicioNetlifeCloud["intIdServicio"]
                                                                          ));
                    if($arrayRespuestaFacturaNetlifeCloud["status"] == 'ERROR')
                    {
                        $strMuestraErrorAdicionalCRS    = "SI";
                        $strMsjUsrErrorAdicionalCRS     .= $arrayRespuestaFacturaNetlifeCloud["mensaje"] . ". ";
                    }
                }
            }
            
            if($strMuestraErrorAdicionalCRS === "SI")
            {
                $strMensaje =   'Se ha realizado de manera correcta el proceso principal de aprobación de contrato por cambio de razón social. '.
                                'Sin embargo, se tuvieron los siguientes inconvenientes: '.$strMsjUsrErrorAdicionalCRS.
                                'Por favor verificar con el departamento de Sistemas!';
                $this->serviceUtil->insertError('Telcos+', 
                                                'InfoContratoAprobService->procesaAprobContratoCambioRazonSocial',
                                                $strMsjUsrErrorAdicionalCRS, 
                                                $arrayParams['strUsrCreacion'], 
                                                $arrayParams['strClientIp']
                                               );
                $strTipoMensaje = "warning";
            }
            else
            {
                $strMensaje     = "Cambio de Razón Social ejecutado con éxito";
                $strTipoMensaje = "success";
            }
        }
        $strMensaje = $strMensaje." ".$strMensajeCorreoECDF;
        $arrayRespuestaProceso  = array("status"                    => $strStatus,
                                        "mensaje"                   => $strMensaje,
                                        "tipoMensaje"               => $strTipoMensaje,
                                        "arrayPuntosCRS"            => $arrayPuntosCRSActivar,
                                        "arrayServiciosPreActivo"   => $arrayServiciosPreActivo,
                                        "objInfoPersonaEmpresaRol"  => $objInfoPersonaEmpresaRol);
        return $arrayRespuestaProceso;
    }
    
    
    /**    
     * Documentación para el método 'aprobacionContratoDigitalPorPagoFactura'.
     *
     * Función que aprueba los contratos digitales que ya hayan pagado la factura por instalación. 
     * Esta función será llamado por un crontab cada 5 minutos.
     * 
     * @return int $intContadorContratosAprobados
     * 
     * @author Edson Franco <efranco@telconet.ec>       
     * @version 1.0 08-09-2016
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 14-04-2020 Se modifica para autorizar adendums
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 16-04-2020 Se modifica la manera de llamar la plantilla html para envío de correo, se utiliza templating
     *  
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.3 07-07-2020 Se modifica para validar el estado del servicio del adendum, se agregan logs para seguimiento de contratos.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.4 25-09-2020 se modifica textos del log para una mejor lectura del mismo
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.5 27-03-2022 - Se valida que tenga una factura de instalación cerrada por el punto procesado.
     * @since 1.4
     * 
     * @author Alex Gómez <algomez@telconet.ec>
     * @version 1.6 06-03-2023 - Se adapta proceso para multiempresa
     */
   public function aprobacionContratoDigitalPorPagoFactura($arrayParams, OutputInterface $objOutput)
    {       

        $strUsrCreacion = $arrayParams["strUsrCreacion"];
        $strIpCreacion  = $arrayParams["strIpCreacion"];
       
        $this->emFinanciero->getConnection()->beginTransaction();

        try
        {
            //Consulta prefijos de empresas a procesar
            $arrayPrefijosEmpresas = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne(
                                                        'APROBACION_CONTRATO_COMMAND',
                                                        'COMERCIAL',
                                                        '',
                                                        'PREFIJOS_EMPRESA',
                                                        '','','','','',
                                                        '');

            if(empty($arrayPrefijosEmpresas) || empty($arrayPrefijosEmpresas["valor1"]))
            {
                throw new \Exception("No se encontraron los prefijos de las empresas a procesar");
            }

            $arrayPrefijosEmpresas = explode(",",$arrayPrefijosEmpresas["valor1"]);

            //Recorre proceso de aprobación por cada empresa parametrizado
            foreach($arrayPrefijosEmpresas as $strPrefijoEmpresa)
            {
                $intContadorContratosAprobados = 0;
                $intContadorAdendumsAprobados = 0;
                $objInfoEmpresaGrupo = $this->emcom->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($strPrefijoEmpresa);
                $strEmpresaCod       = "";
                
                if( is_object($objInfoEmpresaGrupo) )
                {
                    $strEmpresaCod = $objInfoEmpresaGrupo->getId() ? $objInfoEmpresaGrupo->getId() : "";
                }
                $objOutput->writeln("*******PROCESAR CONTRATOS - EMPRESA " . $strPrefijoEmpresa . "**********");
                
                if( !empty($strEmpresaCod) )
                {
                    $arrayResultados = $this->emFinanciero->getRepository('schemaBundle:InfoServicio')
                                                        ->getContratosDigitalesConFacturas($strEmpresaCod);

                    if( !empty($arrayResultados['registros']) )
                    {
                        foreach( $arrayResultados['registros'] as $arrayItem )
                        {
                            $intIdContrato              = $arrayItem['id'];
                            $intFacturasNoPagadas       = $arrayItem['FACT_ACTIVAS_PENDIENTES'];
                            $intFacturasCerradas        = $arrayItem['FACT_CERRADAS'];
                            $strCliente                 = "";
                            $arrayServiciosEncontrados  = array();

                            if( $intFacturasNoPagadas == 0 && $intFacturasCerradas > 0 )
                            {
                                $objOutput->writeln( "[ID DEL CONTRATO A PROCESAR]: ".$intIdContrato );
                                $objOutput->writeln( "[ID PREFIJO EMPRESA]: ".$strPrefijoEmpresa );
                                
                                $boolSeguir             = true;      
                                $objContrato            = $this->getDatosContratoId($intIdContrato);
                                $objPersonaEmpresaRol   = null;
                                $objEmpresaRol          = null;
                                $objProspecto           = null;

                                $objOutput->writeln( "[FACT_ACTIVAS_PENDIENTES]: ".$intFacturasNoPagadas );
                                $objOutput->writeln( "[FACT_CERRADAS]: ".$intFacturasCerradas );
                                
                                if( $objContrato)
                                {
                                    if( $objContrato->getPersonaEmpresaRolId() )
                                    {
                                        $objPersonaEmpresaRol = $this->getDatosPersonaEmpresaRolId($objContrato->getPersonaEmpresaRolId()->getId());
                                    }//( $objContrato->getPersonaEmpresaRolId() )
                                    else
                                    {
                                        $boolSeguir = false;
                                    }
                                }//( $objContrato)
                                else
                                {
                                    $boolSeguir = false;
                                }


                                if( $boolSeguir && $objPersonaEmpresaRol )
                                {
                                    if( $objPersonaEmpresaRol->getEmpresaRolId() )
                                    {
                                        $objEmpresaRol = $this->getDatosEmpresaRolId($objPersonaEmpresaRol->getEmpresaRolId()->getId());
                                    }//( $objPersonaEmpresaRol->getEmpresaRolId() )
                                    else
                                    {
                                        $boolSeguir = false;
                                    }


                                    if( $objPersonaEmpresaRol->getPersonaId() )
                                    {
                                        $objProspecto = $this->getDatosPersonaId($objPersonaEmpresaRol->getPersonaId()->getId());
                                    }//( $objPersonaEmpresaRol->getPersonaId() )
                                    else
                                    {
                                        $boolSeguir = false;
                                    } 

                                    if( $objProspecto &&  $objEmpresaRol && $boolSeguir )
                                    {
                                        //obtener el arreglo de los datos de formas de pago
                                        $arrayFormaPago       = array();
                                        $boolSeguirAprob      = true;
                                        $intIdFormaPago       = $objContrato->getFormaPagoId() ? $objContrato->getFormaPagoId()->getId() : 0;
                                        $intIdTipoCuenta      = 0;
                                        $objContratoFormaPago = $this->getDatosContratoFormaPagoId($intIdContrato);
                                        $intIdPunto           = 0;
                                        $strNumeroAdendum     = '';
                                        $strTipoAdendum       = '';
                                        $strOrigen            = '';
                                    
                                        if( $objContratoFormaPago )
                                        {   
                                            $strNumTarjetaCuentaEncriptada          = $objContratoFormaPago->getNumeroCtaTarjeta();
                                            $strNumeroTarjetaBancoDesencriptada     = $this->serviceCrypt->descencriptar($strNumTarjetaCuentaEncriptada);
                                            $arrayFormaPago['bancoTipoCuentaId']    = $objContratoFormaPago->getBancoTipoCuentaId() ?
                                                                                    $objContratoFormaPago->getBancoTipoCuentaId()->getId() : 0;
                                            $arrayFormaPago['numeroCtaTarjeta']     = $strNumeroTarjetaBancoDesencriptada;
                                            $arrayFormaPago['mesVencimiento']       = $objContratoFormaPago->getMesVencimiento();
                                            $arrayFormaPago['anioVencimiento']      = $objContratoFormaPago->getAnioVencimiento();
                                            $arrayFormaPago['codigoVerificacion']   = $objContratoFormaPago->getCodigoVerificacion();
                                            $arrayFormaPago['titularCuenta']        = $objContratoFormaPago->getTitularCuenta();
                                            $intIdTipoCuenta                        = $objContratoFormaPago->getTipoCuentaId() 
                                                                                    ? $objContratoFormaPago->getTipoCuentaId()->getId() : 0;
                                        }

                                        if( $objProspecto->getIdentificacionCliente() )
                                        {
                                            $strCliente = $this->getClientesPorIdentificacion( $objProspecto->getIdentificacionCliente(), 
                                                                                                $objEmpresaRol->getEmpresaCod()->getId() );
                                        }


                                        if( empty($strCliente) )
                                        {
                                            /**
                                             * Obtiene los servicios de la persona empresa rol
                                             */ 
                                            if($strPrefijoEmpresa=='TN')
                                            {
                                                $arrayEstado       = array( 'Rechazado', 'Rechazada', 'Cancelado', 'Anulado', 'Cancel', 'Eliminado', 
                                                                            'Reubicado', 'Trasladado' );
                                                $arrayServiciosTmp = $this->getTodosServiciosXEstadoTn( $objPersonaEmpresaRol->getId(), 
                                                                                                        0, 
                                                                                                        10000, 
                                                                                                        $arrayEstado ); 
                                                $arrayRegistrosServicios = $arrayServiciosTmp['registros'];

                                                if( !empty($arrayRegistrosServicios) )
                                                {
                                                    foreach($arrayRegistrosServicios as $objServicioTmp)
                                                    {
                                                        $arrayServiciosEncontrados[] = $objServicioTmp->getId();
                                                        
                                                        $objOutput->writeln( "[SERVICIO FACTIBLE ENCONTRADO]: ".$objServicioTmp->getId() );
                                                    }
                                                }
                                                else
                                                {
                                                    $objOutput->writeln( "[CONTRATO NO POSEE SERVICIOS FACTIBLES] ");
                                                }//( !empty($arrayRegistrosServicios) )
                                            }
                                            else
                                            {
                                                $arrayAdendumServicio = $this->emcom->getRepository('schemaBundle:InfoAdendum')
                                                                                    ->findBy(array("contratoId" => $intIdContrato,
                                                                                                "tipo"       => "C",
                                                                                                    ));
                                                foreach($arrayAdendumServicio as $objAdendum)
                                                { 
                                                    $entityServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                                                ->findOneById( $objAdendum->getServicioId());
                                                
                                                    if( $entityServicio )
                                                    {
                                                        $strEstadoServicio = $entityServicio->getEstado();
                                                        
                                                        if($strEstadoServicio=='Factible')
                                                        {
                                                            $arrayServiciosEncontrados[] = $objAdendum->getServicioId() ;         
                                                            $objOutput->writeln( "[SERVICIO FACTIBLE ENCONTRADO]: ".$objAdendum->getServicioId() );
                                                        }
                                                        else
                                                        {
                                                            $objOutput->writeln( "[SERVICIO NO FACTIBLE]: ".$objAdendum->getServicioId() );
                                                        }
                                                    
                                                    }

                                                    $arrayDocumentoFinanciero = $this->emFinanciero
                                                                                    ->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                                    ->existeFacturaCerradaPunto(
                                                                                        array(
                                                                                            "intPunto" => $objAdendum->getPuntoId()
                                                                                        )
                                                                                    );
                                                    $intIdPunto = $objAdendum->getPuntoId();
                                                    $strNumeroAdendum = $objAdendum->getNumero();
                                                    $strTipoAdendum = $objAdendum->getTipo();
                                                    $strOrigen = $objAdendum->getOrigen();

                                                    if(!$arrayDocumentoFinanciero || count($arrayDocumentoFinanciero)==0 || 
                                                    !isset($arrayDocumentoFinanciero[0]['intIdDocumento']))
                                                    {
                                                        $boolSeguirAprob = false;
                                                    }
                                                }
                                            }

                                            if($boolSeguirAprob)
                                            {
                                                //Funcion que guarda todo el proceso
                                                $arrayParametros = array();
                                                $arrayParametros['strIpCreacion']           = $strIpCreacion;
                                                $arrayParametros['strEmpresaCod']           = $strEmpresaCod;
                                                $arrayParametros['strPrefijoEmpresa']       = $strPrefijoEmpresa;
                                                $arrayParametros['strUsrCreacion']          = $strUsrCreacion;
                                                $arrayParametros['strOrigen']               = $strOrigen;
                                                $arrayParametros['strTipo']                 = $strTipoAdendum;
                                                $arrayParametros['intPersonaEmpresaRolId']  = $objPersonaEmpresaRol->getId();
                                                $arrayParametros['intIdContrato']           = $intIdContrato;
                                                $arrayParametros['intIdPunto']              = $intIdPunto;
                                                $arrayParametros['strNumeroAdendum']        = $strNumeroAdendum;
                                                $arrayParametros['strAplicaTentativa']      = "N";
                                                
                                                $objOutput->writeln( "[FORMA PAGO]: ".$intIdFormaPago );

                                                $objOutput->writeln( "DATA APROBACION: ". json_encode($arrayParametros) );

                                                $arrayGuardarProceso = $this->serviceInfoContrato->procesaAprobacionContrato($arrayParametros);
                                                
                                                if( $arrayGuardarProceso && array_key_exists('status', $arrayGuardarProceso) 
                                                    && $arrayGuardarProceso['status']!='OK' )
                                                {
                                                    $objOutput->writeln( "[ERROR AL APROBAR CONTRATO DIGITAL]: ERROR_SERVICE = ".
                                                                    $arrayGuardarProceso['mensaje'] );
                                                }
                                                else
                                                {
                                                    $intContadorContratosAprobados++;
                                                    
                                                    $objOutput->writeln( "[ID DEL CONTRATO DIGITAL APROBADO]: ".$intIdContrato );

                                                    foreach($arrayServiciosEncontrados as $intId)
                                                    {
                                                        $entityInfoServicio = $this->getDatosServicioId($intId);

                                                        if($entityInfoServicio && count($entityInfoServicio) > 0)
                                                        {
                                                            //------- COMUNICACIONES --- NOTIFICACIONES                           
                                                            $entitySolicitud = $this->getSolicitudPrePlanifId( $entityInfoServicio->getId(), 
                                                                                                                "PrePlanificada" );

                                                            if(isset($entitySolicitud))
                                                            {
                                                                $strHtml = $this->templating
                                                                                ->render('planificacionBundle:Coordinar:notificacion.html.twig', 
                                                                                            array('detalleSolicitud'     => $entitySolicitud, 
                                                                                                'detalleSolicitudHist' => null, 
                                                                                                'motivo'               => null));

                                                                $strAsunto  = "Solicitud de Instalacion #" . $entitySolicitud->getId();

                                                                //DESTINATARIOS....  
                                                                $strUsrVendedor = "";

                                                                if( $entityInfoServicio->getPuntoId() )
                                                                {
                                                                    $strUsrVendedor = $entityInfoServicio->getPuntoId()->getUsrVendedor();
                                                                }

                                                                $arrayFormasContactoTmp = $this
                                                                                        ->getContactosByLoginPersonaAndFormaContacto($strUsrVendedor,
                                                                                                                                    'Correo Electronico');
                                                                $to = array('notificaciones_telcos@telconet.ec');

                                                                $arrayFormasContactoTmp = ($arrayFormasContactoTmp)?$arrayFormasContactoTmp:array();
                                                                
                                                                foreach($arrayFormasContactoTmp as $arrayFormaContactoTmp)
                                                                {
                                                                    $to[] = $arrayFormaContactoTmp['valor'];
                                                                }
                                                                
                                                                if (!$this->serviceEnvioPlantilla->enviarCorreo($strAsunto, $to, $strHtml))
                                                                {
                                                                    $objOutput->writeln("no se pudo enviar el mail de solicitud de instalación");
                                                                }

                                                            }//(isset($entitySolicitud))
                                                        }//($entityInfoServicio && count($entityInfoServicio) > 0)
                                                    }//foreach($arrayServiciosEncontrados as $intId)
                                                }/*ELSE( $arrayGuardarProceso && array_key_exists('status', $arrayGuardarProceso) 
                                                        && $arrayGuardarProceso['status']=='ERROR_SERVICE' )*/
                                            }
                                            else
                                            {
                                                $objOutput->writeln("No se encontró facturas generadas punto en base al contrato ".$intIdContrato);
                                            }
                                        }//( empty($strCliente) )
                                        else
                                        {
                                            $objOutput->writeln( "[ERROR AL APROBAR CONTRATO DIGITAL]: Ya existe un cliente "
                                                            ."con la misma identificación, ".
                                                            "por favor corregir y volver a intentar. IDENTIFICACION = ".
                                                            $objProspecto->getIdentificacionCliente() );
                                        }
                                    }//( $objProspecto && $objEmpresaRol && $boolSeguir )
                                    else
                                    {
                                        $objOutput->writeln("[ERROR AL APROBAR CONTRATO DIGITAL]: ID_PERSONA_ROL = ".$objPersonaEmpresaRol->getId());
                                    }
                                }//( $boolSeguir && $objPersonaEmpresaRol )
                                else
                                {
                                    $objOutput->writeln("[ERROR AL APROBAR CONTRATO DIGITAL]: CONTRATO_ID = ".$intIdContrato);
                                }
                            }//( $intFacturasNoPagadas == 0 && $intFacturasCerradas > 0 )
                        }//foreach( $arrayResultados['registros'] as $arrayItem )


                        $objOutput->writeln('Contratos '. $strPrefijoEmpresa .' Aprobados: ' . $intContadorContratosAprobados);
                    }//( !empty($arrayResultados['registros']) )
                    //preplanificada
                    $arrayAdendum = $this->emFinanciero->getRepository('schemaBundle:InfoServicio')
                                                        ->getAdendumConFacturas($strEmpresaCod);
                    $objOutput->writeln("*******PROCESAR ADENDUMS - EMPRESA " . $strPrefijoEmpresa . "**********");

                    if( !empty($arrayAdendum['registros']) )
                    {
                        foreach( $arrayAdendum['registros'] as $arrayItem )
                        {
                            $intIdContrato              = $arrayItem['id'];
                            $intFacturasNoPagadas       = $arrayItem['FACT_ACTIVAS_PENDIENTES'];
                            $intFacturasCerradas        = $arrayItem['FACT_CERRADAS'];      
                            $arrayServiciosEncontrados  = array();

                            
                            if( $intFacturasNoPagadas == 0 && $intFacturasCerradas > 0 )
                            {
                                $objOutput->writeln( "[PROCESANDO ADENDUMS DEL CONTRATO]: ".$intIdContrato );

                                $objContrato            = $this->getDatosContratoId($intIdContrato);
                                $objPersonaEmpresaRol   = null;
                                $objEmpresaRol          = null;
                                $objProspecto           = null;
                                
                                $objOutput->writeln( "[FACT_ACTIVAS_PENDIENTES]: ".$intFacturasNoPagadas );
                                $objOutput->writeln( "[FACT_CERRADAS]: ".$intFacturasCerradas );

                                if( $objContrato)
                                {
                                    $boolSeguirAprob = true;
                                    $arrayAdendumServicio = $this->emcom->getRepository('schemaBundle:InfoAdendum')
                                                                            ->findBy(array("contratoId" => $intIdContrato,
                                                                                            "tipo"       => "AP",
                                                                                            "estado"     => "Pendiente" ));
                                    $strNumeroAdendum =  "";   
                                    foreach($arrayAdendumServicio as $objAdendum)
                                    {
                                        $strNumeroAdendum = $objAdendum->getId();
                                        
                                        $entityServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                                            ->findOneById( $objAdendum->getServicioId());
                                        
                                        if( $entityServicio )
                                        {
                                            $strEstadoServicio = $entityServicio->getEstado();
                                            
                                            if($strEstadoServicio=='Factible')
                                            {
                                                $objServicio = array();
                                                $objServicio['intIdAdendum'] = $strNumeroAdendum;
                                                $objServicio['intIdPunto'] = $objAdendum->getPuntoId();
                                                $objServicio['strNumeroAdendum'] = $objAdendum->getNumero();
                                                $objServicio['strTipo']= $objAdendum->getTipo();
                                                $objServicio['strOrigen'] = $objAdendum->getOrigen();
                                                $objServicio['intIdServicio'] = $objAdendum->getServicioId();

                                                array_push($arrayServiciosEncontrados,$objServicio);
                                                
                                                $objOutput->writeln( "[SERVICIO FACTIBLE ENCONTRADO]: ".$objAdendum->getServicioId() );
                                            }
                                            else
                                            {
                                                    $objOutput->writeln( "[SERVICIO NO FACTIBLE]: ".$objAdendum->getServicioId() );
                                            }
                                            
                                        }

                                        $arrayDocumentoFinanciero = $this->emFinanciero
                                                                        ->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                        ->existeFacturaCerradaPunto(
                                                                            array(
                                                                                "intPunto" => $objAdendum->getPuntoId()
                                                                            )
                                                                        );

                                        if(!$arrayDocumentoFinanciero || count($arrayDocumentoFinanciero)==0 || 
                                        !isset($arrayDocumentoFinanciero[0]['intIdDocumento']))
                                        {
                                            $boolSeguirAprob = false;
                                        }
                                        
                                    }

                                    if($boolSeguirAprob)
                                    {
                                        foreach($arrayServiciosEncontrados as $objServicio)
                                        {
                                            //Funcion que guarda todo el proceso
                                            $objServicio['strIpCreacion']           = $strIpCreacion;
                                            $objServicio['strEmpresaCod']           = $strEmpresaCod;
                                            $objServicio['strPrefijoEmpresa']       = $strPrefijoEmpresa;
                                            $objServicio['strUsrCreacion']          = $strUsrCreacion;
                                            $objServicio['intPersonaEmpresaRolId']  = $objContrato->getPersonaEmpresaRolId()->getId();
                                            $objServicio['intIdContrato']           = $objContrato->getId();
                                            $objServicio['strAplicaTentativa']      = "N";

                                            $objOutput->writeln( "DATA APROBACION: ". json_encode($objServicio) );

                                            $arrayRespuesta = $this->serviceInfoContrato->procesaAprobacionContrato($objServicio);

                                            if( $arrayRespuesta && array_key_exists('status', $arrayRespuesta) 
                                                && $arrayRespuesta['status']!='OK' )
                                            {
                                                $objOutput->writeln( "[ERROR AL APROBAR ADENDUM - " . $objServicio['intIdAdendum'] . "]: = ".
                                                                $arrayRespuesta['mensaje'] );
                                            }
                                            else
                                            {
                                                $intContadorAdendumsAprobados++;
                                                $objOutput->writeln("[ID DEL ADENDUM APROBADO]" . $objServicio['intIdAdendum']);
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $objOutput->writeln("No se encontró facturas generadas punto en base al contrato ".$intIdContrato);
                                    }
                                
                                }//( $objContrato)
                                
                            }                                           
                        }    
                        $objOutput->writeln('Adendums '. $strPrefijoEmpresa .' Aprobados: ' . $intContadorAdendumsAprobados);                   
                    } 
                    else
                    {
                        $objOutput->writeln("No se encontraron adendum para aprobar ");
                    } 
                    
                    if($this->emFinanciero->getConnection()->isTransactionActive())
                    {
                        $this->emFinanciero->getConnection()->commit();
                    }
                }
                else
                {           

                    $objOutput->writeln("[ERROR AL APROBAR CONTRATO DIGITAL]: NO SE ENCONTRO ID EMPRESA DEL PREFIJO ENVIADO ".$strPrefijoEmpresa);
                }//( empty($strEmpresaCod) ) 
            }
        }
        catch(\Exception $e)
        {
            if($this->emFinanciero->getConnection()->isTransactionActive())
            {
                $this->emFinanciero->getConnection()->rollback();
            }

            $objOutput->writeln("[ERROR AL APROBAR CONTRATO DIGITAL]: ".$e->getMessage());
        }
        
        $this->emFinanciero->close();
    }
    
    
    /**    
     * Documentación para el método 'rechazarContratoDigitalPorNoPagarFactura'.
     *
     * Función que rechazará los contratos digitales cuando el cliente no haya pagado la o las facturas de instalación.
     * Para este proceso se realiza lo siguiente:
     * - Se coloca en estado 'Cancelado' la InfoPersonaEmpresaRol
     * - Se coloca en estado 'Rechazado' la InfoContrato
     * - Se coloca en estado 'Eliminado' la InfoPunto
     * - Se coloca en estado 'Eliminado' la InfoServicio
     * - Se coloca en estado 'not connect' los puertos asociados a los servicios (liberación de puertos)
     * - Se coloca en estado 'Eliminado' las solicitudes asociadas al servicio en la InfoDetalleSolicitud
     * - Se crean N/C en caso de que la(s) factura(s) en estado 'Activo'
     * - Se enviará un correo con la(s) factur(a) que este(n) en estado 'Pendiente', para la respectiva gestión manual por los usuarios.
     * - Se eliminan las formas de contacto de la persona y del punto
     * 
     * @return int $intContadorContratosRechazados
     * 
     * @author Edson Franco <efranco@telconet.ec>       
     * @version 1.0 05-08-2016
     * 
     * @author Anabelle Peñlaherrera <apenaherrera@telconet.ec>       
     * @version 1.1 03-05-2018
     * 
     * Se agrega Validacion al momento de generar NC automatica a la Factura de Instalación por Contrato Digital, que se verifique si
     * ya existe una NC manual aplicada a la Factura, de ser el caso no debe generarse la NC en el proceso de Rechazo.
     * Se agrega funcion getFacturasPorPuntoPorEstado que obtiene Facturas asociadas al Punto por estados Activo, Cerrado, Pendiente
     * por Tipo de Documento Factura o Factura Proporcional.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.2 05-10-2020 Impresión de información en logs al rechazar contratos.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.3 15-10-2020 - Se agrega nuevo parámetro 'intEditValoresNcCaract' al $arrayParametrosNc para el proceso de 
     *                           generar nota de crédito.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.4 29-07-2022 - Se agrega envío de parámetros de características,valor y estado en la función getFacturasPorPuntoPorEstado  
     *                           para validación en el query que obtenga facturas con la características POR_CONTRATO_FISICO o POR_CONTRATO_DIGITAL.
     * 
     * @author Alex Gómez <algomez@telconet.ec>
     * @version 1.5 06-03-2023 - Se adapta proceso para multiempresa
     * 
     */
    public function rechazarContratoDigitalPorNoPagarFactura($arrayParams,  OutputInterface $objOutput)
    {    
        $strUsrCreacion                 = $arrayParams["strUsrCreacion"];
        $strIpCreacion                  = $arrayParams["strIpCreacion"];
        $strObservacionHistorial        = 'Se cancela por falta de pago de las facturas de instalación';
        $strMotivo                      = 'Falta de pago de las facturas de instalación';
        $intContadorContratosRechazados = 0;
       
        $this->emFinanciero->getConnection()->beginTransaction();  
        $this->emcom->getConnection()->beginTransaction(); 
        $this->emInfraestructura->getConnection()->beginTransaction();
        
        try
        {
            //Consulta prefijos de empresas a procesar
            $arrayPrefijosEmpresas = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne(
                                                        'APROBACION_CONTRATO_COMMAND',
                                                        'COMERCIAL',
                                                        '',
                                                        'PREFIJOS_EMPRESA',
                                                        '','','','','',
                                                        '');

            if(empty($arrayPrefijosEmpresas) || empty($arrayPrefijosEmpresas["valor1"]))
            {
                throw new \Exception("No se encontraron los prefijos de las empresas a procesar");
            }

            $arrayPrefijosEmpresas = explode(",",$arrayPrefijosEmpresas["valor1"]);

            //Recorre proceso de aprobación por cada empresa parametrizado
            foreach($arrayPrefijosEmpresas as $strPrefijoEmpresa)
            {

                $objInfoEmpresaGrupo = $this->emcom->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($strPrefijoEmpresa);
                $strEmpresaCod       = "";
                
                if( is_object($objInfoEmpresaGrupo) )
                {
                    $strEmpresaCod = $objInfoEmpresaGrupo->getId() ? $objInfoEmpresaGrupo->getId() : "";
                    $objOutput->writeln("\tEmpresa: " . $strEmpresaCod);
                }

                if( !empty($strEmpresaCod) )
                {
                    $objOutput->writeln("\tObteniendo registros de facturas...");
                    $arrayResultados = $this->emFinanciero->getRepository('schemaBundle:InfoServicio')
                                                        ->getContratosDigitalesConFacturas($strEmpresaCod);

                    $objOutput->writeln("\tRegistros obtenidos de facturas: " . count($arrayResultados['registros']));

                    if( !empty($arrayResultados['registros']) )
                    {
                        foreach( $arrayResultados['registros'] as $arrayItem )
                        {
                            $intIdContrato          = $arrayItem['id'];
                            $intFacturasNoPagadas   = $arrayItem['FACT_ACTIVAS_PENDIENTES'];
                            $intFacturasCerradas    = $arrayItem['FACT_CERRADAS'];

                            $objOutput->writeln("\tContrato a procesar: " . $intIdContrato);
                            $objOutput->writeln("\tId Prefijo Empresa: ". $strPrefijoEmpresa );
                            $objOutput->writeln("\tFacturas activas pendientes: " . $intFacturasNoPagadas);
                            $objOutput->writeln("\tFacturas cerradas: " . $intFacturasCerradas);

                            if( $intFacturasNoPagadas > 0 && $intFacturasCerradas >= 0 )
                            {

                                $boolSeguir                 = false;
                                $objOutput->writeln("\t\tObteniendo registros de vista...");
                                $objVistaContratosRechazado = $this->emFinanciero->getRepository('schemaBundle:VistaContratosRechazar')
                                                                                ->findById($intIdContrato);
                                $objOutput->writeln("\t\tRegistros obtenidos de vista: " . (!is_null($objVistaContratosRechazado)
                                    ? count($objVistaContratosRechazado)
                                    : "0"));
                                $arrayDestinatarios         = array();
                                $strCuerpoCorreo            = "";
                                $intContadorFacturasSinNc   = 0;

                                if( !empty($objVistaContratosRechazado) )
                                {
                                    $objOutput->writeln("\t\tVerificando rechazo de contrato...");

                                    foreach($objVistaContratosRechazado as $objContratoARechazar)
                                    {
                                        if(!is_null($objContratoARechazar))
                                        {
                                            $strRechazar = $objContratoARechazar->getRechazar();

                                            if (!empty($strRechazar))
                                            {
                                                $objOutput->writeln("\t\tVerificando... " . $strRechazar);
                                            }
                                            else
                                            {
                                                $objOutput->writeln("\t\tVerificando... Parametro de rechazo no definido");
                                            }
                                        }
                                        else
                                        {
                                            $objOutput->writeln("\t\tobjContratoARechazar no definido");
                                        }
                                        if( $objContratoARechazar->getRechazar() == "S" )
                                        {
                                            $boolSeguir = true;
                                            $objOutput->writeln("\t\tContrato se rechaza");
                                            break;
                                        }//( $objContratoARechazar->getRechazar() == "S" )
                                    }
                                    //foreach($objVistaContratosRechazado as $objContratoARechazar)
                                }
                                else
                                {
                                    $objOutput->writeln("\t\tRespuesta vista sin registros, Contrato NO se rechaza...");
                                }//( !empty($objVistaContratosRechazado) )

                                if( $boolSeguir )
                                {
                                    $objContrato            = $this->getDatosContratoId($intIdContrato);
                                    $objPersonaEmpresaRol   = null;
                                    $objPersona             = null;
                                    $objMotivoContrato      = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                                            ->findOneByNombreMotivo($strMotivo);

                                    if( $objContrato)
                                    {
                                        if( $objContrato->getPersonaEmpresaRolId() )
                                        {
                                            $intTmpIdPersonaEmpresaRolId = $objContrato->getPersonaEmpresaRolId()->getId();
                                            $objPersonaEmpresaRol        = $this->getDatosPersonaEmpresaRolId($intTmpIdPersonaEmpresaRolId);
                                        }//( $objContrato->getPersonaEmpresaRolId() )

                                        /**
                                         * SE RECHAZA EL CONTRATO
                                         */
                                        $intTmpMotivoRechazoId = $objMotivoContrato ? $objMotivoContrato->getId() : 0;

                                        $objContrato->setEstado('Rechazado');
                                        $objContrato->setMotivoRechazoId($intTmpMotivoRechazoId);
                                        $objContrato->setUsrRechazo($strUsrCreacion);
                                        $objContrato->setFeRechazo(new \DateTime('now'));
                                        $this->emcom->persist($objContrato);
                                        $this->emcom->flush();
                                        /**
                                         * FIN SE RECHAZA EL CONTRATO
                                         */

                                        $objOutput->writeln("\t\t\tContrato rechazado");

                                        /**
                                         * SE INACTIVA LA FORMA DE PAGO DEL CONTRATO
                                         */
                                        $intTmpFormaPagoId         = $objContrato->getFormaPagoId() ? $objContrato->getFormaPagoId()->getId() : 0;
                                        $objInfoContratoFormasPago = $this->emcom->getRepository('schemaBundle:InfoContratoFormaPago')
                                                                                ->findBy( array( 'estado'      => array('Pendiente', 'Activo'),
                                                                                                'contratoId'  => $objContrato ) );
                                        
                                        if( !empty($objInfoContratoFormasPago) )
                                        {
                                            foreach($objInfoContratoFormasPago as $objInfoContratoFormaPago)
                                            {
                                                $objInfoContratoFormaPago->setEstado('Inactivo');
                                                $objInfoContratoFormaPago->setFeUltMod(new \DateTime('now'));
                                                $objInfoContratoFormaPago->setUsrUltMod($strUsrCreacion);
                                                $this->emcom->persist($objInfoContratoFormaPago);
                                                $this->emcom->flush();
                                                
                                                
                                                //SE CREA EL HISTORIAL DE LA FORMA DE PAGO INACTIVA
                                                $intIdTipoCuentaTmp = $objInfoContratoFormaPago->getTipoCuentaId() 
                                                                    ? $objInfoContratoFormaPago->getTipoCuentaId()->getId() : 0;
                                                    
                                                $objInfoContratoFormaPagoHisto = new InfoContratoFormaPagoHist();
                                                $objInfoContratoFormaPagoHisto->setMesVencimiento($objInfoContratoFormaPago->getMesVencimiento());
                                                $objInfoContratoFormaPagoHisto->setAnioVencimiento($objInfoContratoFormaPago->getAnioVencimiento());
                                                $objInfoContratoFormaPagoHisto->setBancoTipoCuentaId($objInfoContratoFormaPago->getBancoTipoCuentaId());
                                                $objInfoContratoFormaPagoHisto->setCedulaTitular($objInfoContratoFormaPago->getCedulaTitular());
                                                $objInfoContratoFormaPagoHisto->setCodigoVerificacion($objInfoContratoFormaPago->getCodigoVerificacion());
                                                $objInfoContratoFormaPagoHisto->setContratoId($objContrato);
                                                $objInfoContratoFormaPagoHisto->setEstado('Inactivo');
                                                $objInfoContratoFormaPagoHisto->setFeCreacion(new \DateTime('now'));
                                                $objInfoContratoFormaPagoHisto->setFeUltMod(new \DateTime('now'));
                                                $objInfoContratoFormaPagoHisto->setFormaPago($intTmpFormaPagoId);
                                                $objInfoContratoFormaPagoHisto->setIpCreacion($strIpCreacion);
                                                $objInfoContratoFormaPagoHisto->setNumeroCtaTarjeta($objInfoContratoFormaPago->getNumeroCtaTarjeta());
                                                $objInfoContratoFormaPagoHisto->setNumeroDebitoBanco($objInfoContratoFormaPago->getNumeroDebitoBanco());
                                                $objInfoContratoFormaPagoHisto->setTipoCuentaId($intIdTipoCuentaTmp);
                                                $objInfoContratoFormaPagoHisto->setTitularCuenta($objInfoContratoFormaPago->getTitularCuenta());
                                                $objInfoContratoFormaPagoHisto->setUsrCreacion($strUsrCreacion);
                                                $objInfoContratoFormaPagoHisto->setUsrUltMod($strUsrCreacion);
                                                $this->emcom->persist($objInfoContratoFormaPagoHisto);
                                                $this->emcom->flush();
                                            }//foreach($objInfoContratoFormasPago as $objInfoContratoFormaPago)

                                            $objOutput->writeln("\t\t\tSe inactiva formas de pago del contrato");

                                        }//( !empty($objInfoContratoFormasPago) )
                                        /**
                                         * FIN SE INACTIVA LA FORMA DE PAGO DEL CONTRATO
                                         */


                                        /**
                                         * SE CANCELA EL PRE-CLIENTE
                                         */
                                        if( !empty($objPersonaEmpresaRol) )
                                        {
                                            if( $objPersonaEmpresaRol->getPersonaId() )
                                            {
                                                $objPersona = $objPersonaEmpresaRol->getPersonaId();
                                                
                                                /**
                                                 * SE ELIMINAN LAS FORMAS DE CONTACTO DE LA PERSONA
                                                 */
                                                $objPersonaFormasContacto = $this->emcom->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                                        ->findBy( array( 'estado'    => 'Activo',
                                                                                                        'personaId' => $objPersona ) );
                                                
                                                if( !empty($objPersonaFormasContacto) )
                                                {
                                                    foreach($objPersonaFormasContacto as $objPersonaFormaContacto)
                                                    {
                                                        $objPersonaFormaContacto->setEstado('Inactivo');
                                                        $objPersonaFormaContacto->setFeUltMod(new \DateTime('now'));
                                                        $objPersonaFormaContacto->setUsrUltMod($strUsrCreacion);
                                                        $this->emcom->persist($objPersonaFormaContacto);
                                                        $this->emcom->flush();
                                                    }//foreach($objPersonaFormasContacto as $objPersonaFormaContacto)

                                                    $objOutput->writeln("\t\t\tSe inactiva formas de pago de la persona");

                                                }//( !empty($objPersonaFormasContacto) )
                                                /**
                                                 * FIN SE ELIMINAN LAS FORMAS DE CONTACTO DE LA PERSONA
                                                 */
                                            }//( $objPersonaEmpresaRol->getPersonaId() )
                                            
                                            $objPersonaEmpresaRol->setEstado('Cancelado');
                                            $this->emcom->persist($objContrato);
                                            $this->emcom->flush();

                                            $objOutput->writeln("\t\t\tSe cancela rol de la persona");

                                            //SE INGRESA EL HISTORIAL DE LA INFO_PERSONA_EMPRESA_ROL
                                            $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
                                            $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                                            $objInfoPersonaEmpresaRolHisto->setUsrCreacion($strUsrCreacion);
                                            $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
                                            $objInfoPersonaEmpresaRolHisto->setIpCreacion($strIpCreacion);
                                            $objInfoPersonaEmpresaRolHisto->setMotivoId($intTmpMotivoRechazoId);
                                            $objInfoPersonaEmpresaRolHisto->setObservacion($strObservacionHistorial);
                                            $objInfoPersonaEmpresaRolHisto->setEstado('Cancelado');
                                            $this->emcom->persist($objInfoPersonaEmpresaRolHisto);
                                            $this->emcom->flush();
                                        }//( !empty($objPersonaEmpresaRol) )
                                        /**
                                         * FIN SE CANCELA EL PRE-CLIENTE
                                         */


                                        /**
                                         * SE ELIMINAN LOS PUNTOS, SERVICIOS Y SOLICITUDES RELACIONADOS AL PRE-CLIENTE
                                         */
                                        $objInfoPuntosPreCliente = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                                                            ->findBy( array( 'estado'              => array('Pendiente', 'Activo'),
                                                                                                'personaEmpresaRolId' => $objPersonaEmpresaRol ) );

                                        if( !empty($objInfoPuntosPreCliente) )
                                        {
                                            foreach( $objInfoPuntosPreCliente as $objInfoPunto )
                                            {
                                                /**
                                                 * SE ELIMINAN LOS SERVICIOS EN ESTADO 'Factible' RELACIONADOS AL PRE-CLIENTE
                                                 */
                                                $objInfoServiciosFactibles = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                                                        ->findBy( array( 'estado'  => 'Factible',
                                                                                                        'puntoId' => $objInfoPunto ) );
                                                
                                                if( !empty($objInfoServiciosFactibles) )
                                                {
                                                    foreach( $objInfoServiciosFactibles as $objInfoServicio )
                                                    {
                                                        /**
                                                         * SE ELIMINAN LAS SOLICITUDES ASOCIADAS AL SERVICIO
                                                         */
                                                        $objInfoDetalleSolicitudes = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                                                ->findBy( array( 'estado'      => array( 'Pendiente', 
                                                                                                                                        'Aprobado',
                                                                                                                                        'Aprobada' ),
                                                                                                                'servicioId'  => $objInfoServicio ) );
                                                        
                                                        if( !empty($objInfoDetalleSolicitudes) )
                                                        {
                                                            foreach($objInfoDetalleSolicitudes as $objInfoDetalleSolicitud)
                                                            {
                                                                $objInfoDetalleSolicitud->setEstado('Eliminado');
                                                                $this->emcom->persist($objInfoDetalleSolicitud);
                                                                $this->emcom->flush();
                                                                
                                                                
                                                                //SE CREA HISTORIAL DE LA SOLICITUD ELIMINADA
                                                                $objInfoDetalleSolHistorial = new InfoDetalleSolHist();
                                                                $objInfoDetalleSolHistorial->setDetalleSolicitudId($objInfoDetalleSolicitud);
                                                                $objInfoDetalleSolHistorial->setEstado('Eliminado');
                                                                $objInfoDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
                                                                $objInfoDetalleSolHistorial->setIpCreacion($strIpCreacion);
                                                                $objInfoDetalleSolHistorial->setMotivoId($intTmpMotivoRechazoId);
                                                                $objInfoDetalleSolHistorial->setUsrCreacion($strUsrCreacion);
                                                                $objInfoDetalleSolHistorial->setObservacion($strObservacionHistorial);
                                                                $this->emcom->persist($objInfoDetalleSolHistorial);
                                                                $this->emcom->flush();
                                                            }//foreach($objInfoDetalleSolicitudes as $objInfoDetalleSolicitud)

                                                            $objOutput->writeln("\t\t\tSe elimina solictudes del servicio");

                                                        }//( !empty($objInfoDetalleSolicitudes) )
                                                        /**
                                                         * FIN SE ELIMINAN LAS SOLICITUDES ASOCIADAS AL SERVICIO
                                                         */
                                                        
                                                        
                                                        /**
                                                         * SE LIBERAN LOS PUERTOS ASOCIADOS AL SERVICIO FACTIBLE
                                                         */
                                                        $objInfoServicioTecnico = $this->emcom->getRepository("schemaBundle:InfoServicioTecnico")
                                                                                            ->findOneByServicioId($objInfoServicio);
                                                        
                                                        if( !empty($objInfoServicioTecnico) )
                                                        {
                                                            $strObservacionLiberarPuerto    = "Se libera la siguiente interface elemento conector: ";
                                                            $intIdInterfaceElementoConector = $objInfoServicioTecnico->getInterfaceElementoConectorId();
                                                            
                                                            if( $intIdInterfaceElementoConector > 0 )
                                                            {
                                                                $objInterface = $this->emInfraestructura
                                                                                    ->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                                    ->findOneById($intIdInterfaceElementoConector);
                                                                
                                                                if( !empty($objInterface) )
                                                                {
                                                                    $strObservacionLiberarPuerto .= $intIdInterfaceElementoConector;
                                                                    
                                                                    $objInterface->setEstado('not connect');
                                                                    $this->emInfraestructura->persist($objInterface);
                                                                    $this->emInfraestructura->flush();

                                                                    $objOutput->writeln("\t\t\tSe libera puerto");
                                                                }
                                                                else
                                                                {
                                                                    $strObservacionLiberarPuerto = "No se encontró interface elemento conector para ".
                                                                                                "liberar";
                                                                }//( !empty($objInterface) )
                                                            }//( $intIdInterfaceElementoConector > 0 )
                                                            else
                                                            {
                                                                $strObservacionLiberarPuerto = "No se encontró interface elemento conector para liberar";
                                                            }
                                                            
                                                            
                                                            //SE CREA HISTORIAL EN EL SERVICIO POR LIBERACIÓN DE PUERTO
                                                            $objInfoServicioHistorial = new InfoServicioHistorial();
                                                            $objInfoServicioHistorial->setEstado($objInfoServicio->getEstado());
                                                            $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                                                            $objInfoServicioHistorial->setIpCreacion($strIpCreacion);
                                                            $objInfoServicioHistorial->setMotivoId($intTmpMotivoRechazoId);
                                                            $objInfoServicioHistorial->setObservacion($strObservacionLiberarPuerto);
                                                            $objInfoServicioHistorial->setServicioId($objInfoServicio);
                                                            $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
                                                            $this->emcom->persist($objInfoServicioHistorial);
                                                            $this->emcom->flush();
                                                        }//( !empty($objInfoServicioTecnico) )
                                                        /**
                                                         * FIN SE LIBERAN LOS PUERTOS ASOCIADOS AL SERVICIO FACTIBLE
                                                         */
                                                        
                                                        
                                                        /**
                                                         * SE ELIMINAN LOS SERVICIO
                                                         */
                                                        $objInfoServicio->setEstado('Eliminado');
                                                        $this->emcom->persist($objInfoServicio);
                                                        $this->emcom->flush();

                                                        $objOutput->writeln("\t\t\tSe elimina servicio " . $objInfoServicio->getId());

                                                        //SE CREA HISTORIAL DEL SERVICIO ELIMINADO
                                                        $objInfoServicioHistorial = new InfoServicioHistorial();
                                                        $objInfoServicioHistorial->setEstado($objInfoServicio->getEstado());
                                                        $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                                                        $objInfoServicioHistorial->setIpCreacion($strIpCreacion);
                                                        $objInfoServicioHistorial->setMotivoId($intTmpMotivoRechazoId);
                                                        $objInfoServicioHistorial->setObservacion($strObservacionHistorial);
                                                        $objInfoServicioHistorial->setServicioId($objInfoServicio);
                                                        $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
                                                        $this->emcom->persist($objInfoServicioHistorial);
                                                        $this->emcom->flush();

                                                        $objOutput->writeln("\t\t\tSe agrega historial del servicio");
                                                        /**
                                                         * FIN SE ELIMINAN LOS SERVICIO
                                                         */
                                                    }//foreach( $objInfoServiciosFactibles as $objInfoServicio )
                                                }//( !empty($objInfoServiciosFactibles) )
                                                /**
                                                 * FIN SE ELIMINAN LOS SERVICIOS EN ESTADO 'Factible' RELACIONADOS AL PRE-CLIENTE
                                                 */
                                                
                                                
                                                /**
                                                 * SE ELIMINAN LAS FORMAS DE CONTACTO DEL PUNTO
                                                 */
                                                $objPuntoFormasContacto = $this->emcom->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                                    ->findBy( array( 'estado'  => 'Activo',
                                                                                                    'puntoId' => $objInfoPunto ) );
                                                
                                                if( !empty($objPuntoFormasContacto) )
                                                {
                                                    foreach($objPuntoFormasContacto as $objPuntoFormaContacto)
                                                    {
                                                        $objPuntoFormaContacto->setEstado('Eliminado');
                                                        $this->emcom->persist($objPuntoFormaContacto);
                                                        $this->emcom->flush();
                                                    }//foreach($objPuntoFormasContacto as $objPuntoFormaContacto)

                                                    $objOutput->writeln("\t\t\tSe elimina formas de contacto del punto " . $objInfoPunto->getLogin());

                                                }//( !empty($objPuntoFormasContacto) )
                                                /**
                                                 * SE ELIMINAN LAS FORMAS DE CONTACTO DEL PUNTO
                                                 */
                                                
                                                /**
                                                 * SE ELIMINAN LOS PUNTOS
                                                 */
                                                $objInfoPunto->setEstado('Eliminado');
                                                $objInfoPunto->setFeUltMod(new \DateTime('now'));
                                                $objInfoPunto->setUsrUltMod($strUsrCreacion);
                                                $objInfoPunto->setIpUltMod($strIpCreacion);
                                                $this->emcom->persist($objInfoPunto);
                                                $this->emcom->flush();

                                                $objOutput->writeln("\t\t\tSe elimina el punto " . $objInfoPunto->getLogin());

                                                //SE CREA HISTORIAL DEL PUNTO ELIMINADO
                                                $objInfoPuntoHistorial = new InfoPuntoHistorial();
                                                $objInfoPuntoHistorial->setFeCreacion(new \DateTime('now'));
                                                $objInfoPuntoHistorial->setIpCreacion($strIpCreacion);
                                                $objInfoPuntoHistorial->setPuntoId($objInfoPunto);
                                                $objInfoPuntoHistorial->setUsrCreacion($strUsrCreacion);
                                                $objInfoPuntoHistorial->setValor($strObservacionHistorial);
                                                $this->emcom->persist($objInfoPuntoHistorial);
                                                $this->emcom->flush();

                                                $objOutput->writeln("\t\t\tSe agrega historial del punto");
                                                /**
                                                 * FIN SE ELIMINAN LOS PUNTOS
                                                 */
                                                
                                                
                                                /**
                                                 * CREACION DE N/C
                                                 * 
                                                 * SE CREAN LAS N/C DE LAS FACTURAS ACTIVAS Y CERRADAS, Y SE ENVIAN POR CORREO LAS FACTURAS QUE ESTAN EN
                                                 * ESTADO PENDIENTE PARA LA CORRESPONDIENTE GESTIÓN MANUAL POR EL DEPARTAMENTO DE FACTURACIÓN
                                                 */
                                                /* Se agrega funcion que obtiene Facturas asociadas al Punto por estados Activo, Cerrado, Pendiente
                                                * por Tipo de Documento Factura o Factura Proporcional.
                                                */
                                                $arrayFacturas = $this->emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                                                    ->getFacturasPorPuntoPorEstado(array(
                                                                                    'intIdPunto'         => $objInfoPunto->getId(),
                                                                                    'arrayInEstados'     => array('Activo','Cerrado','Pendiente'),
                                                                                    'arrayTipoDocumento' => array('FAC','FACP'),
                                                                                    'arrayCaracteristicas' => array('POR_CONTRATO_FISICO',
                                                                                                                    'POR_CONTRATO_DIGITAL'),
                                                                                    'strValor'             => 'S',
                                                                                    'strEstadoCaracDoc'    => 'Activo')); 
                                                if( !empty($arrayFacturas) )
                                                {
                                                    foreach($arrayFacturas as $objInfoDocumentoFinancieroCab)
                                                    {
                                                        $objNotaCreditoActivas = $this->emFinanciero
                                                                                    ->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                                    ->getNotasDeCreditoActivas(array(
                                                                                        'intIdDocumento' => $objInfoDocumentoFinancieroCab->getId(),
                                                                                        'arrayInEstados' => array('Activo')));
                                                
                                                        if( (empty($objNotaCreditoActivas) || !isset($objNotaCreditoActivas)) &&
                                                            ($objInfoDocumentoFinancieroCab->getEstadoImpresionFact() == 'Activo'
                                                            || $objInfoDocumentoFinancieroCab->getEstadoImpresionFact() == 'Cerrado') )
                                                        {
                                                            $boolCrearHistorial                           = false;
                                                            $arrayInformacionGrid                         = array();
                                                            $arrayParametrosDet                           = array();
                                                            $arrayParametrosDet["idFactura"]              = $objInfoDocumentoFinancieroCab->getId();
                                                            $arrayParametrosDet["tipo"]                   = "VO";
                                                            $arrayParametrosDet["fechaDesde"]             = null;
                                                            $arrayParametrosDet["fechaHasta"]             = null;
                                                            $arrayParametrosDet["porcentaje"]             = null;
                                                            $arrayParametrosDet["strPagaIva"]             = null;
                                                            $arrayParametrosDet["boolWithoutValues"]      = 'N';
                                                            $arrayParametrosDet["jsonListadoInformacion"] = null;

                                                            $arrayTmpDetallesNc = $this->serviceNotaCredito
                                                                                    ->generarDetallesNotaDeCredito($arrayParametrosDet);
                                                            
                                                            if( !empty($arrayTmpDetallesNc) )
                                                            {
                                                                foreach($arrayTmpDetallesNc as $arrayItem)
                                                                {
                                                                    $arrayItem["tipoNC"] = "ValorOriginal";
                                                                    $objItem             = (object) $arrayItem;
                                                                    
                                                                    $arrayInformacionGrid[] = $objItem;
                                                                }//foreach($arrayTmpDetallesNc as $arrayItem)
                                                            }//( !empty($arrayTmpDetallesNc) )
                                                            
                                                            
                                                            if( !empty($arrayInformacionGrid) )
                                                            {
                                                                $intTmpIdOficina  = $objInfoDocumentoFinancieroCab->getOficinaId();
                                                                    
                                                                $arrayParametrosNc                          = array();
                                                                $arrayParametrosNc["estado"]                = "Aprobada";
                                                                $arrayParametrosNc["codigo"]                = "NC";
                                                                $arrayParametrosNc["informacionGrid"]       = $arrayInformacionGrid;
                                                                $arrayParametrosNc["punto_id"]              = $objInfoPunto->getId();
                                                                $arrayParametrosNc["oficina_id"]            = $intTmpIdOficina;
                                                                $arrayParametrosNc["observacion"]           = $strMotivo;
                                                                $arrayParametrosNc["facturaId"]             = $objInfoDocumentoFinancieroCab->getId();
                                                                $arrayParametrosNc["user"]                  = $strUsrCreacion;
                                                                $arrayParametrosNc["motivo_id"]             = array($intTmpMotivoRechazoId);
                                                                $arrayParametrosNc["intIdEmpresa"]          = $strEmpresaCod;
                                                                $arrayParametrosNc["strEselectronica"]      = 'S';
                                                                $arrayParametrosNc["strPrefijoEmpresa"]     = $strPrefijoEmpresa;
                                                                $arrayParametrosNc["strPagaIva"]            = '';
                                                                $arrayParametrosNc["strTipoResponsable"]    = '';
                                                                $arrayParametrosNc["strClienteResponsable"] = '';
                                                                $arrayParametrosNc["strEmpresaResponsable"] = '';
                                                                $arrayParametrosNc["strIpCreacion"]         = $strIpCreacion;
                                                                $arrayParametrosNc["strDescripcionInterna"] = '';
                                                                $arrayParametrosNc["strTipoNotaCredito"]    = 'El tipo de la nota de crédito es Valor '.
                                                                                                            'Original';
                                                                $arrayParametrosNc["intEditValoresNcCaract"] = 0;

                                                                $objNotaDeCredito = $this->serviceNotaCredito->generarNotaDeCredito($arrayParametrosNc);
                                                                
                                                                $boolCrearHistorial = ($objNotaDeCredito)?false:true;

                                                                if( !$boolCrearHistorial && $objNotaDeCredito->getId() > 0 )
                                                                {
                                                                    //Obtiene los datos de numeracion
                                                                    $objNumeracion  = $this->emFinanciero
                                                                                        ->getRepository('schemaBundle:AdmiNumeracion')
                                                                                        ->findOficinaMatrizYFacturacion( $strEmpresaCod, 'NCE' );
                                                                    
                                                                    $strSecuencia = str_pad($objNumeracion->getSecuencia(), 9, "0", STR_PAD_LEFT);
                                                                    
                                                                    //Genera el numero de NC
                                                                    $strNumeroFacturaSri = $objNumeracion->getNumeracionUno()."-". 
                                                                                        $objNumeracion->getNumeracionDos()."-".$strSecuencia;
                                                                    
                                                                    //Actualiza el numero de NC del SRI
                                                                    $objNotaDeCredito->setNumeroFacturaSri($strNumeroFacturaSri);
                                                                    $this->emFinanciero->persist($objNotaDeCredito);
                                                                    $this->emFinanciero->flush();
                                                                    
                                                                    //Actualizo la secuencia de la Numeracion de la NC
                                                                    $strSecuenciaNumeracion = ($objNumeracion->getSecuencia() + 1);
                                                                    $objNumeracion->setSecuencia($strSecuenciaNumeracion);
                                                                    $this->emFinanciero->persist($objNumeracion);
                                                                    $this->emFinanciero->flush();

                                                                    $objOutput->writeln("\t\t\tSe genera nota de crédito ". $strNumeroFacturaSri);
                                                                }
                                                                else
                                                                {
                                                                    $boolCrearHistorial = true;
                                                                }//( !$objNotaDeCredito->getId() > 0 )
                                                                
                                                            }//( !empty($arrayInformacionGrid) )
                                                            else
                                                            {
                                                                $boolCrearHistorial = true;
                                                            }
                                                                
                                                                
                                                            if( $boolCrearHistorial )
                                                            {
                                                                $strTmpEstadoFactura = $objInfoDocumentoFinancieroCab->getEstadoImpresionFact();

                                                                $objInfoDocumentoHistorial = new InfoDocumentoHistorial();
                                                                $objInfoDocumentoHistorial->setDocumentoId($objInfoDocumentoFinancieroCab);
                                                                $objInfoDocumentoHistorial->setEstado($strTmpEstadoFactura);
                                                                $objInfoDocumentoHistorial->setFeCreacion(new \DateTime('now'));
                                                                $objInfoDocumentoHistorial->setObservacion("No se crea NC en el proceso de rechazar"
                                                                                                            ." contratos");
                                                                $objInfoDocumentoHistorial->setUsrCreacion($strUsrCreacion);
                                                                $this->emFinanciero->persist($objInfoDocumentoHistorial);
                                                                $this->emFinanciero->flush();
                                                            }//( $boolCrearHistorial )
                                                        }/*( (empty($objNotaCreditoActivas) || !isset($objNotaCreditoActivas)) &&
                                                        * ($objInfoDocumentoFinancieroCab->getEstadoImpresionFact() == 'Activo'
                                                            || $objInfoDocumentoFinancieroCab->getEstadoImpresionFact() == 'Cerrado') )*/
                                                        elseif( (empty($objNotaCreditoActivas) || !isset($objNotaCreditoActivas)) &&
                                                                $objInfoDocumentoFinancieroCab->getEstadoImpresionFact() == 'Pendiente' )
                                                        {
                                                            $intContadorFacturasSinNc ++;
                                                            
                                                            $strCuerpoCorreo .= '<tr>'
                                                                                .'<td>'.$intContadorFacturasSinNc.'</td>'
                                                                                .'<td>'.$objInfoPunto->getLogin().'</td>'
                                                                                .'<td>'.$objInfoDocumentoFinancieroCab->getNumeroFacturaSri().'</td>'
                                                                                .'<td> $'.$objInfoDocumentoFinancieroCab->getValorTotal().'</td>'
                                                                                .'<td>'.$objInfoDocumentoFinancieroCab->getEstadoImpresionFact().'</td>'
                                                                                .'</tr>';
                                                            
                                                            //Obtiene los correos del usuario vendedor para la notificación correspondiente
                                                            $strUsrVendedor    = $objInfoPunto->getUsrVendedor();
                                                            $entityInfoPersona = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                                                                ->getContactosByLoginPersonaAndFormaContacto( $strUsrVendedor,
                                                                                                                            'Correo Electronico' );
                                                            
                                                            //Itera los correos de los usuarios para almacenarlos en $arrayDestinatarios
                                                            foreach($entityInfoPersona as $arrayPersonaFormaContato)
                                                            {
                                                                if(!empty($arrayPersonaFormaContato['valor']))
                                                                {
                                                                    $arrayDestinatarios[] = $arrayPersonaFormaContato['valor'];
                                                                }
                                                            }//foreach($entityInfoPersona as $arrayPersonaFormaContato)
                                                        }/*( (empty($objNotaCreditoActivas) || !isset($objNotaCreditoActivas)) &&
                                                        * objInfoDocumentoFinancieroCab->getEstadoImpresionFact() == 'Pendiente' )
                                                        */
                                                    }//foreach($arrayFacturas as $objInfoDocumentoFinancieroCab)
                                                }//( !empty($arrayFacturas) )
                                                /**
                                                 * FIN CREACION DE N/C
                                                 */
                                            }//foreach( $objInfoPuntosPreCliente as $objInfoPunto )
                                        }//( !empty($objInfoPuntosPreCliente) )
                                        /**
                                         * FIN SE ELIMINAN LOS PUNTOS, SERVICIOS Y SOLICITUDES RELACIONADOS AL PRE-CLIENTE
                                         */
                                        
                                        $intContadorContratosRechazados++;
                                    }//( $objContrato)
                                }
                                else
                                {
                                    $objOutput->writeln("\t\tContrato NO se rechaza...");
                                }//( $boolSeguir )
                                
                                
                                /**
                                 * SE NOTIFICA A LOS USUARIOS CORRESPONDIENTES QUE FACTURAS NO SE LES CREO N/C
                                 */
                                if( !empty($arrayDestinatarios) && $strCuerpoCorreo != "" )
                                {                                
                                    $this->serviceEnvioPlantilla->generarEnvioPlantilla( 'Notificación Facturas sin Nota de Crédito', 
                                                                                        $arrayDestinatarios, 
                                                                                        'FACT_SN_NC', 
                                                                                        array('facturas' => $strCuerpoCorreo), 
                                                                                        $strEmpresaCod, 
                                                                                        '', 
                                                                                        '', 
                                                                                        NULL, 
                                                                                        FALSE );
                                    $objOutput->writeln("\t\t\tSe envia notificaciones al cliente");
                                }//( !empty($arrayDestinatarios) && $strCuerpoCorreo != "" )
                                /**
                                 * FIN SE NOTIFICA A LOS USUARIOS CORRESPONDIENTES QUE FACTURAS NO SE LES CREO N/C
                                 */
                            }//( $intFacturasNoPagadas > 0 && $intFacturasCerradas >= 0 )
                        }//foreach( $arrayResultados['registros'] as $arrayItem )
                    }//( !empty($arrayResultados['registros']) )

                    if($this->emcom->getConnection()->isTransactionActive())
                    {
                        $this->emcom->getConnection()->commit();
                    }
                    if($this->emFinanciero->getConnection()->isTransactionActive())
                    {
                        $this->emFinanciero->getConnection()->commit();
                    }
                    if($this->emInfraestructura->getConnection()->isTransactionActive())
                    {
                        $this->emInfraestructura->getConnection()->commit();
                    }
                }//( !empty($strEmpresaCod) )
                else
                {
                    $objOutput->writeln("[ERROR AL RECHAZAR CONTRATO DIGITAL]: NO SE ENCONTRO EL ID EMPRESA DEL PREFIJO ENVIADO ".$strPrefijoEmpresa);
                }//( empty($strEmpresaCod) )
            }
        }
        catch(\Exception $e)
        {
            $objOutput->writeln("[ERROR AL RECHAZAR CONTRATO DIGITAL]: ".$e->getMessage());
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
            }
            if($this->emFinanciero->getConnection()->isTransactionActive())
            {
                $this->emFinanciero->getConnection()->rollback();
            }
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
        }
        $this->emcom->close();
        $this->emFinanciero->close();
        $this->emInfraestructura->close();
        return $intContadorContratosRechazados;
    }
    
    
    /**    
     * Documentación para el método 'rechazarContratoPorError'.
     *
     * Función que rechazará el contrato digital enviado por parametro.
     * Para este proceso se realiza lo siguiente:
     * rechaza contrato
     * inactiva formas de pago
     * inserta historial de forma de pago
     * 
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.0 14-03-2017
     * @param array $arrayParametros[
     *     - objContrato    => datos del contrato
     *     - strUsrCreacion => usuario de creacion
     *     - strIpCreacion  => ip de creacion
     *     - strMotivo      => motivo de rechazo de contrato
     * ]
     */
    public function rechazarContratoPorError($arrayParametros)
    {
        $objContrato    = $arrayParametros['objContrato'];
        $strUsrCreacion = $arrayParametros['strUsrCreacion'];
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
        $strMotivo      = $arrayParametros['strMotivo'];

        if(is_object($objContrato))
        {
            $this->emcom->getConnection()->beginTransaction();
            try
            {
                /**
                 * SE RECHAZA EL CONTRATO
                 */
                $objMotivoContrato     = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMotivo);
                $intTmpMotivoRechazoId = $objMotivoContrato ? $objMotivoContrato->getId() : 0;

                $objContrato->setEstado('Rechazado');
                $objContrato->setMotivoRechazoId($intTmpMotivoRechazoId);
                $objContrato->setUsrRechazo($strUsrCreacion);
                $objContrato->setFeRechazo(new \DateTime('now'));
                $this->emcom->persist($objContrato);
                $this->emcom->flush();

                /**
                 * SE INACTIVA LA FORMA DE PAGO DEL CONTRATO
                 */
                $intTmpFormaPagoId           = $objContrato->getFormaPagoId() ? $objContrato->getFormaPagoId()->getId() : 0;
                $arrayInfoContratoFormasPago = $this->emcom->getRepository('schemaBundle:InfoContratoFormaPago')
                                                           ->findBy( array( 
                                                                           'estado'      => array('Pendiente', 'Activo'),
                                                                           'contratoId'  => $objContrato 
                                                                          )
                                                                   );

                if(count($arrayInfoContratoFormasPago)>0)
                {
                    foreach($arrayInfoContratoFormasPago as $objInfoContratoFormaPago)
                    {
                        $objInfoContratoFormaPago->setEstado('Inactivo');
                        $objInfoContratoFormaPago->setFeUltMod(new \DateTime('now'));
                        $objInfoContratoFormaPago->setUsrUltMod($strUsrCreacion);
                        $this->emcom->persist($objInfoContratoFormaPago);
                        $this->emcom->flush();


                        //SE CREA EL HISTORIAL DE LA FORMA DE PAGO INACTIVA
                        $intIdTipoCuentaTmp = is_object($objInfoContratoFormaPago->getTipoCuentaId())
                                              ? $objInfoContratoFormaPago->getTipoCuentaId()->getId() : 0;

                        $objInfoContratoFormaPagoHisto = new InfoContratoFormaPagoHist();
                        $objInfoContratoFormaPagoHisto->setMesVencimiento($objInfoContratoFormaPago->getMesVencimiento());
                        $objInfoContratoFormaPagoHisto->setAnioVencimiento($objInfoContratoFormaPago->getAnioVencimiento());
                        $objInfoContratoFormaPagoHisto->setBancoTipoCuentaId($objInfoContratoFormaPago->getBancoTipoCuentaId());
                        $objInfoContratoFormaPagoHisto->setCedulaTitular($objInfoContratoFormaPago->getCedulaTitular());
                        $objInfoContratoFormaPagoHisto->setCodigoVerificacion($objInfoContratoFormaPago->getCodigoVerificacion());
                        $objInfoContratoFormaPagoHisto->setContratoId($objContrato);
                        $objInfoContratoFormaPagoHisto->setEstado('Inactivo');
                        $objInfoContratoFormaPagoHisto->setFeCreacion(new \DateTime('now'));
                        $objInfoContratoFormaPagoHisto->setFeUltMod(new \DateTime('now'));
                        $objInfoContratoFormaPagoHisto->setFormaPago($intTmpFormaPagoId);
                        $objInfoContratoFormaPagoHisto->setIpCreacion($strIpCreacion);
                        $objInfoContratoFormaPagoHisto->setNumeroCtaTarjeta($objInfoContratoFormaPago->getNumeroCtaTarjeta());
                        $objInfoContratoFormaPagoHisto->setNumeroDebitoBanco($objInfoContratoFormaPago->getNumeroDebitoBanco());
                        $objInfoContratoFormaPagoHisto->setTipoCuentaId($intIdTipoCuentaTmp);
                        $objInfoContratoFormaPagoHisto->setTitularCuenta($objInfoContratoFormaPago->getTitularCuenta());
                        $objInfoContratoFormaPagoHisto->setUsrCreacion($strUsrCreacion);
                        $objInfoContratoFormaPagoHisto->setUsrUltMod($strUsrCreacion);
                        $this->emcom->persist($objInfoContratoFormaPagoHisto);
                        $this->emcom->flush();
                    }
                }
                $this->emcom->getConnection()->commit();
                
            } 
            catch (\Exception $e) 
            {
                if ($this->emcom->getConnection()->isTransactionActive())
                {
                    $this->emcom->getConnection()->rollback();
                }
                $this->serviceUtil->insertError(
                                                "Telcos+",
                                                "InfoContratoAprobService->rechazarContratoPorError", 
                                                $e->getMessage(), 
                                                $strUsrCreacion, 
                                                $strIpCreacion
                                               );
            }
        }
    }

    /**    
     * Documentación para el método 'rechazarContratoPorCertificado'.
     *
     * Función que rechazará el contrato digital enviado por parametro.
     * Para este proceso se realiza lo siguiente:
     * rechaza contrato
     * inactiva formas de pago
     * inserta historial de forma de pago
     * Reversa lo generaro por certificacion de documentos
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 09-05-2018

     * ]
     */
    public function rechazarContratoPorCertificado($arrayParametros)
    {
        $objContrato    = $arrayParametros['objContrato'];
        $strUsrCreacion = $arrayParametros['strUsrCreacion'];
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
        $strMotivo      = $arrayParametros['strMotivo'];

        if(is_object($objContrato))
        {
            $this->emcom->getConnection()->beginTransaction();
            try
            {
                /**
                 * SE RECHAZA EL CONTRATO
                 */
                $objMotivoContrato     = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMotivo);
                $intTmpMotivoRechazoId = $objMotivoContrato ? $objMotivoContrato->getId() : 0;

                $objContrato->setEstado('Rechazado');
                $objContrato->setMotivoRechazoId($intTmpMotivoRechazoId);
                $objContrato->setUsrRechazo($strUsrCreacion);
                $objContrato->setFeRechazo(new \DateTime('now'));
                $this->emcom->persist($objContrato);
                $this->emcom->flush();

                /**
                 * SE INACTIVA LA FORMA DE PAGO DEL CONTRATO
                 */
                $intTmpFormaPagoId           = $objContrato->getFormaPagoId() ? $objContrato->getFormaPagoId()->getId() : 0;
                $arrayInfoContratoFormasPago = $this->emcom->getRepository('schemaBundle:InfoContratoFormaPago')
                                                           ->findBy( array( 
                                                                           'estado'      => array('Pendiente', 'Activo'),
                                                                           'contratoId'  => $objContrato 
                                                                          )
                                                                   );

                if(count($arrayInfoContratoFormasPago)>0)
                {
                    foreach($arrayInfoContratoFormasPago as $objInfoContratoFormaPago)
                    {
                        $objInfoContratoFormaPago->setEstado('Inactivo');
                        $objInfoContratoFormaPago->setFeUltMod(new \DateTime('now'));
                        $objInfoContratoFormaPago->setUsrUltMod($strUsrCreacion);
                        $this->emcom->persist($objInfoContratoFormaPago);
                        $this->emcom->flush();


                        //SE CREA EL HISTORIAL DE LA FORMA DE PAGO INACTIVA
                        $intIdTipoCuentaTmp = is_object($objInfoContratoFormaPago->getTipoCuentaId())
                                              ? $objInfoContratoFormaPago->getTipoCuentaId()->getId() : 0;

                        $objInfoContratoFormaPagoHisto = new InfoContratoFormaPagoHist();
                        $objInfoContratoFormaPagoHisto->setMesVencimiento($objInfoContratoFormaPago->getMesVencimiento());
                        $objInfoContratoFormaPagoHisto->setAnioVencimiento($objInfoContratoFormaPago->getAnioVencimiento());
                        $objInfoContratoFormaPagoHisto->setBancoTipoCuentaId($objInfoContratoFormaPago->getBancoTipoCuentaId());
                        $objInfoContratoFormaPagoHisto->setCedulaTitular($objInfoContratoFormaPago->getCedulaTitular());
                        $objInfoContratoFormaPagoHisto->setCodigoVerificacion($objInfoContratoFormaPago->getCodigoVerificacion());
                        $objInfoContratoFormaPagoHisto->setContratoId($objContrato);
                        $objInfoContratoFormaPagoHisto->setEstado('Inactivo');
                        $objInfoContratoFormaPagoHisto->setFeCreacion(new \DateTime('now'));
                        $objInfoContratoFormaPagoHisto->setFeUltMod(new \DateTime('now'));
                        $objInfoContratoFormaPagoHisto->setFormaPago($intTmpFormaPagoId);
                        $objInfoContratoFormaPagoHisto->setIpCreacion($strIpCreacion);
                        $objInfoContratoFormaPagoHisto->setNumeroCtaTarjeta($objInfoContratoFormaPago->getNumeroCtaTarjeta());
                        $objInfoContratoFormaPagoHisto->setNumeroDebitoBanco($objInfoContratoFormaPago->getNumeroDebitoBanco());
                        $objInfoContratoFormaPagoHisto->setTipoCuentaId($intIdTipoCuentaTmp);
                        $objInfoContratoFormaPagoHisto->setTitularCuenta($objInfoContratoFormaPago->getTitularCuenta());
                        $objInfoContratoFormaPagoHisto->setUsrCreacion($strUsrCreacion);
                        $objInfoContratoFormaPagoHisto->setUsrUltMod($strUsrCreacion);
                        $this->emcom->persist($objInfoContratoFormaPagoHisto);
                        $this->emcom->flush();
                    }
                }
                $this->emcom->getConnection()->commit();
            } 
            catch (\Exception $e) 
            {
                if ($this->emcom->getConnection()->isTransactionActive())
                {
                    $this->emcom->getConnection()->rollback();
                }
                $this->serviceUtil->insertError(
                                                "Telcos+",
                                                "InfoContratoAprobService->rechazarContratoPorCertificado", 
                                                $e->getMessage(), 
                                                $strUsrCreacion, 
                                                $strIpCreacion
                                               );
            }
        }
    }

    /**
     * Función que guarda el proceso de aprobación de adendum
     * Genera Orden de Trabajo de los servicios Factibles que pasarán al proceso de Planificación
     * Actualiza estado de contrato a Aprobado  
     * Se llama a service que realiza la encriptación del numero de cuenta tarjeta
     * 
     * @author Edgar Pin Villavicencio  email <epin@telconet.ec>
     * @version 1.0 05-11/2019
     * 
     * @author Edgar Pin Villavicencio email<epin@telconet.ec>
     * @version 1.1 17-04-2020 Se agrega en el catch para que inserte en info_log, 
     *                         se modifica el array de respuesta para que devuelva cuando hay error
     * 
     * @author Edgar Pin Villavicencio email<epin@telconet.ec>
     * @version 1.2 18-05-2020 Se modifica para que solo los planes y los productos que requieren planificación
     *                         se genere solicitud de instalación y cambia el estado del servicio a Preplanificada
     * 
     * @author Edgar Pin Villavicencio email<epin@telconet.ec>
     * @version 1.3 08-10-2020 Se modifica para que al autorizar adendum se active el adendum de manera individual
     * 
     * @author Edgar Pin Villavicencio email<epin@telconet.ec>
     * @version 1.4 16-08-2022 Se modifica para que la planificacion comercial se guarde el historial del servicio con el estado del servicio 
     * 
     **/
    
    public function aprobarAdendum($arrayParametros)
    {       
        try 
        {
            
            $arrayServicios      = $arrayParametros['arrayServicios'];
            $intPersonaEmpRolId  = $arrayParametros['personaEmpresaRolId'];
            $strUsrCreacion      = $arrayParametros['strUsrCreacion'];
            $strIpCreacion       = $arrayParametros['strIpCreacion'];
            $strOrigen           = $arrayParametros['strOrigen'];
            $arrayData           = array();
            
            $strMensajeHist = "";

            $arrayValorParametros =  $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                ->getOne('PRODUCTOS QUE NO SE PLANIFICAN',
                                        '',
                                        '',
                                        'PRODUCTOS QUE NO SE PLANIFICAN',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '');
            $arrayProdNolanif = explode(",",$arrayValorParametros['valor1']);
            $strMensajeHist = $arrayValorParametros['valor4'] ;  
                          
            foreach( $arrayServicios as $idServicio )
            {
                $objServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($idServicio);
                $objAdendum = $this->emcom->getRepository('schemaBundle:InfoAdendum')
                                ->findOneBy(array("servicioId" => $objServicio->getId()));
                if ($objAdendum)
                {
                    //con el servicio obtengo el adendum completo a autorizar
                    $objAdendums = $this->emcom->getRepository('schemaBundle:InfoAdendum')
                            ->findBy(array("tipo" => $objAdendum->getTipo(),
                                           "numero" => $objAdendum->getNumero()));
                    if ($objAdendums)
                    {
                        foreach ($objAdendums as $entityAdendum)
                        {                                           
                            $objServAdendum = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($entityAdendum->getServicioId());
                            if (($objServAdendum->getProductoId())
                                && in_array($objServAdendum->getProductoId()->getId(),$arrayProdNolanif))
                            {   
                                $strMensajeHist = $arrayValorParametros['valor3'] ; 
                                break;
                            } 
                        }
                    }       
                }
            }
            foreach( $arrayServicios as $idServicio )
            {
                $objServicio          = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($idServicio);
                $objPunto             = $this->emcom->getRepository('schemaBundle:InfoPunto')->find($objServicio->getPuntoId()->getId());
                $objPersonaEmpresaRol = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intPersonaEmpRolId);
                $objOficina           = $this->emcom->getRepository('schemaBundle:InfoOficinaGrupo')
                                                    ->find($objPersonaEmpresaRol->getOficinaId()->getId());
                            
                // =================================================================================           
                // [CREATE] - Se crea la Orden de Trabajo
                // =================================================================================
                $objOrdenTrabajo = new InfoOrdenTrabajo();
                
                // [LEGACY] Se genera el numero de la Orden de Trabajo
                $objNumeracion = $this->emcom->getRepository('schemaBundle:AdmiNumeracion')
                                        ->findByEmpresaYOficina($objPersonaEmpresaRol->getEmpresaRolId()->getEmpresaCod()->getId(), 
                                                                $objOficina->getId(), 
                                                                "ORD");
                
                if( $objNumeracion )
                {
                    $strSecuenciaAsignada  = str_pad($objNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
                    $strNumeroOrdenTrabajo = $objNumeracion->getNumeracionUno() 
                                           . "-" . $objNumeracion->getNumeracionDos() 
                                           . "-" . $strSecuenciaAsignada;
                    $objOrdenTrabajo->setNumeroOrdenTrabajo($strNumeroOrdenTrabajo);
                }
                
                $objOrdenTrabajo->setPuntoId($objPunto);
                $objOrdenTrabajo->setTipoOrden('N');                    
                $objOrdenTrabajo->setFeCreacion(new \DateTime('now'));
                $objOrdenTrabajo->setUsrCreacion($strUsrCreacion);
                $objOrdenTrabajo->setIpCreacion($strIpCreacion);
                $objOrdenTrabajo->setOficinaId($objOficina->getId());
                $objOrdenTrabajo->setEstado("Activa");
                $this->emcom->persist($objOrdenTrabajo);
                $this->emcom->flush();
                
                // =================================================================================
                // [UPDATE] - Se actualiza la numeracion de las Ordenes de Trabajo
                // =================================================================================
                if( $objOrdenTrabajo )
                {
                    $intNumero = ($objNumeracion->getSecuencia() + 1);
                    $objNumeracion->setSecuencia($intNumero);
                    $this->emcom->persist($objNumeracion);
                    $this->emcom->flush();
                }
                
                 
                if(isset($objServicio))
                {
                    $strObservacionHistorialServ  = "Se solicito planificacion";
                    $strEstadoSolPlanificacion    = "PrePlanificada";
                    $objSolFactibilidadAnticipada = null;
                    $boolRequierePlanificacion    = false;
                    // =================================================================================
                    // [UPDATE] - Se actualiza el Servicio con la Orden de Trabajo generada
                    // =================================================================================
                    $objServicio->setOrdenTrabajoId($objOrdenTrabajo);
                    
                    // Si se trata de empresa MD los servicios pasan a estado PrePlanificada
                    if($arrayParametros['strPrefijoEmpresa']=='MD' || $arrayParametros['strPrefijoEmpresa']=='EN')
                    {
                        if (!is_null($objServicio->getPlanId()))
                        {
                            $objServicio->setEstado('PrePlanificada');   
                            $boolRequierePlanificacion = true;
                            $boolHayServicio           = true;
                            $intIdJurisdiccion         = $objServicio->getPuntoId()->getPuntoCoberturaId()->getId();
                            
                        }
                        else
                        {
                            $objAdmiProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')->find($objServicio->getProductoId());
                            if(isset($objAdmiProducto) && $objAdmiProducto->getRequierePlanificacion()=='SI' 
                                && $objServicio->getEstado()=='Factible')
                            {
                                $objServicio->setEstado('PrePlanificada');   
                                $boolRequierePlanificacion = true;
                                $boolHayServicio           = true;
                                $intIdJurisdiccion         = $objServicio->getPuntoId()->getPuntoCoberturaId()->getId();
                                    
                            }
    
                        }
                    }
                    else
                    {
                        // Si no es MD debo verificar si el producto esta marcado que requiere Planificacion y que sea Factible.
                        $objAdmiProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')->find($objServicio->getProductoId());
                        if(isset($objAdmiProducto) && $objAdmiProducto->getRequierePlanificacion()=='SI' 
                            && $objServicio->getEstado()=='Factible')
                        {
                            $objServicio->setEstado('PrePlanificada');   
                            $boolRequierePlanificacion = true;
                            
                        }
                        elseif($objServicio->getEstado()=='Factibilidad-anticipada')
                        {
                            if($arrayParametros['strPrefijoEmpresa'] == 'TN')
                            {
                                $objServicioTecnico = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                                  ->findOneByServicioId($objServicio->getId());
                                if (is_object($objServicioTecnico) && ($objServicioTecnico->getUltimaMillaId() > 0))
                                {
                                    $objUltimaMilla = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                              ->find($objServicioTecnico->getUltimaMillaId());
                                    if(is_object($objUltimaMilla))
                                    {
                                        $strUltimaMilla = $objUltimaMilla->getNombreTipoMedio();
                                        if ($strUltimaMilla == 'Radio' && $objServicio->getEstado() == "Factibilidad-anticipada")
                                        {
                                            $strEstadoSolPlanificacion = "Asignar-factibilidad";
                                            $boolRequierePlanificacion = true;
                                            $objServicio->setEstado($strEstadoSolPlanificacion);   
                                            $objSolFactibilidadAnticipada = $this->emcom
                                                                                 ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                                 ->findOneBy(array(
                                                                                            "servicioId" => $objServicio->getId(), 
                                                                                            "estado"     => "Factibilidad-anticipada")
                                                                                            );
                                            if(is_object($objSolFactibilidadAnticipada))
                                            {
                                                $objSolFactibilidadAnticipada->setEstado($strEstadoSolPlanificacion);
                                                $this->emcom->persist($objSolFactibilidadAnticipada);
                                                $this->emcom->flush();
    
                                                $objDetalleSolHist = new InfoDetalleSolHist();
                                                $objDetalleSolHist->setDetalleSolicitudId($objSolFactibilidadAnticipada);
                                                $objDetalleSolHist->setIpCreacion($strIpCreacion);
                                                $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                                                $objDetalleSolHist->setUsrCreacion($strUsrCreacion);
                                                $objDetalleSolHist->setEstado($strEstadoSolPlanificacion);
                                                $this->emcom->persist($objDetalleSolHist);
                                                $this->emcom->flush();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        elseif(isset($objAdmiProducto) && $objAdmiProducto->getEstadoInicial()=='Activo' 
                            && $objServicio->getEstado()=='Pendiente')
                        {
                             //Si Producto no requiere flujo. Se realizará Activación automática en la Aprobación del Contrato.
                             $objServicio->setEstado($objAdmiProducto->getEstadoInicial());  
                            
                             $objServicioHist = new InfoServicioHistorial();
                             $objServicioHist->setServicioId($objServicio);
                             $objServicioHist->setObservacion('Se Confirmo el Servicio');
                             $objServicioHist->setIpCreacion($strIpCreacion);
                             $objServicioHist->setUsrCreacion($strUsrCreacion);
                             $objServicioHist->setFeCreacion(new \DateTime('now'));
                             $objServicioHist->setAccion('confirmarServicio');
                             $objServicioHist->setEstado($objAdmiProducto->getEstadoInicial());
                             $this->emcom->persist($objServicioHist);
                             $this->emcom->flush();
                        }
                    }
                    $this->emcom->persist($objServicio);
                    $this->emcom->flush();
                    if( $objServicio->getTipoOrden() )
                    {
                        $objOrdenTrabajo->setTipoOrden($objServicio->getTipoOrden());
                        $this->emcom->persist($objOrdenTrabajo);
                        $this->emcom->flush();
                    }
                    $this->emcom->flush();
                    if($boolRequierePlanificacion)
                    {
                        $strUsrCreacionHistorial = $strUsrCreacion;
                        if('MOVIL' === strtoupper($strOrigen))
                        {
                            $strUsrCreacionHistorial = 'telcos_contrato';
                        }
                        
                        // =================================================================================
                        // [CREATE] - Se crea el Historial del Servicio
                        // =================================================================================
                        $objServicioHist = new InfoServicioHistorial();
                        $objServicioHist->setServicioId($objServicio);
                        $objServicioHist->setObservacion($strObservacionHistorialServ);
                        $objServicioHist->setIpCreacion($strIpCreacion);
                        $objServicioHist->setUsrCreacion($strUsrCreacionHistorial);
                        $objServicioHist->setFeCreacion(new \DateTime('now'));
                        $objServicioHist->setEstado($objServicio->getEstado());
                        $this->emcom->persist($objServicioHist);
                        $this->emcom->flush();
    
                        // =================================================================================
                        // [CREATE] - Se crea la solicitud de planificacion para el servicio
                        // =================================================================================
                        $objTipoSolicitud    = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                    ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");
    
                        $objDetalleSolicitud = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->findCountDetalleSolicitudByIds($objServicio->getId(), $objTipoSolicitud->getId());
    
                        // Se valida que no exista otra solicitud de Planificacion 
                        if(!$objDetalleSolicitud || $objDetalleSolicitud["cont"] <= 0 || is_object($objSolFactibilidadAnticipada))
                        {
                            $objSolicitudNueva = new InfoDetalleSolicitud();
                            $objSolicitudNueva->setServicioId($objServicio);
                            $objSolicitudNueva->setTipoSolicitudId($objTipoSolicitud);
                            $objSolicitudNueva->setEstado($strEstadoSolPlanificacion);
                            $objSolicitudNueva->setUsrCreacion($strUsrCreacion);
                            $objSolicitudNueva->setFeCreacion(new \DateTime('now'));
                            $this->emcom->persist($objSolicitudNueva);
                            $this->emcom->flush();
    
                            // =================================================================================
                            // [CREATE] - Se crea el Historial del Servicio
                            // =================================================================================
                            $entityDetalleSolHist = new InfoDetalleSolHist();
                            $entityDetalleSolHist->setDetalleSolicitudId($objSolicitudNueva);
                            $entityDetalleSolHist->setIpCreacion($strIpCreacion);
                            $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                            $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                            $entityDetalleSolHist->setEstado($strEstadoSolPlanificacion);
                            $this->emcom->persist($entityDetalleSolHist);
                            $this->emcom->flush();
                            if($boolHayServicio)
                            {
                                $arrayPlanificacion = $this->servicePlanificar->getCuposMobil(array(
                                    "intJurisdiccionId" => $intIdJurisdiccion));
                            }
                            $arrayData['arrayPlanificacion'] = $arrayPlanificacion;
            
                        }
                    }
                }
                if ($objServicio)
                {
                    $objAdendum = $this->emcom->getRepository('schemaBundle:InfoAdendum')
                                                  ->findOneBy(array("servicioId" => $objServicio->getId()));
                    if ($objAdendum)
                    {
                        //con el servicio obtengo el adendum completo a autorizar
                        $objAdendums = $this->emcom->getRepository('schemaBundle:InfoAdendum')
                                            ->findBy(array("tipo" => $objAdendum->getTipo(),
                                                           "numero" => $objAdendum->getNumero()));
                        if ($objAdendums)
                        {
                            foreach ($objAdendums as $entityAdendum)
                            {
                                $entityAdendum->setEstado("Activo");
                                $entityAdendum->setFeModifica(new \DateTime('now'));
                                $entityAdendum->setUsrModifica($strUsrCreacion);
                                $this->emcom->persist($entityAdendum);
                                if ($entityAdendum->getTipo() == "AP")
                                {                                    
                                    $objServicioA = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($entityAdendum->getServicioId());
                                    $objServHist = $this->emcom->getRepository('schemaBundle:InfoServicioHistorial')
                                                        ->findOneBy(array("servicioId" => $entityAdendum->getServicioId(),
                                                                          "usrCreacion" => 'PLANIF_COMERCIAL'));
                                    
                                    if (($objServicioA->getPlanId()) || 
                                         ($objServicioA->getProductoId()->getRequierePlanificacion() == "SI" || 
                                         $objServicioA->getProductoId()->getNombreTecnico() == "EXTENDER_DUAL_BAND") &&
                                         !($objServHist) )
                                    {
    
                                        $objServicioHistS = new InfoServicioHistorial();
                                        $objServicioHistS->setServicioId($objServicioA);
                                        $objServicioHistS->setObservacion($strMensajeHist);
                                        $objServicioHistS->setIpCreacion($strIpCreacion);
                                        $objServicioHistS->setUsrCreacion($strUsrCreacion);
                                        $objServicioHistS->setFeCreacion(new \DateTime('now'));
                                        $objServicioHistS->setAccion('Planificacion Comercial');
                                        $objServicioHistS->setEstado($objServicioA->getEstado());
                                        $this->emcom->persist($objServicioHistS);
                                        $this->emcom->flush();    
                                    }    
                                }
                                
                            }
                            
                        }                                                                 
                    }                              
                    else
                    {
                        throw new \Exception("No se encuentra adendum para activar");
                    } 
                } 
                else
                {
                    throw new \Exception("No se encuentra servicio para activar adendum");
                }
        
                    
            } 
            return $arrayData;
    
        } 
        catch (Exception $ex)
        {

            $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
            $arrayParametrosLog['appClass']         = "ComercialMobileWSControllerRest";
            $arrayParametrosLog['appMethod']        = "putAutorizarContrato";
            $arrayParametrosLog['appAction']        = "putAutorizarContrato";
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = implode(";", $arrayData);
            $arrayParametrosLog['creationUser']     = $strUsrCreacion;  
                
            $this->serviceUtil->insertLog($arrayParametrosLog);           
            $arrayData['status']  = 'ERROR_SERVICE';
            $arrayData['mensaje'] = 'No se pudo aprobar Adendum';
            return $arrayData;            

        }
    }

    /**    
     * Documentación para el método 'rechazarContratoPorCertificadoCaducado'.
     *
     * Función que rechazará el contrato digital enviado por parametro.
     * Para este proceso se realiza lo siguiente:
     * rechaza contrato
     * inactiva formas de pago
     * inserta historial de forma de pago
     * Reversa lo generaro por certificacion de documentos
     * Elimina el servicio y libera la factibilidad del servicio
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 29-10-2020
     * 
     */
    public function rechazarContratoPorCertificadoCaducado($arrayParametros)
    {
        $objContrato    = $arrayParametros['objContrato'];
        $strUsrCreacion = $arrayParametros['strUsrCreacion'];
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
        $strMotivo      = $arrayParametros['strMotivo'];
        $strCodEmpresa  = $arrayParametros['strCodEmpresa'];

        if(is_object($objContrato))
        {
            $this->emcom->getConnection()->beginTransaction();
            try
            {
                /**
                 * SE RECHAZA EL CONTRATO
                 */
                $objMotivoContrato     = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMotivo);
                $intTmpMotivoRechazoId = $objMotivoContrato ? $objMotivoContrato->getId() : 0;

                $objContrato->setEstado('Rechazado');
                $objContrato->setMotivoRechazoId($intTmpMotivoRechazoId);
                $objContrato->setUsrRechazo($strUsrCreacion);
                $objContrato->setFeRechazo(new \DateTime('now'));
                $this->emcom->persist($objContrato);
                $this->emcom->flush();

                /**
                 * SE INACTIVA LA FORMA DE PAGO DEL CONTRATO
                 */
                $intTmpFormaPagoId           = $objContrato->getFormaPagoId() ? $objContrato->getFormaPagoId()->getId() : 0;
                $arrayInfoContratoFormasPago = $this->emcom->getRepository('schemaBundle:InfoContratoFormaPago')
                                                        ->findBy( array( 
                                                                        'estado'      => array('Pendiente', 'Activo'),
                                                                        'contratoId'  => $objContrato 
                                                                        )
                                                                );

                if(count($arrayInfoContratoFormasPago)>0)
                {
                    foreach($arrayInfoContratoFormasPago as $objInfoContratoFormaPago)
                    {
                        $objInfoContratoFormaPago->setEstado('Inactivo');
                        $objInfoContratoFormaPago->setFeUltMod(new \DateTime('now'));
                        $objInfoContratoFormaPago->setUsrUltMod($strUsrCreacion);
                        $this->emcom->persist($objInfoContratoFormaPago);
                        $this->emcom->flush();


                        //SE CREA EL HISTORIAL DE LA FORMA DE PAGO INACTIVA
                        $intIdTipoCuentaTmp = is_object($objInfoContratoFormaPago->getTipoCuentaId())
                                            ? $objInfoContratoFormaPago->getTipoCuentaId()->getId() : 0;

                        $objInfoContratoFormaPagoHisto = new InfoContratoFormaPagoHist();
                        $objInfoContratoFormaPagoHisto->setMesVencimiento($objInfoContratoFormaPago->getMesVencimiento());
                        $objInfoContratoFormaPagoHisto->setAnioVencimiento($objInfoContratoFormaPago->getAnioVencimiento());
                        $objInfoContratoFormaPagoHisto->setBancoTipoCuentaId($objInfoContratoFormaPago->getBancoTipoCuentaId());
                        $objInfoContratoFormaPagoHisto->setCedulaTitular($objInfoContratoFormaPago->getCedulaTitular());
                        $objInfoContratoFormaPagoHisto->setCodigoVerificacion($objInfoContratoFormaPago->getCodigoVerificacion());
                        $objInfoContratoFormaPagoHisto->setContratoId($objContrato);
                        $objInfoContratoFormaPagoHisto->setEstado('Inactivo');
                        $objInfoContratoFormaPagoHisto->setFeCreacion(new \DateTime('now'));
                        $objInfoContratoFormaPagoHisto->setFeUltMod(new \DateTime('now'));
                        $objInfoContratoFormaPagoHisto->setFormaPago($intTmpFormaPagoId);
                        $objInfoContratoFormaPagoHisto->setIpCreacion($strIpCreacion);
                        $objInfoContratoFormaPagoHisto->setNumeroCtaTarjeta($objInfoContratoFormaPago->getNumeroCtaTarjeta());
                        $objInfoContratoFormaPagoHisto->setNumeroDebitoBanco($objInfoContratoFormaPago->getNumeroDebitoBanco());
                        $objInfoContratoFormaPagoHisto->setTipoCuentaId($intIdTipoCuentaTmp);
                        $objInfoContratoFormaPagoHisto->setTitularCuenta($objInfoContratoFormaPago->getTitularCuenta());
                        $objInfoContratoFormaPagoHisto->setUsrCreacion($strUsrCreacion);
                        $objInfoContratoFormaPagoHisto->setUsrUltMod($strUsrCreacion);
                        $this->emcom->persist($objInfoContratoFormaPagoHisto);
                        $this->emcom->flush();
                    }
                }
                $this->emcom->getConnection()->commit();
                //Elimino el servicio
                $this->serviceInfoServicio->eliminarServicioInternetFactible($arrayParametros); 
                

            } 
            catch (\Exception $e) 
            {
                if ($this->emcom->getConnection()->isTransactionActive())
                {
                    $this->emcom->getConnection()->rollback();
                }

                $arrayParametrosLog['enterpriseCode']   = $strCodEmpresa;
                $arrayParametrosLog['logType']          = "0";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = basename(__FILE__);
                $arrayParametrosLog['appClass']         = basename(__CLASS__);
                $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                $arrayParametrosLog['messageUser']      = "ERROR";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = $e->getMessage();
                $arrayParametrosLog['inParameters']     = json_encode($arrayParametros, 128);
                $arrayParametrosLog['creationUser']     = $strUsrCreacion;    

                $this->serviceUtil->insertLog($arrayParametrosLog);                                                  
            }
        }
    }    

    /**    
     * Documentación para el método 'rechazarContratoSinServiciosActivo'.
     *
     * Función que rechazará el contrato digital enviado por parametro.
     * Para este proceso se realiza lo siguiente:
     * rechaza contrato
     * inactiva formas de pago
     * inserta historial de forma de pago
     * Reversa lo generaro por certificacion de documentos
     * Elimina el servicio y libera la factibilidad del servicio
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 17-12-2020
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 07-04-2021 - Se valida que el servicio no este dentro de los estados obtenidos de parametros para que se puedan anular
     * 
     */
    public function rechazarContratoSinServiciosActivo($arrayParametros)
    {
        $objContrato    = $arrayParametros['objContrato'];
        $strUsrCreacion = $arrayParametros['strUsrCreacion'];
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
        $strMotivo      = "Contrato en Estado Rechazado";
        $strCodEmpresa  = $arrayParametros['strCodEmpresa'];

        if(is_object($objContrato))
        {
            $this->emcom->getConnection()->beginTransaction();
            try
            {
                /**
                 * SE RECHAZA EL CONTRATO
                 */
                $objMotivoContrato     = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMotivo);
                $intTmpMotivoRechazoId = $objMotivoContrato ? $objMotivoContrato->getId() : 0;

                $objContrato->setEstado('Rechazado');
                $objContrato->setMotivoRechazoId($intTmpMotivoRechazoId);
                $objContrato->setUsrRechazo($strUsrCreacion);
                $objContrato->setFeRechazo(new \DateTime('now'));
                $this->emcom->persist($objContrato);
                $this->emcom->flush();

                /**
                 * SE INACTIVA LA FORMA DE PAGO DEL CONTRATO
                 */
                $intTmpFormaPagoId           = $objContrato->getFormaPagoId() ? $objContrato->getFormaPagoId()->getId() : 0;
                $arrayInfoContratoFormasPago = $this->emcom->getRepository('schemaBundle:InfoContratoFormaPago')
                                                        ->findBy( array( 
                                                                        'estado'      => array('Pendiente', 'Activo'),
                                                                        'contratoId'  => $objContrato 
                                                                        )
                                                                );

                if(count($arrayInfoContratoFormasPago)>0)
                {
                    foreach($arrayInfoContratoFormasPago as $objInfoContratoFormaPago)
                    {
                        $objInfoContratoFormaPago->setEstado('Inactivo');
                        $objInfoContratoFormaPago->setFeUltMod(new \DateTime('now'));
                        $objInfoContratoFormaPago->setUsrUltMod($strUsrCreacion);
                        $this->emcom->persist($objInfoContratoFormaPago);
                        $this->emcom->flush();


                        //SE CREA EL HISTORIAL DE LA FORMA DE PAGO INACTIVA
                        $intIdTipoCuentaTmp = is_object($objInfoContratoFormaPago->getTipoCuentaId())
                                            ? $objInfoContratoFormaPago->getTipoCuentaId()->getId() : 0;

                        $objInfoContratoFormaPagoHisto = new InfoContratoFormaPagoHist();
                        $objInfoContratoFormaPagoHisto->setMesVencimiento($objInfoContratoFormaPago->getMesVencimiento());
                        $objInfoContratoFormaPagoHisto->setAnioVencimiento($objInfoContratoFormaPago->getAnioVencimiento());
                        $objInfoContratoFormaPagoHisto->setBancoTipoCuentaId($objInfoContratoFormaPago->getBancoTipoCuentaId());
                        $objInfoContratoFormaPagoHisto->setCedulaTitular($objInfoContratoFormaPago->getCedulaTitular());
                        $objInfoContratoFormaPagoHisto->setCodigoVerificacion($objInfoContratoFormaPago->getCodigoVerificacion());
                        $objInfoContratoFormaPagoHisto->setContratoId($objContrato);
                        $objInfoContratoFormaPagoHisto->setEstado('Inactivo');
                        $objInfoContratoFormaPagoHisto->setFeCreacion(new \DateTime('now'));
                        $objInfoContratoFormaPagoHisto->setFeUltMod(new \DateTime('now'));
                        $objInfoContratoFormaPagoHisto->setFormaPago($intTmpFormaPagoId);
                        $objInfoContratoFormaPagoHisto->setIpCreacion($strIpCreacion);
                        $objInfoContratoFormaPagoHisto->setNumeroCtaTarjeta($objInfoContratoFormaPago->getNumeroCtaTarjeta());
                        $objInfoContratoFormaPagoHisto->setNumeroDebitoBanco($objInfoContratoFormaPago->getNumeroDebitoBanco());
                        $objInfoContratoFormaPagoHisto->setTipoCuentaId($intIdTipoCuentaTmp);
                        $objInfoContratoFormaPagoHisto->setTitularCuenta($objInfoContratoFormaPago->getTitularCuenta());
                        $objInfoContratoFormaPagoHisto->setUsrCreacion($strUsrCreacion);
                        $objInfoContratoFormaPagoHisto->setUsrUltMod($strUsrCreacion);
                        $this->emcom->persist($objInfoContratoFormaPagoHisto);
                        $this->emcom->flush();
                    }
                }
                
                //Elimino los servicios y cancelo los puntos
                $objPuntos    = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                                   ->findBy(array("personaEmpresaRolId" => $objContrato->getPersonaEmpresaRolId()));

                 $arrayEstadoServicios = ($arrayValorParametros['valor1']) ? $arrayValorParametros['valor1'] : "Factible";
                $arrayEstadoServiciosNoAnular = ($arrayValorParametros['valor2']) ? $arrayValorParametros['valor2'] : "";
                $arrayEstadoServicios = explode(",",$arrayEstadoServicios);
                $arrayEstadoServiciosNoAnular = explode(",",$arrayEstadoServiciosNoAnular);
                foreach ($objPuntos as $objPunto) 
                {
                    $entityPunto = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                                     ->find($objPunto->getId());
                    $entityPunto->setEstado("Anulado");
                    $this->emcom->persist($entityPunto);
                    $this->emcom->flush();
                    $objServicios = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                     ->findBy(array("puntoId" => $objPunto->getId(),
                                                                    "estado" => $arrayEstadoServicios)); 
                    foreach ($objServicios as $objServicio) 
                    {
                        $arrayParametros['strEstado'] = 'Anulado';
                        $arrayParametros['strAccion'] = 'Eliminar';                    
                        $arrayParametros['objServicio'] = $objServicio;
                        $arrayParametros['objRequest'] = "N";
                        $arrayParametros['strMensaje'] = "Se eliminó servicio por tener contrato activo sin servicio activo";
                        if (!in_array($objServicio->getEstado(), $arrayEstadoServiciosNoAnular))
                        {
                            $this->serviceInfoServicio->eliminarServicioInternetFactible($arrayParametros);                        
                        }
                        
                    }
                }
                //Cancelo el rol de la persona empresa rol
                $entityPersonaEmpresaRol = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->find($objContrato->getPersonaEmpresaRolId()->getId());
                $entityPersonaEmpresaRol->setEstado("Cancelado");                                  
                $this->emcom->persist($entityPersonaEmpresaRol);  
                $this->emcom->flush();                                             
                $objInfoPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
                $objInfoPersonaEmpresaRolHistorial->setEstado( $entityPersonaEmpresaRol->getEstado());
                $objInfoPersonaEmpresaRolHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoPersonaEmpresaRolHistorial->setIpCreacion($strIpCreacion);
                $objInfoPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                $objInfoPersonaEmpresaRolHistorial->setUsrCreacion($strUsrCreacion);
                $objInfoPersonaEmpresaRolHistorial->setObservacion("Por Contrato activo sin servicios activos");
                $objInfoPersonaEmpresaRolHistorial->setMotivoId($intTmpMotivoRechazoId);
                $this->emcom->persist($objInfoPersonaEmpresaRolHistorial);    
                //Elimino el info_adendum
                $objAdendums    = $this->emcom->getRepository('schemaBundle:InfoAdendum')
                                             ->findBy(array("contratoId" => $objContrato->getId()));
                foreach ($objAdendums as $objAdendum)
                {
                    $entityAdendum = $this->emcom->getRepository('schemaBundle:InfoAdendum')
                                                ->find($objAdendum->getId());
                    $entityAdendum->setEstado("Anulado");
                    $entityAdendum->setNumero(null);
                    $entityAdendum->setContratoId(null);
                    $entityAdendum->setTipo(null);
                    $entityAdendum->setFeModifica(new \DateTime('now'));
                    $entityAdendum->setUsrModifica($strUsrCreacion);
                    $this->emcom->persist($entityAdendum);
                    $this->emcom->flush();                    

                }                                            
                            
                
                $this->emcom->getConnection()->commit();
                
 
                

            } 
            catch (\Exception $e) 
            {
                if ($this->emcom->getConnection()->isTransactionActive())
                {
                    $this->emcom->getConnection()->rollback();
                }

                $arrayParametrosLog['enterpriseCode']   = $strCodEmpresa;
                $arrayParametrosLog['logType']          = "0";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = basename(__FILE__);
                $arrayParametrosLog['appClass']         = basename(__CLASS__);
                $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                $arrayParametrosLog['messageUser']      = "ERROR";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = $e->getMessage();
                $arrayParametrosLog['inParameters']     = json_encode($arrayParametros, 128);
                $arrayParametrosLog['creationUser']     = $strUsrCreacion;    

                $this->serviceUtil->insertLog($arrayParametrosLog);                                                  
            }
        }
    }    

}

?>