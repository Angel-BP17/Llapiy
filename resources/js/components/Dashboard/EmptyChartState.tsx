import React from 'react';
import { FileSearch } from 'lucide-react';

interface EmptyChartStateProps {
  message?: string;
  height?: string | number;
}

export default function EmptyChartState({ 
  message = "No hay datos registrados para este periodo", 
  height = "300px" 
}: EmptyChartStateProps) {
  return (
    <div 
      className="flex flex-col items-center justify-center w-full rounded-xl border border-dashed border-border bg-muted/10"
      style={{ height }}
    >
      <div className="flex h-12 w-12 items-center justify-center rounded-full bg-muted text-muted-foreground/50 mb-3">
        <FileSearch className="h-6 w-6" />
      </div>
      <p className="text-sm font-medium text-muted-foreground italic">
        {message}
      </p>
    </div>
  );
}
