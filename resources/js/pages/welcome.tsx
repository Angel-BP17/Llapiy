import React from 'react';
import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';

export default function Welcome() {
    return (
        <>
            <Head title="Welcome" />
            <div className="flex flex-col items-center justify-center min-h-screen bg-background text-foreground space-y-4">
                <h1 className="text-4xl font-bold">Llapiy + Inertia.js</h1>
                <p className="text-muted-foreground">¡Inertia configurado correctamente!</p>
                <Button>Botón de Shadcn</Button>
            </div>
        </>
    );
}
