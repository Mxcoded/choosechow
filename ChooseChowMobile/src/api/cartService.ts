import { api } from './client';
import { ENDPOINTS } from './config';
import { Cart, CartItem, SelectedCustomization } from '../types';

interface AddToCartData {
  menu_id: number;
  quantity: number;
  special_instructions?: string;
  customizations?: SelectedCustomization[];
}

interface UpdateCartItemData {
  quantity?: number;
  special_instructions?: string;
  customizations?: SelectedCustomization[];
}

export const cartService = {
  // Get current cart
  getCart: async (): Promise<Cart> => {
    const response = await api.get<Cart>(ENDPOINTS.CART.GET);
    return response.data.data;
  },

  // Add item to cart
  addItem: async (data: AddToCartData): Promise<Cart> => {
    const response = await api.post<Cart>(ENDPOINTS.CART.ADD, data);
    return response.data.data;
  },

  // Update cart item quantity or instructions
  updateItem: async (itemId: number, data: UpdateCartItemData): Promise<Cart> => {
    const response = await api.put<Cart>(ENDPOINTS.CART.UPDATE(itemId), data);
    return response.data.data;
  },

  // Remove item from cart
  removeItem: async (itemId: number): Promise<Cart> => {
    const response = await api.delete<Cart>(ENDPOINTS.CART.REMOVE(itemId));
    return response.data.data;
  },

  // Clear entire cart
  clearCart: async (): Promise<{ message: string }> => {
    const response = await api.delete<{ message: string }>(ENDPOINTS.CART.CLEAR);
    return response.data.data;
  },

  // Apply coupon code
  applyCoupon: async (code: string): Promise<Cart> => {
    const response = await api.post<Cart>(ENDPOINTS.CART.APPLY_COUPON, { code });
    return response.data.data;
  },

  // Remove coupon
  removeCoupon: async (): Promise<Cart> => {
    const response = await api.delete<Cart>(ENDPOINTS.CART.REMOVE_COUPON);
    return response.data.data;
  },
};

export default cartService;
