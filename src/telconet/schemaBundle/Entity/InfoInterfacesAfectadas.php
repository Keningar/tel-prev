<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiBines
 *
 * @ORM\Table(name="INFO_INTERFACES_AFECTADAS")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoInterfacesAfectadasRepository")
 */
class InfoInterfacesAfectadas
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_INTERFACE_TMP", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_TMP_INTERFACES_AFECTATADAS", allocationSize=1, initialValue=1)
    */
    private $id;

    /**
     * @var integer $interfaceId
     *
     * @ORM\Column(name="INTERFACE_ID", type="integer", nullable=false)
     */
    private $interfaceId;

    /**
     * @var integer $procesoId
     *
     * @ORM\Column(name="PROCESO_ID", type="integer", nullable=false)
     */
    private $procesoId;

    /**
     * Set interfaceId
     *
     * @param integer $interfaceId
     */
    public function setInterfaceId($interfaceId)
    {
        $this->interfaceId = $interfaceId;
    }

    /**
     * Get interfaceId
     *
     * @return integer
     */
    public function getInterfaceId()
    {
        return $this->interfaceId;
    }

    /**
     * Set procesoId
     *
     * @param integer $procesoId
     */
    public function setProcesoId($procesoId)
    {
        $this->procesoId = $procesoId;
    }

    /**
     * Get procesoId
     *
     * @return integer
     */
    public function getProcesoId()
    {
        return $this->procesoId;
    }
}