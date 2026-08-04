<?php

namespace Themosis\Core\Foundation;

use Illuminate\Contracts\Foundation\MaintenanceMode;

/**
 * Minimal file based implementation of the "MaintenanceMode" contract
 * introduced by illuminate/contracts ^9.0.
 *
 * The Themosis framework relies on its own WordPress based maintenance
 * detection (@see \Themosis\Core\Application::isDownForMaintenance()).
 * This class only exists so the Application satisfies the
 * Illuminate\Contracts\Foundation\Application interface, which now
 * requires a `maintenanceMode()` method.
 */
class FileBasedMaintenanceMode implements MaintenanceMode
{
    /**
     * @var string
     */
    protected $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Take the application down for maintenance.
     */
    public function activate(array $payload): void
    {
        file_put_contents($this->path, json_encode($payload));
    }

    /**
     * Take the application out of maintenance.
     */
    public function deactivate(): void
    {
        if (file_exists($this->path)) {
            @unlink($this->path);
        }
    }

    /**
     * Determine if the application is currently down for maintenance.
     */
    public function active(): bool
    {
        return file_exists($this->path);
    }

    /**
     * Get the data array which was provided when the application was placed into maintenance.
     */
    public function data(): array
    {
        return json_decode(file_get_contents($this->path), true) ?: [];
    }
}
