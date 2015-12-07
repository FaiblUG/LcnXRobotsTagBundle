<?php

namespace Lcn\XRobotsTagBundle\EventListeners;

use Lcn\XRobotsTagBundle\Services\XRobotsTag;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class ResponseListenerTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaultResponseHeaders()
    {
        $headersSpy = $this->getHeadersSpy();
        $headersSpy->expects($this->never())->method('set');

        $xRobotsTag = new XRobotsTag();

        $accessMapMock = $this->getAccessMapMock();
        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, true);
        $responseEventMock = $this->getResponseEventMock($headersSpy);

        $responseListener->onKernelResponse($responseEventMock);

        $xRobotsTag = new XRobotsTag(['default' => ['noindex' => false, 'nofollow' => false]]);
        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, true);
        $responseListener->onKernelResponse($responseEventMock);

        $headersSpy = $this->getHeadersSpy();
        $headersSpy->expects($this->once())->method('set')->with($this->identicalTo('X-Robots-Tag'), $this->identicalTo('noindex,nofollow'));
        $responseEventMock = $this->getResponseEventMock($headersSpy);
        $xRobotsTag = new XRobotsTag(['default' => ['noindex' => true, 'nofollow' => true]]);
        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, true);
        $responseListener->onKernelResponse($responseEventMock);

        $headersSpy = $this->getHeadersSpy();
        $headersSpy->expects($this->once())->method('set')->with($this->identicalTo('X-Robots-Tag'), $this->identicalTo('noindex'));
        $responseEventMock = $this->getResponseEventMock($headersSpy);
        $xRobotsTag = new XRobotsTag(['default' => ['noindex' => true, 'nofollow' => false]]);
        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, true);
        $responseListener->onKernelResponse($responseEventMock);

        $headersSpy = $this->getHeadersSpy();
        $headersSpy->expects($this->once())->method('set')->with($this->identicalTo('X-Robots-Tag'), $this->identicalTo('nofollow'));
        $responseEventMock = $this->getResponseEventMock($headersSpy);
        $xRobotsTag = new XRobotsTag(['default' => ['noindex' => false, 'nofollow' => true]]);
        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, true);
        $responseListener->onKernelResponse($responseEventMock);

    }


    public function testEnabled()
    {
        $accessMapMock = $this->getAccessMapMock();
        $xRobotsTag = new XRobotsTag(['default' => ['noindex' => true, 'nofollow' => true]]);

        $headersSpy = $this->getHeadersSpy();
        $headersSpy->expects($this->once())->method('set');
        $responseEventMock = $this->getResponseEventMock($headersSpy);
        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, true);
        $responseListener->onKernelResponse($responseEventMock);

        $headersSpy = $this->getHeadersSpy();
        $headersSpy->expects($this->never())->method('set');
        $responseEventMock = $this->getResponseEventMock($headersSpy);
        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, false);
        $responseListener->onKernelResponse($responseEventMock);
    }


    public function testExplicitResponseHeaders()
    {
        $accessMapMock = $this->getAccessMapMock();
        $headersSpy = $this->getHeadersSpy();
        $headersSpy->expects($this->once())->method('set')->with($this->identicalTo('X-Robots-Tag'), $this->identicalTo('noindex,nofollow'));
        $responseEventMock = $this->getResponseEventMock($headersSpy);
        $xRobotsTag = new XRobotsTag();
        $xRobotsTag->setNoindex(true);
        $xRobotsTag->setNofollow(true);
        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, true);
        $responseListener->onKernelResponse($responseEventMock);



        //        $accessMapMock->method('getPatterns')->willReturn([
//            ['ROLE_USER'],
//        ]);


    }

    public function testUserRolesResponseHeaders()
    {
        $accessMapMock = $this->getAccessMapMock();
        $accessMapMock->method('getPatterns')->willReturn([
            ['ROLE_USER', 'ROLE_ADMIN'],
        ]);

        $headersSpy = $this->getHeadersSpy();
        $headersSpy->expects($this->once())->method('set')->with($this->identicalTo('X-Robots-Tag'), $this->identicalTo('noindex'));
        $responseEventMock = $this->getResponseEventMock($headersSpy);
        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            'ROLE_USER' => ['noindex' => true, 'nofollow' => false],
          ],
        ]);
        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, true);
        $responseListener->onKernelResponse($responseEventMock);

        $headersSpy = $this->getHeadersSpy();
        $headersSpy->expects($this->once())->method('set')->with($this->identicalTo('X-Robots-Tag'), $this->identicalTo('noindex,nofollow'));
        $responseEventMock = $this->getResponseEventMock($headersSpy);
        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            'ROLE_ADMIN' => ['noindex' => true, 'nofollow' => true],
          ],
        ]);
        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, true);
        $responseListener->onKernelResponse($responseEventMock);

        $headersSpy = $this->getHeadersSpy();
        $headersSpy->expects($this->never())->method('set');
        $responseEventMock = $this->getResponseEventMock($headersSpy);
        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            'ROLE_XXX' => ['noindex' => true, 'nofollow' => true],
          ],
        ]);
        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, true);
        $responseListener->onKernelResponse($responseEventMock);

        $headersSpy = $this->getHeadersSpy();
        $headersSpy->expects($this->once())->method('set')->with($this->identicalTo('X-Robots-Tag'), $this->identicalTo('nofollow'));
        $responseEventMock = $this->getResponseEventMock($headersSpy);
        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            '*' => ['noindex' => false, 'nofollow' => true],
          ],
        ]);
        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, true);
        $responseListener->onKernelResponse($responseEventMock);


        $accessMapMock = $this->getAccessMapMock();
        $accessMapMock->method('getPatterns')->willReturn([]);
        $headersSpy = $this->getHeadersSpy();
        $headersSpy->expects($this->never())->method('set');
        $responseEventMock = $this->getResponseEventMock($headersSpy);

        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            'ROLE_ADMIN' => ['noindex' => true, 'nofollow' => true],
          ],
        ]);
        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, true);
        $responseListener->onKernelResponse($responseEventMock);

        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            '*' => ['noindex' => true, 'nofollow' => true],
          ],
        ]);
        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, true);
        $responseListener->onKernelResponse($responseEventMock);

    }

    public function testExplicitValuesOverrideUserRoleRules()
    {
        $accessMapMock = $this->getAccessMapMock();
        $accessMapMock->method('getPatterns')->willReturn([
          ['ROLE_ADMIN'],
        ]);
        $headersSpy = $this->getHeadersSpy();
        $headersSpy->expects($this->once())->method('set')->with($this->identicalTo('X-Robots-Tag'), $this->identicalTo('noindex'));
        $responseEventMock = $this->getResponseEventMock($headersSpy);
        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            'ROLE_ADMIN' => ['noindex' => true, 'nofollow' => true],
          ],
        ]);
        $xRobotsTag->setNoindex(true);

        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, true);
        $responseListener->onKernelResponse($responseEventMock);

        $headersSpy = $this->getHeadersSpy();
        $headersSpy->expects($this->never())->method('set');
        $responseEventMock = $this->getResponseEventMock($headersSpy);
        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            'ROLE_ADMIN' => ['noindex' => true, 'nofollow' => true],
          ],
        ]);
        $xRobotsTag->setNoindex(false);

        $responseListener = new ResponseListener($xRobotsTag, $accessMapMock, true);
        $responseListener->onKernelResponse($responseEventMock);

    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getAccessMapMock()
    {
        $accessMapMock = $this
          ->getMock('Symfony\Component\Security\Http\AccessMapInterface', ['getPatterns']);

        return $accessMapMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getRequestMock()
    {
        $requestMock = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
          ->disableOriginalConstructor()
          ->getMock();

        return $requestMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getResponseEventMock(\PHPUnit_Framework_MockObject_MockObject $headersSpy)
    {
        $responseEventMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterResponseEvent')
          ->disableOriginalConstructor()
          ->setMethods(['getRequest', 'getResponse'])
          ->getMock();

        $requestMock = $this->getRequestMock();

        $responseEventMock->method('getRequest')->willReturn($requestMock);

        $responseMock = new \StdClass();
        $responseMock->headers = $headersSpy;

        $responseEventMock->method('getResponse')->willReturn($responseMock);

        return $responseEventMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getHeadersSpy()
    {
        $headersSpy = $this->getMock('StdClass', ['set']);

        return $headersSpy;
    }


}
