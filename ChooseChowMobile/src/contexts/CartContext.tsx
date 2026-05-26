import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
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

export const CartProvider: React.FC<CartProviderProps> = ({ children }) => {
  const [cart, setCart] = useState<Cart | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const { isAuthenticated } = useAuth();

  // Load cart when user is authenticated
  useEffect(() => {
    if (isAuthenticated) {
      refreshCart();
    } else {
      setCart(null);
    }
  }, [isAuthenticated]);

  const refreshCart = async () => {
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
  };

  const addToCart = async (
    menuId: number,
    quantity: number,
    instructions?: string,
    customizations?: SelectedCustomization[]
  ) => {
    setIsLoading(true);
    try {
      const updatedCart = await cartService.addItem({
        menu_id: menuId,
        quantity,
        special_instructions: instructions,
        customizations,
      });
      setCart(updatedCart);
    } finally {
      setIsLoading(false);
    }
  };

  const updateCartItem = async (itemId: number, quantity: number) => {
    setIsLoading(true);
    try {
      const updatedCart = await cartService.updateItem(itemId, { quantity });
      setCart(updatedCart);
    } finally {
      setIsLoading(false);
    }
  };

  const removeFromCart = async (itemId: number) => {
    setIsLoading(true);
    try {
      const updatedCart = await cartService.removeItem(itemId);
      setCart(updatedCart);
    } finally {
      setIsLoading(false);
    }
  };

  const clearCart = async () => {
    setIsLoading(true);
    try {
      await cartService.clearCart();
      setCart(null);
    } finally {
      setIsLoading(false);
    }
  };

  const applyCoupon = async (code: string) => {
    setIsLoading(true);
    try {
      const updatedCart = await cartService.applyCoupon(code);
      setCart(updatedCart);
    } finally {
      setIsLoading(false);
    }
  };

  const removeCoupon = async () => {
    setIsLoading(true);
    try {
      const updatedCart = await cartService.removeCoupon();
      setCart(updatedCart);
    } finally {
      setIsLoading(false);
    }
  };

  const itemCount = cart?.items?.reduce((sum, item) => sum + item.quantity, 0) || 0;

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
