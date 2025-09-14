import api from './api';

export const login = async (username, password) => {
  try {
    const response = await api.post('/auth/login', new URLSearchParams({ username, password }));
    localStorage.setItem('token', response.data.access_token);
    return response.data;
  } catch (error) {
    throw new Error(error.response?.data?.detail || 'Login failed');
  }
};

export const register = async (username, password, role) => {
  try {
    const response = await api.post('/auth/register', { username, password, role });
    return response.data;
  } catch (error) {
    throw new Error(error.response?.data?.detail || 'Registration failed');
  }
};

export const logout = () => {
  localStorage.removeItem('token');
};