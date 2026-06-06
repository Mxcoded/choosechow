import { api } from './client';
import { ENDPOINTS } from './config';
import type { SubscriptionPlan, SubscriptionStatusResponse, SubscriptionTier } from '../types';

interface SubscribeResult {
  success: boolean;
  message: string;
  subscription?: any;
  prorated_charge?: number;
  effective_at?: string;
  ends_at?: string;
}

interface PaystackInitResult {
  success: boolean;
  authorization_url: string;
  reference: string;
  message: string;
  prorated_charge?: number;
}

export const planSubscriptionService = {
  getPlans: async (): Promise<SubscriptionPlan[]> => {
    const response = await api.get<SubscriptionPlan[]>(ENDPOINTS.SUBSCRIPTION_PLANS.LIST);
    return response.data.data;
  },

  getStatus: async (): Promise<SubscriptionStatusResponse | null> => {
    try {
      const response = await api.get<SubscriptionStatusResponse>(ENDPOINTS.SUBSCRIPTION_PLANS.STATUS);
      const data = response.data.data;
      if (!data || data.tier === 'none') return null;
      return data;
    } catch {
      return null;
    }
  },

  subscribe: async (tier: string, paymentMethod: 'wallet' | 'paystack' = 'paystack'): Promise<SubscribeResult | PaystackInitResult> => {
    const response = await api.post<SubscribeResult | PaystackInitResult>(ENDPOINTS.SUBSCRIPTION_PLANS.SUBSCRIBE, { tier, payment_method: paymentMethod });
    return response.data.data;
  },

  upgrade: async (tier: string, paymentMethod: 'wallet' | 'paystack' = 'paystack'): Promise<SubscribeResult | PaystackInitResult> => {
    const response = await api.post<SubscribeResult | PaystackInitResult>(ENDPOINTS.SUBSCRIPTION_PLANS.UPGRADE, { tier, payment_method: paymentMethod });
    return response.data.data;
  },

  downgrade: async (tier: string): Promise<SubscribeResult> => {
    const response = await api.post<SubscribeResult>(ENDPOINTS.SUBSCRIPTION_PLANS.DOWNGRADE, { tier });
    return response.data.data;
  },

  cancel: async (): Promise<SubscribeResult> => {
    const response = await api.post<SubscribeResult>(ENDPOINTS.SUBSCRIPTION_PLANS.CANCEL);
    return response.data.data;
  },

  verifyPayment: async (reference: string): Promise<SubscribeResult> => {
    const response = await api.post<SubscribeResult>(ENDPOINTS.SUBSCRIPTION_PLANS.VERIFY_PAYMENT, { reference });
    return response.data.data;
  },

  verifyUpgradePayment: async (reference: string): Promise<SubscribeResult> => {
    const response = await api.post<SubscribeResult>(ENDPOINTS.SUBSCRIPTION_PLANS.VERIFY_UPGRADE_PAYMENT, { reference });
    return response.data.data;
  },
};

export default planSubscriptionService;
