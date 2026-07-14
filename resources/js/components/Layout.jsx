import { useState } from 'react';
import { useAuth } from '../hooks/useAuth';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { DarkThemeToggle, Sidebar, Navbar } from 'flowbite-react';
import { HiHome, HiUsers, HiLogout, HiMenu, HiX } from 'react-icons/hi';

export default function Layout({ children }) {
    const { user, logout } = useAuth();
    const navigate = useNavigate();
    const location = useLocation();
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);

    const handleLogout = async () => {
        await logout();
        navigate('/login');
    };

    const navItems = [
        { path: '/dashboard', label: 'Dashboard', icon: HiHome },
        { path: '/beneficiaries', label: 'Beneficiaries', icon: HiUsers },
    ];

    return (
        <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
            {/* Mobile menu button */}
            <div className="lg:hidden fixed top-0 left-0 z-50 p-4">
                <button
                    onClick={() => setIsSidebarOpen(!isSidebarOpen)}
                    className="p-2 rounded-lg bg-white dark:bg-gray-800 shadow"
                >
                    {isSidebarOpen ? <HiX size={24} /> : <HiMenu size={24} />}
                </button>
            </div>

            {/* Sidebar */}
            <aside className={`fixed top-0 left-0 z-40 h-screen w-64 transform transition-transform duration-200 ease-in-out ${
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
            } lg:translate-x-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700`}>
                <div className="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                    <span className="text-xl font-semibold text-gray-900 dark:text-white">Sistem Info</span>
                    <DarkThemeToggle />
                </div>
                <nav className="p-4 space-y-2">
                    {navItems.map(item => (
                        <Link
                            key={item.path}
                            to={item.path}
                            onClick={() => setIsSidebarOpen(false)}
                            className={`flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors ${
                                location.pathname.startsWith(item.path)
                                    ? 'bg-cyan-50 text-cyan-700 dark:bg-cyan-900/50 dark:text-cyan-300'
                                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'
                            }`}
                        >
                            <item.icon className="w-5 h-5" />
                            <span>{item.label}</span>
                        </Link>
                    ))}
                </nav>
                <div className="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-gray-700">
                    <div className="flex items-center justify-between">
                        <span className="text-sm text-gray-600 dark:text-gray-400">{user?.name}</span>
                        <button
                            onClick={handleLogout}
                            className="p-2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400"
                        >
                            <HiLogout size={20} />
                        </button>
                    </div>
                </div>
            </aside>

            {/* Overlay for mobile */}
            {isSidebarOpen && (
                <div
                    className="fixed inset-0 z-30 bg-black/50 lg:hidden"
                    onClick={() => setIsSidebarOpen(false)}
                />
            )}

            {/* Main content */}
            <main className="lg:ml-64 min-h-screen pt-16 lg:pt-0">
                <div className="p-6">
                    {children}
                </div>
            </main>
        </div>
    );
}
