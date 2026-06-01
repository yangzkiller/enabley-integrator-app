import AppLayout from './Layouts/AppLayout';

export default function Home({ group, stats }) {
    return (
        <AppLayout>
            <div className="max-w-4xl mx-auto">
                <div className="mb-8">
                    <h1 className="text-2xl font-bold text-white">Dashboard</h1>
                    <p className="text-gray-400 mt-1">Integração Protheus → Enabley</p>
                </div>

                {/* Grupo */}
                <div className="bg-gray-900 border border-gray-800 rounded-xl p-6 mb-6">
                    <p className="text-xs text-gray-500 uppercase tracking-wider mb-2">Grupo de destino</p>
                    {group ? (
                        <div>
                            <p className="text-xl font-semibold text-white">{group.name}</p>
                            <p className="text-sm text-gray-500 mt-1 font-mono">{group.identifier}</p>
                        </div>
                    ) : (
                        <p className="text-red-400 text-sm">Grupo não encontrado — verifique o ENABLEY_GROUP_ID</p>
                    )}
                </div>

                {/* Stats */}
                <div className="grid grid-cols-3 gap-4">
                    <div className="bg-gray-900 border border-gray-800 rounded-xl p-6 text-center">
                        <p className="text-3xl font-bold text-white">{stats.total}</p>
                        <p className="text-gray-400 text-sm mt-1">Total no Protheus</p>
                    </div>
                    <div className="bg-gray-900 border border-green-900 rounded-xl p-6 text-center">
                        <p className="text-3xl font-bold text-green-400">{stats.synced}</p>
                        <p className="text-gray-400 text-sm mt-1">Sincronizados</p>
                    </div>
                    <div className="bg-gray-900 border border-yellow-900 rounded-xl p-6 text-center">
                        <p className="text-3xl font-bold text-yellow-400">{stats.pending}</p>
                        <p className="text-gray-400 text-sm mt-1">Pendentes</p>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}