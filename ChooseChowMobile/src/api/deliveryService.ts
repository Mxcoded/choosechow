import { api } from './client';
import { ENDPOINTS } from './config';

// ===================== TYPES =====================

export type DeliveryStatus = 
  | 'pending'
  | 'confirmed'
  | 'preparing'
  | 'ready_for_pickup'
  | 'picked_up'
  | 'on_the_way'
  | 'arriving'
  | 'delivered'
  | 'cancelled';

export interface DeliveryStatusInfo {
  order_id: number;
  order_number: string;
  status: DeliveryStatus;
  status_label: string;
  status_description: string;
  progress_percentage: number;
  estimated_delivery_time?: string;
  actual_delivery_time?: string;
  timeline: DeliveryTimelineEvent[];
  driver?: DeliveryDriver;
  can_cancel: boolean;
}

export interface DeliveryTimelineEvent {
  status: DeliveryStatus;
  label: string;
  timestamp?: string;
  completed: boolean;
  current: boolean;
}

export interface DeliveryDriver {
  id: number;
  name: string;
  phone?: string;
  avatar_url?: string;
  rating: number;
  vehicle_type?: string;
  vehicle_number?: string;
}

export interface DeliveryLocation {
  order_id: number;
  driver_location?: {
    latitude: number;
    longitude: number;
    heading?: number;
    updated_at: string;
  };
  pickup_location: {
    latitude: number;
    longitude: number;
    address: string;
  };
  delivery_location: {
    latitude: number;
    longitude: number;
    address: string;
  };
  estimated_distance_km?: number;
  estimated_duration_minutes?: number;
}

export interface DeliveryETA {
  order_id: number;
  estimated_arrival: string;
  estimated_minutes: number;
  confidence: 'high' | 'medium' | 'low';
  factors: string[];
}

export interface DeliverySubscription {
  order_id: number;
  subscription_token: string;
  websocket_url?: string;
  polling_interval_ms: number;
  expires_at: string;
}

// ===================== SERVICE =====================

export const deliveryService = {
  /**
   * Get current delivery status for an order
   */
  getStatus: async (orderId: number): Promise<DeliveryStatusInfo> => {
    const response = await api.get<DeliveryStatusInfo>(
      ENDPOINTS.DELIVERY.STATUS(orderId)
    );
    return response.data.data;
  },

  /**
   * Get real-time delivery location (driver location, pickup/delivery points)
   */
  getLocation: async (orderId: number): Promise<DeliveryLocation> => {
    const response = await api.get<DeliveryLocation>(
      ENDPOINTS.DELIVERY.LOCATION(orderId)
    );
    return response.data.data;
  },

  /**
   * Subscribe to delivery updates (returns token for real-time updates)
   */
  subscribe: async (orderId: number): Promise<DeliverySubscription> => {
    const response = await api.post<DeliverySubscription>(
      ENDPOINTS.DELIVERY.SUBSCRIBE(orderId)
    );
    return response.data.data;
  },

  /**
   * Get estimated time of arrival
   */
  getETA: async (orderId: number): Promise<DeliveryETA> => {
    const response = await api.get<DeliveryETA>(
      ENDPOINTS.DELIVERY.ETA(orderId)
    );
    return response.data.data;
  },

  /**
   * Get status label for display
   */
  getStatusLabel: (status: DeliveryStatus): string => {
    const labels: Record<DeliveryStatus, string> = {
      pending: 'Order Placed',
      confirmed: 'Order Confirmed',
      preparing: 'Preparing Your Food',
      ready_for_pickup: 'Ready for Pickup',
      picked_up: 'Picked Up',
      on_the_way: 'On The Way',
      arriving: 'Almost There',
      delivered: 'Delivered',
      cancelled: 'Cancelled',
    };
    return labels[status] || status;
  },

  /**
   * Get status description for display
   */
  getStatusDescription: (status: DeliveryStatus): string => {
    const descriptions: Record<DeliveryStatus, string> = {
      pending: 'Your order has been received and is being processed.',
      confirmed: 'The restaurant has confirmed your order.',
      preparing: 'The chef is preparing your delicious meal.',
      ready_for_pickup: 'Your order is ready and waiting for pickup.',
      picked_up: 'The delivery person has picked up your order.',
      on_the_way: 'Your order is on its way to you.',
      arriving: 'The delivery person is arriving at your location.',
      delivered: 'Your order has been delivered. Enjoy!',
      cancelled: 'This order has been cancelled.',
    };
    return descriptions[status] || '';
  },

  /**
   * Get progress percentage for status
   */
  getProgressPercentage: (status: DeliveryStatus): number => {
    const progress: Record<DeliveryStatus, number> = {
      pending: 10,
      confirmed: 20,
      preparing: 40,
      ready_for_pickup: 60,
      picked_up: 70,
      on_the_way: 80,
      arriving: 90,
      delivered: 100,
      cancelled: 0,
    };
    return progress[status] || 0;
  },

  /**
   * Build timeline from current status
   */
  buildTimeline: (currentStatus: DeliveryStatus, timestamps?: Record<string, string>): DeliveryTimelineEvent[] => {
    const statuses: DeliveryStatus[] = [
      'pending',
      'confirmed',
      'preparing',
      'ready_for_pickup',
      'picked_up',
      'on_the_way',
      'delivered',
    ];

    const currentIndex = statuses.indexOf(currentStatus);
    
    return statuses.map((status, index) => ({
      status,
      label: deliveryService.getStatusLabel(status),
      timestamp: timestamps?.[status],
      completed: index < currentIndex,
      current: index === currentIndex,
    }));
  },

  /**
   * Check if order can be cancelled based on status
   */
  canCancel: (status: DeliveryStatus): boolean => {
    const cancellableStatuses: DeliveryStatus[] = ['pending', 'confirmed'];
    return cancellableStatuses.includes(status);
  },

  /**
   * Format ETA for display
   */
  formatETA: (minutes: number): string => {
    if (minutes < 1) return 'Arriving now';
    if (minutes < 60) return `${minutes} min`;
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return mins > 0 ? `${hours}h ${mins}m` : `${hours}h`;
  },
};

export default deliveryService;
