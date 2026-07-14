import { useState, useEffect } from 'react';
import { useAuth } from '../hooks/useAuth';
import api from '../api/client';
import { HiUserGroup, HiUserAdd, HiEye, HiClock } from 'react-icons/hi';

export default function Dashboard() {
    const { user } = useAuth();
    const [stats, setStats] = useState({ total: 0, child: 0, elderly: 0, disabled: 0 });
    const [recent, setRecent] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/beneficiaries?per_page=100')
            .then(res => {
                const data = res.data.data || [];
                const meta = res.data.meta || {};

                const types = { child: 0, elderly: 0, disabled: 0 };
                data.forEach(b => {
                    const t = (b.type || '').toLowerCase();
                    if (types[t] !== undefined) types[t]++;
                });

                setStats({
                    total: meta.total || data.length,
                    ...types,
                });
                setRecent(data.slice(0, 5));
            })
            .catch(() => {})
            .finally(() => setLoading(false));
    }, []);

    const statCards = [
        { label: 'Total Beneficiaries', value: stats.total, color: 'text-cyan-600', bg: 'bg-cyan-50', icon: HiUserGroup },
        { label: 'Children', value: stats.child, color: 'text-emerald-600', bg: 'bg-emerald-50', icon: HiUserAdd },
        { label: 'Elderly', value: stats.elderly, color: 'text-violet-600', bg: 'bg-violet-50', icon: HiClock },
        { label: 'Disabled', value: stats.disabled, color: 'text-amber-600', bg: 'bg-amber-50', icon: HiEye },
    ];

    return (
        <div>
            {/* Header */}
            <div className="mb-8">
                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                <p className="text-gray-500 dark:text-gray-400 mt-1">
                    Welcome back, <span className="font-semibold">{user?.name}</span>
                </p>
            </div>

            {/* Stats Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {statCards.map(card => (
                    <div key={card.label}
                        className="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm font-medium text-gray-500 dark:text-gray-400">{card.label}</p>
                                <p className={`text-3xl font-bold mt-1 ${card.color}`}>
                                    {loading ? (
                                        <span className="inline-block w-8 h-8 rounded bg-gray-200 dark:bg-gray-700 animate-pulse" />
                                    ) : card.value}
                                </p>
                            </div>
                            <div className={`p-3 rounded-lg ${card.bg} dark:opacity-80`}>
                                <card.icon className={`w-6 h-6 ${card.color}`} />
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {/* Recent Beneficiaries */}
            <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div className="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Recent Beneficiaries</h2>
                </div>
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                                <th className="px-6 py-3 font-medium">Name</th>
                                <th className="px-6 py-3 font-medium">NIK</th>
                                <th className="px-6 py-3 font-medium">Type</th>
                                <th className="px-6 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {loading ? (
                                Array.from({ length: 3 }).map((_, i) => (
                                    <tr key={i} className="border-b border-gray-50 dark:border-gray-700/50">
                                        {Array.from({ length: 4 }).map((_, j) => (
                                            <td key={j} className="px-6 py-4">
                                                <span className="inline-block h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded animate-pulse" />
                                            </td>
                                        ))}
                                    </tr>
                                ))
                            ) : recent.length === 0 ? (
                                <tr>
                                    <td colSpan={4} className="px-6 py-8 text-center text-gray-400">
                                        No beneficiaries registered yet.
                                    </td>
                                </tr>
                            ) : (
                                recent.map(b => (
                                    <tr key={b.id} className="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td className="px-6 py-4 font-medium text-gray-900 dark:text-white">{b.full_name}</td>
                                        <td className="px-6 py-4 text-gray-600 dark:text-gray-400">{b.nik}</td>
                                        <td className="px-6 py-4">
                                            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                b.type === 'child' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' :
                                                b.type === 'elderly' ? 'bg-violet-100 text-violet-800 dark:bg-violet-900/50 dark:text-violet-300' :
                                                b.type === 'disabled' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300' :
                                                'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                            }`}>
                                                {b.type || '-'}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                b.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300'
                                            }`}>
                                                {b.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}
