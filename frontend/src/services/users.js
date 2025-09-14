import api from './api';

export const createUser = async (userData) => {
  try {
    const response = await api.post('/auth/users', userData);
    return response.data;
  } catch (error) {
    throw new Error(error.response?.data?.detail || 'Failed to create user');
  }
};

export const getUsers = async () => {
  try {
    const response = await api.get('/auth/users');
    return response.data;
  } catch (error) {
    throw new Error(error.response?.data?.detail || 'Failed to fetch users');
  }
};

export const updateUser = async (userId, userData) => {
  try {
    const response = await api.put(`/auth/users/${userId}`, userData);
    return response.data;
  } catch (error) {
    throw new Error(error.response?.data?.detail || 'Failed to update user');
  }
};

export const deleteUser = async (userId) => {
  try {
    const response = await api.delete(`/auth/users/${userId}`);
    return response.data;
  } catch (error) {
    throw new Error(error.response?.data?.detail || 'Failed to delete user');
  }
};