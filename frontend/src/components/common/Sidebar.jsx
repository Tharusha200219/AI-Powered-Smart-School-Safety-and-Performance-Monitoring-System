import { Link } from 'react-router-dom';
import { useAuth } from '../../hooks/useAuth';
import { useTranslation } from 'react-i18next';

const Sidebar = () => {
  const { t } = useTranslation();
  const { user } = useAuth();

  return (
    <aside className="w-64 bg-gray-100 h-screen p-4 hidden md:block">
      <h2 className="text-lg font-bold mb-4">{t('sidebar.navigation')}</h2>
      <nav className="flex flex-col space-y-2">
        {user?.role === 'admin' && (
          <>
            <Link to="/dashboard/admin/threats" className="p-2 hover:bg-blue-100 rounded">{t('sidebar.threats')}</Link>
            <Link to="/dashboard/admin/seating" className="p-2 hover:bg-blue-100 rounded">{t('sidebar.seating')}</Link>
            <Link to="/dashboard/admin/performance" className="p-2 hover:bg-blue-100 rounded">{t('sidebar.performance')}</Link>
            <Link to="/dashboard/admin/users" className="p-2 hover:bg-blue-100 rounded">{t('sidebar.user_management')}</Link>
          </>
        )}
        {user?.role === 'security' && (
          <>
            <Link to="/dashboard/security/threats" className="p-2 hover:bg-blue-100 rounded">{t('sidebar.threat_alerts')}</Link>
            <Link to="/dashboard/security/objects" className="p-2 hover:bg-blue-100 rounded">{t('sidebar.objects')}</Link>
          </>
        )}
        {user?.role === 'teacher' && (
          <>
            <Link to="/dashboard/teacher/quizzes" className="p-2 hover:bg-blue-100 rounded">{t('sidebar.quizzes')}</Link>
            <Link to="/dashboard/teacher/performance" className="p-2 hover:bg-blue-100 rounded">{t('sidebar.class_performance')}</Link>
          </>
        )}
        {user?.role === 'student' && (
          <>
            <Link to="/dashboard/student/quiz" className="p-2 hover:bg-blue-100 rounded">{t('sidebar.daily_quiz')}</Link>
            <Link to="/dashboard/student/performance" className="p-2 hover:bg-blue-100 rounded">{t('sidebar.my_performance')}</Link>
          </>
        )}
      </nav>
    </aside>
  );
};

export default Sidebar;