@extends('layouts.app')

@section('title', 'Permisos del rol')

@section('content')
    @php
        $roleNameLabels = [
            'ADMINISTRADOR' => 'Administrador',
            'admin' => 'Administrador',
        ];
    @endphp

    <section class="forms module-ui">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="no-margin-bottom">Permisos del rol: {{ $roleNameLabels[$role->name] ?? $role->name }}</h2>
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
            </div>

            <form action="{{ route('roles.permissions.update', $role) }}" method="POST" id="role-permissions-form">
                @csrf
                @method('PUT')

                <div class="card module-filter-card bg-white has-shadow mb-3">
                    <div class="card-body d-flex flex-wrap align-items-center gap-2">
                        <input type="text" class="form-control form-control-sm" id="permissions-search"
                            placeholder="Buscar modulo o permiso..." style="max-width: 320px;">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="permissions-expand-all">Expandir todo</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="permissions-collapse-all">Contraer todo</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="permissions-select-all">Seleccionar todo</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="permissions-clear-all">Limpiar</button>
                    </div>
                </div>

                <div class="permissions-grid" id="permissions-groups">
                    @foreach ($permissionGroups as $group)
                        @php
                            $moduleName = $group['module'] ?? null;
                        @endphp
                        <div class="permission-card permission-group">
                            <div class="permission-header-wrap">
                                <label class="permission-header">
                                    @if ($moduleName)
                                        <input class="form-check-input permission-module-checkbox" type="checkbox"
                                            name="permissions[]" value="{{ $moduleName }}"
                                            @checked(in_array($moduleName, $selectedPermissions, true))>
                                    @endif
                                    <span class="permission-module-label">{{ $permissionLabels[$moduleName] ?? $moduleName ?? 'Modulo' }}</span>
                                </label>
                                <button type="button" class="btn btn-link btn-sm permission-toggle-btn">Contraer</button>
                            </div>
                            <div class="permission-items">
                                @forelse ($group['permissions'] as $permission)
                                    <label class="permission-item">
                                        <input class="form-check-input permission-action-checkbox" type="checkbox"
                                            name="permissions[]" value="{{ $permission }}"
                                            @checked(in_array($permission, $selectedPermissions, true))>
                                        <span class="permission-action-label">{{ $permissionLabels[$permission] ?? $permission }}</span>
                                    </label>
                                @empty
                                    <small class="text-muted">Sin permisos adicionales.</small>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Guardar permisos</button>
                </div>
            </form>
        </div>
    </section>

    <style>
        .permissions-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        @media (min-width: 992px) {
            .permissions-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .permission-card {
            border: 1px solid #d7dde5;
            border-radius: 6px;
            padding: 10px;
            background: #fff;
        }

        .permission-header-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
        }

        .permission-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            font-weight: 500;
            line-height: 1.2;
        }

        .permission-items {
            display: grid;
            grid-template-columns: 1fr;
            gap: 6px;
            border-top: 1px solid #eef2f7;
            padding-top: 8px;
        }

        .permission-items.is-collapsed {
            display: none;
        }

        .permission-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            padding: 4px 0;
            border-bottom: 1px dashed #edf2f7;
        }

        .permission-item:last-child {
            border-bottom: 0;
        }

        .permission-highlight {
            background: #fef08a;
            color: inherit;
            padding: 0 1px;
            border-radius: 2px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const escapeRegExp = (value) => value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const groups = Array.from(document.querySelectorAll('#permissions-groups .permission-group'));
            const search = document.getElementById('permissions-search');
            const btnExpand = document.getElementById('permissions-expand-all');
            const btnCollapse = document.getElementById('permissions-collapse-all');
            const btnSelectAll = document.getElementById('permissions-select-all');
            const btnClearAll = document.getElementById('permissions-clear-all');

            const highlightNode = (element, text, query) => {
                const source = String(text ?? '');
                const q = String(query ?? '').trim();
                element.textContent = '';

                if (!q) {
                    element.textContent = source;
                    return;
                }

                const regex = new RegExp(`(${escapeRegExp(q)})`, 'ig');
                source.split(regex).forEach((part) => {
                    if (!part) return;
                    if (part.toLowerCase() === q.toLowerCase()) {
                        const mark = document.createElement('mark');
                        mark.className = 'permission-highlight';
                        mark.textContent = part;
                        element.appendChild(mark);
                    } else {
                        element.appendChild(document.createTextNode(part));
                    }
                });
            };

            const syncGroupState = (group) => {
                const moduleCheckbox = group.querySelector('.permission-module-checkbox');
                const actionCheckboxes = Array.from(group.querySelectorAll('.permission-action-checkbox'));
                if (!moduleCheckbox || actionCheckboxes.length === 0) return;

                const checkedCount = actionCheckboxes.filter((checkbox) => checkbox.checked).length;
                if (checkedCount === 0) {
                    moduleCheckbox.checked = false;
                    moduleCheckbox.indeterminate = false;
                } else if (checkedCount === actionCheckboxes.length) {
                    moduleCheckbox.checked = true;
                    moduleCheckbox.indeterminate = false;
                } else {
                    moduleCheckbox.checked = false;
                    moduleCheckbox.indeterminate = true;
                }
            };

            const setCollapsed = (group, collapsed) => {
                const items = group.querySelector('.permission-items');
                const toggle = group.querySelector('.permission-toggle-btn');
                if (!items || !toggle) return;
                items.classList.toggle('is-collapsed', collapsed);
                toggle.textContent = collapsed ? 'Expandir' : 'Contraer';
            };

            groups.forEach((group) => {
                const moduleCheckbox = group.querySelector('.permission-module-checkbox');
                const actionCheckboxes = Array.from(group.querySelectorAll('.permission-action-checkbox'));
                const toggle = group.querySelector('.permission-toggle-btn');

                if (moduleCheckbox) {
                    moduleCheckbox.addEventListener('change', () => {
                        actionCheckboxes.forEach((checkbox) => {
                            checkbox.checked = moduleCheckbox.checked;
                        });
                        moduleCheckbox.indeterminate = false;
                    });
                }

                actionCheckboxes.forEach((checkbox) => {
                    checkbox.addEventListener('change', () => syncGroupState(group));
                });

                if (toggle) {
                    toggle.addEventListener('click', () => {
                        const items = group.querySelector('.permission-items');
                        if (!items) return;
                        setCollapsed(group, !items.classList.contains('is-collapsed'));
                    });
                }

                syncGroupState(group);
            });

            btnExpand?.addEventListener('click', () => groups.forEach((group) => setCollapsed(group, false)));
            btnCollapse?.addEventListener('click', () => groups.forEach((group) => setCollapsed(group, true)));

            btnSelectAll?.addEventListener('click', () => {
                document.querySelectorAll('#role-permissions-form input[name="permissions[]"]').forEach((checkbox) => {
                    checkbox.checked = true;
                });
                groups.forEach(syncGroupState);
            });

            btnClearAll?.addEventListener('click', () => {
                document.querySelectorAll('#role-permissions-form input[name="permissions[]"]').forEach((checkbox) => {
                    checkbox.checked = false;
                });
                groups.forEach(syncGroupState);
            });

            search?.addEventListener('input', () => {
                const query = search.value.trim().toLowerCase();

                groups.forEach((group) => {
                    const moduleLabelEl = group.querySelector('.permission-module-label');
                    const actionLabelEls = Array.from(group.querySelectorAll('.permission-action-label'));

                    const moduleText = moduleLabelEl?.textContent?.trim() || '';
                    const actionTexts = actionLabelEls.map((el) => el.textContent.trim());

                    const matches =
                        !query ||
                        moduleText.toLowerCase().includes(query) ||
                        actionTexts.some((text) => text.toLowerCase().includes(query));

                    group.style.display = matches ? '' : 'none';

                    if (moduleLabelEl) {
                        highlightNode(moduleLabelEl, moduleText, query);
                    }
                    actionLabelEls.forEach((labelEl, idx) => {
                        highlightNode(labelEl, actionTexts[idx], query);
                    });
                });
            });
        });
    </script>
@endsection
