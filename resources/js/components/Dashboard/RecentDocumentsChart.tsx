import React from "react";
import { 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  ResponsiveContainer,
  Area,
  AreaChart
} from "recharts";

import EmptyChartState from "./EmptyChartState";

const formatDateLabel = (dateStr: string) => {
  if (!dateStr) return "";
  try {
    const isoDate = dateStr.split("T")[0];
    const parts = isoDate.split("-");
    if (parts.length < 3) return dateStr;
    const [year, month, day] = parts;
    const monthNames = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
    return `${day} ${monthNames[parseInt(month) - 1]}`;
  } catch {
    return dateStr;
  }
};

export default function RecentDocumentsChart({ data = [] }: { data: any[] }) {
  if (!data || data.length === 0) {
    return <EmptyChartState height={300} />;
  }

  return (
    <div className="h-[300px] w-full">
      <ResponsiveContainer width="100%" height="100%">
        <AreaChart data={data} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
          <defs>
            <linearGradient id="colorDocs" x1="0" y1="0" x2="0" y2="1">
              <stop offset="5%" stopColor="#2563eb" stopOpacity={0.1}/>
              <stop offset="95%" stopColor="#2563eb" stopOpacity={0}/>
            </linearGradient>
          </defs>
          <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="hsl(var(--border))" />
          <XAxis 
            dataKey="fecha" 
            stroke="hsl(var(--muted-foreground))" 
            fontSize={11} 
            tickLine={false} 
            axisLine={false}
            tickFormatter={formatDateLabel}
            minTickGap={15}
          />
          <YAxis 
            stroke="hsl(var(--muted-foreground))" 
            fontSize={12} 
            tickLine={false} 
            axisLine={false} 
            allowDecimals={false}
          />
          <Tooltip 
            contentStyle={{ 
              backgroundColor: 'hsl(var(--card))', 
              borderColor: 'hsl(var(--border))',
              borderRadius: '8px',
              fontSize: '12px'
            }}
          />
          <Area 
            type="monotone" 
            dataKey="cantidad" 
            stroke="#2563eb" 
            fillOpacity={1} 
            fill="url(#colorDocs)" 
            strokeWidth={2}
          />
        </AreaChart>
      </ResponsiveContainer>
    </div>
  );
}
