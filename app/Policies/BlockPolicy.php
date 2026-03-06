<?php

namespace App\Policies;

use App\Models\Block;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BlockPolicy
{
    /**
     * Filtro previo: El ADMINISTRADOR siempre puede hacer todo.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('ADMINISTRADOR')) {
            return true;
        }

        return null;
    }

    /**
     * Determina si el usuario puede ver el listado.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('blocks.view.all') || 
               $user->can('blocks.view.group') || 
               $user->can('blocks.view.own');
    }

    /**
     * Determina si el usuario puede ver un bloque específico.
     */
    public function view(User $user, Block $block): bool
    {
        if ($user->can('blocks.view.all')) {
            return true;
        }

        if ($user->can('blocks.view.own') && $user->id === $block->user_id) {
            return true;
        }

        if ($user->can('blocks.view.group')) {
            if ($user->subgroup_id) {
                return (int)$block->subgroup_id === (int)$user->subgroup_id;
            }
            return (int)$block->group_id === (int)$user->group_id;
        }

        return false;
    }

    /**
     * Determina si el usuario puede crear bloques.
     */
    public function create(User $user): bool
    {
        return $user->can('blocks.create');
    }

    /**
     * Determina si el usuario puede actualizar el bloque.
     */
    public function update(User $user, Block $block): bool
    {
        return $user->group_id === $block->group_id;
    }

    /**
     * Determina si el usuario puede eliminar el bloque.
     */
    public function delete(User $user, Block $block): bool
    {
        return $user->can('blocks.delete') && $user->group_id === $block->group_id;
    }
}
