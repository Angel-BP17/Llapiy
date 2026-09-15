<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['ADMINISTRADOR', 'ARCHIVO_CENTRAL']) ||
            $user->can('documents.view.all') ||
            $user->can('documents.view.group') ||
            $user->can('documents.view.own');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        if ($user->hasRole(['ADMINISTRADOR', 'ARCHIVO_CENTRAL']) || $user->can('documents.view.all')) {
            return true;
        }

        if ($user->can('documents.view.group')) {
            if ($user->subgroup_id) {
                return $document->subgroup_id === $user->subgroup_id;
            }

            return $document->group_id === $user->group_id;
        }

        if ($user->can('documents.view.own')) {
            return $document->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['ADMINISTRADOR', 'ARCHIVO_CENTRAL']) || $user->can('documents.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): bool
    {
        if (! $user->hasRole(['ADMINISTRADOR', 'ARCHIVO_CENTRAL']) && ! $user->can('documents.update')) {
            return false;
        }

        if ($user->hasRole(['ADMINISTRADOR', 'ARCHIVO_CENTRAL'])) {
            return true;
        }

        if ($user->can('documents.view.all')) {
            return true;
        }

        if ($user->can('documents.view.group')) {
            if ($user->subgroup_id) {
                return $document->subgroup_id === $user->subgroup_id;
            }

            return $document->group_id === $user->group_id;
        }

        if ($user->can('documents.view.own')) {
            return $document->user_id === $user->id;
        }

        return $document->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        if (! $user->hasRole(['ADMINISTRADOR', 'ARCHIVO_CENTRAL']) && ! $user->can('documents.delete')) {
            return false;
        }

        if ($user->hasRole(['ADMINISTRADOR', 'ARCHIVO_CENTRAL'])) {
            return true;
        }

        if ($user->can('documents.view.all')) {
            return true;
        }

        if ($user->can('documents.view.group')) {
            if ($user->subgroup_id) {
                return $document->subgroup_id === $user->subgroup_id;
            }

            return $document->group_id === $user->group_id;
        }

        if ($user->can('documents.view.own')) {
            return $document->user_id === $user->id;
        }

        return $document->user_id === $user->id;
    }
}
