<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoTelconetNmsBackboneUserAuth
 *
 * @ORM\Table(name="user_auth")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTelconetNmsBackboneUserAuthRepository")
 */
class InfoTelconetNmsBackboneUserAuth
{

    /**
     * @var integer id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length = 50 , nullable=false)
     */
    private $username;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length = 50 , nullable=false)
     */
    private $password;

    /**
     * @var integer $realm
     *
     * @ORM\Column(name="realm", type="integer", nullable=false)
     */
    private $realm;

    /**
     * @var string $fullName
     *
     * @ORM\Column(name="full_name", type="string", length = 100)
     */
    private $fullName;

    /**
     * @var string $mustChangePassword
     *
     * @ORM\Column(name="MUST_CHANGE_PASSWORD", type="string", length = 2)
     */
    private $mustChangePassword;

    /**
     * @var string $showTree
     *
     * @ORM\Column(name="show_tree", type="string", length = 2)
     */
    private $showTree;

    /**
     * @var string $showList
     *
     * @ORM\Column(name="show_list", type="string", length = 2)
     */
    private $showList;

    /**
     * @var string $showPreview
     *
     * @ORM\Column(name="show_preview", type="string", length = 2, nullable=false)
     */
    private $showPreview;

    /**
     * @var string $graphSettings
     *
     * @ORM\Column(name="graph_settings", type="string", length = 2 , nullable=false)
     */
    private $graphSettings;

    /**
     * @var integer $loginOpts
     *
     * @ORM\Column(name="login_opts", type="integer", nullable=false)
     */
    private $loginOpts;

    /**
     * @var integer $policyGraphs
     *
     * @ORM\Column(name="policy_graphs", type="integer", nullable=false)
     */
    private $policyGraphs;

    /**
     * @var integer $policyTrees
     *
     * @ORM\Column(name="policy_trees", type="integer", nullable=false)
     */
    private $policyTrees;

    /**
     * @var integer $policyHosts
     *
     * @ORM\Column(name="policy_hosts", type="integer", nullable=false)
     */
    private $policyHosts;

    /**
     * @var integer $policyGraphTemplates
     *
     * @ORM\Column(name="policy_graph_templates", type="integer", nullable=false)
     */
    private $policyGraphTemplates;

    /**
     * @var string $enabled
     *
     * @ORM\Column(name="enabled", type="string", length = 2 , nullable=false)
     */
    private $enabled;

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
     * Get username
     *
     * @return string
     */
    function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    function setUsername($username)
    {
        $this->username = $username;
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
     * Get realm
     *
     * @return integer
     */
    function getRealm()
    {
        return $this->realm;
    }

    /**
     * Set realm
     *
     * @param integer $realm
     */
    function setRealm($realm)
    {
        $this->realm = $realm;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set fullname
     *
     * @param string $fullName
     */
    function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * Get mustChangePassword
     *
     * @return string
     */
    function getMustChangePassword()
    {
        return $this->mustChangePassword;
    }

    /**
     * Set mustChangePassword
     *
     * @param string $mustChangePassword
     */
    function setMustChangePassword($mustChangePassword)
    {
        $this->mustChangePassword = $mustChangePassword;
    }

    /**
     * Get showTree
     *
     * @return string
     */
    function getShowTree()
    {
        return $this->showTree;
    }

    /**
     * Set showTree
     *
     * @param string $showTree
     */
    function setShowTree($showTree)
    {
        $this->showTree = $showTree;
    }

    /**
     * Get showList
     *
     * @return string
     */
    function getShowList()
    {
        return $this->showList;
    }

    /**
     * Set showList
     *
     * @param string $showList
     */
    function setShowList($showList)
    {
        $this->showList = $showList;
    }

    /**
     * Get showPreview
     *
     * @return string
     */
    function getShowPreview()
    {
        return $this->showPreview;
    }

    /**
     * Set showPreview
     *
     * @param string $showPreview
     */
    function setShowPreview($showPreview)
    {
        $this->showPreview = $showPreview;
    }

    /**
     * Get graphSettings
     *
     * @return string
     */
    function getGraphSettings()
    {
        return $this->graphSettings;
    }

    /**
     * Set graphSettings
     *
     * @param string $graphSettings
     */
    function setGraphSettings($graphSettings)
    {
        $this->graphSettings = $graphSettings;
    }

    /**
     * Get loginOpts
     *
     * @return integer
     */
    function getLoginOpts()
    {
        return $this->loginOpts;
    }

    /**
     * Set loginOpts
     *
     * @param integer $loginOpts
     */
    function setLoginOpts($loginOpts)
    {
        $this->loginOpts = $loginOpts;
    }

    /**
     * Get policyGraphs
     *
     * @return integer
     */
    function getPolicyGraphs()
    {
        return $this->policyGraphs;
    }

    /**
     * Set policyGraphs
     *
     * @param integer $policyGraphs
     */
    function setPolicyGraphs($policyGraphs)
    {
        $this->policyGraphs = $policyGraphs;
    }

    /**
     * Get policyTrees
     *
     * @return integer
     */
    function getPolicyTrees()
    {
        return $this->policyTrees;
    }

    /**
     * Set policyTrees
     *
     * @param integer $policyTrees
     */
    function setPolicyTrees($policyTrees)
    {
        $this->policyTrees = $policyTrees;
    }

    /**
     * Get policyHosts
     *
     * @return integer
     */
    function getPolicyHosts()
    {
        return $this->policyHosts;
    }

    /**
     * Set policyHosts
     *
     * @param integer $policyHosts
     */
    function setPolicyHosts($policyHosts)
    {
        $this->policyHosts = $policyHosts;
    }

    /**
     * Get policyGraphTemplates
     *
     * @return integer
     */
    function getPolicyGraphTemplates()
    {
        return $this->policyGraphTemplates;
    }

    /**
     * Set policyGraphTemplates
     *
     * @param integer $policyGraphTemplates
     */
    function setPolicyGraphTemplates($policyGraphTemplates)
    {
        $this->policyGraphTemplates = $policyGraphTemplates;
    }

    /**
     * Get enabled
     *
     * @return string
     */
    function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set enabled
     *
     * @param string $enabled
     */
    function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

}
