<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoTelconetTmpUserAAAA
 *
 * @ORM\Table(name="tmp_users")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTelconetTmpUserAAAARepository")
 */
class InfoTelconetTmpUserAAAA
{

    /**
     * @var string login
     *
     * @ORM\Column(name = "login", type="string", nullable = false)
     * @ORM\Id
     */
    private $login;

    /**
    * @var datetime $feUltMod
    *
    * @ORM\Column(name="fe_ult_mod", type="datetime", nullable=false)
    */		

    private $feUltMod;

    /**
    * @var string $prefijoEmpresa
    *
    * @ORM\Column(name="prefijo_empresa", type="string", nullable=true)
    */		

    private $prefijoEmpresa;

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
     * Get feUltMod
     *
     * @return datetime
     */
    function getFeUltMod()
    {
        return $this->feUltMod;
    }

    /**
     * Set feUltMod
     *
     * @param datetime $feUltMod
     */
    function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
    }
    
     /**
     * Get prefijoEmpresa
     *
     * @return string
     */
    function getPrefijoEmpresa()
    {
        return $this->prefijoEmpresa;
    }

    /**
     * Set prefijoEmpresa
     *
     * @param string $prefijoEmpresa
     */
    function setPrefijoEmpresa($prefijoEmpresa)
    {
        $this->prefijoEmpresa = $prefijoEmpresa;
    }

}
