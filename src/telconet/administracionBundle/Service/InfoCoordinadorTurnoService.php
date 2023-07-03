<?php

namespace telconet\administracionBundle\Service;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use telconet\schemaBundle\Entity\InfoCoordinadorTurno;

/**
 * Clase InfoCoordinadorTurnoService
 *
 * Clase que maneja las funcionales necesarias para la gestión de Coordinadores de Turno
 *
 * @author Daniel Guzmán <ddguzman@telconet.ec>
 * @version 1.0 16-01-2023
 */  
class InfoCoordinadorTurnoService
{
    private $emComercial;
    private $objContainer;


    public function __construct(Container $objContainer) 
    {
        $this->objContainer            = $objContainer;
        $this->emComercial          = $this->objContainer->get('doctrine')->getManager('telconet');
    }


    public function setDependencies()
    {        
        
    }


    /**
     * isCoordinador
     *
     * Método para validar que el coordinador tenga un turno con estado 'Activo'                                   
     *      
     * @param int intIdPersonaEmpresaRol
     * 
     * @return boolean
     *
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.0 16-01-2023
     */
    public function isCoordinador($intIdPersonaEmpresaRol)
    {
        $arrayParametros = array(
                                'personaEmpresaRolId' => $intIdPersonaEmpresaRol,
                                'estado' => 'Activo'
                            );

        $objTurno = $this->emComercial->getRepository('schemaBundle:InfoCoordinadorTurno')->findOneBy($arrayParametros);
        
        if ($objTurno && $objTurno->getEstado() == 'Activo')
        {
            return true;
        }
        return false;
    }


    /**
     * getTurnoPorPersona
     *
     * Método para obtener un turno con estado 'Pendiente' o 'Activo' en base al intIdPersonaEmpresaRol.                                  
     *      
     * @param int intIdPersonaEmpresaRol
     * 
     * @return $arrayRespuesta ['strTieneTurno', 'intIdTurno']
     *
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.0 16-01-2023
     */
    public function getTurnoPorPersona($intIdPersonaEmpresaRol)
    {
        $arrayParametros = array(
                                'personaEmpresaRolId' => $intIdPersonaEmpresaRol,
                                'estado' => array('Activo', 'Pendiente')
                            );
        $arrayRespuesta  = array('strTieneTurno' => 'NO', 'intIdTurno' => 0);
        
        $objTurno = $this->emComercial->getRepository('schemaBundle:InfoCoordinadorTurno')->findOneBy($arrayParametros);
        
        if ($objTurno)
        {
            $arrayRespuesta['strTieneTurno'] = 'SI';
            $arrayRespuesta['intIdTurno'] = $objTurno->getId();
        }

        return $arrayRespuesta;
    }


    /**
     * getTurnoPorId
     *
     * Método para obtener un turno en base a su id.                                  
     *      
     * @param int intIdTurno
     * 
     * @return array arrayRespuesta ['turno', 'strStatus']
     *
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.0 16-01-2023
     */
    public function getTurnoPorId($intIdTurno)
    {
        try
        {
            $objTurno = $this->emComercial->getRepository('schemaBundle:InfoCoordinadorTurno')->find($intIdTurno);

            $arrayRespuesta = array();
            $arrayTurno     = array();

            if ($objTurno)
            {
                $arrayTurno['strFechaInicio']  = $objTurno->getFechaInicio();  
                $arrayTurno['strHoraInicio']   = $objTurno->getHoraInicio();
                $arrayTurno['strFechaFin']     = $objTurno->getFechaFin();
                $arrayTurno['strHoraFin']      = $objTurno->getHoraFin();
                $arrayTurno['strEstado']       = $objTurno->getEstado();
            }

            $arrayRespuesta['turno']        = $arrayTurno;
            $arrayRespuesta['strStatus']    = 'OK';
        }
        catch(\Exception $e)
        {
           $arrayRespuesta['turno']     = 'Hubo un problema recuperar el turno. '.$e;
           $arrayRespuesta['strStatus'] = 'ERROR';
        }

       return $arrayRespuesta;
    }


    /**
     * guardarInfoCoordinadorTurno
     *
     * Método para guardar el registro correspondiente a la entidad InfoCoordinadorTurno.                                  
     *      
     * @param  array arrayParametros [  'intIdPersonaEmpresaRol',
     *                                  'strFechaInicio',
     *                                  'strHoraInicio',
     *                                  'strFechaFin',
     *                                  'strHoraFin',
     *                                  'strUsrCreacion',
     *                                  'strIpCreacion',
     *                                  'strAsignarAhora' ]
     * 
     * @return $arrayRespuesta ['status', 'mensaje']
     *
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.0 16-01-2023
     */
    public function guardarInfoCoordinadorTurno($arrayParametros)
    {
        $intIdPersonaEmpresaRol = $arrayParametros['intIdPersonaEmpresaRol'];
        $strFechaInicio         = $arrayParametros['strFechaInicio'];
        $strHoraInicio          = $arrayParametros['strHoraInicio'];
        $strFechaFin            = $arrayParametros['strFechaFin'];
        $strHoraFin             = $arrayParametros['strHoraFin'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        $strAsignarAhora        = $arrayParametros['strAsignarAhora'];
        $strDatetimeActual      = new \DateTime('now');
        $strEstado              = 'Pendiente';

        if ($strAsignarAhora == 'S')
        {
            $strEstado = 'Activo';
        }

        $objPersonaEmpresaRol   = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonaEmpresaRol);

        $this->emComercial->getConnection()->beginTransaction();

        try
        {
            $objInfoCoordinadorTurno = new InfoCoordinadorTurno();
            $objInfoCoordinadorTurno->setPersonaEmpresaRolId($objPersonaEmpresaRol);
            $objInfoCoordinadorTurno->setFechaInicio($strFechaInicio);
            $objInfoCoordinadorTurno->setHoraInicio($strHoraInicio);
            $objInfoCoordinadorTurno->setFechaFin($strFechaFin);
            $objInfoCoordinadorTurno->setHoraFin($strHoraFin);
            $objInfoCoordinadorTurno->setUsrCreacion($strUsrCreacion);
            $objInfoCoordinadorTurno->setIpCreacion($strIpCreacion);
            $objInfoCoordinadorTurno->setFechaCreacion($strDatetimeActual);
            $objInfoCoordinadorTurno->setEstado($strEstado);

            $this->emComercial->persist($objInfoCoordinadorTurno);
            $this->emComercial->flush();
            $this->emComercial->getConnection()->commit();

            $strMensaje     = 'Coordinador General Asignado';
            $strStatus      = 'OK';
            $arrayRespuesta = array('status' => $strStatus, 'mensaje' => $strMensaje);
        }
        catch (\Exception $e)
        {
            $this->emComercial->getConnection()->rollback();

            $strStatus      = 'ERROR';
            $strMensaje     = 'Mensaje: '. $e->getMessage();
            $arrayRespuesta = array('status' => $strStatus, 'mensaje' => $strMensaje);
        }

        $this->emComercial->getConnection()->close();

        return $arrayRespuesta;
    }


    /**
     * eliminarInfoCoordinadorTurno
     *
     * Método para cambiar el estado del turno a 'Eliminado'                                  
     *      
     * @param array [   'intIdInfoCoordinadorTurno',
     *                  'strUsrUltMod',
     *                  'strIpUltMod' ]
     * 
     * @return $arrayRespuesta ['status', 'mensaje']
     *
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.0 16-01-2023
     */
    public function eliminarInfoCoordinadorTurno($arrayParametros)
    {
        $intIdInfoCoordinadorTurno  = $arrayParametros['intIdInfoCoordinadorTurno'];
        $strUsrUltMod               = $arrayParametros['strUsrUltMod'];
        $strIpUltMod                = $arrayParametros['strIpUltMod'];
        $strDatetimeUltMod          = new \DateTime('now');
        $strEstado                  = 'Eliminado';

        $objInfoCoordinadorTurno   = $this->emComercial->getRepository('schemaBundle:InfoCoordinadorTurno')->find($intIdInfoCoordinadorTurno);

        $this->emComercial->getConnection()->beginTransaction();

        try
        {
            $objInfoCoordinadorTurno->setEstado($strEstado);
            $objInfoCoordinadorTurno->setUsrUltMod($strUsrUltMod);
            $objInfoCoordinadorTurno->setIpUltMod($strIpUltMod);
            $objInfoCoordinadorTurno->setFechaUltMod($strDatetimeUltMod);

            $this->emComercial->persist($objInfoCoordinadorTurno);
            $this->emComercial->flush();
            $this->emComercial->getConnection()->commit();

            $strMensaje     = 'Asignacion editada exitosamente';
            $strStatus      = 'OK';
            $arrayRespuesta = array('status' => $strStatus, 'mensaje' => $strMensaje);
        }
        catch (\Exception $e)
        {
            $this->emComercial->getConnection()->rollback();

            $strStatus      = 'ERROR';
            $strMensaje     = 'Mensaje: '.$e->getMessage();
            $arrayRespuesta = array('status' => $strStatus, 'mensaje' => $strMensaje);
        }

        $this->emComercial->getConnection()->close();

        return $arrayRespuesta;
    }

}