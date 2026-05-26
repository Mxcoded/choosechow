// API Configuration for ChooseChow Mobile App
export const API_CONFIG = {
  // Production API URL - ChooseChow is hosted at choosechow.com
  BASE_URL: 'https://choosechow.com/api/v1',
  
  // Development URL (uncomment for local testing)
  // BASE_URL: 'http://10.0.2.2:8000/api/v1', // Android emulator
  // BASE_URL: 'http://localhost:8000/api/v1', // iOS simulator
  
  // Request timeout in milliseconds
  TIMEOUT: 30000,
  
  // API Version
  VERSION: 'v1',
};

// API Endpoints
export const ENDPOINTS = {
  // Authentication
  AUTH: {
    REGISTER: '/auth/register',
    LOGIN: '/auth/login',
    LOGOUT: '/auth/logout',
    USER: '/auth/user',
    FORGOT_PASSWORD: '/auth/forgot-password',
    RESET_PASSWORD: '/auth/reset-password',
    VERIFY_EMAIL: '/auth/verify-email',
    RESEND_VERIFICATION: '/auth/resend-verification',
    REFRESH_TOKEN: '/auth/refresh',
    SOCIAL_LOGIN: '/auth/social',
  },
  
  // User Profile
  USER: {
    PROFILE: '/user/profile',
    UPDATE_PROFILE: '/user/profile',
    CHANGE_PASSWORD: '/user/change-password',
    PREFERENCES: '/user/preferences',
    DELETE_ACCOUNT: '/user/delete-account',
  },
  
  // Addresses
  ADDRESSES: {
    LIST: '/addresses',
    CREATE: '/addresses',
    UPDATE: (id: number) => `/addresses/${id}`,
    DELETE: (id: number) => `/addresses/${id}`,
    SET_DEFAULT: (id: number) => `/addresses/${id}/default`,
  },
  
  // Chefs
  CHEFS: {
    LIST: '/chefs',
    DETAIL: (id: number) => `/chefs/${id}`,
    SEARCH: '/chefs/search',
    NEARBY: '/chefs/nearby',
    TOP_RATED: '/chefs/top-rated',
    MENUS: (id: number) => `/chefs/${id}/menus`,
    REVIEWS: (id: number) => `/chefs/${id}/reviews`,
    AVAILABILITY: (id: number) => `/chefs/${id}/availability`,
  },
  
  // Menus
  MENUS: {
    LIST: '/menus',
    DETAIL: (id: number) => `/menus/${id}`,
    SEARCH: '/menus/search',
    BY_CUISINE: (cuisineId: number) => `/menus/cuisine/${cuisineId}`,
    POPULAR: '/menus/popular',
  },
  
  // Cuisines
  CUISINES: {
    LIST: '/cuisines',
    DIETARY: '/cuisines/dietary-preferences',
  },
  
  // Cart
  CART: {
    GET: '/cart',
    ADD: '/cart/items',
    UPDATE: (itemId: number) => `/cart/items/${itemId}`,
    REMOVE: (itemId: number) => `/cart/items/${itemId}`,
    CLEAR: '/cart/clear',
    APPLY_COUPON: '/cart/coupon',
    REMOVE_COUPON: '/cart/coupon',
  },
  
  // Orders
  ORDERS: {
    LIST: '/orders',
    CREATE: '/orders',
    DETAIL: (id: number) => `/orders/${id}`,
    CANCEL: (id: number) => `/orders/${id}/cancel`,
    TRACK: (id: number) => `/orders/${id}/track`,
    REORDER: (id: number) => `/orders/${id}/reorder`,
    HISTORY: '/orders/history',
  },
  
  // Payment
  PAYMENT: {
    INITIALIZE: '/payment/initialize',
    VERIFY: '/payment/verify',
    METHODS: '/payment/methods',
  },
  
  // Reviews
  REVIEWS: {
    CREATE: '/reviews',
    UPDATE: (id: number) => `/reviews/${id}`,
    DELETE: (id: number) => `/reviews/${id}`,
  },
  
  // Favorites
  FAVORITES: {
    LIST: '/favorites',
    ADD: '/favorites',
    REMOVE: (id: number) => `/favorites/${id}`,
  },
  
  // Notifications
  NOTIFICATIONS: {
    LIST: '/notifications',
    MARK_READ: (id: number) => `/notifications/${id}/read`,
    MARK_ALL_READ: '/notifications/read-all',
    SETTINGS: '/notifications/settings',
    REGISTER_DEVICE: '/notifications/device',
  },
  
  // Admin Endpoints
  ADMIN: {
    // Dashboard
    DASHBOARD: '/admin/dashboard',
    STATS: '/admin/stats',
    
    // Users Management
    USERS: {
      LIST: '/admin/users',
      DETAIL: (id: number) => `/admin/users/${id}`,
      UPDATE: (id: number) => `/admin/users/${id}`,
      DELETE: (id: number) => `/admin/users/${id}`,
      TOGGLE_STATUS: (id: number) => `/admin/users/${id}/toggle-status`,
    },
    
    // Vendors Management
    VENDORS: {
      LIST: '/admin/vendors',
      PENDING: '/admin/vendors/pending',
      DETAIL: (id: number) => `/admin/vendors/${id}`,
      APPROVE: (id: number) => `/admin/vendors/${id}/approve`,
      REJECT: (id: number) => `/admin/vendors/${id}/reject`,
      SUSPEND: (id: number) => `/admin/vendors/${id}/suspend`,
      ACTIVATE: (id: number) => `/admin/vendors/${id}/activate`,
    },
    
    // Orders Management
    ORDERS: {
      LIST: '/admin/orders',
      DETAIL: (id: number) => `/admin/orders/${id}`,
      UPDATE_STATUS: (id: number) => `/admin/orders/${id}/status`,
    },
    
    // Reports & Analytics
    REPORTS: {
      OVERVIEW: '/admin/reports/overview',
      REVENUE: '/admin/reports/revenue',
      ORDERS: '/admin/reports/orders',
      USERS: '/admin/reports/users',
    },
    
    // Activity Log
    ACTIVITY: '/admin/activity',
  },
};
