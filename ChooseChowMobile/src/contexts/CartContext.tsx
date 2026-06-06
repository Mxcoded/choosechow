import React, { createContext, useContext, useState, useEffect, ReactNode, useCallback, useRef } from 'react';
import { cartService } from '../api';
import { Cart, SelectedCustomization } from '../types';
import { useAuth } from './AuthContext';

interface CartContextType {
  cart: Cart | null;
  isLoading: boolean;
  itemCount: number;
  addToCart: (menuId: number, quantity: number, instructions?: string, customizations?: SelectedCustomization[]) => Promise<void>;
  updateCartItem: (itemId: number, quantity: number) => Promise<void>;
  removeFromCart: (itemId: number) => Promise<void>;
  clearCart: () => Promise<void>;
  applyCoupon: (code: string) => Promise<void>;
  removeCoupon: () => Promise<void>;
  refreshCart: () => Promise<void>;
}

const CartContext = createContext<CartContextType | undefined>(undefined);

interface CartProviderProps {
  children: ReactNode;
}

function recalcTotals(cart: Cart): Cart {
  const subtotal = cart.items.reduce((s, i) => s + i.total_price, 0);
  return { ...cart, subtotal, total: subtotal + cart.delivery_fee + cart.service_fee - cart.discount };
}

export const CartProvider: React.FC<CartProviderProps> = ({ children }) => {
  const [cart, setCart] = useState<Cart | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const { isAuthenticated } = useAuth();
  const cartSnapshot = useRef<Cart | null>(null);

  const refreshCart = useCallback(async () => {
    if (!isAuthenticated) return;

    setIsLoading(true);
    try {
      const cartData = await cartService.getCart();
      setCart(cartData);
    } catch (error) {
      console.error('Failed to fetch cart:', error);
    } finally {
      setIsLoading(false);
    }
  }, [isAuthenticated]);

  const silentRefresh = useCallback(async () => {
    if (!isAuthenticated) return;
    try {
      const cartData = await cartService.getCart();
      setCart(cartData);
    } catch {
      // silent fail — optimistic state remains
    }
  }, [isAuthenticated]);

  useEffect(() => {
    if (isAuthenticated) {
      refreshCart();
    } else {
      setCart(null);
    }
  }, [isAuthenticated, refreshCart]);

  const addToCart = async (
    menuId: number,
    quantity: number,
    instructions?: string,
    customizations?: SelectedCustomization[]
  ) => {
    try {
      await cartService.addItem({ menu_id: menuId, quantity, special_instructions: instructions, customizations });
      await silentRefresh();
    } catch (error) {
      console.error('Failed to add item to cart:', error);
      throw error;
    }
  };

  const updateCartItem = async (itemId: number, quantity: number) => {
    cartSnapshot.current = cart ? { ...cart, items: [...cart.items] } : null;

    setCart((prev) => {
      if (!prev) return prev;
      const items = prev.items.map((item) => {
        if (item.id !== itemId) return item;
        const unitPrice = item.unit_price;
        return { ...item, quantity, total_price: unitPrice * quantity };
      });
      return recalcTotals({ ...prev, items });
    });

    try {
      await cartService.updateItem(itemId, { quantity });
      silentRefresh();
    } catch (error) {
      setCart(cartSnapshot.current);
      console.error('Failed to update cart item:', error);
    }
  };

  const removeFromCart = async (itemId: number) => {
    cartSnapshot.current = cart ? { ...cart, items: [...cart.items] } : null;

    setCart((prev) => {
      if (!prev) return prev;
      const items = prev.items.filter((item) => item.id !== itemId);
      const updated = recalcTotals({ ...prev, items });
      return updated.items.length === 0 ? null : updated;
    });

    try {
      await cartService.removeItem(itemId);
      silentRefresh();
    } catch (error) {
      setCart(cartSnapshot.current);
      console.error('Failed to remove cart item:', error);
    }
  };

  const clearCart = async () => {
    cartSnapshot.current = cart ? { ...cart, items: [...cart.items] } : null;
    setCart(null);

    try {
      await cartService.clearCart();
    } catch (error) {
      setCart(cartSnapshot.current);
      console.error('Failed to clear cart:', error);
      throw error;
    }
  };

  const applyCoupon = async (code: string) => {
    try {
      await cartService.applyCoupon(code);
      await silentRefresh();
    } catch (error) {
      console.error('Failed to apply coupon:', error);
      throw error;
    }
  };

  const removeCoupon = async () => {
    try {
      await cartService.removeCoupon();
      await silentRefresh();
    } catch (error) {
      console.error('Failed to remove coupon:', error);
      throw error;
    }
  };

  const itemCount = cart?.items?.reduce((sum, item) => sum + Number(item.quantity), 0) || 0;

  const value: CartContextType = {
    cart,
    isLoading,
    itemCount,
    addToCart,
    updateCartItem,
    removeFromCart,
    clearCart,
    applyCoupon,
    removeCoupon,
    refreshCart,
  };

  return <CartContext.Provider value={value}>{children}</CartContext.Provider>;
};

export const useCart = (): CartContextType => {
  const context = useContext(CartContext);
  if (context === undefined) {
    throw new Error('useCart must be used within a CartProvider');
  }
  return context;
};

export default CartContext;
