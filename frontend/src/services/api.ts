declare const process: {
  env: Record<string, string | undefined>;
};

// @ts-ignore - resolved in frontend runtime dependency install
import axios from 'axios';
// @ts-ignore - resolved in frontend runtime dependency install
import { getAuth } from 'firebase/auth';

const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000';

// Create axios instance
const api = axios.create({
  baseURL: `${API_BASE_URL}/api/v1`,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor to add auth token
api.interceptors.request.use(
  async (config) => {
    const auth = getAuth();
    const user = auth.currentUser;
    
    if (user) {
      try {
        const token = await user.getIdToken();
        config.headers.Authorization = `Bearer ${token}`;
      } catch (error) {
        console.error('Error getting auth token:', error);
      }
    }
    
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor for error handling
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Handle unauthorized access
      console.error('Unauthorized access');
      // You could redirect to login here
    }
    return Promise.reject(error);
  }
);

export { api }; 