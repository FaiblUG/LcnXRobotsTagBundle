<?php

namespace Lcn\XRobotsTagBundle\Services;

class XRobotsTag
{

    /**
     * @var bool
     */
    private $noindex;

    /**
     * @var bool
     */
    private $nofollow;

    /**
     * @var bool
     */
    private $noindexDefault;

    /**
     * @var bool
     */
    private $nofollowDefault;

    /**
     * @var array
     */
    private $userRoleRules;

    /**
     * @var bool
     */
    private $isExplicitlySet;

    /**
     * Rules can be defined in config.yml:
     * <pre>
     * lcn_x_robots_tag:
     *     rules:
     *         default:
     *             noindex: false
     *             nofollow: false
     *         user_roles:
     *             ROLE_USER:
     *                 noindex: true
     *                 nofollow: false
     *             *:
     *                 noindex: true
     *                 nofollow: true
     * </pre>
     *
     * @var array $rules
     */
    public function __construct(array $rules = null)
    {
        if ($rules) {
            $this->setRules($rules);
        }
    }

    /**
     * @param array $rules
     */
    private function setRules($rules)
    {
        if (array_key_exists('default', $rules)) {
            if (array_key_exists('noindex', $rules['default'])) {
                $this->setNoindexDefault($rules['default']['noindex']);
            }
            if (array_key_exists('nofollow', $rules['default'])) {
                $this->setNofollowDefault($rules['default']['nofollow']);
            }
        }

        if (array_key_exists('user_roles', $rules)) {
            $this->userRoleRules = $rules['user_roles'];
        }

        return $this;
    }


    /**
     * @return boolean
     */
    public function isNoindex()
    {
        return $this->isExplicitlySet() ? (bool)$this->noindex : $this->isNoindexDefault();
    }

    /**
     * @param boolean $noindex
     */
    public function setNoindex($noindex)
    {
        $this->noindex = (bool)$noindex;
        $this->setIsExplicitlySet(true);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isNofollow()
    {
        return $this->isExplicitlySet() ? (bool)$this->nofollow : $this->isNofollowDefault();
    }

    /**
     * @param boolean $nofollow
     */
    public function setNofollow($nofollow)
    {
        $this->nofollow = (bool)$nofollow;
        $this->setIsExplicitlySet(true);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isNoindexDefault()
    {
        return (bool)$this->noindexDefault;
    }

    /**
     * @param boolean $noindexDefault
     */
    private function setNoindexDefault($noindexDefault)
    {
        $this->noindexDefault = (bool)$noindexDefault;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isNofollowDefault()
    {
        return (bool)$this->nofollowDefault;
    }

    /**
     * @param boolean $nofollowDefault
     */
    private function setNofollowDefault($nofollowDefault)
    {
        $this->nofollowDefault = (bool)$nofollowDefault;

        return $this;
    }



    public function isNoindexForUserRole($role)
    {
        if ($this->userRoleRules && array_key_exists($role, $this->userRoleRules)) {
            return (bool)$this->userRoleRules[$role]['noindex'];
        }
        elseif ($this->userRoleRules && array_key_exists('*', $this->userRoleRules)) {
            return (bool)$this->userRoleRules['*']['noindex'];
        }

        return false;
    }


    public function isNofollowForUserRole($role)
    {
        if ($this->userRoleRules && array_key_exists($role, $this->userRoleRules)) {
            return (bool)$this->userRoleRules[$role]['nofollow'];
        }
        elseif ($this->userRoleRules && array_key_exists('*', $this->userRoleRules)) {
            return (bool)$this->userRoleRules['*']['nofollow'];
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isExplicitlySet()
    {
        return (bool)$this->isExplicitlySet;
    }

    /**
     * @param bool $isExplicitlySet
     */
    public function setIsExplicitlySet($isExplicitlySet)
    {
        $this->isExplicitlySet = (bool)$isExplicitlySet;

        return $this;
    }






}
