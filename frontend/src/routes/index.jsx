import { Routes, Route, Navigate } from 'react-router-dom';
import MainLayout from '../components/layout/MainLayout';
import Login from '../pages/Login';
import Dashboard from '../pages/Dashboard';
import UserManagement from '../pages/UserManagement';
import Unauthorized from '../pages/Unauthorized';
import NotFound from '../pages/NotFound';
import { useAuth } from '../hooks/useAuth';

const ProtectedRoute = ({ children, allowedRoles }) => {
  const { user } = useAuth();
  if (!user) return <Navigate to="/login" replace />;
  if (allowedRoles && !allowedRoles.includes(user.role)) return <Navigate to="/unauthorized" replace />;
  return children;
};

const AppRoutes = () => {
  return (
    <Routes>
      <Route path="/login" element={<Login />} />
      <Route
        path="/dashboard"
        element={
          <ProtectedRoute allowedRoles={['admin', 'security', 'teacher', 'student']}>
            <MainLayout />
          </ProtectedRoute>
        }
      >
        <Route index element={<Dashboard />} />
        <Route
          path="admin"
          element={<ProtectedRoute allowedRoles={['admin']}><Dashboard /></ProtectedRoute>}
        />
        <Route
          path="admin/users"
          element={<ProtectedRoute allowedRoles={['admin']}><UserManagement /></ProtectedRoute>}
        />
        <Route
          path="security"
          element={<ProtectedRoute allowedRoles={['security']}><Dashboard /></ProtectedRoute>}
        />
        <Route
          path="teacher"
          element={<ProtectedRoute allowedRoles={['teacher']}><Dashboard /></ProtectedRoute>}
        />
        <Route
          path="student"
          element={<ProtectedRoute allowedRoles={['student']}><Dashboard /></ProtectedRoute>}
        />
      </Route>
      <Route path="/unauthorized" element={<Unauthorized />} />
      <Route path="*" element={<NotFound />} />
    </Routes>
  );
};

export default AppRoutes;