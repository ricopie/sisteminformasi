import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../../api/client';
import { HiOutlineUserGroup, HiOutlineSearch } from 'react-icons/hi';

export default function BeneficiaryList() {
    const [beneficiaries, setBeneficiaries] = useState([]);
    const [meta, setMeta] = useState({ total: 0, current_page: 1, last_page: 1 });
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [typeFilter, setTypeFilter] = useState('');

    const fetchData = (page = 1) => {
        setLoading(true);
        const params = { per_page: 15, page };
        if (search) params.search = search;
        if (typeFilter) params.type = typeFilter;

        api.get('/beneficiaries', { params })
            .then(res => {
                setBeneficiaries(res.data.data || []);
                setMeta(res.data.meta || { total: 0, current_page: 1, last_page: 1 });
            })
            .catch(() => {})
            .finally(() => setLoading(false));
    };

    useEffect(() => { fetchData(); }, [typeFilter]);

    const handleSearch = (e) => {
        e.preventDefault();
        fetchData(1);
    };

    const typeBadge = (type) => {
        const styles = {
            child: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300',
            elderly: 'bg-violet-100 text-violet-800 dark:bg-violet-900/50 dark:text-violet-300',
            disabled: 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300',
        };
        return styles[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    };

    return (
        <div>
            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Beneficiaries</h1>
                    <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {meta.total} total beneficiaries
                    </p>
                </div>
                <Link to="/beneficiaries/create"
                    className="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    <HiOutlineUserGroup className="w-5 h-5" />
                    Add Beneficiary
                </Link>
            </div>

            {/* Filters */}
            <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-6">
                <div className="flex flex-col sm:flex-row gap-4">
                    <form onSubmit={handleSearch} className="flex-1 flex gap-2">
                        <div className="relative flex-1">
                            <HiOutlineSearch className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                            <input type="text" value={search} onChange={e => setSearch(e.target.value)}
                                placeholder="Search by name or NIK..."
                                className="w-full pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent" />
                        </div>
                        <button type="submit"
                            className="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Search
                        </button>
                    </form>
                    <select value={typeFilter} onChange={e => setTypeFilter(e.target.value)}
                        className="px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                        <option value="">All Types</option>
                        <option value="child">Child</option>
                        <option value="elderly">Elderly</option>
                        <option value="disabled">Disabled</option>
                    </select>
                </div>
            </div>

            {/* Table */}
            <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                <th className="px-6 py-3 font-medium">Full Name</th>
                                <th className="px-6 py-3 font-medium">NIK</th>
                                <th className="px-6 py-3 font-medium">Type</th>
                                <th className="px-6 py-3 font-medium">Status</th>
                                <th className="px-6 py-3 font-medium">Gender</th>
                                <th className="px-6 py-3 font-medium">Birth Place</th>
                                <th className="px-6 py-3 font-medium">Birth Date</th>
                                <th className="px-6 py-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {loading ? (
                                Array.from({ length: 5 }).map((_, i) => (
                                    <tr key={i} className="border-b border-gray-50 dark:border-gray-700/50">
                                        {Array.from({ length: 8 }).map((_, j) => (
                                            <td key={j} className="px-6 py-4">
                                                <span className="inline-block h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded animate-pulse" />
                                            </td>
                                        ))}
                                    </tr>
                                ))
                            ) : beneficiaries.length === 0 ? (
                                <tr>
                                    <td colSpan={8} className="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                        <HiOutlineUserGroup className="w-12 h-12 mx-auto mb-3 opacity-50" />
                                        <p className="text-lg font-medium mb-1">No beneficiaries found</p>
                                        <p className="text-sm">Start by adding a new beneficiary.</p>
                                    </td>
                                </tr>
                            ) : (
                                beneficiaries.map(b => (
                                    <tr key={b.id} className="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td className="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            {b.full_name}
                                            {b.nick_name && <span className="text-gray-400 ml-1">({b.nick_name})</span>}
                                        </td>
                                        <td className="px-6 py-4 text-gray-600 dark:text-gray-400 font-mono text-xs">{b.nik}</td>
                                        <td className="px-6 py-4">
                                            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${typeBadge(b.type)}`}>
                                                {b.type}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                b.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300'
                                            }`}>{
                                                b.is_active ? 'Active' : 'Inactive'
                                            }</span>
                                        </td>
                                        <td className="px-6 py-4 text-gray-600 dark:text-gray-400">{b.gender}</td>
                                        <td className="px-6 py-4 text-gray-600 dark:text-gray-400">{b.birth_place}</td>
                                        <td className="px-6 py-4 text-gray-600 dark:text-gray-400">{b.birth_date}</td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center gap-2">
                                                <Link to={`/beneficiaries/${b.id}/edit`}
                                                    className="text-xs font-medium text-cyan-600 hover:text-cyan-800 dark:text-cyan-400 dark:hover:text-cyan-300 bg-cyan-50 dark:bg-cyan-900/30 px-3 py-1.5 rounded-lg transition-colors">
                                                    Edit
                                                </Link>
                                                <button onClick={() => {
                                                    if (confirm('Delete this beneficiary?')) {
                                                        api.delete(`/beneficiaries/${b.id}`)
                                                            .then(() => fetchData(meta.current_page))
                                                            .catch(() => {});
                                                    }
                                                }}
                                                    className="text-xs font-medium text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 bg-red-50 dark:bg-red-900/30 px-3 py-1.5 rounded-lg transition-colors">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                {meta.last_page > 1 && (
                    <div className="flex items-center justify-between px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            Page {meta.current_page} of {meta.last_page}
                        </p>
                        <div className="flex gap-2">
                            <button onClick={() => fetchData(meta.current_page - 1)}
                                disabled={meta.current_page <= 1}
                                className="px-3 py-1.5 text-sm rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 disabled:opacity-50 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                Previous
                            </button>
                            <button onClick={() => fetchData(meta.current_page + 1)}
                                disabled={meta.current_page >= meta.last_page}
                                className="px-3 py-1.5 text-sm rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 disabled:opacity-50 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                Next
                            </button>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
