import { useAuth } from '../../hooks/useAuth';
import LoadingSpinner from '../common/LoadingSpinner';
import api from '../../services/api';
import { useEffect, useState } from 'react';

const Dashboard = () => {
  const { user } = useAuth();
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        let endpoint = '';
        if (user.role === 'admin') endpoint = '/dashboard/threats';
        else if (user.role === 'security') endpoint = '/dashboard/security/threats';
        else if (user.role === 'teacher') endpoint = '/dashboard/teacher/performance';
        else if (user.role === 'student') endpoint = '/dashboard/student/quiz';
        
        const response = await api.get(endpoint);
        setData(response.data);
      } catch (error) {
        console.error('Failed to fetch dashboard data:', error);
      } finally {
        setLoading(false);
      }
    };
    if (user) fetchData();
  }, [user]);

  if (!user) return <p>Please log in to view the dashboard.</p>;
  if (loading) return <LoadingSpinner />;

  return (
    <div className="p-6">
      <h2 className="text-2xl font-bold mb-4">
        {user.role.charAt(0).toUpperCase() + user.role.slice(1)} Dashboard
      </h2>
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        {user.role === 'admin' && (
          <>
            <div className="bg-white p-4 rounded shadow">
              <h3 className="text-lg font-semibold">Recent Threats</h3>
              <p>{data?.threats?.length ? data.threats.join(', ') : 'No recent threats'}</p>
            </div>
            <div className="bg-white p-4 rounded shadow">
              <h3 className="text-lg font-semibold">Seating Arrangements</h3>
              <p>View and manage seating plans.</p>
            </div>
          </>
        )}
        {user.role === 'security' && (
          <div className="bg-white p-4 rounded shadow">
            <h3 className="text-lg font-semibold">Threat Alerts</h3>
            <p>{data?.threats?.length ? data.threats.join(', ') : 'No active alerts'}</p>
          </div>
        )}
        {user.role === 'teacher' && (
          <div className="bg-white p-4 rounded shadow">
            <h3 className="text-lg font-semibold">Class Performance</h3>
            <p>{data?.performance ? `Average Score: ${data.performance}` : 'No data'}</p>
          </div>
        )}
        {user.role === 'student' && (
          <div className="bg-white p-4 rounded shadow">
            <h3 className="text-lg font-semibold">Daily Quiz</h3>
            <p>{data?.quiz ? 'Take today\'s quiz' : 'No quiz available'}</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default Dashboard;