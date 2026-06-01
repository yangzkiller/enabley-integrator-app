import { Link } from '@inertiajs/react';

const navLinks = [
    { label: 'Home', href: '/' },
    { label: 'Colaboradores', href: '/colaboradores' },
    { label: 'Configurações', href: '/configuracoes' },
];

export default function AppLayout({ children }) {
    return (
        <div className="min-h-screen flex flex-col bg-gray-950">
            <nav className="bg-gray-900 border-b border-gray-800 px-6 py-4 flex items-center justify-between">
                <span className="text-white font-bold text-lg tracking-tight">
                    Enabley Integrator
                </span>
                <div className="flex gap-6">
                    {navLinks.map(link => (
                        <Link
                            key={link.href}
                            href={link.href}
                            className="text-gray-400 hover:text-white text-sm font-medium transition-colors"
                        >
                            {link.label}
                        </Link>
                    ))}
                </div>
            </nav>
            <main className="flex-1 p-8">
                {children}
            </main>
        </div>
    );
}