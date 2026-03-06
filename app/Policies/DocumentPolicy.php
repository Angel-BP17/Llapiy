<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
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
        return $user->can('documents.view.all') || 
               $user->can('documents.view.group') || 
               $user->can('documents.view.own');
    }

    /**
     * Determina si el usuario puede ver un documento específico.
     */
    public function view(User $user, Document $document): bool
    {
        if ($user->can('documents.view.all')) {
            return true;
        }

        if ($user->can('documents.view.own') && $user->id === $document->user_id) {
            return true;
        }

        if ($user->can('documents.view.group')) {
            if ($user->subgroup_id) {
                return (int)$document->subgroup_id === (int)$user->subgroup_id;
            }
            return (int)$document->group_id === (int)$user->group_id;
        }

        return false;
    }

    /**
     * Determina si el usuario puede crear documentos (ya cubierto por middleware can:documents.create).
     */
    public function create(User $user): bool
    {
        return $user->can('documents.create');
    }

    /**
     * Determina si el usuario puede actualizar el documento.
     * Regla: Debe pertenecer a su mismo grupo.
     */
    public function update(User $user, Document $document): bool
    {
        return $user->group_id === $document->group_id;
    }

    /**
     * Determina si el usuario puede eliminar el documento.
     */
    public function delete(User $user, Document $document): bool
    {
        // En este sistema, solo el ADMIN puede borrar (manejado por middleware can:documents.delete)
        // Pero si quisiéramos permitir al usuario borrar sus propios documentos:
        // return $user->id === $document->user_id;
        return $user->can('documents.delete') && $user->group_id === $document->group_id;
    }
}
