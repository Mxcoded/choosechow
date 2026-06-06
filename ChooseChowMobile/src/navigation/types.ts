// Navigation Type Definitions
// Separated to avoid circular imports

export type AuthStackParamList = {
  Onboarding: undefined;
  Welcome: undefined;
  Login: undefined;
  Register: { role?: 'customer' | 'chef' };
  ForgotPassword: undefined;
};

// Customer/Foodie Navigation
export type MainStackParamList = {
  MainTabs: undefined;
  ChefDetail: { chefId: number };
  ChefList: { search?: string; cuisine?: string; sortBy?: string };
  MenuDetail: { menuId: number };
  Checkout: undefined;
  Payment: { authorizationUrl: string; reference: string };
  OrderDetail: { orderId: number };
  OrderTracking: { orderId: number };
  SubscriptionPlans: undefined;
  MySubscription: undefined;
};

export type MainTabParamList = {
  Home: undefined;
  Search: undefined;
  Cart: undefined;
  Orders: undefined;
  Profile: undefined;
};

// Vendor/Chef Navigation
export type VendorStackParamList = {
  VendorTabs: undefined;
  VendorMenu: undefined;
  VendorAddMenuItem: undefined;
  VendorEditMenuItem: { menuId: number };
  VendorOrders: undefined;
  VendorOrderDetail: { orderId: number };
  VendorEarnings: undefined;
  VendorProfile: undefined;
  VendorSettings: undefined;
  VendorSubscribers: undefined;
};

export type VendorTabParamList = {
  VendorDashboard: undefined;
  VendorOrders: undefined;
  VendorMenu: undefined;
  VendorProfile: undefined;
};

// Admin Navigation
export type AdminStackParamList = {
  AdminTabs: undefined;
  AdminUsers: undefined;
  AdminUserDetail: { userId: number };
  AdminVendors: undefined;
  AdminVendorDetail: { vendorId: number };
  AdminOrders: undefined;
  AdminOrderDetail: { orderId: number };
  AdminPayouts: undefined;
  AdminReports: undefined;
  AdminActivity: undefined;
  AdminSettings: undefined;
};

export type AdminTabParamList = {
  AdminDashboard: undefined;
  AdminUsers: undefined;
  AdminVendors: undefined;
  AdminSettings: undefined;
};
