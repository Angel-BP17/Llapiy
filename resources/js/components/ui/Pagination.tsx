import React from 'react';
import { ChevronLeft, ChevronRight, Hash } from 'lucide-react';

interface PaginationProps {
  current_page: number;
  last_page: number;
  total: number;
  from?: number | null;
  to?: number | null;
  onPageChange: (page: number) => void;
  label?: string;
}

export default function Pagination({
  current_page = 1,
  last_page = 1,
  total = 0,
  from = 0,
  to = 0,
  onPageChange,
  label = "registros"
}: PaginationProps) {
  if (!total || total === 0) return null;

  return (
    <div className="flex flex-col sm:flex-row items-center justify-between rounded-2xl border border-border bg-card/50 backdrop-blur-sm px-6 py-4 text-sm shadow-sm gap-4 transition-all hover:bg-card hover:shadow-md">
      <div className="flex items-center gap-3 text-muted-foreground">
        <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary">
          <Hash className="h-4 w-4" />
        </div>
        <p className="font-medium">
          Mostrando <span className="font-extrabold text-foreground underline decoration-primary/30 decoration-2 underline-offset-4">
            {from === to ? from : `${from} - ${to}`}
          </span> de <span className="font-extrabold text-foreground">{total}</span> {label}
        </p>
      </div>
      
      <div className="flex items-center gap-3">
        <button
          type="button"
          disabled={current_page <= 1}
          onClick={() => onPageChange(current_page - 1)}
          className="inline-flex h-9 items-center gap-2 rounded-xl border border-border bg-background px-4 text-xs font-bold text-foreground transition-all hover:bg-muted hover:border-primary/20 disabled:opacity-40 disabled:cursor-not-allowed group"
        >
          <ChevronLeft className="h-4 w-4 transition-transform group-hover:-translate-x-0.5" />
          Anterior
        </button>
        
        <div className="flex items-center justify-center min-w-[100px] h-9 px-4 rounded-xl bg-muted/50 border border-border/50">
          <span className="text-[11px] uppercase tracking-tighter text-muted-foreground font-black">
            Pág. <span className="text-primary text-sm ml-1">{current_page}</span> <span className="mx-1 opacity-30">/</span> {last_page}
          </span>
        </div>

        <button
          type="button"
          disabled={current_page >= last_page}
          onClick={() => onPageChange(current_page + 1)}
          className="inline-flex h-9 items-center gap-2 rounded-xl border border-border bg-background px-4 text-xs font-bold text-foreground transition-all hover:bg-muted hover:border-primary/20 disabled:opacity-40 disabled:cursor-not-allowed group"
        >
          Siguiente
          <ChevronRight className="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
        </button>
      </div>
    </div>
  );
}
