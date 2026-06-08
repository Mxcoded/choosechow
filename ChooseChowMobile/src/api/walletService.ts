import { api } from './client';
import { ENDPOINTS } from './config';

export interface WalletBalance {
  balance: number;
  formatted_balance: string;
}

export interface WalletTransaction {
  id: number;
  type: string;
  amount: number;
  formatted_amount: string;
  balance_before: number;
  balance_after: number;
  reference: string | null;
  description: string | null;
  created_at: string;
}

interface FundResult {
  authorization_url: string;
  reference: string;
}

export const walletService = {
  getBalance: async (): Promise<WalletBalance> => {
    const response = await api.get<WalletBalance>(ENDPOINTS.WALLET.BALANCE);
    return response.data.data;
  },

  getTransactions: async (page = 1, perPage = 20): Promise<{ data: WalletTransaction[]; meta: any }> => {
    const response = await api.get<any>(ENDPOINTS.WALLET.TRANSACTIONS, { page, per_page: perPage });
    return response.data;
  },

  fund: async (amount: number): Promise<FundResult> => {
    const response = await api.post<FundResult>(ENDPOINTS.WALLET.FUND, { amount });
    return response.data.data;
  },

  verifyFunding: async (reference: string): Promise<WalletBalance> => {
    const response = await api.post<WalletBalance>(ENDPOINTS.WALLET.VERIFY_FUNDING, { reference });
    return response.data.data;
  },
};

export default walletService;
