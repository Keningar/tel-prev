<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoTelconetUserHistoryPassAAAA
 *
 * @ORM\Table(name="user_history_passwd")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTelconetUserHistoryPassAAAARepository")
 */
class InfoTelconetUserHistoryPassAAAA
{

    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $login
     *
     * @ORM\Column(name = "login", type="string", nullable = false)
     */
    private $login;

    /**
     * @var string $encrypt
     *
     * @ORM\Column(name = "encrypt", type="string", nullable = false)
     */
    private $encrypt;

    /**
     * @var string $passwd
     *
     * @ORM\Column(name = "passwd", type="string", nullable = false)
     */
    private $passwd;

    /**
     * @var string $feCambio
     *
     * @ORM\Column(name="fe_cambio", type="string", nullable=true)
     */
    private $feCambio;

    /**
     * @var string $observacion
     *
     * @ORM\Column(name = "observacion", type="string", nullable = true)
     */
    private $observacion;

    /**
     * Get login
     *
     * @return string
     */
    function getLogin()
    {
        return $this->login;
    }

    /**
     * Set login
     *
     * @param string $login
     */
    function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * Get id
     *
     * @return integer
     */
    function getId()
    {
        return $this->id;
    }

    /**
     * Get encrypt
     *
     * @return string
     */
    function getEncrypt()
    {
        return $this->encrypt;
    }

    /**
     * Get passwd
     *
     * @return string
     */
    function getPasswd()
    {
        return $this->passwd;
    }

    /**
     * Get feCambio
     *
     * @return string
     */
    function getFeCambio()
    {
        return $this->feCambio;
    }

    /**
     * Get observacion
     *
     * @return string
     */
    function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set encrypt
     *
     * @param string $encrypt
     */
    function setEncrypt($encrypt)
    {
        $this->encrypt = $encrypt;
    }

    /**
     * Set passwd
     *
     * @param string $passwd
     */
    function setPasswd($passwd)
    {
        $this->passwd = $passwd;
    }

    /**
     * Set feCambio
     *
     * @param string $feCambio
     */
    function setFeCambio($feCambio)
    {
        $this->feCambio = $feCambio;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     */
    function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    }

}
