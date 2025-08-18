<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LabOrder;

class LabOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['viewer', 'staff']);
    }

    public function view(User $user, LabOrder $order): bool
    {
        return in_array($user->role, ['viewer', 'staff']);
    }

    public function create(User $user): bool
    {
        return $user->role === 'staff';
    }

    public function update(User $user, LabOrder $order): bool
    {
        return $user->role === 'staff';
    }

    public function delete(User $user, LabOrder $order): bool
    {
        return $user->role === 'staff';
    }
}
