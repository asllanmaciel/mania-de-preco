<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Console\Command\Command;
use Tests\TestCase;

class LaunchPreflightTest extends TestCase
{
    use RefreshDatabase;

    public function test_launch_check_reports_local_critical_blockers(): void
    {
        $this->artisan('launch:check')
            ->expectsOutput('Pre-flight de lancamento')
            ->assertExitCode(Command::FAILURE);
    }
}
