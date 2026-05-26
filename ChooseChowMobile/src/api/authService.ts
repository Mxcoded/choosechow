import { api, setToken, removeToken } from './client';
import { ENDPOINTS } from './config';
import { User, LoginCredentials, RegisterData, AuthResponse, ApiUser, normalizeUser } from '../types';

// Raw API response type (before normalization)
interface RawAuthResponse {
  user: ApiUser;
  token: string;
  token_type: string;
}

export const authService = {
  // Register new user
  register: async (data: RegisterData): Promise<AuthResponse> => {
    const response = await api.post<RawAuthResponse>(ENDPOINTS.AUTH.REGISTER, {
      first_name: data.first_name,
      last_name: data.last_name,
      email: data.email,
      phone: data.phone,
      password: data.password,
      password_confirmation: data.password_confirmation,
      role: data.role || 'customer', // Default to customer
      device_name: 'mobile_app',
    });
    
    const rawData = response.data.data;
    
    if (rawData.token) {
      await setToken(rawData.token);
    }
    
    // Normalize user to extract role from Spatie format
    return {
      user: normalizeUser(rawData.user),
      token: rawData.token,
      token_type: rawData.token_type,
    };
  },

  // Login user
  login: async (credentials: LoginCredentials): Promise<AuthResponse> => {
    const response = await api.post<RawAuthResponse>(ENDPOINTS.AUTH.LOGIN, {
      ...credentials,
      device_name: 'mobile_app',
    });
    
    const rawData = response.data.data;
    
    if (rawData.token) {
      await setToken(rawData.token);
    }
    
    // Normalize user to extract role from Spatie format
    return {
      user: normalizeUser(rawData.user),
      token: rawData.token,
      token_type: rawData.token_type,
    };
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
    const response = await api.get<ApiUser>(ENDPOINTS.AUTH.USER);
    // Normalize user to extract role from Spatie format
    return normalizeUser(response.data.data);
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
    const response = await api.post<RawAuthResponse>(ENDPOINTS.AUTH.SOCIAL_LOGIN, {
      provider,
      token,
      device_name: 'mobile_app',
    });
    
    const rawData = response.data.data;
    
    if (rawData.token) {
      await setToken(rawData.token);
    }
    
    // Normalize user to extract role from Spatie format
    return {
      user: normalizeUser(rawData.user),
      token: rawData.token,
      token_type: rawData.token_type,
    };
  },
};

export default authService;
