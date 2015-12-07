<?php

namespace Lcn\XRobotsTagBundle\Tests\Services;

use Lcn\XRobotsTagBundle\Services\XRobotsTag;

class XRobotsTagTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $xRobotsTag = new XRobotsTag();
        $this->assertFalse($xRobotsTag->isNoindex());
        $this->assertFalse($xRobotsTag->isNofollow());

        $xRobotsTag = new XRobotsTag(['default' => ['noindex' => false, 'nofollow' => false]]);
        $this->assertFalse($xRobotsTag->isNoindex());
        $this->assertFalse($xRobotsTag->isNofollow());

        $xRobotsTag = new XRobotsTag(['default' => ['noindex' => true, 'nofollow' => false]]);
        $this->assertTrue($xRobotsTag->isNoindexDefault());
        $this->assertFalse($xRobotsTag->isNofollowDefault());

        $xRobotsTag = new XRobotsTag(['default' => ['noindex' => false, 'nofollow' => true]]);
        $this->assertFalse($xRobotsTag->isNoindex());
        $this->assertTrue($xRobotsTag->isNofollow());

        $xRobotsTag = new XRobotsTag(['default' => ['noindex' => true, 'nofollow' => true]]);
        $this->assertTrue($xRobotsTag->isNoindex());
        $this->assertTrue($xRobotsTag->isNofollow());
    }

    public function testSetterAndGetter()
    {
        $xRobotsTag = new XRobotsTag();
        $xRobotsTag->setNoindex(true);
        $this->assertTrue($xRobotsTag->isNoindex());
        $this->assertFalse($xRobotsTag->isNofollow());

        $xRobotsTag->setNofollow(true);
        $this->assertTrue($xRobotsTag->isNoindex());
        $this->assertTrue($xRobotsTag->isNofollow());

        $xRobotsTag->setNofollow(false);
        $this->assertTrue($xRobotsTag->isNoindex());
        $this->assertFalse($xRobotsTag->isNofollow());

        $xRobotsTag->setNoindex(false);
        $this->assertFalse($xRobotsTag->isNoindex());
        $this->assertFalse($xRobotsTag->isNofollow());
    }

    public function testUserRoles()
    {
        $roleUser = 'ROLE_USER';
        $roleAdmin = 'ROLE_ADMIN';

        $xRobotsTag = new XRobotsTag();

        $this->assertFalse($xRobotsTag->isNoindexForUserRole($roleUser));
        $this->assertFalse($xRobotsTag->isNofollowForUserRole($roleUser));

        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            $roleUser => ['noindex' => true, 'nofollow' => true],
          ],
        ]);

        $this->assertTrue($xRobotsTag->isNoindexForUserRole($roleUser));
        $this->assertTrue($xRobotsTag->isNofollowForUserRole($roleUser));

        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            $roleUser => ['noindex' => false, 'nofollow' => true],
          ],
        ]);


        $this->assertFalse($xRobotsTag->isNoindexForUserRole($roleUser));
        $this->assertTrue($xRobotsTag->isNofollowForUserRole($roleUser));

        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            $roleUser => ['noindex' => true, 'nofollow' => false],
          ],
        ]);


        $this->assertTrue($xRobotsTag->isNoindexForUserRole($roleUser));
        $this->assertFalse($xRobotsTag->isNofollowForUserRole($roleUser));

        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            $roleUser => ['noindex' => false, 'nofollow' => false],
          ],
        ]);


        $this->assertFalse($xRobotsTag->isNoindexForUserRole($roleUser));
        $this->assertFalse($xRobotsTag->isNofollowForUserRole($roleUser));

        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            $roleUser => ['noindex' => false, 'nofollow' => true],
            $roleAdmin => ['noindex' => true, 'nofollow' => false],
          ],
        ]);


        $this->assertFalse($xRobotsTag->isNoindexForUserRole($roleUser));
        $this->assertTrue($xRobotsTag->isNofollowForUserRole($roleUser));
        $this->assertTrue($xRobotsTag->isNoindexForUserRole($roleAdmin));
        $this->assertFalse($xRobotsTag->isNofollowForUserRole($roleAdmin));

        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            '*' => ['noindex' => false, 'nofollow' => true],
          ],
        ]);

        $this->assertFalse($xRobotsTag->isNoindexForUserRole($roleUser));
        $this->assertTrue($xRobotsTag->isNofollowForUserRole($roleUser));
        $this->assertFalse($xRobotsTag->isNoindexForUserRole($roleAdmin));
        $this->assertTrue($xRobotsTag->isNofollowForUserRole($roleAdmin));

        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            '*' => ['noindex' => true, 'nofollow' => true],
          ],
        ]);

        $this->assertTrue($xRobotsTag->isNoindexForUserRole($roleUser));
        $this->assertTrue($xRobotsTag->isNofollowForUserRole($roleUser));
        $this->assertTrue($xRobotsTag->isNoindexForUserRole($roleAdmin));
        $this->assertTrue($xRobotsTag->isNofollowForUserRole($roleAdmin));

        $xRobotsTag = new XRobotsTag([
          'user_roles' => [
            $roleUser => ['noindex' => false, 'nofollow' => false],
            '*' => ['noindex' => true, 'nofollow' => true],
          ],
        ]);

        $this->assertFalse($xRobotsTag->isNoindexForUserRole($roleUser));
        $this->assertFalse($xRobotsTag->isNofollowForUserRole($roleUser));
        $this->assertTrue($xRobotsTag->isNoindexForUserRole($roleAdmin));
        $this->assertTrue($xRobotsTag->isNofollowForUserRole($roleAdmin));
    }
}
