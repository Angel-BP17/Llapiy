import React from 'react';
import { 
  BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer 
} from 'recharts';

import EmptyChartState from './EmptyChartState';

interface UserActivityChartProps {
  data: any[];
}

export default function UserActivityChart({ data }: UserActivityChartProps) {
  if (!data || data.length === 0) {
    return <EmptyChartState height={300} />;
  }

  return (
    <div className="h-[300px] w-full">
      <ResponsiveContainer width="100%" height="100%">
        <BarChart
          data={data}
          margin={{ top: 10, right: 10, left: -20, bottom: 0 }}
        >
          <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#e2e8f0" />
          <XAxis 
            dataKey="name" 
            tick={{ fontSize: 11, fill: '#64748b' }}
            axisLine={false}
            tickLine={false}
          />
          <YAxis 
            tick={{ fontSize: 11, fill: '#64748b' }}
            axisLine={false}
            tickLine={false}
          />
          <Tooltip 
            contentStyle={{ borderRadius: '8px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' }}
          />
          <Bar 
            dataKey="actividad" 
            fill="#f59e0b" 
            radius={[4, 4, 0, 0]} 
            barSize={30}
          />
        </BarChart>
      </ResponsiveContainer>
    </div>
  );
}
