<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDocumentoComp
 *
 * @ORM\Table(name="INFO_DOCUMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDocumentoCompRepository")
 */
class InfoDocumentoComp
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_DOCUMENTO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ELEMENTO", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer $tipoDocId
     *
     * @ORM\Column(name="TIPO_DOC_ID", type="integer", nullable=false)
    */
    private $tipoDocId;

    /**
     * @var integer $formatoId
     *
     * @ORM\Column(name="FORMATO_ID", type="integer", nullable=false)
    */
    private $formatoId;

    /**
     * @var integer $documentoIfFinan
     *
     * @ORM\Column(name="DOCUMENTO_ID_FINAN", type="integer", nullable=true)
     */
    private $documentoIfFinan;

    
   
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
     * Get tipoDocId
     *
     * @return integer
     */
    public function getTipoDocId()
    {
        return $this->tipoDocId;
    }

    /**
     * Set tipoDocId
     *
     * @param string $tipoDocId
     */
    public function setTipoDocId($tipoDocId)
    {
        $this->tipoDocId = $tipoDocId;
    }

    /**
     * Get formatoId
     *
     * @return integer
     */
    public function getFormatoId()
    {
        return $this->formatoId;
    }

    /**
     * Set formatoId
     *
     * @param string $formatoId
     */
    public function setFormatoId($formatoId)
    {
        $this->formatoId = $formatoId;
    }
    
    /**
     * Get documentoIfFinan
     *
     * @return integer
     */
    public function getDocumentoIdFinan()
    {
        return $this->documentoIfFinan;
    }

    /**
     * Set documentoIfFinan
     *
     * @param string $documentoIfFinan
     */
    public function setDocumentoIdFinan($documentoIfFinan)
    {
        $this->documentoIfFinan = $documentoIfFinan;
    }
    

}
