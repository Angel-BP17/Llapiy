import React from "react";
import DashboardLayout from "@/Layouts/DashboardLayout";
import RecentDocumentsChart from "@/components/Dashboard/RecentDocumentsChart";
import DocumentTypesPieChart from "@/components/Dashboard/DocumentTypesPieChart";
import DashboardCharts from "@/components/Dashboard/DashboardCharts";
import DigitalizationFunnel from "@/components/Dashboard/DigitalizationFunnel";
import UserActivityChart from "@/components/Dashboard/UserActivityChart";
import { usePermissions } from "@/hooks/use-permissions";
import { Link } from "@inertiajs/react";

interface DashboardProps {
    stats: any;
    documentosRecientes: any[];
    documentosPorTipo: any[];
    docsByArea: any[];
    funnelData: any[];
    activityStats: any[];
}

export default function Dashboard({
    stats = {},
    documentosRecientes = [],
    documentosPorTipo = [],
    docsByArea = [],
    funnelData = [],
    activityStats = [],
}: DashboardProps) {
    const { can, isAdmin } = usePermissions();

    const statCards = [
        {
            label: "Usuarios registrados",
            value: stats?.userCount || 0,
            href: "/usuarios",
            permission: "users.view",
            bgColor: "bg-blue-500",
            textColor: "text-white",
            icon: (
                <svg
                    className="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                    ></path>
                </svg>
            ),
        },
        {
            label: "Archivos registrados",
            value: stats?.documentCount || 0,
            href: "/documentos",
            permission: ["documents.view.all", "documents.view.own", "documents.view.group"],
            bgColor: "bg-emerald-500",
            textColor: "text-white",
            icon: (
                <svg
                    className="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    ></path>
                </svg>
            ),
        },
        {
            label: "Bloques registrados",
            value: stats?.totalNoAlmacenados || 0,
            href: "/bloques",
            permission: ["blocks.view.all", "blocks.view.own", "blocks.view.group"],
            bgColor: "bg-amber-500",
            textColor: "text-white",
            icon: (
                <svg
                    className="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                    ></path>
                </svg>
            ),
        },
        {
            label: "Tipos de documentos",
            value: stats?.documentTypeCount || 0,
            href: "/tipos-documentos",
            permission: "document-types.view",
            bgColor: "bg-indigo-500",
            textColor: "text-white",
            icon: (
                <svg
                    className="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M7 7h.01M7 11h.01M7 15h.01M11 7h.01M11 11h.01M11 15h.01M15 7h.01M15 11h.01M15 15h.01"
                    ></path>
                </svg>
            ),
        },
    ];

    const visibleCards = statCards.filter((card) => {
        if (Array.isArray(card.permission)) {
            return card.permission.some(p => can(p));
        }
        return can(card.permission);
    });

    return (
        <DashboardLayout title="Dashboard">
            <div className="mb-8">
                <h1 className="text-3xl font-bold text-foreground">
                    Panel de Control
                </h1>
                <p className="text-muted-foreground mt-1">
                    Bienvenido al sistema de gestión documental Llapiy.
                </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
              {visibleCards.map((stat, idx) => (
                <div key={idx} className="bg-card border border-border rounded-xl p-5 shadow-sm">
                  <div className="flex items-center justify-between mb-4">
                    <div className={`flex p-2.5 rounded-lg ${stat.bgColor} text-white shadow-sm`}>
                      {stat.icon}
                    </div>
                    <div className="flex flex-col items-end">
                      <span className="text-2xl font-bold text-foreground">{stat.value}</span>
                    </div>
                  </div>
                        <div className="flex items-center justify-between">
                            <span className="text-sm font-medium text-muted-foreground">
                                {stat.label}
                            </span>
                            <Link
                                href={stat.href}
                                className="text-xs font-semibold text-primary hover:underline"
                            >
                                Ver detalle
                            </Link>
                        </div>
                    </div>
                ))}
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="bg-card border border-border rounded-xl p-5 shadow-sm lg:col-span-2 relative min-h-[400px]">
                    <div className="mb-6">
                        <h3 className="text-lg font-semibold text-foreground">
                            Documentos registrados
                        </h3>
                        <p className="text-sm text-muted-foreground">
                            Actividad reciente
                        </p>
                    </div>
                    <RecentDocumentsChart data={documentosRecientes} />
                </div>

                <div className="bg-card border border-border rounded-xl p-5 shadow-sm relative min-h-[400px]">
                    <div className="mb-6">
                        <h3 className="text-lg font-semibold text-foreground">
                            Tipos de documentos
                        </h3>
                        <p className="text-sm text-muted-foreground">
                            Distribución porcentual
                        </p>
                    </div>
                    <DocumentTypesPieChart data={documentosPorTipo} />
                </div>
            </div>

            {isAdmin && (
                <section className="mt-6">
                    <DashboardCharts data={docsByArea} />
                </section>
            )}

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <div className="bg-card border border-border rounded-xl p-5 shadow-sm">
                    <div className="mb-6">
                        <h3 className="text-lg font-semibold text-foreground">
                            Flujo de Digitalización
                        </h3>
                        <p className="text-sm text-muted-foreground">
                            Progreso de procesamiento documental
                        </p>
                    </div>
                    <DigitalizationFunnel data={funnelData} />
                </div>

                <div className="bg-card border border-border rounded-xl p-5 shadow-sm">
                    <div className="mb-6">
                        <h3 className="text-lg font-semibold text-foreground">
                            Actividad de Usuarios
                        </h3>
                        <p className="text-sm text-muted-foreground">
                            Top 5 usuarios más activos (Últimos 30 días)
                        </p>
                    </div>
                    <UserActivityChart data={activityStats} />
                </div>
            </div>
        </DashboardLayout>
    );
}
