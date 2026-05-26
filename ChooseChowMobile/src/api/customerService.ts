import { api, PaginatedResponse } from './client';
import apiClient from './client';
import { ENDPOINTS } from './config';

// ===================== TYPES =====================

// Addresses
export interface Address {
  id: number;
  label: string;
  address_line_1: string;
  address_line_2?: string;
  city: string;
  state?: string;
  postal_code?: string;
  country?: string;
  latitude?: number;
  longitude?: number;
  is_default: boolean;
  delivery_instructions?: string;
  created_at: string;
}

export interface CreateAddressData {
  label: string;
  address_line_1: string;
  address_line_2?: string;
  city: string;
  state?: string;
  postal_code?: string;
  country?: string;
  latitude?: number;
  longitude?: number;
  is_default?: boolean;
  delivery_instructions?: string;
}

export interface UpdateAddressData extends Partial<CreateAddressData> {}

// Notifications
export interface Notification {
  id: number;
  type: 'order' | 'promotion' | 'system' | 'review';
  title: string;
  message: string;
  data?: Record<string, any>;
  read_at?: string;
  created_at: string;
  time_ago: string;
}

export interface NotificationSettings {
  push_enabled: boolean;
  email_enabled: boolean;
  order_updates: boolean;
  promotions: boolean;
  new_from_favorites: boolean;
}

// Favorites
export interface FavoriteChef {
  id: number;
  chef_id: number;
  business_name: string;
  slug: string;
  city?: string;
  rating: number;
  total_reviews: number;
  is_online: boolean;
  profile_image_url?: string;
  cuisines?: Array<{ id: number; name: string }>;
  created_at: string;
}

// Reviews
export interface Review {
  id: number;
  user_id: number;
  chef_id: number;
  order_id?: number;
  rating: number;
  comment?: string;
  created_at: string;
  time_ago?: string;
  chef?: {
    id: number;
    business_name: string;
    profile_image_url?: string;
  };
}

export interface CreateReviewData {
  chef_id: number;
  order_id?: number;
  rating: number;
  comment?: string;
}

export interface UpdateReviewData {
  rating?: number;
  comment?: string;
}

// Payment
export interface PaymentMethod {
  id: string;
  type: 'card' | 'bank' | 'paystack';
  name: string;
  last_four?: string;
  is_default: boolean;
}

export interface PaymentIntent {
  reference: string;
  authorization_url: string;
  access_code: string;
  amount: number;
}

export interface PaymentHistory {
  id: number;
  reference: string;
  amount: number;
  status: 'pending' | 'success' | 'failed';
  order_id?: number;
  created_at: string;
}

// User Profile
export interface UserProfile {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  phone?: string;
  avatar_url?: string;
  email_verified_at?: string;
  referral_code?: string;
}

export interface UpdateProfileData {
  first_name?: string;
  last_name?: string;
  phone?: string;
}

export interface UserPreferences {
  dietary_preferences?: number[];
  notification_settings?: NotificationSettings;
}

// ===================== SERVICE =====================

export const customerService = {
  // ==================== USER PROFILE ====================

  /**
   * Get current user profile
   */
  getProfile: async (): Promise<UserProfile> => {
    const response = await api.get<UserProfile>(ENDPOINTS.USER.PROFILE);
    return response.data.data;
  },

  /**
   * Update user profile
   */
  updateProfile: async (data: UpdateProfileData): Promise<UserProfile> => {
    const response = await api.put<UserProfile>(ENDPOINTS.USER.UPDATE_PROFILE, data);
    return response.data.data;
  },

  /**
   * Update user avatar
   */
  updateAvatar: async (avatar: any): Promise<{ avatar_url: string }> => {
    const formData = new FormData();
    formData.append('avatar', avatar);

    const response = await apiClient.post<{ success: boolean; data: { avatar_url: string } }>(
      ENDPOINTS.USER.AVATAR,
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
   * Update user preferences
   */
  updatePreferences: async (preferences: UserPreferences): Promise<UserPreferences> => {
    const response = await api.put<UserPreferences>(ENDPOINTS.USER.PREFERENCES, preferences);
    return response.data.data;
  },

  // ==================== ADDRESSES ====================

  /**
   * Get user's addresses
   */
  getAddresses: async (): Promise<Address[]> => {
    const response = await api.get<Address[]>(ENDPOINTS.ADDRESSES.LIST);
    return response.data.data;
  },

  /**
   * Create a new address
   */
  createAddress: async (data: CreateAddressData): Promise<Address> => {
    const response = await api.post<Address>(ENDPOINTS.ADDRESSES.CREATE, data);
    return response.data.data;
  },

  /**
   * Update an address
   */
  updateAddress: async (addressId: number, data: UpdateAddressData): Promise<Address> => {
    const response = await api.put<Address>(ENDPOINTS.ADDRESSES.UPDATE(addressId), data);
    return response.data.data;
  },

  /**
   * Delete an address
   */
  deleteAddress: async (addressId: number): Promise<void> => {
    await api.delete(ENDPOINTS.ADDRESSES.DELETE(addressId));
  },

  /**
   * Set address as default
   */
  setDefaultAddress: async (addressId: number): Promise<Address> => {
    const response = await api.post<Address>(ENDPOINTS.ADDRESSES.SET_DEFAULT(addressId));
    return response.data.data;
  },

  // ==================== NOTIFICATIONS ====================

  /**
   * Get user's notifications
   */
  getNotifications: async (page = 1): Promise<PaginatedResponse<Notification>> => {
    const response = await api.get<Notification[]>(ENDPOINTS.NOTIFICATIONS.LIST, { page });
    return response.data as unknown as PaginatedResponse<Notification>;
  },

  /**
   * Get unread notification count
   */
  getUnreadCount: async (): Promise<{ count: number }> => {
    const response = await api.get<{ count: number }>(ENDPOINTS.NOTIFICATIONS.UNREAD_COUNT);
    return response.data.data;
  },

  /**
   * Mark notification as read
   */
  markNotificationAsRead: async (notificationId: number): Promise<Notification> => {
    const response = await api.post<Notification>(ENDPOINTS.NOTIFICATIONS.MARK_READ(notificationId));
    return response.data.data;
  },

  /**
   * Mark all notifications as read
   */
  markAllNotificationsAsRead: async (): Promise<{ message: string }> => {
    const response = await api.post<{ message: string }>(ENDPOINTS.NOTIFICATIONS.MARK_ALL_READ);
    return response.data.data;
  },

  /**
   * Delete a notification
   */
  deleteNotification: async (notificationId: number): Promise<void> => {
    await api.delete(ENDPOINTS.NOTIFICATIONS.DELETE(notificationId));
  },

  /**
   * Get notification settings
   */
  getNotificationSettings: async (): Promise<NotificationSettings> => {
    const response = await api.get<NotificationSettings>(ENDPOINTS.NOTIFICATIONS.SETTINGS);
    return response.data.data;
  },

  /**
   * Update notification settings
   */
  updateNotificationSettings: async (settings: Partial<NotificationSettings>): Promise<NotificationSettings> => {
    const response = await api.put<NotificationSettings>(ENDPOINTS.NOTIFICATIONS.UPDATE_SETTINGS, settings);
    return response.data.data;
  },

  // ==================== FAVORITES ====================

  /**
   * Get user's favorite chefs
   */
  getFavorites: async (): Promise<FavoriteChef[]> => {
    const response = await api.get<FavoriteChef[]>(ENDPOINTS.FAVORITES.LIST);
    return response.data.data;
  },

  /**
   * Add chef to favorites
   */
  addFavorite: async (chefId: number): Promise<FavoriteChef> => {
    const response = await api.post<FavoriteChef>(ENDPOINTS.FAVORITES.ADD(chefId));
    return response.data.data;
  },

  /**
   * Remove chef from favorites
   */
  removeFavorite: async (chefId: number): Promise<void> => {
    await api.delete(ENDPOINTS.FAVORITES.REMOVE(chefId));
  },

  /**
   * Check if chef is in favorites
   */
  checkFavorite: async (chefId: number): Promise<{ is_favorite: boolean }> => {
    const response = await api.get<{ is_favorite: boolean }>(ENDPOINTS.FAVORITES.CHECK(chefId));
    return response.data.data;
  },

  // ==================== REVIEWS ====================

  /**
   * Get user's reviews
   */
  getReviews: async (page = 1): Promise<PaginatedResponse<Review>> => {
    const response = await api.get<Review[]>(ENDPOINTS.REVIEWS.LIST, { page });
    return response.data as unknown as PaginatedResponse<Review>;
  },

  /**
   * Create a review
   */
  createReview: async (data: CreateReviewData): Promise<Review> => {
    const response = await api.post<Review>(ENDPOINTS.REVIEWS.CREATE, data);
    return response.data.data;
  },

  /**
   * Update a review
   */
  updateReview: async (reviewId: number, data: UpdateReviewData): Promise<Review> => {
    const response = await api.put<Review>(ENDPOINTS.REVIEWS.UPDATE(reviewId), data);
    return response.data.data;
  },

  /**
   * Delete a review
   */
  deleteReview: async (reviewId: number): Promise<void> => {
    await api.delete(ENDPOINTS.REVIEWS.DELETE(reviewId));
  },

  // ==================== PAYMENT ====================

  /**
   * Get available payment methods
   */
  getPaymentMethods: async (): Promise<PaymentMethod[]> => {
    const response = await api.get<PaymentMethod[]>(ENDPOINTS.PAYMENT.METHODS);
    return response.data.data;
  },

  /**
   * Initialize a payment (Paystack)
   */
  initializePayment: async (orderId: number): Promise<PaymentIntent> => {
    const response = await api.post<PaymentIntent>(ENDPOINTS.PAYMENT.INITIALIZE, {
      order_id: orderId,
    });
    return response.data.data;
  },

  /**
   * Verify a payment
   */
  verifyPayment: async (reference: string): Promise<{ status: string; order_id?: number }> => {
    const response = await api.post<{ status: string; order_id?: number }>(ENDPOINTS.PAYMENT.VERIFY, {
      reference,
    });
    return response.data.data;
  },

  /**
   * Get payment history
   */
  getPaymentHistory: async (page = 1): Promise<PaginatedResponse<PaymentHistory>> => {
    const response = await api.get<PaymentHistory[]>(ENDPOINTS.PAYMENT.HISTORY, { page });
    return response.data as unknown as PaginatedResponse<PaymentHistory>;
  },
};

export default customerService;
