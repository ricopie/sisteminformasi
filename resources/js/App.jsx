import { Routes, Route, Navigate } from 'react-router-dom';
import { useAuth } from './hooks/useAuth';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import BeneficiaryList from './pages/beneficiaries/List';
import BeneficiaryForm from './pages/beneficiaries/Form';
import Layout from './components/Layout';

function ProtectedRoute({ children }) {
    const { isAuthenticated, loading } = useAuth();

    if (loading) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-cyan-600"></div>
            </div>
        );
    }

    if (!isAuthenticated) {
        return <Navigate to="/login" replace />;
    }

    return <Layout>{children}</Layout>;
}

export default function App() {
    return (
        <Routes>
            <Route path="/login" element={<Login />} />
            <Route path="/dashboard" element={
                <ProtectedRoute><Dashboard /></ProtectedRoute>
            } />
            <Route path="/beneficiaries" element={
                <ProtectedRoute><BeneficiaryList /></ProtectedRoute>
            } />
            <Route path="/beneficiaries/create" element={
                <ProtectedRoute><BeneficiaryForm /></ProtectedRoute>
            } />
            <Route path="/beneficiaries/:id/edit" element={
                <ProtectedRoute><BeneficiaryForm /></ProtectedRoute>
            } />
            <Route path="/" element={<Navigate to="/dashboard" replace />} />
            <Route path="*" element={<Navigate to="/dashboard" replace />} />
        </Routes>
    );
}
