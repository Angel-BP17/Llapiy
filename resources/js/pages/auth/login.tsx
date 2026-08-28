import React from "react";
import { useForm, Head } from "@inertiajs/react";
import { Loader2 } from "lucide-react";
import login from "@/routes/login";

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        user_name: "",
        password: "",
        remember: false,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(login.store.url());
    };

    return (
        <main className="relative min-h-screen overflow-hidden font-sans">
            <Head title="Iniciar Sesión" />
            <div
                className="absolute inset-0 bg-cover bg-center blur-2xl"
                style={{ backgroundImage: "url('img/background2.jpg')" }}
            ></div>
            <div className="absolute inset-0 bg-slate-900/20"></div>

            <div className="relative mx-auto flex min-h-screen max-w-6xl items-center px-4 py-10">
                <div className="relative w-full overflow-hidden rounded-3xl border border-blue-300/70 bg-white/90 shadow-2xl backdrop-blur">
                    <div className="grid gap-0 lg:grid-cols-2">
                        <section className="flex items-center justify-center bg-blue-300 px-10 py-16 text-black">
                            <div className="max-w-sm text-center">
                                <img
                                    src="img/logo.png"
                                    alt="Llapiy"
                                    className="mx-auto mb-6 w-80 drop-shadow-lg"
                                />
                                <h1 className="text-3xl font-semibold tracking-tight">
                                    Bienvenido a Llapiy
                                </h1>
                                <p className="mt-3 text-sm text-black/80">
                                    Accede a tu panel con tus credenciales
                                    autorizadas.
                                </p>
                                <div className="mt-8 rounded-2xl border border-white/10 bg-white/30 px-5 py-4">
                                    <p className="text-xs uppercase tracking-[0.2em] text-black/80">
                                        Plataforma segura
                                    </p>
                                    <p className="mt-2 text-sm text-black/80">
                                        Gestión centralizada y auditoría en
                                        tiempo real.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section className="flex items-center justify-center px-8 py-14 sm:px-12">
                            <div className="w-full max-w-md">
                                <div className="mb-8">
                                    <h2 className="text-2xl font-semibold text-slate-900">
                                        Iniciar sesión
                                    </h2>
                                    <p className="mt-2 text-sm text-slate-500">
                                        Ingresa tu usuario y contraseña para
                                        continuar.
                                    </p>
                                </div>

                                <form className="space-y-5" onSubmit={submit}>
                                    <div className="space-y-2">
                                        <label
                                            className="text-sm font-medium text-slate-700"
                                            htmlFor="user_name"
                                        >
                                            Nombre de usuario
                                        </label>
                                        <input
                                            id="user_name"
                                            type="text"
                                            value={data.user_name}
                                            onChange={(e) =>
                                                setData(
                                                    "user_name",
                                                    e.target.value.toUpperCase(),
                                                )
                                            }
                                            className="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm uppercase text-slate-900 shadow-sm focus:border-blue-400 focus:ring-4 focus:ring-blue-100 outline-none transition"
                                            required
                                        />
                                        {errors.user_name && (
                                            <p className="text-xs text-red-600">
                                                {errors.user_name}
                                            </p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <label
                                            className="text-sm font-medium text-slate-700"
                                            htmlFor="password"
                                        >
                                            Contraseña
                                        </label>
                                        <input
                                            id="password"
                                            type="password"
                                            value={data.password}
                                            onChange={(e) =>
                                                setData(
                                                    "password",
                                                    e.target.value,
                                                )
                                            }
                                            className="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:ring-4 focus:ring-blue-100 outline-none transition"
                                            required
                                        />
                                        {errors.password && (
                                            <p className="text-xs text-red-600">
                                                {errors.password}
                                            </p>
                                        )}
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="h-12 w-full rounded-xl bg-blue-500 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 disabled:opacity-70 transition flex items-center justify-center gap-2"
                                    >
                                        {processing && (
                                            <Loader2 className="h-4 w-4 animate-spin" />
                                        )}
                                        {processing
                                            ? "Ingresando..."
                                            : "Iniciar sesión"}
                                    </button>
                                </form>

                                <p className="mt-6 text-center text-xs text-slate-400">
                                    ¿Problemas para ingresar? Contacta al
                                    administrador.
                                </p>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </main>
    );
}
