<?php

namespace telconet\seguridadBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Clase encargada de las encryptaciones
 *
 * @since 24-11-2014
 * @version 1.0 
 * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
 */
class CryptService
{       
    private $secretKey;
    private $em;    
    
    function setDependencies(ContainerInterface $container)
    {        
        $this->secretKey    = $container->getParameter('secret');
        $this->em           = $container->get('doctrine.orm.telconet_seguridad_entity_manager');           
    }

    /**
     * Funcion que se encarga del encriptado
     * Consideraciones: Funcion debe recibir la cadena a encriptar y el key configurado en el parameter.yml para poder ejecutar 
     * la encriptacion
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 27-11-2014     
     * @param string $strCadena    
     * @throws Exception 
     * @return $strValorEncriptado  CRYPT_RAW   RAW(2000) tipo de datos utilizado para almacenar datos binarios
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 12-11-2020 Se elimina uso de transacciones ya que la función es sólo de obtención de valor encriptado y en estos casos nunca se
     *                         usa, ya que al colocar transacciones y hacer el close se cierra la misma conexión desde la función que la invoca y por
     *                         ende no se guarda nada de lo anterior que se haya realizado ya que no se hizo commit y así mismo lo que esté programado
     *                         después hará commit automático(sin necesidad de escribirlo) ya que ya no estará abierta la transacción.
     *                         Este error ha sido detectado desde la opción de aprobación de contrato por cambio de razón social por punto.
     */
    public function encriptar($strCadena)
    {
        if($strCadena)
        {
            try
            {
                $strValorEncriptado = str_repeat(' ', 200); // se debe enviar un string con suficiente espacio para la respuesta
                $sql = 'BEGIN PAQ_ENCRIPCION.PROC_ENCRIPTAR(:TXT_ENCRIP, :KEY_ENCRIP, :VALOR_ENCRIPTADO); END;';
                $stmt = $this->em->getConnection()->prepare($sql);
                $stmt->bindParam('TXT_ENCRIP', $strCadena);
                $stmt->bindParam('KEY_ENCRIP', $this->secretKey);
                $stmt->bindParam('VALOR_ENCRIPTADO', $strValorEncriptado);
                $stmt->execute();
                $strValorEncriptado = trim($strValorEncriptado);
            }
            catch(\Exception $e)
            {
                $strValorEncriptado = false;
                error_log($e->getMessage());
            }
            return $strValorEncriptado;
        }
        else
        {
            return false;
        }
    }

    /**
     * Funcion que se encarga del descencriptado
     * Consideraciones: Funcion debe recibir la cadena a descencriptar y el key configurado en el parameter.yml para poder ejecutar 
     * la descencriptacion
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 27-11-2014
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 12-11-2020 Se elimina uso de transacciones ya que la función es sólo de obtención de valor desencriptado y en estos casos nunca se
     *                         usa, ya que al colocar transacciones y hacer el close se cierra la misma conexión desde la función que la invoca y por
     *                         ende no se guarda nada de lo anterior que se haya realizado ya que no se hizo commit y así mismo lo que esté programado
     *                         después hará commit automático(sin necesidad de escribirlo) ya que ya no estará abierta la transacción.
     *                         Este error ha sido detectado desde la opción de aprobación de contrato por cambio de razón social por punto.
     *
     * @param string $strCadena    
     * @throws Exception 
     * @return $strValorDescencriptado CRYPT_STR  
     */
    public function descencriptar($strCadena)
    {
        if($strCadena)
        {
            try
            {
                $strValorDescencriptado = str_repeat(' ', 200); // se debe enviar un string con suficiente espacio para la respuesta
                $sql = 'BEGIN PAQ_ENCRIPCION.PROC_DESCENCRIPTAR(:TXT_DESENCRIP, :KEY_ENCRIP, :VALOR_DESCENCRIPTADO); END;';
                $stmt = $this->em->getConnection()->prepare($sql);
                $stmt->bindParam('TXT_DESENCRIP', $strCadena);
                $stmt->bindParam('KEY_ENCRIP', $this->secretKey);
                $stmt->bindParam('VALOR_DESCENCRIPTADO', $strValorDescencriptado);
                $stmt->execute();
                $strValorDescencriptado = trim($strValorDescencriptado);
            }
            catch(\Exception $e)
            {
                $strValorDescencriptado = false;
                error_log($e->getMessage());
            }
            return $strValorDescencriptado;
        }
        else
        {
            return false;
        }
    }

}
