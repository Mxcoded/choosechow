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
  
  // Enable debug logging in development
  DEBUG: __DEV__ ?? false,
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
    PROFILE: '/user',
    UPDATE_PROFILE: '/user',
    AVATAR: '/user/avatar',
    CHANGE_PASSWORD: '/auth/change-password',
    PREFERENCES: '/user/preferences',
    DELETE_ACCOUNT: '/auth/account',
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
    POPULAR: '/menus/featured',
  },
  
  // Cuisines
  CUISINES: {
    LIST: '/cuisines',
    DIETARY: '/dietary-preferences',
  },
  
  // Cart
  CART: {
    GET: '/cart',
    ADD: '/cart/items',
    UPDATE: (itemId: number) => `/cart/items/${itemId}`,
    REMOVE: (itemId: number) => `/cart/items/${itemId}`,
    CLEAR: '/cart',
    SUMMARY: '/cart/summary',
    GET_COUPON: '/cart/coupon',
    APPLY_COUPON: '/cart/coupon',
    REMOVE_COUPON: '/cart/coupon',
  },
  
  // Orders
  ORDERS: {
    LIST: '/orders',
    CREATE: '/orders',
    ACTIVE: '/orders/active',
    HISTORY: '/orders/history',
    TIME_SLOTS: '/orders/time-slots',
    DETAIL: (id: number) => `/orders/${id}`,
    CANCEL: (id: number) => `/orders/${id}/cancel`,
    TRACK: (id: number) => `/orders/${id}/track`,
    REORDER: (id: number) => `/orders/${id}/reorder`,
    RATE: (id: number) => `/orders/${id}/rate`,
  },
  
  // Delivery Tracking
  DELIVERY: {
    STATUS: (orderId: number) => `/delivery/${orderId}/status`,
    LOCATION: (orderId: number) => `/delivery/${orderId}/location`,
    SUBSCRIBE: (orderId: number) => `/delivery/${orderId}/subscribe`,
    ETA: (orderId: number) => `/delivery/${orderId}/eta`,
  },
  
  // Subscriptions (Customer subscribing to Chef/Vendor)
  SUBSCRIPTIONS: {
    LIST: '/subscriptions',
    SUBSCRIBE: (chefId: number) => `/subscriptions/chef/${chefId}`,
    UNSUBSCRIBE: (chefId: number) => `/subscriptions/chef/${chefId}`,
    CHECK: (chefId: number) => `/subscriptions/check/${chefId}`,
    SETTINGS: '/subscriptions/settings',
    CHEF_MENU_UPDATES: '/subscriptions/menu-updates',
  },
  
  // Payment
  PAYMENT: {
    METHODS: '/payment/methods',
    INITIALIZE: '/payment/initialize',
    VERIFY: '/payment/verify',
    HISTORY: '/payment/history',
  },
  
  // Reviews
  REVIEWS: {
    LIST: '/reviews',
    CREATE: '/reviews',
    DETAIL: (id: number) => `/reviews/${id}`,
    UPDATE: (id: number) => `/reviews/${id}`,
    DELETE: (id: number) => `/reviews/${id}`,
  },
  
  // Favorites
  FAVORITES: {
    LIST: '/favorites',
    ADD: (chefId: number) => `/favorites/${chefId}`,
    REMOVE: (chefId: number) => `/favorites/${chefId}`,
    CHECK: (chefId: number) => `/favorites/check/${chefId}`,
  },
  
  // Chef Menu Subscriptions (get notified when chef updates menu)
  MENU_SUBSCRIPTIONS: {
    LIST: '/menu-subscriptions',
    SUBSCRIBE: (chefId: number) => `/menu-subscriptions/${chefId}`,
    UNSUBSCRIBE: (chefId: number) => `/menu-subscriptions/${chefId}`,
    CHECK: (chefId: number) => `/menu-subscriptions/check/${chefId}`,
    UPDATES: '/menu-subscriptions/updates',
  },
  
  // Notifications
  NOTIFICATIONS: {
    LIST: '/notifications',
    UNREAD_COUNT: '/notifications/unread-count',
    SETTINGS: '/notifications/settings',
    UPDATE_SETTINGS: '/notifications/settings',
    MARK_READ: (id: number) => `/notifications/${id}/read`,
    MARK_ALL_READ: '/notifications/read-all',
    DELETE: (id: number) => `/notifications/${id}`,
  },
  
  // Vendor/Chef Endpoints
  VENDOR: {
    // Dashboard
    DASHBOARD: '/chef/dashboard',
    STATISTICS: '/chef/statistics',
    
    // Orders
    ORDERS: {
      LIST: '/chef/orders',
      DETAIL: (id: number) => `/chef/orders/${id}`,
      UPDATE_STATUS: (id: number) => `/chef/orders/${id}/status`,
    },
    
    // Menu Management
    MENUS: {
      LIST: '/chef/menus',
      CREATE: '/chef/menus',
      UPDATE: (id: number) => `/chef/menus/${id}`,
      DELETE: (id: number) => `/chef/menus/${id}`,
      TOGGLE_AVAILABILITY: (id: number) => `/chef/menus/${id}/toggle-availability`,
    },
    
    // Earnings
    EARNINGS: '/chef/earnings',
    
    // Reviews
    REVIEWS: '/chef/reviews',
    
    // Profile
    PROFILE: {
      GET: '/chef/profile',
      UPDATE: '/chef/profile',
      SETUP: '/chef/profile/setup',
    },
    
    // Business Settings
    BANK_DETAILS: '/chef/bank-details',
    OPERATING_HOURS: '/chef/operating-hours',
    
    // Documents & Verification
    DOCUMENTS: {
      LIST: '/chef/documents',
      UPLOAD: '/chef/documents',
    },
    REQUEST_VERIFICATION: '/chef/request-verification',
    
    // Availability
    TOGGLE_AVAILABILITY: '/chef/toggle-availability',
    
    // Subscribers
    SUBSCRIBERS: {
      LIST: '/chef/subscribers',
      COUNT: '/chef/subscribers/count',
      NOTIFY: '/chef/subscribers/notify',
    },
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
    
    // Payouts/Withdrawals
    PAYOUTS: {
      STATS: '/admin/payouts/stats',
      LIST: '/admin/payouts',
      DETAIL: (id: number) => `/admin/payouts/${id}`,
      APPROVE: (id: number) => `/admin/payouts/${id}/approve`,
      REJECT: (id: number) => `/admin/payouts/${id}/reject`,
    },
    
    // Activity Log
    ACTIVITY: '/admin/activity',
  },

  // Wallet
  WALLET: {
    BALANCE: '/wallet/balance',
    TRANSACTIONS: '/wallet/transactions',
    FUND: '/wallet/fund',
    VERIFY_FUNDING: '/wallet/verify-funding',
  },

  // Subscription Plans (Customer Tier Subscription)
  SUBSCRIPTION_PLANS: {
    LIST: '/subscriptions/plans',
    STATUS: '/subscriptions/status',
    SUBSCRIBE: '/subscriptions/subscribe',
    UPGRADE: '/subscriptions/upgrade',
    DOWNGRADE: '/subscriptions/downgrade',
    CANCEL: '/subscriptions/cancel',
    VERIFY_PAYMENT: '/subscriptions/verify-payment',
    VERIFY_UPGRADE_PAYMENT: '/subscriptions/verify-upgrade-payment',
  },
};
