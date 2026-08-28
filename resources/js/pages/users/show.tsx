import React from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Head, Link } from '@inertiajs/react';
import { UserRecord, defaultAvatar } from '@/components/Users/UserTable';
import { ArrowLeft, Mail, Fingerprint, Shield, Building2 } from 'lucide-react';
import usersRoutes from '@/routes/users';

interface ShowProps {
  user: any;
}

export default function Show({ user }: ShowProps) {
  return (
    <DashboardLayout title={`Usuario: ${user.name}`}>
      <Head title={`Usuario: ${user.name}`} />
      
      <div className="space-y-6">
        <div className="flex items-center gap-4">
          <Link 
            href={usersRoutes.index.url()} 
            className="flex h-10 w-10 items-center justify-center rounded-full border border-border bg-card text-muted-foreground transition hover:text-foreground"
          >
            <ArrowLeft className="h-5 w-5" />
          </Link>
          <div>
            <h2 className="text-2xl font-bold text-foreground">Detalle de Usuario</h2>
            <p className="text-sm text-muted-foreground">Información completa de la cuenta y asignaciones.</p>
          </div>
        </div>

        <div className="grid gap-6 lg:grid-cols-3">
          <div className="space-y-6 lg:col-span-1">
            <div className="rounded-2xl border border-border bg-card p-6 shadow-sm">
              <div className="flex flex-col items-center text-center">
                <img 
                  src={user.foto_perfil || defaultAvatar} 
                  className="h-32 w-32 rounded-full border-4 border-primary/10 object-cover shadow-md"
                  alt={user.name}
                />
                <h3 className="mt-4 text-xl font-bold text-foreground">{user.name} {user.last_name}</h3>
                <p className="text-sm font-medium text-muted-foreground">@{user.user_name}</p>
                
                <div className="mt-6 flex flex-wrap justify-center gap-2">
                  {user.roles?.map((role: any) => (
                    <span key={role.id} className="rounded-full bg-primary/10 px-3 py-1 text-xs font-bold text-primary uppercase">
                      {role.name}
                    </span>
                  ))}
                </div>
              </div>
            </div>
          </div>

          <div className="space-y-6 lg:col-span-2">
            <div className="rounded-2xl border border-border bg-card p-6 shadow-sm">
              <h4 className="mb-4 flex items-center gap-2 text-lg font-semibold text-foreground">
                <Shield className="h-5 w-5 text-primary" /> Información Personal
              </h4>
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="rounded-xl border border-border bg-muted/30 p-4">
                  <p className="text-xs font-bold uppercase tracking-wider text-muted-foreground">Correo Electrónico</p>
                  <div className="mt-1 flex items-center gap-2 text-foreground font-medium">
                    <Mail className="h-4 w-4 opacity-70" /> {user.email}
                  </div>
                </div>
                <div className="rounded-xl border border-border bg-muted/30 p-4">
                  <p className="text-xs font-bold uppercase tracking-wider text-muted-foreground">DNI / Identificación</p>
                  <div className="mt-1 flex items-center gap-2 text-foreground font-medium">
                    <Fingerprint className="h-4 w-4 opacity-70" /> {user.dni || "No registrado"}
                  </div>
                </div>
              </div>
            </div>

            <div className="rounded-2xl border border-border bg-card p-6 shadow-sm">
              <h4 className="mb-4 flex items-center gap-2 text-lg font-semibold text-foreground">
                <Building2 className="h-5 w-5 text-primary" /> Organización
              </h4>
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="rounded-xl border border-border bg-muted/30 p-4">
                  <p className="text-xs font-bold uppercase tracking-wider text-muted-foreground">Área</p>
                  <p className="mt-1 text-foreground font-medium">{user.group?.area_group_type?.area?.descripcion || "Sin área"}</p>
                </div>
                <div className="rounded-xl border border-border bg-muted/30 p-4">
                  <p className="text-xs font-bold uppercase tracking-wider text-muted-foreground">Grupo</p>
                  <p className="mt-1 text-foreground font-medium">{user.group?.descripcion || "Sin grupo"}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </DashboardLayout>
  );
}
