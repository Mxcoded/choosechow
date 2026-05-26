import { api, PaginatedResponse } from './client';
import { ENDPOINTS } from './config';

// Admin Types
export interface AdminStats {
  total_users: number;
  total_vendors: number;
  total_orders: number;
  total_revenue: number;
  pending_approvals: number;
  active_vendors: number;
  today_orders: number;
  today_revenue: number;
}

export interface AdminUser {
  id: number;
  first_name: string;
  last_name: string;
  name?: string;
  email: string;
  phone?: string;
  avatar_url?: string;
  role: string;
  roles?: string[];
  status: 'active' | 'inactive' | 'suspended';
  email_verified_at?: string;
  created_at: string;
  orders_count?: number;
  total_spent?: number;
}

export interface AdminVendor {
  id: number;
  user_id: number;
  business_name: string;
  email: string;
  phone?: string;
  logo_url?: string;
  status: 'pending' | 'approved' | 'rejected' | 'suspended';
  is_verified: boolean;
  rating: number;
  total_orders: number;
  total_revenue: number;
  created_at: string;
  user?: {
    first_name: string;
    last_name: string;
    email: string;
  };
}

export interface AdminOrder {
  id: number;
  order_number: string;
  user: {
    id: number;
    name: string;
    email: string;
  };
  vendor: {
    id: number;
    business_name: string;
  };
  status: string;
  payment_status: string;
  total: number;
  items_count: number;
  created_at: string;
}

export interface ActivityLog {
  id: number;
  type: 'user' | 'vendor' | 'order' | 'payment' | 'system';
  action: string;
  description: string;
  user_name?: string;
  created_at: string;
}

export interface ReportData {
  labels: string[];
  data: number[];
  total: number;
  change_percentage: number;
}

export interface PayoutStats {
  total_requests: number;
  pending_count: number;
  pending_amount: number;
  approved_count: number;
  approved_amount: number;
  rejected_count: number;
  rejected_amount: number;
}

export interface AdminPayout {
  id: number;
  reference: string;
  amount: number;
  status: 'pending' | 'approved' | 'rejected';
  vendor_id: number;
  vendor_name: string;
  vendor_email: string;
  bank_name?: string;
  account_number?: string;
  rejection_reason?: string;
  created_at: string;
  updated_at: string;
  bank_details?: {
    bank_name: string;
    account_number: string;
    account_name: string;
  };
}

// Helper to extract data from response (handles both {data: {...}} and direct response)
const extractData = <T>(responseData: any): T => {
  // If response has a nested data property, use it; otherwise use the response directly
  return responseData?.data !== undefined ? responseData.data : responseData;
};

// Admin Service
export const adminService = {
  // Dashboard & Stats
  getDashboard: async (): Promise<{
    stats: AdminStats;
    pending_vendors: AdminVendor[];
    recent_orders: AdminOrder[];
    recent_activity: ActivityLog[];
  }> => {
    const response = await api.get<{
      stats: AdminStats;
      pending_vendors: AdminVendor[];
      recent_orders: AdminOrder[];
      recent_activity: ActivityLog[];
    }>(ENDPOINTS.ADMIN.DASHBOARD);
    return extractData(response.data);
  },

  getStats: async (): Promise<AdminStats> => {
    const response = await api.get<AdminStats>(ENDPOINTS.ADMIN.STATS);
    return extractData(response.data);
  },

  // Users Management
  getUsers: async (params?: {
    page?: number;
    search?: string;
    status?: string;
    role?: string;
  }): Promise<PaginatedResponse<AdminUser>> => {
    const response = await api.get<AdminUser[]>(ENDPOINTS.ADMIN.USERS.LIST, params);
    return response.data as unknown as PaginatedResponse<AdminUser>;
  },

  getUser: async (id: number): Promise<AdminUser> => {
    const response = await api.get<AdminUser>(ENDPOINTS.ADMIN.USERS.DETAIL(id));
    return response.data.data;
  },

  updateUser: async (id: number, data: Partial<AdminUser>): Promise<AdminUser> => {
    const response = await api.put<AdminUser>(ENDPOINTS.ADMIN.USERS.UPDATE(id), data);
    return response.data.data;
  },

  deleteUser: async (id: number): Promise<void> => {
    await api.delete(ENDPOINTS.ADMIN.USERS.DELETE(id));
  },

  toggleUserStatus: async (id: number): Promise<AdminUser> => {
    const response = await api.post<AdminUser>(ENDPOINTS.ADMIN.USERS.TOGGLE_STATUS(id));
    return response.data.data;
  },

  // Vendors Management
  getVendors: async (params?: {
    page?: number;
    search?: string;
    status?: string;
  }): Promise<PaginatedResponse<AdminVendor>> => {
    const response = await api.get<AdminVendor[]>(ENDPOINTS.ADMIN.VENDORS.LIST, params);
    return response.data as unknown as PaginatedResponse<AdminVendor>;
  },

  getPendingVendors: async (): Promise<AdminVendor[]> => {
    const response = await api.get<AdminVendor[]>(ENDPOINTS.ADMIN.VENDORS.PENDING);
    return response.data.data;
  },

  getVendor: async (id: number): Promise<AdminVendor> => {
    const response = await api.get<AdminVendor>(ENDPOINTS.ADMIN.VENDORS.DETAIL(id));
    return response.data.data;
  },

  approveVendor: async (id: number): Promise<AdminVendor> => {
    const response = await api.post<AdminVendor>(ENDPOINTS.ADMIN.VENDORS.APPROVE(id));
    return response.data.data;
  },

  rejectVendor: async (id: number, reason?: string): Promise<AdminVendor> => {
    const response = await api.post<AdminVendor>(ENDPOINTS.ADMIN.VENDORS.REJECT(id), { reason });
    return response.data.data;
  },

  suspendVendor: async (id: number, reason?: string): Promise<AdminVendor> => {
    const response = await api.post<AdminVendor>(ENDPOINTS.ADMIN.VENDORS.SUSPEND(id), { reason });
    return response.data.data;
  },

  activateVendor: async (id: number): Promise<AdminVendor> => {
    const response = await api.post<AdminVendor>(ENDPOINTS.ADMIN.VENDORS.ACTIVATE(id));
    return response.data.data;
  },

  // Orders Management
  getOrders: async (params?: {
    page?: number;
    search?: string;
    status?: string;
    date_from?: string;
    date_to?: string;
  }): Promise<PaginatedResponse<AdminOrder>> => {
    const response = await api.get<AdminOrder[]>(ENDPOINTS.ADMIN.ORDERS.LIST, params);
    return response.data as unknown as PaginatedResponse<AdminOrder>;
  },

  getOrder: async (id: number): Promise<AdminOrder> => {
    const response = await api.get<AdminOrder>(ENDPOINTS.ADMIN.ORDERS.DETAIL(id));
    return response.data.data;
  },

  updateOrderStatus: async (id: number, status: string): Promise<AdminOrder> => {
    const response = await api.post<AdminOrder>(ENDPOINTS.ADMIN.ORDERS.UPDATE_STATUS(id), { status });
    return response.data.data;
  },

  // Reports
  getReportsOverview: async (params?: { period?: string }): Promise<{
    period: string;
    total_revenue: number;
    total_orders: number;
    average_order_value: number;
    new_users: number;
    new_vendors: number;
  }> => {
    const response = await api.get(ENDPOINTS.ADMIN.REPORTS.OVERVIEW, params);
    return extractData(response.data);
  },

  getRevenueReport: async (params?: { period?: string }): Promise<ReportData> => {
    const response = await api.get<ReportData>(ENDPOINTS.ADMIN.REPORTS.REVENUE, params);
    return extractData(response.data);
  },

  getOrdersReport: async (params?: { period?: string }): Promise<ReportData> => {
    const response = await api.get<ReportData>(ENDPOINTS.ADMIN.REPORTS.ORDERS, params);
    return extractData(response.data);
  },

  getUsersReport: async (params?: { period?: string }): Promise<ReportData> => {
    const response = await api.get<ReportData>(ENDPOINTS.ADMIN.REPORTS.USERS, params);
    return extractData(response.data);
  },

  // Activity Log
  getActivityLog: async (params?: {
    page?: number;
    type?: string;
  }): Promise<PaginatedResponse<ActivityLog>> => {
    const response = await api.get<ActivityLog[]>(ENDPOINTS.ADMIN.ACTIVITY, params);
    return response.data as unknown as PaginatedResponse<ActivityLog>;
  },

  // Payouts/Withdrawals
  getPayoutStats: async (): Promise<PayoutStats> => {
    const response = await api.get<PayoutStats>(ENDPOINTS.ADMIN.PAYOUTS.STATS);
    return extractData(response.data);
  },

  getPayouts: async (params?: {
    page?: number;
    search?: string;
    status?: string;
  }): Promise<PaginatedResponse<AdminPayout>> => {
    const response = await api.get<AdminPayout[]>(ENDPOINTS.ADMIN.PAYOUTS.LIST, params);
    return response.data as unknown as PaginatedResponse<AdminPayout>;
  },

  getPayout: async (id: number): Promise<AdminPayout> => {
    const response = await api.get<AdminPayout>(ENDPOINTS.ADMIN.PAYOUTS.DETAIL(id));
    return extractData(response.data);
  },

  approvePayout: async (id: number): Promise<AdminPayout> => {
    const response = await api.post<AdminPayout>(ENDPOINTS.ADMIN.PAYOUTS.APPROVE(id));
    return extractData(response.data);
  },

  rejectPayout: async (id: number, reason?: string): Promise<AdminPayout> => {
    const response = await api.post<AdminPayout>(ENDPOINTS.ADMIN.PAYOUTS.REJECT(id), { reason });
    return extractData(response.data);
  },
};

export default adminService;
