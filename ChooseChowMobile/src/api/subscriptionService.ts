import { api, PaginatedResponse } from './client';
import { ENDPOINTS } from './config';

// ===================== TYPES =====================

export interface ChefSubscription {
  id: number;
  chef_id: number;
  user_id: number;
  notify_new_menu: boolean;
  notify_promotions: boolean;
  notify_availability: boolean;
  created_at: string;
  chef: {
    id: number;
    business_name: string;
    slug: string;
    profile_image_url?: string;
    rating: number;
    is_online: boolean;
    city?: string;
  };
}

export interface SubscriptionSettings {
  notify_new_menu: boolean;
  notify_promotions: boolean;
  notify_availability: boolean;
  email_notifications: boolean;
  push_notifications: boolean;
}

export interface MenuUpdate {
  id: number;
  chef_id: number;
  menu_id: number;
  type: 'new' | 'updated' | 'promotion' | 'back_in_stock';
  title: string;
  message: string;
  menu: {
    id: number;
    name: string;
    price: number;
    image_url?: string;
    is_available: boolean;
  };
  chef: {
    id: number;
    business_name: string;
    profile_image_url?: string;
  };
  created_at: string;
  read_at?: string;
}

export interface Subscriber {
  id: number;
  user_id: number;
  name: string;
  email: string;
  avatar_url?: string;
  subscribed_at: string;
  notify_new_menu: boolean;
  notify_promotions: boolean;
}

// ===================== SERVICE =====================

export const subscriptionService = {
  // ==================== CUSTOMER SUBSCRIPTIONS ====================

  /**
   * Get list of chefs the customer is subscribed to
   */
  getSubscriptions: async (params?: {
    page?: number;
    per_page?: number;
  }): Promise<PaginatedResponse<ChefSubscription>> => {
    const response = await api.get<ChefSubscription[]>(
      ENDPOINTS.SUBSCRIPTIONS.LIST,
      params
    );
    return response.data as unknown as PaginatedResponse<ChefSubscription>;
  },

  /**
   * Subscribe to a chef's updates
   */
  subscribeToChef: async (
    chefId: number,
    settings?: Partial<SubscriptionSettings>
  ): Promise<ChefSubscription> => {
    const response = await api.post<ChefSubscription>(
      ENDPOINTS.SUBSCRIPTIONS.SUBSCRIBE(chefId),
      settings
    );
    return response.data.data;
  },

  /**
   * Unsubscribe from a chef
   */
  unsubscribeFromChef: async (chefId: number): Promise<{ message: string }> => {
    const response = await api.delete<{ message: string }>(
      ENDPOINTS.SUBSCRIPTIONS.UNSUBSCRIBE(chefId)
    );
    return response.data.data;
  },

  /**
   * Check if subscribed to a chef
   */
  isSubscribed: async (chefId: number): Promise<{ subscribed: boolean; subscription?: ChefSubscription }> => {
    const response = await api.get<{ subscribed: boolean; subscription?: ChefSubscription }>(
      ENDPOINTS.SUBSCRIPTIONS.CHECK(chefId)
    );
    return response.data.data;
  },

  /**
   * Get subscription notification settings
   */
  getSettings: async (): Promise<SubscriptionSettings> => {
    const response = await api.get<SubscriptionSettings>(
      ENDPOINTS.SUBSCRIPTIONS.SETTINGS
    );
    return response.data.data;
  },

  /**
   * Update subscription notification settings
   */
  updateSettings: async (settings: Partial<SubscriptionSettings>): Promise<SubscriptionSettings> => {
    const response = await api.put<SubscriptionSettings>(
      ENDPOINTS.SUBSCRIPTIONS.SETTINGS,
      settings
    );
    return response.data.data;
  },

  /**
   * Get recent menu updates from subscribed chefs
   */
  getMenuUpdates: async (params?: {
    page?: number;
    per_page?: number;
    unread_only?: boolean;
  }): Promise<PaginatedResponse<MenuUpdate>> => {
    const response = await api.get<MenuUpdate[]>(
      ENDPOINTS.SUBSCRIPTIONS.CHEF_MENU_UPDATES,
      params
    );
    return response.data as unknown as PaginatedResponse<MenuUpdate>;
  },

  /**
   * Mark menu update as read
   */
  markUpdateAsRead: async (updateId: number): Promise<{ success: boolean }> => {
    const response = await api.post<{ success: boolean }>(
      `${ENDPOINTS.SUBSCRIPTIONS.CHEF_MENU_UPDATES}/${updateId}/read`
    );
    return response.data.data;
  },

  // ==================== MENU SUBSCRIPTIONS (Alternative) ====================

  /**
   * Get menu subscriptions list
   */
  getMenuSubscriptions: async (params?: {
    page?: number;
    per_page?: number;
  }): Promise<PaginatedResponse<ChefSubscription>> => {
    const response = await api.get<ChefSubscription[]>(
      ENDPOINTS.MENU_SUBSCRIPTIONS.LIST,
      params
    );
    return response.data as unknown as PaginatedResponse<ChefSubscription>;
  },

  /**
   * Subscribe to chef's menu updates
   */
  subscribeToChefMenu: async (chefId: number): Promise<ChefSubscription> => {
    const response = await api.post<ChefSubscription>(
      ENDPOINTS.MENU_SUBSCRIPTIONS.SUBSCRIBE(chefId)
    );
    return response.data.data;
  },

  /**
   * Unsubscribe from chef's menu updates
   */
  unsubscribeFromChefMenu: async (chefId: number): Promise<{ message: string }> => {
    const response = await api.delete<{ message: string }>(
      ENDPOINTS.MENU_SUBSCRIPTIONS.UNSUBSCRIBE(chefId)
    );
    return response.data.data;
  },

  /**
   * Check menu subscription status
   */
  isSubscribedToMenu: async (chefId: number): Promise<{ subscribed: boolean }> => {
    const response = await api.get<{ subscribed: boolean }>(
      ENDPOINTS.MENU_SUBSCRIPTIONS.CHECK(chefId)
    );
    return response.data.data;
  },

  /**
   * Get latest menu updates feed
   */
  getMenuUpdatesFeed: async (params?: {
    page?: number;
    per_page?: number;
  }): Promise<PaginatedResponse<MenuUpdate>> => {
    const response = await api.get<MenuUpdate[]>(
      ENDPOINTS.MENU_SUBSCRIPTIONS.UPDATES,
      params
    );
    return response.data as unknown as PaginatedResponse<MenuUpdate>;
  },

  // ==================== VENDOR/CHEF SUBSCRIBER MANAGEMENT ====================

  /**
   * Get chef's subscribers list (for vendor)
   */
  getSubscribers: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
  }): Promise<PaginatedResponse<Subscriber>> => {
    const response = await api.get<Subscriber[]>(
      ENDPOINTS.VENDOR.SUBSCRIBERS.LIST,
      params
    );
    return response.data as unknown as PaginatedResponse<Subscriber>;
  },

  /**
   * Get subscriber count (for vendor)
   */
  getSubscriberCount: async (): Promise<{ total: number; this_month: number }> => {
    const response = await api.get<{ total: number; this_month: number }>(
      ENDPOINTS.VENDOR.SUBSCRIBERS.COUNT
    );
    return response.data.data;
  },

  /**
   * Send notification to all subscribers (for vendor)
   */
  notifySubscribers: async (data: {
    title: string;
    message: string;
    type: 'new_menu' | 'promotion' | 'announcement';
    menu_id?: number;
  }): Promise<{ sent_count: number }> => {
    const response = await api.post<{ sent_count: number }>(
      ENDPOINTS.VENDOR.SUBSCRIBERS.NOTIFY,
      data
    );
    return response.data.data;
  },
};

export default subscriptionService;
