<?php

namespace App\Policies;

use App\Models\DeviceReport;
use App\Models\User;

class DeviceReportPolicy
{
    /**
     * Semua user terautentikasi boleh melihat daftar (di-scope di controller) dan membuat laporan.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function view(User $user, DeviceReport $report): bool
    {
        return $this->ownsOrAdmin($user, $report);
    }

    public function update(User $user, DeviceReport $report): bool
    {
        return $this->ownsOrAdmin($user, $report);
    }

    public function delete(User $user, DeviceReport $report): bool
    {
        return $this->ownsOrAdmin($user, $report);
    }

    /**
     * Admin: semua. Selain admin: hanya laporan miliknya (user_id null -> admin saja).
     */
    private function ownsOrAdmin(User $user, DeviceReport $report): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $report->user_id !== null && (int) $report->user_id === (int) $user->id;
    }
}
