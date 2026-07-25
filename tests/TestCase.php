<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;

    protected $user;
    protected $token;
    protected $tenant;
    protected bool $initializeTenancy = true;

    protected function setUp(): void
    {
        parent::setUp();
        
        if ($this->initializeTenancy) {
            $this->tenant = \App\Models\Tenant::create(['id' => 'test-tenant-' . uniqid()]);
            $this->tenant->domains()->create(['domain' => $this->tenant->id . '.localhost']);

            tenancy()->initialize($this->tenant);
            
            $this->app[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

            $this->user = User::factory()->create();
            $this->token = $this->user->createToken('test-token')->plainTextToken;
            
            \Illuminate\Support\Facades\URL::forceRootUrl('http://' . $this->tenant->id . '.localhost');
        }
    }

    protected function tearDown(): void
    {
        tenancy()->end();

        if ($this->tenant) {
            $dbPath = database_path('tenant' . $this->tenant->id);
            $this->tenant->delete();
            if (file_exists($dbPath)) {
                unlink($dbPath);
            }
        }
        parent::tearDown();
    }
}
