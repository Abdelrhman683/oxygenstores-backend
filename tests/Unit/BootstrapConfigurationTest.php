<?php

namespace Tests\Unit;

use Tests\TestCase;

class BootstrapConfigurationTest extends TestCase
{
    public function test_domain_pointed_directory_is_defined_during_bootstrap(): void
    {
        $this->assertTrue(defined('DOMAIN_POINTED_DIRECTORY'));
        $this->assertSame('public', DOMAIN_POINTED_DIRECTORY);
    }
}
