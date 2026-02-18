import { defineStore } from 'pinia';
import axios from 'axios';

export const useAppStore = defineStore('app', {
  state: () => ({
    count: 0,
    role: 'guest',
    user: null,
    isAuthenticated: false,
    isAuthChecked: false,
  }),
  actions: {
    increment() {
      this.count++;
    },
    setRole(r) {
      this.role = r;
    },
    setUser(userData) {
      this.user = userData;
      this.role = userData?.role || 'guest';
      this.isAuthenticated = !!userData;
    },
    async fetchUser() {
      try {
        const response = await axios.get('/api/user');
        if (response.data.success) {
          this.setUser(response.data.user);
          return true;
        }
        this.setUser(null);
        return false;
      } catch (error) {
        this.setUser(null);
        return false;
      } finally {
        this.isAuthChecked = true;
      }
    },
    async login(credentials) {
      try {
        const response = await axios.post('/api/login', credentials);
        if (response.data.success) {
          this.setUser(response.data.user);
          this.isAuthChecked = true;
          return { success: true };
        }
        return { success: false, message: response.data.message };
      } catch (error) {
        return { 
          success: false, 
          message: error.response?.data?.message || 'Login failed' 
        };
      }
    },
    async logout() {
      try {
        await axios.post('/api/logout');
        this.setUser(null);
        this.isAuthChecked = true;
        return true;
      } catch (error) {
        console.error('Logout error:', error);
        return false;
      }
    },
    async register(payload) {
      try {
        const response = await axios.post('/api/register', payload)
        if (response.data.success) {
          this.setUser(response.data.user)
          return { success: true }
        }
        return { success: false, message: response.data.message }
      } catch (error) {
        return {
          success: false,
          message: error.response?.data?.message || 'Registration failed'
        }
      }
    },
  },
});
