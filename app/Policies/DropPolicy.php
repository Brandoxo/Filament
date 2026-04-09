<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Drop;
use Illuminate\Auth\Access\HandlesAuthorization;

class DropPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Drop');
    }

    public function view(AuthUser $authUser, Drop $drop): bool
    {
        return $authUser->can('View:Drop');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Drop');
    }

    public function update(AuthUser $authUser, Drop $drop): bool
    {
        return $authUser->can('Update:Drop');
    }

    public function delete(AuthUser $authUser, Drop $drop): bool
    {
        return $authUser->can('Delete:Drop');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Drop');
    }

    public function restore(AuthUser $authUser, Drop $drop): bool
    {
        return $authUser->can('Restore:Drop');
    }

    public function forceDelete(AuthUser $authUser, Drop $drop): bool
    {
        return $authUser->can('ForceDelete:Drop');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Drop');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Drop');
    }

    public function replicate(AuthUser $authUser, Drop $drop): bool
    {
        return $authUser->can('Replicate:Drop');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Drop');
    }

}