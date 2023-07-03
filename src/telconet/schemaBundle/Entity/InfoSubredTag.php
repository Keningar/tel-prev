<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoSubredTag
 *
 * @ORM\Table(name="INFO_SUBRED_TAG")
 * @ORM\Entity
 */
class InfoSubredTag
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_SUBRED_TAG", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_SUBRED_TAG", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer $subredId
     *
     * @ORM\Column(name="SUBRED_ID", type="integer", nullable=false)
     */
    private $subredId;

    /**
     * @var integer $tagId
     *
     * @ORM\Column(name="TAG_ID", type="integer", nullable=false)
     */
    private $tagId;

    /**
     * @var string $usrCreacion
     *
     * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
     */
    private $usrCreacion;

    /**
     * @var datetime $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
     */
    private $feCreacion;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estado;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get subredId
     *
     * @return integer
     */
    public function getSubredId()
    {
        return $this->subredId;
    }

    /**
     * Set subredId
     *
     * @param integer $subredId
     */
    public function setSubredId($subredId)
    {
        $this->subredId = $subredId;
    }

    /**
     * Get tagId
     *
     * @return integer
     */
    public function getTagId()
    {
        return $this->tagId;
    }

    /**
     * Set tagId
     *
     * @param integer $tagId
     */
    public function setTagId($tagId)
    {
        $this->tagId = $tagId;
    }

    /**
     * Get usrCreacion
     *
     * @return string
     */
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
     * Set usrCreacion
     *
     * @param string $usrCreacion
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
    }

    /**
     * Get feCreacion
     *
     * @return datetime
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
     * Set feCreacion
     *
     * @param datetime $feCreacion
     */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->ipCreacion;
    }

    /**
     * Set estado
     *
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

}
