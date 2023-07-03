<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoTacacsUserPeer
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTacacsUserPeerRepository")
 */
class InfoTacacsUserPeer
{

    /**
     * @var integer id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $uid
     *
     * @ORM\Column(name="uid", type="string", length = 20 , nullable=false)
     */
    private $uid;

    /**
     * @var string $gid
     *
     * @ORM\Column(name="gid", type="string", length = 20)
     */
    private $gid;

    /**
     * @var integer $groupId
     *
     * @ORM\Column(name="groupid", type="integer", nullable=false)
     */
    private $groupId;

    /**
     * @var string $comment
     *
     * @ORM\Column(name="comment", type="string", nullable=false)
     */
    private $comment;

    /**
     * @var integer $auth
     *
     * @ORM\Column(name="auth", type="integer", nullable=false)
     */
    private $auth;

    /**
     * @var integer $flags
     *
     * @ORM\Column(name="flags", type="integer", nullable=false)
     */
    private $flags;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length = 50)
     */
    private $password;

    /**
     * @var string $enable
     *
     * @ORM\Column(name="enable", type="string", length = 35)
     */
    private $enable;

    /**
     * @var string $arap
     *
     * @ORM\Column(name="arap", type="string", length = 35)
     */
    private $arap;

    /**
     * @var string $pap
     *
     * @ORM\Column(name="pap", type="string", length = 35)
     */
    private $pap;

    /**
     * @var string $chap
     *
     * @ORM\Column(name="chap", type="string", length = 35)
     */
    private $chap;

    /**
     * @var string $mschap
     *
     * @ORM\Column(name="mschap", type="string", length = 35)
     */
    private $mschap;

    /**
     * @var datetime $expires
     *
     * @ORM\Column(name="expires", type="datetime")
     */
    private $expires;

    /**
     * @var integer $disable
     *
     * @ORM\Column(name="disable", type="integer", nullable=false)
     */
    private $disable;

    /**
     * @var string $bAuthor
     *
     * @ORM\Column(name="b_author", type="string", length = 20)
     */
    private $bAuthor;

    /**
     * @var string $aAuthor
     *
     * @ORM\Column(name="a_author", type="string", length = 20)
     */
    private $aAuthor;

    /**
     * @var integer $svcDflt
     *
     * @ORM\Column(name="svc_dflt", type="integer", nullable=false)
     */
    private $svcDflt;

    /**
     * @var integer $cmdDflt
     *
     * @ORM\Column(name="cmd_dflt", type="integer", nullable=false)
     */
    private $cmdDflt;

    /**
     * @var integer $maxsess
     *
     * @ORM\Column(name="maxsess", type="integer")
     */
    private $maxsess;

    /**
     * @var integer $user
     *
     * @ORM\Column(name="user", type="integer", nullable=false)
     */
    private $user;

    /**
     * @var integer $aclId
     *
     * @ORM\Column(name="acl_id", type="integer")
     */
    private $aclId;

    /**
     * @var integer $sess
     *
     * @ORM\Column(name="sess", type="integer")
     */
    private $sess;

    /**
     * @var string $shell
     *
     * @ORM\Column(name="shell", type="string", length = 255)
     */
    private $shell;

    /**
     * @var string $homedir
     *
     * @ORM\Column(name="homedir", type="string", length = 255)
     */
    private $homedir;

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
     * Get uid
     *
     * @return string
     */
    function getUid()
    {
        return $this->uid;
    }

    /**
     * Set uid
     *
     * @param string $uid
     */
    function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * Get gid
     *
     * @return string
     */
    function getGid()
    {
        return $this->gid;
    }

    /**
     * Set gid
     *
     * @param string $gid
     */
    function setGid($gid)
    {
        $this->gid = $gid;
    }

    /**
     * Get groupId
     *
     * @return integer
     */
    function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set groupId
     *
     * @param integer $groupId
     */
    function setGroupid($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * Get comment
     *
     * @return string
     */
    function getComment()
    {
        return $this->comment;
    }

    /**
     * Set comment
     *
     * @param string $comment
     */
    function setCommet($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get auth
     *
     * @return integer
     */
    function getAuth()
    {
        return $this->auth;
    }

    /**
     * Set auth
     *
     * @param integer $auth
     */
    function setAuth($auth)
    {
        $this->auth = $auth;
    }

    /**
     * Get flags
     *
     * @return integer
     */
    function getFlags()
    {
        return $this->flags;
    }

    /**
     * Set flags
     *
     * @param integer $flags
     */
    function setFlags($flags)
    {
        $this->flags = $flags;
    }

    /**
     * Get password
     *
     * @return string
     */
    function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get enable
     *
     * @return string
     */
    function getEnable()
    {
        return $this->enable;
    }

    /**
     * Set enable
     *
     * @param string $enable
     */
    function setEnable($enable)
    {
        $this->enable = $enable;
    }

    /**
     * Get arap
     *
     * @return string
     */
    function getArap()
    {
        return $this->arap;
    }

    /**
     * Set arap
     *
     * @param string $arap
     */
    function setArap($arap)
    {
        $this->arap = $arap;
    }

    /**
     * Get pap
     *
     * @return string
     */
    function getPap()
    {
        return $this->pap;
    }

    /**
     * Set pap
     *
     * @param string $pap
     */
    function setPap($pap)
    {
        $this->pap = $pap;
    }

    /**
     * Get chap
     *
     * @return string
     */
    function getChap()
    {
        return $this->chap;
    }

    /**
     * Set chap
     *
     * @param string $chap
     */
    function setChap($chap)
    {
        $this->chap = $chap;
    }

    /**
     * Get mschap
     *
     * @return string
     */
    function getMschap()
    {
        return $this->mschap;
    }

    /**
     * Set mschap
     *
     * @param string $mschap
     */
    function setMschap($mschap)
    {
        $this->mschap = $mschap;
    }

    /**
     * Get expires
     *
     * @return datetime
     */
    function getExpires()
    {
        return $this->expires;
    }

    /**
     * Set expires
     *
     * @param datetime $expires
     */
    function setExpires($expires)
    {
        $this->expires = $expires;
    }

    /**
     * Get disable
     *
     * @return integer
     */
    function getDisable()
    {
        return $this->disable;
    }

    /**
     * Set disable
     *
     * @param integer $disable
     */
    function setDisable($disable)
    {
        $this->disable = $disable;
    }

    /**
     * Get bAuthor
     *
     * @return string
     */
    function getBauthor()
    {
        return $this->bAauthor;
    }

    /**
     * Set bAuthor
     *
     * @param string $bAuthor
     */
    function setBauthor($bAuthor)
    {
        $this->bAuthor = $bAuthor;
    }

    /**
     * Get aAuthor
     *
     * @return string
     */
    function getAauthor()
    {
        return $this->aauthor;
    }

    /**
     * Set aAuthor
     *
     * @param string $aAuthor
     */
    function setAauthor($aAuthor)
    {
        $this->aAuthor = $aAuthor;
    }

    /**
     * Get svcDflt
     *
     * @return integer
     */
    function getSvcdflt()
    {
        return $this->svcDflt;
    }

    /**
     * Set svcDflt
     *
     * @param integer $svcDflt
     */
    function setSvcdflt($svcDflt)
    {
        $this->svcDflt = $svcDflt;
    }

    /**
     * Get cmdDflt
     *
     * @return integer
     */
    function getCmdDflt()
    {
        return $this->cmdDflt;
    }

    /**
     * Set cmdDflt
     *
     * @param integer $cmdDflt
     */
    function setCmdDflt($cmdDflt)
    {
        $this->cmdDflt = $cmdDflt;
    }

    /**
     * Get maxsess
     *
     * @return integer
     */
    function getMaxsess()
    {
        return $this->maxsess;
    }

    /**
     * Set maxsess
     *
     * @param integer $maxsess
     */
    function setMaxsess($maxsess)
    {
        $this->maxsess = $maxsess;
    }

    /**
     * Get user
     *
     * @return integer
     */
    function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param integer $user
     */
    function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get aclId
     *
     * @return integer
     */
    function getAclId()
    {
        return $this->aclId;
    }

    /**
     * Set aclId
     *
     * @param integer $aclId
     */
    function setAclId($aclId)
    {
        $this->aclId = $aclId;
    }

    /**
     * Get sess
     *
     * @return integer
     */
    function getSess()
    {
        return $this->sess;
    }

    /**
     * Set sess
     *
     * @param integer $sess
     */
    function setSess($sess)
    {
        $this->sess = $sess;
    }

    /**
     * Get shell
     *
     * @return string
     */
    function getShell()
    {
        return $this->shell;
    }

    /**
     * Set shell
     *
     * @param string $shell
     */
    function setShell($shell)
    {
        $this->shell = $shell;
    }

    /**
     * Get homedir
     *
     * @return string
     */
    function getHomedir()
    {
        return $this->homedir;
    }

    /**
     * Set homedir
     *
     * @param string $homedir
     */
    function setHomedir($homedir)
    {
        $this->homedir = $homedir;
    }
}
