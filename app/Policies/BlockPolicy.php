<?php

namespace App\Policies;

use App\Models\Block;
use App\Models\User;

class BlockPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['ADMINISTRADOR', 'ARCHIVO_CENTRAL']) ||
            $user->can('blocks.view.all') ||
            $user->can('blocks.view.group') ||
            $user->can('blocks.view.own');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Block $block): bool
    {
        if ($user->hasRole(['ADMINISTRADOR', 'ARCHIVO_CENTRAL']) || $user->can('blocks.view.all')) {
            return true;
        }

        if ($user->can('blocks.view.group')) {
            if ($user->subgroup_id) {
                return $block->subgroup_id === $user->subgroup_id;
            }

            return $block->group_id === $user->group_id;
        }

        if ($user->can('blocks.view.own')) {
            return $block->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['ADMINISTRADOR', 'ARCHIVO_CENTRAL']) || $user->can('blocks.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Block $block): bool
    {
        if (! $user->hasRole(['ADMINISTRADOR', 'ARCHIVO_CENTRAL']) && ! $user->can('blocks.update')) {
            return false;
        }

        if ($user->hasRole(['ADMINISTRADOR', 'ARCHIVO_CENTRAL'])) {
            return true;
        }

        // Si ya tiene PDF ingresado por Archivo Central, el usuario común no puede editar
        if (! empty($block->root)) {
            return false;
        }

        if ($user->can('blocks.view.all')) {
            return true;
        }

        if ($user->can('blocks.view.group')) {
            if ($user->subgroup_id) {
                return $block->subgroup_id === $user->subgroup_id;
            }

            return $block->group_id === $user->group_id;
        }

        if ($user->can('blocks.view.own')) {
            return $block->user_id === $user->id;
        }

        return $block->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Block $block): bool
    {
        if (! $user->hasRole(['ADMINISTRADOR', 'ARCHIVO_CENTRAL']) && ! $user->can('blocks.delete')) {
            return false;
        }

        if ($user->hasRole(['ADMINISTRADOR', 'ARCHIVO_CENTRAL'])) {
            return true;
        }

        // Si ya tiene PDF ingresado por Archivo Central, el usuario común no puede eliminar
        if (! empty($block->root)) {
            return false;
        }

        if ($user->can('blocks.view.all')) {
            return true;
        }

        if ($user->can('blocks.view.group')) {
            if ($user->subgroup_id) {
                return $block->subgroup_id === $user->subgroup_id;
            }

            return $block->group_id === $user->group_id;
        }

        if ($user->can('blocks.view.own')) {
            return $block->user_id === $user->id;
        }

        return $block->user_id === $user->id;
    }
}
