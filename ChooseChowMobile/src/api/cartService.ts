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

interface ApiChefGroup {
  chef: {
    id: number;
    full_name?: string;
    business_name: string;
    delivery_fee: number;
    minimum_order: number;
    is_online: boolean;
  };
  items: ApiCartItem[];
  items_count: number;
  subtotal: number;
  formatted_subtotal: string;
  meets_minimum: boolean;
}

interface ApiCartItem {
  id: number;
  menu_id: number;
  quantity: number;
  special_instructions: string | null;
  price: number;
  line_total: number;
  formatted_price: string;
  formatted_line_total: string;
  menu: {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    image: string | null;
    is_available: boolean;
    preparation_time: string | null;
  };
  chef: {
    id: number;
    full_name: string;
    business_name: string;
    delivery_fee: number;
    minimum_order: number;
  };
}

interface ApiCartResponse {
  chefs: ApiChefGroup[];
  summary: {
    total_items: number;
    total_chefs: number;
    grand_subtotal: number;
    formatted_grand_subtotal: string;
  };
}

function normalizeCart(apiData: ApiCartResponse): Cart {
  const items: CartItem[] = [];
  let deliveryFee = 0;

  for (const group of apiData.chefs) {
    deliveryFee += group.chef.delivery_fee;
    for (const apiItem of group.items) {
      items.push({
        id: apiItem.id,
        cart_id: 0,
        menu_id: apiItem.menu_id,
        menu: {
          id: apiItem.menu.id,
          chef_id: apiItem.chef.id,
          name: apiItem.menu.name,
          slug: apiItem.menu.slug,
          description: apiItem.menu.description || undefined,
          image_url: apiItem.menu.image || undefined,
          price: apiItem.price,
          is_available: apiItem.menu.is_available,
          preparation_time: apiItem.menu.preparation_time ? parseInt(apiItem.menu.preparation_time) : undefined,
        } as any,
        quantity: apiItem.quantity,
        unit_price: apiItem.price,
        total_price: apiItem.line_total,
        special_instructions: apiItem.special_instructions || undefined,
      });
    }
  }

  const subtotal = apiData.summary.grand_subtotal;
  const discount = 0;
  const serviceFee = 0;
  const total = subtotal + deliveryFee + serviceFee - discount;

  const firstChef = apiData.chefs[0]?.chef;
  const chefId = firstChef?.id;

  return {
    id: 0,
    user_id: 0,
    chef_id: chefId,
    items,
    subtotal,
    delivery_fee: deliveryFee,
    service_fee: serviceFee,
    discount,
    total,
  };
}

export const cartService = {
  getCart: async (): Promise<Cart> => {
    const response = await api.get<ApiCartResponse>(ENDPOINTS.CART.GET);
    return normalizeCart(response.data.data);
  },

  addItem: async (data: AddToCartData): Promise<void> => {
    await api.post(ENDPOINTS.CART.ADD, data);
  },

  updateItem: async (itemId: number, data: UpdateCartItemData): Promise<void> => {
    await api.put(ENDPOINTS.CART.UPDATE(itemId), data);
  },

  removeItem: async (itemId: number): Promise<void> => {
    await api.delete(ENDPOINTS.CART.REMOVE(itemId));
  },

  clearCart: async (): Promise<{ message: string }> => {
    const response = await api.delete<{ message: string }>(ENDPOINTS.CART.CLEAR);
    return response.data.data;
  },

  applyCoupon: async (code: string): Promise<void> => {
    await api.post(ENDPOINTS.CART.APPLY_COUPON, { code });
  },

  removeCoupon: async (): Promise<void> => {
    await api.delete(ENDPOINTS.CART.REMOVE_COUPON);
  },
};

export default cartService;
