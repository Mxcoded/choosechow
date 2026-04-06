import { api, setToken, removeToken } from './client';
import { ENDPOINTS } from './config';
import { User, LoginCredentials, RegisterData, AuthResponse } from '../types';

export const authService = {
  // Register new user
  register: async (data: RegisterData): Promise<AuthResponse> => {
    const response = await api.post<AuthResponse>(ENDPOINTS.AUTH.REGISTER, {
      ...data,
      device_name: 'mobile_app',
    });
    
    if (response.data.data.token) {
      await setToken(response.data.data.token);
    }
    
    return response.data.data;
  },

  // Login user
  login: async (credentials: LoginCredentials): Promise<AuthResponse> => {
    const response = await api.post<AuthResponse>(ENDPOINTS.AUTH.LOGIN, {
      ...credentials,
      device_name: 'mobile_app',
    });
    
    if (response.data.data.token) {
      await setToken(response.data.data.token);
    }
    
    return response.data.data;
  },

  // Logout user
  logout: async (): Promise<void> => {
    try {
      await api.post(ENDPOINTS.AUTH.LOGOUT);
    } finally {
      await removeToken();
    }
  },

  // Get current user
  getCurrentUser: async (): Promise<User> => {
    const response = await api.get<User>(ENDPOINTS.AUTH.USER);
    return response.data.data;
  },

  // Forgot password
  forgotPassword: async (email: string): Promise<{ message: string }> => {
    const response = await api.post<{ message: string }>(ENDPOINTS.AUTH.FORGOT_PASSWORD, { email });
    return response.data.data;
  },

  // Reset password
  resetPassword: async (data: {
    email: string;
    token: string;
    password: string;
    password_confirmation: string;
  }): Promise<{ message: string }> => {
    const response = await api.post<{ message: string }>(ENDPOINTS.AUTH.RESET_PASSWORD, data);
    return response.data.data;
  },

  // Verify email
  verifyEmail: async (id: number, hash: string): Promise<{ message: string }> => {
    const response = await api.get<{ message: string }>(`${ENDPOINTS.AUTH.VERIFY_EMAIL}/${id}/${hash}`);
    return response.data.data;
  },

  // Resend verification email
  resendVerificationEmail: async (): Promise<{ message: string }> => {
    const response = await api.post<{ message: string }>(ENDPOINTS.AUTH.RESEND_VERIFICATION);
    return response.data.data;
  },

  // Social login (Google, Facebook, Apple)
  socialLogin: async (provider: string, token: string): Promise<AuthResponse> => {
    const response = await api.post<AuthResponse>(ENDPOINTS.AUTH.SOCIAL_LOGIN, {
      provider,
      token,
      device_name: 'mobile_app',
    });
    
    if (response.data.data.token) {
      await setToken(response.data.data.token);
    }
    
    return response.data.data;
  },
};

export default authService;
