<?php

namespace Lcn\XRobotsTagBundle\Tests\DependencyInjection;

use Lcn\XRobotsTagBundle\DependencyInjection\LcnXRobotsTagExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

class LcnXRobotsTagExtensionTest extends AbstractExtensionTestCase
{

    protected function getContainerExtensions()
    {
        return array(
            new LcnXRobotsTagExtension()
        );
    }

    public function testDefaultConfiguration()
    {
        $this->load();

        $this->assertContainerBuilderHasService('lcn_x_robots_tag', 'Lcn\\XRobotsTagBundle\\Services\\XRobotsTag');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('lcn_x_robots_tag', 0, [
            'default' => ['noindex' => false, 'nofollow' => false],
            'user_roles' => [],
        ]);

        $this->assertContainerBuilderHasService('lcn_x_robots_tag.response_listener', 'Lcn\\XRobotsTagBundle\\EventListeners\\ResponseListener');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('lcn_x_robots_tag.response_listener', 2, false);
    }

    public function testEnabledConfiguration()
    {
        $this->load(['enabled' => true]);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('lcn_x_robots_tag.response_listener', 2, true);

        $this->load(['enabled' => false]);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('lcn_x_robots_tag.response_listener', 2, false);
    }



    public function testDefaultRulesConfiguration()
    {
        $this->load([
            'rules' => [
                'default' => true,
            ]
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('lcn_x_robots_tag', 0, [
          'default' => ['noindex' => true, 'nofollow' => true],
          'user_roles' => [],
        ]);

        $this->load([
            'rules' => [
                'default' => [
                    'noindex' => true,
                    'nofollow' => false,
                ],
            ]
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('lcn_x_robots_tag', 0, [
          'default' => ['noindex' => true, 'nofollow' => false],
          'user_roles' => [],
        ]);
    }

    public function testUserRoleRulesConfiguration()
    {
        $this->load([
            'rules' => [
                'user_roles' => ['ROLE_USER' => true],
            ]
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('lcn_x_robots_tag', 0, [
          'default' => ['noindex' => false, 'nofollow' => false],
          'user_roles' => [
              'ROLE_USER' => ['noindex' => true, 'nofollow' => true],
          ],
        ]);

        $this->load([
            'rules' => [
                'user_roles' => true,
            ]
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('lcn_x_robots_tag', 0, [
          'default' => ['noindex' => false, 'nofollow' => false],
          'user_roles' => [
              '*' => ['noindex' => true, 'nofollow' => true],
          ],
        ]);

        $this->load([
          'rules' => [
            'user_roles' => [
              'ROLE_1' => ['noindex' => true, 'nofollow' => false],
              'ROLE_2' => ['noindex' => true, 'nofollow' => true],
            ]
          ]
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('lcn_x_robots_tag', 0, [
          'default' => ['noindex' => false, 'nofollow' => false],
          'user_roles' => [
            'ROLE_1' => ['noindex' => true, 'nofollow' => false],
            'ROLE_2' => ['noindex' => true, 'nofollow' => true],
          ],
        ]);
    }
}
