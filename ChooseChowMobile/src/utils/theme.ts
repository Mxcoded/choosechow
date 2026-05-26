// ChooseChow Theme - Red Color Scheme
export const COLORS = {
  // Primary Red Theme
  primary: '#E53935',
  primaryDark: '#C62828',
  primaryLight: '#EF5350',
  primaryFaded: '#FFEBEE',
  
  // Secondary Colors
  secondary: '#1A237E',
  accent: '#FFC107',
  
  // Neutrals
  white: '#FFFFFF',
  black: '#000000',
  gray: {
    50: '#FAFAFA',
    100: '#F5F5F5',
    200: '#EEEEEE',
    300: '#E0E0E0',
    400: '#BDBDBD',
    500: '#9E9E9E',
    600: '#757575',
    700: '#616161',
    800: '#424242',
    900: '#212121',
  },
  
  // Text Colors
  text: {
    primary: '#1F2937',
    secondary: '#6B7280',
    light: '#9CA3AF',
    white: '#FFFFFF',
  },
  
  // Status Colors
  success: '#10B981',
  warning: '#F59E0B',
  error: '#EF4444',
  info: '#3B82F6',
  
  // Background Colors
  background: {
    primary: '#FFFFFF',
    secondary: '#F9FAFB',
    dark: '#1F2937',
  },
  
  // Border Colors
  border: {
    light: '#E5E7EB',
    medium: '#D1D5DB',
  },
};

export const FONTS = {
  sizes: {
    xs: 10,
    sm: 12,
    md: 14,
    lg: 16,
    xl: 18,
    xxl: 20,
    xxxl: 24,
    title: 28,
    logo: 36,
  },
  weights: {
    normal: '400' as const,
    medium: '500' as const,
    semibold: '600' as const,
    bold: '700' as const,
  },
};

export const SPACING = {
  xs: 4,
  sm: 8,
  md: 12,
  lg: 16,
  xl: 20,
  xxl: 24,
  xxxl: 32,
};

export const BORDER_RADIUS = {
  sm: 8,
  md: 12,
  lg: 16,
  xl: 20,
  full: 9999,
};

export default { COLORS, FONTS, SPACING, BORDER_RADIUS };
