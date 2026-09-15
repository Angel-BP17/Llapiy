import React, { useEffect, useState } from 'react';
import { CheckCircle2, AlertCircle, Info, X } from 'lucide-react';

export interface ToastProps {
  message: string | null;
  error?: string | null;
  duration?: number;
  onClose?: () => void;
}

export function Toast({ message, error, duration = 4000, onClose }: ToastProps) {
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    if (message || error) {
      setVisible(true);
      const timer = setTimeout(() => {
        setVisible(false);
        if (onClose) onClose();
      }, duration);
      return () => clearTimeout(timer);
    } else {
      setVisible(false);
    }
  }, [message, error, duration, onClose]);

  if (!visible || (!message && !error)) return null;

  const isError = Boolean(error);

  return (
    <div
      className={`fixed bottom-5 right-5 z-50 flex items-center gap-3 rounded-xl border px-4 py-3.5 shadow-2xl backdrop-blur-md transition-all duration-300 animate-in fade-in slide-in-from-bottom-5 ${
        isError
          ? 'border-red-200 bg-red-50/95 text-red-900 dark:border-red-900/40 dark:bg-red-950/90 dark:text-red-200'
          : 'border-emerald-200 bg-emerald-50/95 text-emerald-900 dark:border-emerald-900/40 dark:bg-emerald-950/90 dark:text-emerald-200'
      }`}
    >
      {isError ? (
        <AlertCircle className="h-5 w-5 shrink-0 text-red-600 dark:text-red-400" />
      ) : (
        <CheckCircle2 className="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
      )}
      <div className="text-xs font-semibold max-w-sm">
        {error || message}
      </div>
      <button
        type="button"
        onClick={() => {
          setVisible(false);
          if (onClose) onClose();
        }}
        className="rounded-lg p-1 transition-colors hover:bg-black/5 dark:hover:bg-white/10"
      >
        <X className="h-3.5 w-3.5 opacity-70" />
      </button>
    </div>
  );
}
