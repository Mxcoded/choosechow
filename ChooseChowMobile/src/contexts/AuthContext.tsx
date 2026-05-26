import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { authService, getToken } from '../api';
import { User, LoginCredentials, RegisterData } from '../types';

interface AuthContextType {
  user: User | null;
  isLoading: boolean; // Only true during initial app load
  isAuthenticated: boolean;
  login: (credentials: LoginCredentials) => Promise<void>;
  register: (data: RegisterData) => Promise<void>;
  logout: () => Promise<void>;
  refreshUser: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

interface AuthProviderProps {
  children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  // isLoading is ONLY for initial app load - NOT for login/register/logout actions
  const [isLoading, setIsLoading] = useState(true);

  // Check if user is already authenticated on app load
  useEffect(() => {
    checkAuthStatus();
  }, []);

  const checkAuthStatus = async () => {
    try {
      const token = await getToken();
      if (token) {
        const currentUser = await authService.getCurrentUser();
        setUser(currentUser);
      }
    } catch (error) {
      // Token invalid or expired, user needs to login again
      setUser(null);
    } finally {
      setIsLoading(false);
    }
  };

  // Login does NOT set isLoading - the screen handles its own loading state
  const login = async (credentials: LoginCredentials) => {
    const response = await authService.login(credentials);
    setUser(response.user);
  };

  // Register does NOT set isLoading - the screen handles its own loading state
  const register = async (data: RegisterData) => {
    const response = await authService.register(data);
    setUser(response.user);
  };

  // Logout does NOT set isLoading - prevents navigation state reset
  const logout = async () => {
    try {
      await authService.logout();
    } catch (error) {
      // Even if the API call fails, we still want to log out locally
      console.warn('Logout API call failed, clearing local session:', error);
    } finally {
      // Always clear user state regardless of API success
      setUser(null);
    }
  };

  const refreshUser = async () => {
    try {
      const currentUser = await authService.getCurrentUser();
      setUser(currentUser);
    } catch {
      setUser(null);
    }
  };

  const value: AuthContextType = {
    user,
    isLoading,
    isAuthenticated: !!user,
    login,
    register,
    logout,
    refreshUser,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};

export const useAuth = (): AuthContextType => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

export default AuthContext;
