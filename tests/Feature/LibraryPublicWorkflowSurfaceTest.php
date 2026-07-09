<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryPublicWorkflowSurfaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_library_workflow_surfaces_are_available(): void
    {
        $this->get('/opac')->assertOk();
        $this->get('/kiosk/scan')->assertOk();
        $this->get('/rooms/schedule')->assertOk();
    }
}
