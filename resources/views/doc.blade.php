<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LlapiyAPI - Documentacion API detallada</title>
    <style>
        :root {
            --bg: #f4f7fc;
            --panel: #ffffff;
            --line: #d9e2ef;
            --text: #182438;
            --muted: #5d6c86;
            --get: #0f766e;
            --post: #1d4ed8;
            --put: #7c3aed;
            --delete: #b91c1c;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            background: linear-gradient(180deg, #eaf0ff 0%, var(--bg) 24%);
        }

        .page {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .hero,
        .sidebar,
        .module,
        .footer-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 14px;
        }

        .hero {
            padding: 18px;
            margin-bottom: 14px;
        }

        .hero h1 {
            margin: 0 0 8px;
            font-size: 30px;
        }

        .hero p {
            margin: 5px 0;
            color: var(--muted);
        }

        .layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 14px;
            align-items: start;
        }

        .sidebar {
            position: sticky;
            top: 12px;
            padding: 14px;
        }

        .sidebar h2 {
            margin: 0 0 10px;
            font-size: 18px;
        }

        .sidebar a {
            display: block;
            text-decoration: none;
            color: #1e3a8a;
            padding: 7px 8px;
            border-radius: 8px;
            font-size: 14px;
        }

        .sidebar a:hover {
            background: #eef3ff;
        }

        .content {
            display: grid;
            gap: 14px;
        }

        .module {
            padding: 14px;
        }

        .module h2 {
            margin: 0 0 8px;
            font-size: 21px;
        }

        .module > p {
            margin: 0 0 10px;
            color: var(--muted);
        }

        .endpoint {
            border: 1px solid var(--line);
            border-radius: 11px;
            padding: 10px;
            margin-bottom: 10px;
            background: #fff;
        }

        .ep-head {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .method {
            display: inline-block;
            min-width: 62px;
            text-align: center;
            color: #fff;
            font-weight: 700;
            font-size: 12px;
            border-radius: 8px;
            padding: 4px 8px;
        }

        .get { background: var(--get); }
        .post { background: var(--post); }
        .put { background: var(--put); }
        .delete { background: var(--delete); }

        .path {
            font-family: Consolas, Monaco, "Courier New", monospace;
            font-size: 13px;
        }

        .hint {
            color: var(--muted);
            font-size: 13px;
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th,
        td {
            border: 1px solid var(--line);
            padding: 7px;
            text-align: left;
            vertical-align: top;
            font-size: 13px;
        }

        th {
            background: #f5f8ff;
        }

        code,
        pre {
            font-family: Consolas, Monaco, "Courier New", monospace;
            background: #f4f6fb;
            border: 1px solid var(--line);
            border-radius: 8px;
        }

        code { padding: 2px 6px; }

        pre {
            padding: 9px;
            overflow-x: auto;
            font-size: 12px;
        }

        .footer-card {
            padding: 14px;
            margin-top: 14px;
        }

        @media (max-width: 1024px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
            }
        }
    </style>
</head>
<body>
<div class="page">
    <section class="hero">
        <h1>LlapiyAPI - Documentacion detallada por modulo</h1>
        <p>Base API: <code>{{ url('/api') }}</code> | Documentacion: <code>{{ url('/doc') }}</code></p>
        <p>Autenticacion por Bearer Token (Sanctum). Excepto <code>POST /api/auth/login</code>, las rutas API requieren token.</p>
        <p>Formato comun: <code>{"message":"...","data":...}</code>. En validacion: <code>{"message":"...","errors":{...}}</code>.</p>
    </section>

    <div class="layout">
        <aside class="sidebar">
            <h2>Sidebar de modulos</h2>
            <a href="#mod-auth">1. Auth y sesion</a>
            <a href="#mod-dashboard">2. Dashboard y usuario</a>
            <a href="#mod-docs">3. Documentos</a>
            <a href="#mod-blocks">4. Bloques</a>
            <a href="#mod-types">5. Tipos y campos</a>
            <a href="#mod-org">6. Areas, grupos, subgrupos</a>
            <a href="#mod-storage">7. Almacenamiento</a>
            <a href="#mod-users">8. Usuarios, roles, permisos</a>
            <a href="#mod-inbox">9. Inbox y notificaciones</a>
            <a href="#mod-system">10. Sistema</a>
            <a href="#mod-pdf">11. Reportes PDF (web)</a>
            <a href="#mod-errors">12. Errores y ejemplos</a>
        </aside>

        <main class="content">
            <section id="mod-auth" class="module">
                <h2>1) Auth y sesion</h2>
                <div class="endpoint">
                    <div class="ep-head"><span class="method post">POST</span><span class="path">/api/auth/login</span></div>
                    <p class="hint">Inicia sesion y genera token Sanctum.</p>
                    <table>
                        <thead><tr><th>Campo</th><th>Tipo</th><th>Req.</th><th>Regla</th></tr></thead>
                        <tbody>
                        <tr><td>user_name</td><td>string</td><td>Si</td><td>required|string</td></tr>
                        <tr><td>password</td><td>string</td><td>Si</td><td>required|string</td></tr>
                        </tbody>
                    </table>
                    <pre>{
  "user_name": "admin",
  "password": "secret"
}</pre>
                </div>
                <div class="endpoint">
                    <div class="ep-head"><span class="method post">POST</span><span class="path">/api/auth/logout</span></div>
                    <p class="hint">Revoca el token actual del usuario autenticado.</p>
                </div>
            </section>

            <section id="mod-dashboard" class="module">
                <h2>2) Dashboard y usuario</h2>
                <div class="endpoint">
                    <div class="ep-head"><span class="method get">GET</span><span class="path">/api/user</span></div>
                    <p class="hint">Retorna el usuario autenticado en <code>data</code>.</p>
                </div>
                <div class="endpoint">
                    <div class="ep-head"><span class="method get">GET</span><span class="path">/api/dashboard</span></div>
                    <p class="hint">Retorna metricas agregadas: conteos, series por fecha y distribucion por tipo.</p>
                </div>
            </section>

            <section id="mod-docs" class="module">
                <h2>3) Documentos</h2>
                <p>Permisos: <code>documents.view/create/update/upload/delete</code>.</p>
                <div class="endpoint">
                    <div class="ep-head"><span class="method get">GET</span><span class="path">/api/documents</span></div>
                    <p class="hint">Lista paginada + datos auxiliares para filtros y formularios.</p>
                    <table>
                        <thead><tr><th>Query param</th><th>Regla</th></tr></thead>
                        <tbody>
                        <tr><td>asunto</td><td>nullable|string|max:255</td></tr>
                        <tr><td>document_type_id</td><td>nullable|exists:document_types,id</td></tr>
                        <tr><td>area_id / group_id / subgroup_id</td><td>nullable|exists:tabla,id</td></tr>
                        <tr><td>role_id / year / month</td><td>nullable (month 1..12)</td></tr>
                        <tr><td>document_type_scope</td><td>nullable|string|max:100</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="endpoint">
                    <div class="ep-head"><span class="method post">POST</span><span class="path">/api/documents</span></div>
                    <p class="hint">Crea documento. Si incluye <code>root</code>, enviar multipart/form-data.</p>
                    <table>
                        <thead><tr><th>Campo</th><th>Regla principal</th></tr></thead>
                        <tbody>
                        <tr><td>n_documento</td><td>required|string, unico por periodo</td></tr>
                        <tr><td>asunto, folios, document_type_id, fecha</td><td>required</td></tr>
                        <tr><td>root</td><td>nullable|file|pdf|max:15MB</td></tr>
                        <tr><td>campos[]</td><td>array opcional de <code>{id,dato}</code> con validacion dinamica por tipo de campo</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/documents/{document}</span></div><p class="hint">Actualiza metadata/campos. <code>root</code> solo si tiene permiso upload.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/documents/{document}/upload</span></div><p class="hint">Sube/reemplaza PDF: <code>root required|pdf|max:15MB</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method delete">DELETE</span><span class="path">/api/documents/{document}</span></div><p class="hint">Elimina documento y archivo.</p></div>
            </section>

            <section id="mod-blocks" class="module">
                <h2>4) Bloques</h2>
                <p>Permisos: <code>blocks.view/create/update/upload/delete</code>.</p>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/blocks</span></div><p class="hint">Listado paginado con filtros <code>asunto, area_id, group_id, subgroup_id, role_id, year, month</code>.</p></div>
                <div class="endpoint">
                    <div class="ep-head"><span class="method post">POST</span><span class="path">/api/blocks</span></div>
                    <table>
                        <thead><tr><th>Campo</th><th>Regla</th></tr></thead>
                        <tbody>
                        <tr><td>n_bloque</td><td>required|string, unico por periodo</td></tr>
                        <tr><td>fecha, asunto, folios, rango_inicial, rango_final</td><td>required</td></tr>
                        <tr><td>root</td><td>nullable|file|pdf|max:50MB</td></tr>
                        </tbody>
                    </table>
                    <p class="hint">Dispara notificaciones a usuarios con permiso <code>notifications.receive</code>.</p>
                </div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/blocks/{block}</span></div><p class="hint">Actualiza bloque.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/blocks/{block}/upload</span></div><p class="hint">Sube PDF del bloque (<code>root</code> requerido).</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method delete">DELETE</span><span class="path">/api/blocks/{block}</span></div><p class="hint">Elimina bloque y archivo.</p></div>
            </section>

            <section id="mod-types" class="module">
                <h2>5) Tipos de documento y campos</h2>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/document_types</span></div><p class="hint">Filtros: <code>name, area_id, group_id, subgroup_id</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method post">POST</span><span class="path">/api/document_types</span></div><p class="hint">Body: <code>name</code> req, <code>campos/groups/subgroups</code> como string JSON.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/document_types/{document_type}</span></div><p class="hint">Actualiza nombre y relaciones.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method delete">DELETE</span><span class="path">/api/document_types/{document_type}</span></div><p class="hint">Elimina tipo y pivots.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/campos</span></div><p class="hint">Query: <code>search</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method post">POST</span><span class="path">/api/campos</span></div><p class="hint">Campo: <code>name</code> req. Si <code>data_type=enum</code>, <code>enum_values</code> es requerido.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/campos/{campo}</span></div><p class="hint">Mismas reglas de creacion con unicidad por id.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method delete">DELETE</span><span class="path">/api/campos/{campo}</span></div><p class="hint">Elimina tipo de campo.</p></div>
            </section>

            <section id="mod-org" class="module">
                <h2>6) Areas, grupos y subgrupos</h2>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/areas</span></div><p class="hint">Lista areas.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method post">POST</span><span class="path">/api/areas</span></div><p class="hint">Body: <code>descripcion</code> req, <code>abreviacion</code> opc, <code>grupos[]</code> opcional (anidado).</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/areas/{area}</span></div><p class="hint">Detalle area.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/areas/{area}</span></div><p class="hint">Actualiza area y estructura anidada.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method delete">DELETE</span><span class="path">/api/areas/{area}</span></div><p class="hint">Elimina area.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method post">POST</span><span class="path">/api/groups</span></div><p class="hint">Body: <code>area_id</code>, <code>group_type_id</code> req, <code>descripcion</code> opc.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/groups/{group}</span></div><p class="hint">Obtiene grupo.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/groups/{group}</span></div><p class="hint">Campos: descripcion req, abreviacion opc.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method delete">DELETE</span><span class="path">/api/groups/{group}</span></div><p class="hint">Elimina grupo.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method post">POST</span><span class="path">/api/subgroups</span></div><p class="hint">Body: <code>group_id</code> req, <code>descripcion/abreviacion/parent_subgroup_id</code> opc.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/subgroups/{subgroup}</span></div><p class="hint">Obtiene subgrupo.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/subgroups/{subgroup}</span></div><p class="hint">Actualiza subgrupo.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method delete">DELETE</span><span class="path">/api/subgroups/{subgroup}</span></div><p class="hint">Elimina subgrupo.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/group_types</span></div><p class="hint">Query: <code>search</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method post">POST</span><span class="path">/api/group_types</span></div><p class="hint">Body: <code>descripcion</code> req, <code>abreviacion</code> opc.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/group_types/{group_type}</span></div><p class="hint">Actualiza tipo.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method delete">DELETE</span><span class="path">/api/group_types/{group_type}</span></div><p class="hint">Si tiene grupos asociados retorna 422.</p></div>
            </section>

            <section id="mod-storage" class="module">
                <h2>7) Almacenamiento (secciones, andamios, cajas, archivos)</h2>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/sections</span></div><p class="hint">Query: <code>search</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method post">POST</span><span class="path">/api/sections</span></div><p class="hint">Campos req: <code>n_section</code> unico, <code>descripcion</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/sections/{section}</span></div><p class="hint">Actualiza seccion.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method delete">DELETE</span><span class="path">/api/sections/{section}</span></div><p class="hint">Elimina seccion.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/sections/{section}/andamios</span></div><p class="hint">Query: <code>search</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method post">POST</span><span class="path">/api/sections/{section}/andamios</span></div><p class="hint">Campos req: <code>n_andamio</code> unico por seccion, <code>descripcion</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/sections/{section}/andamios/{andamio}</span></div><p class="hint">Actualiza andamio.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method delete">DELETE</span><span class="path">/api/sections/{section}/andamios/{andamio}</span></div><p class="hint">Si tiene cajas => 422.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/sections/{section}/andamios/{andamio}/boxes</span></div><p class="hint">Query: <code>search</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method post">POST</span><span class="path">/api/sections/{section}/andamios/{andamio}/boxes</span></div><p class="hint">Campo req: <code>n_box</code> unico por andamio.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/sections/{section}/andamios/{andamio}/boxes/{box}</span></div><p class="hint">Actualiza caja.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method delete">DELETE</span><span class="path">/api/sections/{section}/andamios/{andamio}/boxes/{box}</span></div><p class="hint">Si tiene bloques => 422.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/sections/{section}/andamios/{andamio}/boxes/{box}/archivos</span></div><p class="hint">Lista bloques de la caja. Query: <code>search</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method post">POST</span><span class="path">/api/sections/{section}/andamios/{andamio}/boxes/{box}/archivos/{block}/move</span></div><p class="hint">Mueve bloque a default. Si no pertenece a caja => 422.</p></div>
            </section>

            <section id="mod-users" class="module">
                <h2>8) Usuarios, roles y permisos</h2>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/users</span></div><p class="hint">Query opcional: <code>search</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method post">POST</span><span class="path">/api/users</span></div><p class="hint">Campos req: <code>name,last_name,user_name,dni,password</code>. <code>group_id</code> es requerido salvo rol ADMINISTRADOR.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/users/{user}</span></div><p class="hint">En update se usan <code>group</code> y <code>subgroup</code> para asignaciones.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method delete">DELETE</span><span class="path">/api/users/{user}</span></div><p class="hint">Elimina usuario.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/roles</span></div><p class="hint">Listado roles. Query: <code>search</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method post">POST</span><span class="path">/api/roles</span></div><p class="hint">Body: <code>name</code> req, <code>permissions[]</code> opcional.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/roles/{role}</span></div><p class="hint">Actualiza nombre/permisos.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method delete">DELETE</span><span class="path">/api/roles/{role}</span></div><p class="hint">Elimina rol.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/roles/{role}/permissions</span></div><p class="hint">Matriz de permisos seleccionados.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/roles/{role}/permissions</span></div><p class="hint">Body: <code>permissions[]</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method post">POST</span><span class="path">(Controlador de permisos API, sin ruta expuesta actual)</span></div><p class="hint">El controlador de permisos ya responde JSON, pero no hay endpoint registrado en <code>routes/api.php</code>.</p></div>
            </section>

            <section id="mod-inbox" class="module">
                <h2>9) Inbox, notificaciones y bitacora</h2>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/inbox</span></div><p class="hint">Filtros: <code>search, area_id, fecha</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method put">PUT</span><span class="path">/api/inbox/update-storage/{id}</span></div><p class="hint">Body req: <code>n_box</code> (int), <code>n_andamio</code>, <code>n_section</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/notifications</span></div><p class="hint">Listado de notificaciones del usuario.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/notifications/{notification}</span></div><p class="hint">Marca como leida. Si no es propietario => 403.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/activity-logs</span></div><p class="hint">Filtros de servicio: <code>date</code>, <code>user_id</code>, <code>module</code>.</p></div>
            </section>

            <section id="mod-system" class="module">
                <h2>10) Sistema</h2>
                <div class="endpoint"><div class="ep-head"><span class="method delete">DELETE</span><span class="path">/api/admin/clear-all</span></div><p class="hint">Requiere middleware <code>can:clear-system</code>.</p></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/api/storage-link</span></div><p class="hint">Ejecuta <code>storage:link</code> y devuelve estado.</p></div>
            </section>

            <section id="mod-pdf" class="module">
                <h2>11) Reportes PDF (rutas web)</h2>
                <p class="hint">Fuera de <code>/api</code>, pero protegidas por <code>auth:sanctum</code>.</p>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/documents/pdf</span></div></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/blocks/pdf</span></div></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/users/pdf</span></div></div>
                <div class="endpoint"><div class="ep-head"><span class="method get">GET</span><span class="path">/activity-logs/pdf</span></div></div>
            </section>

            <section id="mod-errors" class="module">
                <h2>12) Errores y ejemplos</h2>
                <table>
                    <thead><tr><th>Codigo</th><th>Significado</th><th>Que revisar</th></tr></thead>
                    <tbody>
                    <tr><td>401</td><td>Token invalido o faltante</td><td>Header Authorization</td></tr>
                    <tr><td>403</td><td>No autorizado por permisos/politicas</td><td>Rol y permisos del usuario</td></tr>
                    <tr><td>405</td><td>Metodo no permitido</td><td>Ver endpoint correcto en esta guia</td></tr>
                    <tr><td>422</td><td>Error de validacion</td><td>Campo y reglas en <code>errors</code></td></tr>
                    <tr><td>500</td><td>Error interno</td><td>Logs de Laravel</td></tr>
                    </tbody>
                </table>
                <pre>{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["El campo name es obligatorio."]
  }
}</pre>
            </section>
        </main>
    </div>

    <section class="footer-card">
        <p class="muted">Ultima actualizacion de esta pagina: {{ now()->format('Y-m-d H:i:s') }}</p>
    </section>
</div>
</body>
</html>
