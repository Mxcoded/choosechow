import { api, PaginatedResponse } from './client';
import { ENDPOINTS } from './config';
import { Order, OrderTracking, PaymentIntent } from '../types';

interface CreateOrderData {
  address_id: number;
  payment_method: 'paystack' | 'card' | 'cash';
  special_instructions?: string;
  scheduled_for?: string;
}

export const orderService = {
  // Get user's orders
  getOrders: async (page = 1): Promise<PaginatedResponse<Order>> => {
    const response = await api.get<Order[]>(ENDPOINTS.ORDERS.LIST, { page });
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
  reorder: async (orderId: number): Promise<{ message: string }> => {
    const response = await api.post<{ message: string }>(ENDPOINTS.ORDERS.REORDER(orderId));
    return response.data.data;
  },

  // Get order history (past completed/cancelled orders)
  getOrderHistory: async (page = 1): Promise<PaginatedResponse<Order>> => {
    const response = await api.get<Order[]>(ENDPOINTS.ORDERS.HISTORY, { page });
    return response.data as unknown as PaginatedResponse<Order>;
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
