import { api, PaginatedResponse } from './client';
import { ENDPOINTS } from './config';
import { Order, OrderTracking, PaymentIntent } from '../types';

interface CreateOrderData {
  address_id: number;
  payment_method: 'paystack' | 'card' | 'cash';
  special_instructions?: string;
  scheduled_for?: string;
  delivery_type?: 'asap' | 'scheduled';
  tip_amount?: number;
}

interface RateOrderData {
  rating: number;
  comment?: string;
  food_rating?: number;
  delivery_rating?: number;
  would_recommend?: boolean;
}

export const orderService = {
  // Get user's orders (all orders)
  getOrders: async (params?: {
    page?: number;
    status?: string;
    from_date?: string;
    to_date?: string;
  }): Promise<PaginatedResponse<Order>> => {
    const response = await api.get<Order[]>(ENDPOINTS.ORDERS.LIST, params);
    return response.data as unknown as PaginatedResponse<Order>;
  },

  // Get active orders (pending, preparing, on the way)
  getActiveOrders: async (): Promise<Order[]> => {
    const response = await api.get<Order[]>(ENDPOINTS.ORDERS.ACTIVE);
    return response.data.data || [];
  },

  // Get order history (completed/cancelled orders)
  getOrderHistory: async (params?: {
    page?: number;
    per_page?: number;
  }): Promise<PaginatedResponse<Order>> => {
    const response = await api.get<Order[]>(ENDPOINTS.ORDERS.HISTORY, params);
    return response.data as unknown as PaginatedResponse<Order>;
  },

  // Create a new order
  createOrder: async (data: CreateOrderData): Promise<{ order: Order; payment?: PaymentIntent }> => {
    const response = await api.post<{ order: Order; payment?: PaymentIntent }>(
      ENDPOINTS.ORDERS.CREATE,
      data
    );
    return response.data.data;
  },

  // Get order details
  getOrder: async (orderId: number): Promise<Order> => {
    const response = await api.get<Order>(ENDPOINTS.ORDERS.DETAIL(orderId));
    return response.data.data;
  },

  // Cancel order
  cancelOrder: async (orderId: number, reason?: string): Promise<Order> => {
    const response = await api.post<Order>(ENDPOINTS.ORDERS.CANCEL(orderId), { reason });
    return response.data.data;
  },

  // Track order
  trackOrder: async (orderId: number): Promise<OrderTracking> => {
    const response = await api.get<OrderTracking>(ENDPOINTS.ORDERS.TRACK(orderId));
    return response.data.data;
  },

  // Reorder (add same items to cart)
  reorder: async (orderId: number): Promise<{ message: string; cart_items_count: number }> => {
    const response = await api.post<{ message: string; cart_items_count: number }>(
      ENDPOINTS.ORDERS.REORDER(orderId)
    );
    return response.data.data;
  },

  // Rate an order
  rateOrder: async (orderId: number, data: RateOrderData): Promise<{ message: string }> => {
    const response = await api.post<{ message: string }>(
      ENDPOINTS.ORDERS.RATE(orderId),
      data
    );
    return response.data.data;
  },

  // Get available time slots for scheduled delivery
  getTimeSlots: async (chefId?: number): Promise<{
    date: string;
    slots: Array<{ time: string; available: boolean }>;
  }[]> => {
    const response = await api.get<{
      date: string;
      slots: Array<{ time: string; available: boolean }>;
    }[]>(ENDPOINTS.ORDERS.TIME_SLOTS, chefId ? { chef_id: chefId } : undefined);
    return response.data.data || [];
  },

  // Initialize Paystack payment
  initializePayment: async (orderId: number): Promise<PaymentIntent> => {
    const response = await api.post<PaymentIntent>(ENDPOINTS.PAYMENT.INITIALIZE, {
      order_id: orderId,
    });
    return response.data.data;
  },

  // Verify Paystack payment
  verifyPayment: async (reference: string): Promise<{ status: string; order: Order }> => {
    const response = await api.post<{ status: string; order: Order }>(ENDPOINTS.PAYMENT.VERIFY, {
      reference,
    });
    return response.data.data;
  },
};

export default orderService;
