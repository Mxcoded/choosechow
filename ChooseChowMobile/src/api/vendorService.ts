import { api, PaginatedResponse } from './client';
import apiClient from './client';
import { ENDPOINTS } from './config';

// ===================== TYPES =====================

export interface VendorStats {
  today_orders: number;
  today_earnings: number;
  pending_orders: number;
  completed_orders: number;
  weekly_orders: number;
  weekly_earnings: number;
  monthly_orders: number;
  monthly_earnings: number;
  total_orders: number;
  total_earnings: number;
  rating: number;
  total_reviews: number;
  menu_items: number;
  is_online: boolean;
}

export interface VendorProfile {
  id: number;
  user_id: number;
  business_name: string;
  slug: string;
  bio?: string;
  city?: string;
  is_online: boolean;
  is_verified: boolean;
  is_featured: boolean;
  rating: number;
  total_reviews: number;
  total_orders: number;
  profile_image_url?: string;
  cover_image_url?: string;
  // Detailed fields
  kitchen_address?: string;
  minimum_order?: number;
  delivery_fee?: number;
  delivery_radius_km?: number;
  operating_hours?: OperatingHours;
  years_of_experience?: number;
  verification_status?: 'pending' | 'approved' | 'rejected';
  bank_name?: string;
  account_number?: string; // Masked
  account_name?: string;
  cuisines?: Array<{ id: number; name: string }>;
}

export interface OperatingHours {
  monday?: DayHours;
  tuesday?: DayHours;
  wednesday?: DayHours;
  thursday?: DayHours;
  friday?: DayHours;
  saturday?: DayHours;
  sunday?: DayHours;
}

export interface DayHours {
  open: string;
  close: string;
  closed?: boolean;
}

export interface VendorOrder {
  id: number;
  order_number: string;
  status: 'pending' | 'preparing' | 'ready' | 'completed' | 'cancelled';
  payment_status: 'pending' | 'paid' | 'failed' | 'refunded';
  subtotal: number;
  delivery_fee: number;
  total_amount: number;
  delivery_type: 'asap' | 'scheduled';
  delivery_time_display?: string;
  items_count: number;
  created_at: string;
  time_ago: string;
  customer: {
    id: number;
    name: string;
    phone?: string;
  } | null;
  // Detailed fields
  delivery_address?: string;
  notes?: string;
  scheduled_date?: string;
  scheduled_time_slot?: string;
  items?: VendorOrderItem[];
}

export interface VendorOrderItem {
  id: number;
  menu_id: number;
  name: string;
  quantity: number;
  price: number;
  total: number;
  notes?: string;
}

export interface VendorMenuItem {
  id: number;
  name: string;
  slug: string;
  description?: string;
  price: number;
  category?: string;
  preparation_time?: number;
  is_available: boolean;
  is_featured: boolean;
  image_url?: string;
  cuisines: Array<{ id: number; name: string }>;
  dietary_preferences: Array<{ id: number; name: string }>;
  created_at: string;
}

export interface CreateMenuItemData {
  name: string;
  description?: string;
  price: number;
  category?: string;
  preparation_time?: number;
  is_available?: boolean;
  is_featured?: boolean;
  image?: any; // File for FormData
  cuisine_ids?: number[];
  dietary_preference_ids?: number[];
}

export interface UpdateMenuItemData extends Partial<CreateMenuItemData> {}

export interface VendorEarnings {
  period: string;
  total_earnings: number;
  total_orders: number;
  average_order_value: number;
  pending_payout: number;
  daily_breakdown: Array<{
    date: string;
    day: string;
    earnings: number;
    orders: number;
  }>;
}

export interface VendorStatistics {
  period_days: number;
  orders_over_time: Array<{
    date: string;
    orders: number;
    revenue: number;
  }>;
  status_breakdown: Record<string, number>;
  top_selling_items: Array<{
    menu_id: number;
    name: string;
    total_quantity: number;
    total_revenue: number;
  }>;
  peak_hours: Array<{
    hour: number;
    orders: number;
  }>;
  customer_stats: {
    total_customers: number;
    repeat_customers: number;
    repeat_rate: number;
  };
  averages: {
    orders_per_day: number;
    revenue_per_day: number;
  };
}

export interface VendorReview {
  id: number;
  rating: number;
  comment?: string;
  created_at: string;
  time_ago: string;
  customer: {
    id: number;
    name: string;
    avatar?: string;
  } | null;
}

export interface VendorReviewsResponse {
  data: VendorReview[];
  summary: {
    average_rating: number;
    total_reviews: number;
    rating_distribution: Record<number, number>;
  };
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

export interface VendorDocument {
  id: string;
  type: 'id_card' | 'business_license' | 'food_handler_certificate' | 'other';
  file_url: string;
  description?: string;
  uploaded_at: string;
  status: 'pending' | 'approved' | 'rejected';
}

export interface VendorDashboard {
  stats: VendorStats;
  recent_orders: VendorOrder[];
  pending_orders: VendorOrder[];
  profile: VendorProfile | null;
}

// ===================== SERVICE =====================

export const vendorService = {
  // ==================== DASHBOARD ====================
  
  /**
   * Get vendor dashboard data including stats, orders, and profile
   */
  getDashboard: async (): Promise<VendorDashboard> => {
    const response = await api.get<VendorDashboard>(ENDPOINTS.VENDOR.DASHBOARD);
    return response.data.data;
  },

  /**
   * Get detailed statistics for analytics
   */
  getStatistics: async (period = 30): Promise<VendorStatistics> => {
    const response = await api.get<VendorStatistics>(ENDPOINTS.VENDOR.STATISTICS, { period });
    return response.data.data;
  },

  // ==================== ORDERS ====================

  /**
   * Get vendor's orders with optional filters
   */
  getOrders: async (params?: {
    page?: number;
    status?: string;
    date?: string;
    from?: string;
    to?: string;
    search?: string;
    per_page?: number;
  }): Promise<PaginatedResponse<VendorOrder>> => {
    const response = await api.get<VendorOrder[]>(ENDPOINTS.VENDOR.ORDERS.LIST, params);
    return response.data as unknown as PaginatedResponse<VendorOrder>;
  },

  /**
   * Get single order details
   */
  getOrder: async (orderId: number): Promise<VendorOrder> => {
    const response = await api.get<VendorOrder>(ENDPOINTS.VENDOR.ORDERS.DETAIL(orderId));
    return response.data.data;
  },

  /**
   * Update order status
   */
  updateOrderStatus: async (
    orderId: number, 
    status: 'pending' | 'preparing' | 'ready' | 'completed' | 'cancelled'
  ): Promise<VendorOrder> => {
    const response = await api.put<VendorOrder>(
      ENDPOINTS.VENDOR.ORDERS.UPDATE_STATUS(orderId), 
      { status }
    );
    return response.data.data;
  },

  // ==================== MENU MANAGEMENT ====================

  /**
   * Get vendor's menu items
   */
  getMenus: async (params?: {
    page?: number;
    is_available?: boolean;
    category?: string;
    search?: string;
    per_page?: number;
  }): Promise<PaginatedResponse<VendorMenuItem>> => {
    const response = await api.get<VendorMenuItem[]>(ENDPOINTS.VENDOR.MENUS.LIST, params);
    return response.data as unknown as PaginatedResponse<VendorMenuItem>;
  },

  /**
   * Create a new menu item
   */
  createMenu: async (data: CreateMenuItemData): Promise<VendorMenuItem> => {
    // Use FormData for file upload support
    const formData = new FormData();
    
    Object.entries(data).forEach(([key, value]) => {
      if (value === undefined || value === null) return;
      
      if (key === 'cuisine_ids' || key === 'dietary_preference_ids') {
        // Arrays need special handling
        (value as number[]).forEach((id, index) => {
          formData.append(`${key}[${index}]`, String(id));
        });
      } else if (key === 'image' && value) {
        // File upload
        formData.append('image', value);
      } else {
        formData.append(key, String(value));
      }
    });

    const response = await apiClient.post<{ success: boolean; data: VendorMenuItem }>(
      ENDPOINTS.VENDOR.MENUS.CREATE,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    );
    return response.data.data;
  },

  /**
   * Update a menu item
   */
  updateMenu: async (menuId: number, data: UpdateMenuItemData): Promise<VendorMenuItem> => {
    // Use FormData for file upload support
    const formData = new FormData();
    
    Object.entries(data).forEach(([key, value]) => {
      if (value === undefined || value === null) return;
      
      if (key === 'cuisine_ids' || key === 'dietary_preference_ids') {
        (value as number[]).forEach((id, index) => {
          formData.append(`${key}[${index}]`, String(id));
        });
      } else if (key === 'image' && value) {
        formData.append('image', value);
      } else {
        formData.append(key, String(value));
      }
    });

    // PUT with FormData requires special handling
    formData.append('_method', 'PUT');
    
    const response = await apiClient.post<{ success: boolean; data: VendorMenuItem }>(
      ENDPOINTS.VENDOR.MENUS.UPDATE(menuId),
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    );
    return response.data.data;
  },

  /**
   * Delete a menu item
   */
  deleteMenu: async (menuId: number): Promise<void> => {
    await api.delete(ENDPOINTS.VENDOR.MENUS.DELETE(menuId));
  },

  /**
   * Toggle menu item availability
   */
  toggleMenuAvailability: async (menuId: number): Promise<VendorMenuItem> => {
    const response = await api.post<VendorMenuItem>(
      ENDPOINTS.VENDOR.MENUS.TOGGLE_AVAILABILITY(menuId)
    );
    return response.data.data;
  },

  // ==================== EARNINGS ====================

  /**
   * Get earnings summary
   */
  getEarnings: async (period: 'day' | 'week' | 'month' | 'year' | 'all' = 'month'): Promise<VendorEarnings> => {
    const response = await api.get<VendorEarnings>(ENDPOINTS.VENDOR.EARNINGS, { period });
    return response.data.data;
  },

  // ==================== REVIEWS ====================

  /**
   * Get vendor's reviews
   */
  getReviews: async (params?: {
    page?: number;
    rating?: number;
    sort?: 'latest' | 'highest' | 'lowest';
    per_page?: number;
  }): Promise<VendorReviewsResponse> => {
    const response = await api.get<VendorReviewsResponse>(ENDPOINTS.VENDOR.REVIEWS, params);
    return response.data.data;
  },

  // ==================== PROFILE ====================

  /**
   * Get vendor profile
   */
  getProfile: async (): Promise<VendorProfile> => {
    const response = await api.get<VendorProfile>(ENDPOINTS.VENDOR.PROFILE.GET);
    return response.data.data;
  },

  /**
   * Update vendor profile
   */
  updateProfile: async (data: {
    business_name?: string;
    bio?: string;
    kitchen_address?: string;
    city?: string;
    minimum_order?: number;
    delivery_fee?: number;
    delivery_radius_km?: number;
    operating_hours?: OperatingHours;
    profile_image?: any;
    cover_image?: any;
    bank_name?: string;
    account_number?: string;
    account_name?: string;
    cuisine_ids?: number[];
  }): Promise<VendorProfile> => {
    const formData = new FormData();
    
    Object.entries(data).forEach(([key, value]) => {
      if (value === undefined || value === null) return;
      
      if (key === 'operating_hours') {
        formData.append(key, JSON.stringify(value));
      } else if (key === 'cuisine_ids') {
        (value as number[]).forEach((id, index) => {
          formData.append(`cuisine_ids[${index}]`, String(id));
        });
      } else if (key === 'profile_image' || key === 'cover_image') {
        if (value) formData.append(key, value);
      } else {
        formData.append(key, String(value));
      }
    });

    const response = await apiClient.put<{ success: boolean; data: VendorProfile }>(
      ENDPOINTS.VENDOR.PROFILE.UPDATE,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    );
    return response.data.data;
  },

  /**
   * Setup initial vendor profile (for new vendors)
   */
  setupProfile: async (data: {
    business_name: string;
    bio?: string;
    kitchen_address: string;
    city: string;
    years_of_experience?: number;
    minimum_order?: number;
    delivery_fee?: number;
    delivery_radius_km?: number;
    operating_hours?: OperatingHours;
    profile_image?: any;
    cover_image?: any;
    bank_name?: string;
    account_number?: string;
    account_name?: string;
    cuisine_ids?: number[];
  }): Promise<VendorProfile> => {
    const formData = new FormData();
    
    Object.entries(data).forEach(([key, value]) => {
      if (value === undefined || value === null) return;
      
      if (key === 'operating_hours') {
        formData.append(key, JSON.stringify(value));
      } else if (key === 'cuisine_ids') {
        (value as number[]).forEach((id, index) => {
          formData.append(`cuisine_ids[${index}]`, String(id));
        });
      } else if (key === 'profile_image' || key === 'cover_image') {
        if (value) formData.append(key, value);
      } else {
        formData.append(key, String(value));
      }
    });

    const response = await apiClient.post<{ success: boolean; data: VendorProfile }>(
      ENDPOINTS.VENDOR.PROFILE.SETUP,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    );
    return response.data.data;
  },

  // ==================== BUSINESS SETTINGS ====================

  /**
   * Update bank details
   */
  updateBankDetails: async (data: {
    bank_name: string;
    account_number: string;
    account_name: string;
  }): Promise<{ bank_name: string; account_number: string; account_name: string }> => {
    const response = await api.put<{ bank_name: string; account_number: string; account_name: string }>(
      ENDPOINTS.VENDOR.BANK_DETAILS,
      data
    );
    return response.data.data;
  },

  /**
   * Update operating hours
   */
  updateOperatingHours: async (operating_hours: OperatingHours): Promise<{ operating_hours: OperatingHours }> => {
    const response = await api.put<{ operating_hours: OperatingHours }>(
      ENDPOINTS.VENDOR.OPERATING_HOURS,
      { operating_hours }
    );
    return response.data.data;
  },

  // ==================== DOCUMENTS & VERIFICATION ====================

  /**
   * Get uploaded documents
   */
  getDocuments: async (): Promise<VendorDocument[]> => {
    const response = await api.get<VendorDocument[]>(ENDPOINTS.VENDOR.DOCUMENTS.LIST);
    return response.data.data;
  },

  /**
   * Upload a verification document
   */
  uploadDocument: async (data: {
    document_type: 'id_card' | 'business_license' | 'food_handler_certificate' | 'other';
    document: any; // File
    description?: string;
  }): Promise<{ document_type: string; file_url: string; status: string }> => {
    const formData = new FormData();
    formData.append('document_type', data.document_type);
    formData.append('document', data.document);
    if (data.description) {
      formData.append('description', data.description);
    }

    const response = await apiClient.post<{ 
      success: boolean; 
      data: { document_type: string; file_url: string; status: string } 
    }>(
      ENDPOINTS.VENDOR.DOCUMENTS.UPLOAD,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    );
    return response.data.data;
  },

  /**
   * Request profile verification
   */
  requestVerification: async (): Promise<{ verification_status: string }> => {
    const response = await api.post<{ verification_status: string }>(
      ENDPOINTS.VENDOR.REQUEST_VERIFICATION
    );
    return response.data.data;
  },

  // ==================== AVAILABILITY ====================

  /**
   * Toggle online/offline status
   */
  toggleAvailability: async (): Promise<{ is_online: boolean }> => {
    const response = await api.post<{ is_online: boolean }>(
      ENDPOINTS.VENDOR.TOGGLE_AVAILABILITY
    );
    return response.data.data;
  },
};

export default vendorService;
