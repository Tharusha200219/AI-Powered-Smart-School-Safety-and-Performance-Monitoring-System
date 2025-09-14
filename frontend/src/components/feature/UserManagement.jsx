import { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { createUser, getUsers, updateUser, deleteUser } from '../../services/users';
import LoadingSpinner from '../common/LoadingSpinner';

const UserManagement = () => {
  const { t } = useTranslation();
  const [users, setUsers] = useState([]);
  const [formData, setFormData] = useState({
    username: '',
    password: '',
    role: 'student',
    permissions: {
      view_threats: false,
      manage_users: false,
      view_performance: false,
      manage_seating: false,
      generate_quizzes: false,
      view_objects: false,
      take_quiz: false,
      view_own_performance: false
    }
  });
  const [editingUserId, setEditingUserId] = useState(null);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    fetchUsers();
  }, []);

  const fetchUsers = async () => {
    setLoading(true);
    try {
      const data = await getUsers();
      setUsers(data);
    } catch (err) {
      setError(t('user_management.error', { message: err.message }));
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setSuccess('');
    setLoading(true);
    try {
      if (editingUserId) {
        await updateUser(editingUserId, formData);
        setSuccess(t('user_management.success', { action: 'updated' }));
      } else {
        await createUser(formData);
        setSuccess(t('user_management.success', { action: 'created' }));
      }
      setFormData({
        username: '',
        password: '',
        role: 'student',
        permissions: {
          view_threats: false,
          manage_users: false,
          view_performance: false,
          manage_seating: false,
          generate_quizzes: false,
          view_objects: false,
          take_quiz: false,
          view_own_performance: false
        }
      });
      setEditingUserId(null);
      fetchUsers();
    } catch (err) {
      setError(t('user_management.error', { message: err.message }));
    } finally {
      setLoading(false);
    }
  };

  const handleEdit = (user) => {
    setFormData({
      username: user.username,
      password: '', // Password not fetched for security
      role: user.role,
      permissions: user.permissions
    });
    setEditingUserId(user.id);
  };

  const handleDelete = async (userId) => {
    setError('');
    setSuccess('');
    setLoading(true);
    try {
      await deleteUser(userId);
      setSuccess(t('user_management.success', { action: 'deleted' }));
      fetchUsers();
    } catch (err) {
      setError(t('user_management.error', { message: err.message }));
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="p-6">
      <h2 className="text-2xl font-bold mb-4">{t('user_management.title')}</h2>
      {error && <p className="text-red-500 mb-4">{error}</p>}
      {success && <p className="text-green-500 mb-4">{success}</p>}
      <form onSubmit={handleSubmit} className="mb-8 bg-white p-6 rounded-lg shadow-md space-y-4">
        <div>
          <label htmlFor="username" className="block text-sm font-medium">{t('user_management.username')}</label>
          <input
            id="username"
            type="text"
            value={formData.username}
            onChange={(e) => setFormData({ ...formData, username: e.target.value })}
            className="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600"
            required
          />
        </div>
        <div>
          <label htmlFor="password" className="block text-sm font-medium">{t('user_management.password')}</label>
          <input
            id="password"
            type="password"
            value={formData.password}
            onChange={(e) => setFormData({ ...formData, password: e.target.value })}
            className="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600"
            required={!editingUserId}
          />
        </div>
        <div>
          <label htmlFor="role" className="block text-sm font-medium">{t('user_management.role')}</label>
          <select
            id="role"
            value={formData.role}
            onChange={(e) => setFormData({ ...formData, role: e.target.value })}
            className="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600"
          >
            <option value="admin">Admin</option>
            <option value="security">Security</option>
            <option value="teacher">Teacher</option>
            <option value="student">Student</option>
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium">{t('user_management.permissions')}</label>
          {Object.keys(formData.permissions).map((perm) => (
            <div key={perm} className="flex items-center">
              <input
                type="checkbox"
                id={perm}
                checked={formData.permissions[perm]}
                onChange={(e) => setFormData({
                  ...formData,
                  permissions: { ...formData.permissions, [perm]: e.target.checked }
                })}
                className="mr-2"
              />
              <label htmlFor={perm}>{t(`user_management.${perm}`)}</label>
            </div>
          ))}
        </div>
        <button
          type="submit"
          className="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700 disabled:bg-gray-400"
          disabled={loading}
        >
          {loading ? <LoadingSpinner /> : editingUserId ? t('user_management.update') : t('user_management.submit')}
        </button>
      </form>
      {loading && <LoadingSpinner />}
      <div className="bg-white p-6 rounded-lg shadow-md">
        <h3 className="text-lg font-semibold mb-4">Users</h3>
        <table className="w-full table-auto">
          <thead>
            <tr>
              <th className="px-4 py-2">Username</th>
              <th className="px-4 py-2">Role</th>
              <th className="px-4 py-2">Permissions</th>
              <th className="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            {users.map((user) => (
              <tr key={user.id}>
                <td className="border px-4 py-2">{user.username}</td>
                <td className="border px-4 py-2">{user.role}</td>
                <td className="border px-4 py-2">
                  {Object.entries(user.permissions)
                    .filter(([_, value]) => value)
                    .map(([key]) => t(`user_management.${key}`))
                    .join(', ')}
                </td>
                <td className="border px-4 py-2">
                  <button
                    onClick={() => handleEdit(user)}
                    className="text-blue-600 hover:underline mr-2"
                  >
                    Edit
                  </button>
                  <button
                    onClick={() => handleDelete(user.id)}
                    className="text-red-600 hover:underline"
                  >
                    {t('user_management.delete')}
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default UserManagement;