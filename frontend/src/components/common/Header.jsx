import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../hooks/useAuth';
import { logout } from '../../services/auth';
import { useTranslation } from 'react-i18next';

const Header = () => {
  const { t } = useTranslation();
  const { user, setUser } = useAuth();
  const navigate = useNavigate();
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);

  const handleLogout = () => {
    logout();
    setUser(null);
    navigate('/login');
  };

  return (
    <header className="bg-blue-600 text-white p-4 flex justify-between items-center">
      <div className="flex items-center">
        <button
          className="md:hidden mr-4"
          onClick={() => setIsSidebarOpen(!isSidebarOpen)}
        >
          <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16m-7 6h7" />
          </svg>
        </button>
        <Link to="/" className="text-xl font-bold">
          <img src="/assets/logo.png" alt={t('header.title')} className="h-8 inline" /> {t('header.title')}
        </Link>
      </div>
      <nav className="hidden md:flex space-x-4">
        {user ? (
          <>
            {user.role === 'admin' && <Link to="/dashboard/admin" className="hover:underline">{t('header.admin_dashboard')}</Link>}
            {user.role === 'security' && <Link to="/dashboard/security" className="hover:underline">{t('header.security_dashboard')}</Link>}
            {user.role === 'teacher' && <Link to="/dashboard/teacher" className="hover:underline">{t('header.teacher_dashboard')}</Link>}
            {user.role === 'student' && <Link to="/dashboard/student" className="hover:underline">{t('header.student_dashboard')}</Link>}
            <button onClick={handleLogout} className="hover:underline">{t('header.logout')}</button>
          </>
        ) : (
          <Link to="/login" className="hover:underline">{t('header.login')}</Link>
        )}
      </nav>
      {isSidebarOpen && (
        <div className="md:hidden fixed inset-0 bg-gray-800 bg-opacity-75 z-50">
          <div className="w-64 bg-blue-600 h-full p-4">
            <button onClick={() => setIsSidebarOpen(false)} className="text-white mb-4">
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
            <nav className="flex flex-col space-y-4">
              {user ? (
                <>
                  {user.role === 'admin' && <Link to="/dashboard/admin" className="hover:underline" onClick={() => setIsSidebarOpen(false)}>{t('header.admin_dashboard')}</Link>}
                  {user.role === 'security' && <Link to="/dashboard/security" className="hover:underline" onClick={() => setIsSidebarOpen(false)}>{t('header.security_dashboard')}</Link>}
                  {user.role === 'teacher' && <Link to="/dashboard/teacher" className="hover:underline" onClick={() => setIsSidebarOpen(false)}>{t('header.teacher_dashboard')}</Link>}
                  {user.role === 'student' && <Link to="/dashboard/student" className="hover:underline" onClick={() => setIsSidebarOpen(false)}>{t('header.student_dashboard')}</Link>}
                  <button onClick={handleLogout} className="text-left hover:underline">{t('header.logout')}</button>
                </>
              ) : (
                <Link to="/login" className="hover:underline" onClick={() => setIsSidebarOpen(false)}>{t('header.login')}</Link>
              )}
            </nav>
          </div>
        </div>
      )}
    </header>
  );
};

export default Header;