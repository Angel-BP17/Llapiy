import React, { useEffect, useState, useRef } from "react";
import { Bell, BellOff, Check, Clock, ExternalLink } from "lucide-react";
import axios from "axios";
import { Link, usePage } from "@inertiajs/react";
import { api, read } from "@/routes/notifications";

export type NotificationData = {
  id: string;
  type: string;
  data: {
    message: string;
    action_url?: string;
    [key: string]: any;
  };
  read_at: string | null;
  created_at: string;
};

export default function NotificationsDropdown() {
  const { app_url } = usePage().props as any;
  const [isMounted, setIsMounted] = useState(false);
  const [notifications, setNotifications] = useState<NotificationData[]>([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const [isOpen, setIsOpen] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const dropdownRef = useRef<HTMLDivElement>(null);

  const prefixUrl = (path: string) => {
    const cleanAppUrl = app_url ? app_url.replace(/\/+$/, "") : "";
    return `${cleanAppUrl}${path}`;
  };

  const formatActionUrl = (url: string) => {
    if (!url) {
      return "";
    }
    if (url.startsWith("http://") || url.startsWith("https://")) {
      return url;
    }
    return prefixUrl(url.startsWith("/") ? url : `/${url}`);
  };

  const loadNotifications = async () => {
    try {
      const response = await axios.get(prefixUrl(api.url()));
      const list = response.data || [];
      setNotifications(list);
      setUnreadCount(list.length);
    } catch (error) {
      console.error("[Notifications] Error loading:", error);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    setIsMounted(true);
    void loadNotifications();
    const interval = setInterval(loadNotifications, 5000);
    return () => clearInterval(interval);
  }, [app_url]);

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
        setIsOpen(false);
      }
    };
    if (isOpen) {
      document.addEventListener("mousedown", handleClickOutside);
    }
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, [isOpen]);

  const handleMarkAsRead = async (id: string) => {
    try {
      await axios.get(prefixUrl(read.url(id)));
      setNotifications(prev => prev.map(n => n.id === id ? { ...n, read_at: new Date().toISOString() } : n));
      setUnreadCount(prev => Math.max(0, prev - 1));
    } catch (error) {
      console.error("[Notifications] Error marking as read:", error);
    }
  };

  const formatTime = (dateStr: string) => {
    const date = new Date(dateStr);
    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const minutes = Math.floor(diff / 60000);
    if (minutes < 1) return "Ahora";
    if (minutes < 60) return `Hace ${minutes} min`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `Hace ${hours} h`;
    return date.toLocaleDateString();
  };

  if (!isMounted) {
    return (
      <div className="relative inline-flex h-10 w-10 items-center justify-center rounded-lg border border-border bg-card text-muted-foreground">
        <Bell className="h-5 w-5" />
      </div>
    );
  }

  return (
    <div className="relative" ref={dropdownRef}>
      <button
        type="button"
        onClick={() => setIsOpen(!isOpen)}
        className="relative inline-flex h-10 w-10 items-center justify-center rounded-lg border border-border bg-card text-muted-foreground transition hover:bg-accent hover:text-foreground"
        aria-label="Notificaciones"
      >
        <Bell className="h-5 w-5" />
        {unreadCount > 0 && (
          <span className="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-destructive text-[10px] font-bold text-destructive-foreground animate-in zoom-in">
            {unreadCount > 9 ? "+9" : unreadCount}
          </span>
        )}
      </button>

      {isOpen && (
        <div className="absolute right-0 mt-2 w-80 origin-top-right rounded-xl border border-border bg-card shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50 animate-in fade-in slide-in-from-top-2 duration-200">
          <div className="p-4 border-b border-border flex items-center justify-between">
            <h3 className="text-sm font-semibold text-foreground">Notificaciones</h3>
            <span className="text-[10px] uppercase font-bold text-muted-foreground tracking-wider">
              {unreadCount} pendientes
            </span>
          </div>

          <div className="max-h-96 overflow-y-auto">
            {isLoading && notifications.length === 0 ? (
              <div className="p-8 text-center">
                <div className="mx-auto h-8 w-8 animate-spin rounded-full border-2 border-primary border-t-transparent" />
              </div>
            ) : notifications.length > 0 ? (
              <div className="divide-y divide-border">
                {notifications.map((n) => (
                  <div
                    key={n.id}
                    className={`p-4 transition hover:bg-muted/50 relative group ${!n.read_at ? 'bg-primary/5' : ''}`}
                  >
                    <div className="flex gap-3">
                      <div className={`mt-1 h-2 w-2 rounded-full shrink-0 ${!n.read_at ? 'bg-primary' : 'bg-transparent'}`} />
                      <div className="flex-1 min-w-0">
                        <p className="text-sm text-foreground leading-snug">
                          {n.data.message}
                        </p>
                        <div className="mt-2 flex items-center gap-3 text-[11px] text-muted-foreground font-medium">
                          <span className="flex items-center gap-1">
                            <Clock className="h-3 w-3" />
                            {formatTime(n.created_at)}
                          </span>
                          {!n.read_at && (
                            <button
                              onClick={() => handleMarkAsRead(n.id)}
                              className="text-primary hover:underline flex items-center gap-1"
                            >
                              <Check className="h-3 w-3" />
                              Marcar como leída
                            </button>
                          )}
                        </div>
                      </div>
                      {n.data.action_url && (
                        <Link
                          href={formatActionUrl(n.data.action_url)}
                          className="text-muted-foreground hover:text-primary transition"
                          title="Ir al recurso"
                          onClick={() => setIsOpen(false)}
                        >
                          <ExternalLink className="h-4 w-4" />
                        </Link>
                      )}
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <div className="p-10 text-center flex flex-col items-center gap-3">
                <div className="h-12 w-12 rounded-full bg-muted flex items-center justify-center text-muted-foreground">
                  <BellOff className="h-6 w-6" />
                </div>
                <p className="text-sm text-muted-foreground">No tienes notificaciones</p>
              </div>
            )}
          </div>

          <div className="p-3 border-t border-border text-center">
            <button
              onClick={() => setIsOpen(false)}
              className="text-xs font-medium text-muted-foreground hover:text-foreground transition"
            >
              Cerrar panel
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
