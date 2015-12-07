<?php

namespace Lcn\XRobotsTagBundle\EventListeners;

use Lcn\XRobotsTagBundle\Services\XRobotsTag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\AccessMapInterface;


class ResponseListener
{

    /**
     * @var XRobotsTag
     */
    private $xRobotsTag;

    /**
     * @var AccessMapInterface
     */
    private $accessMap;

    /**
     * @var
     */
    private $enabled;

    /**
     * @param XRobotsTag $xRobotsTag
     * @param AccessMapInterface $accessMap
     * @param bool $enabled
     */
    public function __construct(XRobotsTag $xRobotsTag, AccessMapInterface $accessMap, $enabled)
    {
        $this->xRobotsTag = $xRobotsTag;
        $this->accessMap = $accessMap;
        $this->enabled = $enabled;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$this->enabled) {

            return;
        }

        $isNoindex = $this->xRobotsTag->isNoindex();
        $isNofollow = $this->xRobotsTag->isNofollow();

        if (!$this->xRobotsTag->isExplicitlySet()) {
            $requiredUserRoles = $this->getRequiredUserRolesForRequest($event->getRequest());
            foreach ($requiredUserRoles as $requiredUserRole) {
                if ($this->xRobotsTag->isNoindexForUserRole($requiredUserRole) || $this->xRobotsTag->isNofollowForUserRole($requiredUserRole)) {
                    $isNoindex = $this->xRobotsTag->isNoindexForUserRole($requiredUserRole);;
                    $isNofollow = $this->xRobotsTag->isNofollowForUserRole($requiredUserRole);
                    break;
                }
            }
        }

        $tags = [];
        if ($isNoindex) {
            $tags[] = 'noindex';
        }
        if ($isNofollow) {
            $tags[] = 'nofollow';
        }

        if (count($tags)) {
            $event->getResponse()->headers->set('X-Robots-Tag', implode(',', $tags));
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getRequiredUserRolesForRequest(Request $request)
    {
        $patterns = $this->accessMap->getPatterns($request);
        if ($patterns && is_array($patterns) && is_array($patterns[0])) {
            return $patterns[0];
        }

        return [];
    }



}
