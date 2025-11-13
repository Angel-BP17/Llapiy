document.addEventListener("DOMContentLoaded", function () {
    const dataRecientes = window.dashboardData.documentosRecientes || [];
    const dataPorTipo = window.dashboardData.documentosPorTipo || [];
    const dataPorMes = window.dashboardData.documentosPorMes || [];

    // Función para formatear fechas ISO a "DD/MM"
    function formatDate(dateString) {
        const date = new Date(dateString);
        const day = date.getDate().toString().padStart(2, "0");
        const month = (date.getMonth() + 1).toString().padStart(2, "0");
        return `${day}/${month}`;
    }

    // Gráfico de líneas: Documentos registrados últimos 17 días
    (function updateLineChart() {
        const labels = dataRecientes.map((item) => formatDate(item.fecha));
        const valores = dataRecientes.map((item) => item.cantidad);

        const ctx = document.getElementById("lineCahrt").getContext("2d");
        new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Documentos Registrados",
                        data: valores,
                        borderColor: "#f15765",
                        backgroundColor: "transparent",
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3, // curva suave
                        pointRadius: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                        },
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45,
                            font: { size: 12 },
                        },
                    },
                },
                plugins: {
                    legend: {
                        labels: { font: { size: 14 } },
                    },
                    tooltip: {
                        enabled: true,
                    },
                },
            },
        });
    })();

    // Gráfico de pastel: Porcentaje de documentos por tipo
    (function updatePieChart() {
        const labels = dataPorTipo.map(
            (item) => `${item.tipo} (${item.porcentaje}%)`
        );
        const valores = dataPorTipo.map((item) => item.porcentaje);

        const ctx = document.getElementById("pieChart").getContext("2d");
        new Chart(ctx, {
            type: "doughnut",
            data: {
                labels: labels,
                datasets: [
                    {
                        data: valores,
                        backgroundColor: [
                            "#44b2d7",
                            "#59c2e6",
                            "#71d1f2",
                            "#96e5ff",
                            "#aacdff",
                        ],
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: { font: { size: 12 } },
                    },
                },
            },
        });
    })();

    // Gráfico de barras: Cantidad de documentos por mes/año
    (function updateBarChart() {
        const meses = [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre",
        ];

        // Etiquetas con mes + año para más claridad
        const labels = dataPorMes.map(
            (item) => `${meses[item.mes - 1]} ${item.anio}`
        );
        const valores = dataPorMes.map((item) => item.cantidad);

        const ctx = document.getElementById("barChartHome").getContext("2d");
        new Chart(ctx, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Documentos por Mes",
                        data: valores,
                        backgroundColor: "rgb(121, 106, 238)",
                    },
                ],
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            font: { size: 14 },
                        },
                    },
                    x: {
                        ticks: {
                            font: { size: 14 },
                            maxRotation: 45,
                            minRotation: 45,
                        },
                    },
                },
                plugins: {
                    legend: {
                        labels: { font: { size: 16 } },
                    },
                },
            },
        });
    })();
});
