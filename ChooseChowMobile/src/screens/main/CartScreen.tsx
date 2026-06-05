import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Image,
  Alert,
  ActivityIndicator,
  TextInput,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useCart } from '../../contexts';
import { CartItem as CartItemType } from '../../types';
import { COLORS } from '../../utils/theme';
import { scaleWidth, scaleHeight, screenWidth } from '../../utils/dimensions';

type CartScreenProps = {
  navigation: NativeStackNavigationProp<any>;
};

// Sample cart items for demo
const SAMPLE_CART_ITEMS = [
  { id: 1, name: 'Prime Pancake with egg yoke sauce', price: 2500, quantity: 2, image: null },
  { id: 2, name: 'Grilled Chicken Salad', price: 3500, quantity: 1, image: null },
  { id: 3, name: 'Fresh Orange Juice', price: 1200, quantity: 2, image: null },
];

const CartItemRow: React.FC<{
  item: typeof SAMPLE_CART_ITEMS[0];
  onUpdateQuantity: (quantity: number) => void;
  onRemove: () => void;
}> = ({ item, onUpdateQuantity, onRemove }) => {
  return (
    <View style={styles.cartItem}>
      {/* Item Image */}
      <View style={styles.itemImageContainer}>
        <View style={styles.itemImagePlaceholder}>
          <Text style={styles.itemImageEmoji}>🍽️</Text>
        </View>
      </View>
      
      {/* Item Details */}
      <View style={styles.itemDetails}>
        <Text style={styles.itemName} numberOfLines={2}>{item.name}</Text>
        <Text style={styles.itemPrice}>₦{item.price.toLocaleString()}</Text>
      </View>
      
      {/* Quantity Controls */}
      <View style={styles.quantityContainer}>
        <TouchableOpacity
          style={styles.quantityButton}
          onPress={() => item.quantity > 1 ? onUpdateQuantity(item.quantity - 1) : onRemove()}
        >
          <Text style={styles.quantityButtonText}>−</Text>
        </TouchableOpacity>
        <Text style={styles.quantity}>{item.quantity}</Text>
        <TouchableOpacity
          style={[styles.quantityButton, styles.quantityButtonPlus]}
          onPress={() => onUpdateQuantity(item.quantity + 1)}
        >
          <Text style={[styles.quantityButtonText, styles.quantityButtonPlusText]}>+</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
};

export const CartScreen: React.FC<CartScreenProps> = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  const { cart, isLoading, updateCartItem, removeFromCart, clearCart } = useCart();
  const [promoCode, setPromoCode] = useState('');
  const [localItems, setLocalItems] = useState(SAMPLE_CART_ITEMS);

  // Use local items for demo, or cart items if available
  const items = cart?.items?.length ? cart.items : null;
  const hasItems = items ? items.length > 0 : localItems.length > 0;

  const handleUpdateQuantity = (itemId: number, quantity: number) => {
    if (items) {
      updateCartItem(itemId, quantity).catch(() => {
        Alert.alert('Error', 'Failed to update item quantity');
      });
    } else {
      setLocalItems(localItems.map(item => 
        item.id === itemId ? { ...item, quantity } : item
      ));
    }
  };

  const handleRemoveItem = (itemId: number) => {
    if (items) {
      removeFromCart(itemId).catch(() => {
        Alert.alert('Error', 'Failed to remove item');
      });
    } else {
      setLocalItems(localItems.filter(item => item.id !== itemId));
    }
  };

  const handleClearCart = () => {
    Alert.alert(
      'Clear Cart',
      'Are you sure you want to remove all items from your cart?',
      [
        { text: 'Cancel', style: 'cancel' },
        { 
          text: 'Clear', 
          style: 'destructive', 
          onPress: () => {
            if (items) {
              clearCart();
            } else {
              setLocalItems([]);
            }
          }
        },
      ]
    );
  };

  const handleCheckout = () => {
    navigation.navigate('Checkout');
  };

  const handleApplyPromo = () => {
    if (promoCode.trim()) {
      Alert.alert('Promo Code', `Applying code: ${promoCode}`);
    }
  };

  // Calculate totals
  const subtotal = items 
    ? cart?.subtotal || 0 
    : localItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  const deliveryFee = items ? cart?.delivery_fee || 500 : 500;
  const serviceFee = items ? cart?.service_fee || 100 : 100;
  const discount = items ? cart?.discount || 0 : 0;
  const total = subtotal + deliveryFee + serviceFee - discount;

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={COLORS.primary} />
      </View>
    );
  }

  if (!hasItems) {
    return (
      <View style={styles.emptyContainer}>
        <View style={styles.emptyIconContainer}>
          <Text style={styles.emptyIcon}>🛒</Text>
        </View>
        <Text style={styles.emptyTitle}>Your Cart is Empty</Text>
        <Text style={styles.emptySubtitle}>
          Looks like you haven't added{"\n"}any items yet.
        </Text>
        <TouchableOpacity
          style={styles.browseButton}
          onPress={() => navigation.navigate('Home')}
        >
          <Text style={styles.browseButtonText}>Start Shopping</Text>
        </TouchableOpacity>
      </View>
    );
  }

  const displayItems = items || localItems;

  return (
    <View style={styles.container}>
      <ScrollView 
        style={styles.scrollView}
        showsVerticalScrollIndicator={false}
        contentContainerStyle={{ paddingBottom: 20 }}
      >
        {/* Cart Header */}
        <View style={styles.cartHeader}>
          <Text style={styles.cartTitle}>My Cart</Text>
          <TouchableOpacity onPress={handleClearCart}>
            <Text style={styles.clearButton}>Clear All</Text>
          </TouchableOpacity>
        </View>

        {/* Cart Items */}
        <View style={styles.itemsContainer}>
          {displayItems.map((item: any) => (
            <CartItemRow
              key={item.id}
              item={{
                id: item.id,
                name: item.menu?.name || item.name,
                price: item.unit_price || item.price,
                quantity: item.quantity,
                image: item.menu?.image_url || item.image,
              }}
              onUpdateQuantity={(qty) => handleUpdateQuantity(item.id, qty)}
              onRemove={() => handleRemoveItem(item.id)}
            />
          ))}
        </View>

        {/* Promo Code Section */}
        <View style={styles.promoContainer}>
          <View style={styles.promoInputContainer}>
            <Text style={styles.promoIcon}>🏷️</Text>
            <TextInput
              style={styles.promoInput}
              placeholder="Enter promo code"
              placeholderTextColor="#9CA3AF"
              value={promoCode}
              onChangeText={setPromoCode}
            />
          </View>
          <TouchableOpacity style={styles.applyButton} onPress={handleApplyPromo}>
            <Text style={styles.applyButtonText}>Apply</Text>
          </TouchableOpacity>
        </View>

        {/* Order Summary */}
        <View style={styles.summaryContainer}>
          <Text style={styles.summaryTitle}>Order Summary</Text>
          
          <View style={styles.summaryRow}>
            <Text style={styles.summaryLabel}>Subtotal</Text>
            <Text style={styles.summaryValue}>₦{subtotal.toLocaleString()}</Text>
          </View>
          
          <View style={styles.summaryRow}>
            <Text style={styles.summaryLabel}>Delivery Fee</Text>
            <Text style={styles.summaryValue}>₦{deliveryFee.toLocaleString()}</Text>
          </View>
          
          <View style={styles.summaryRow}>
            <Text style={styles.summaryLabel}>Service Fee</Text>
            <Text style={styles.summaryValue}>₦{serviceFee.toLocaleString()}</Text>
          </View>
          
          {discount > 0 && (
            <View style={styles.summaryRow}>
              <Text style={styles.discountLabel}>Discount</Text>
              <Text style={styles.discountValue}>-₦{discount.toLocaleString()}</Text>
            </View>
          )}
          
          <View style={styles.divider} />
          
          <View style={styles.summaryRow}>
            <Text style={styles.totalLabel}>Total</Text>
            <Text style={styles.totalValue}>₦{total.toLocaleString()}</Text>
          </View>
        </View>
      </ScrollView>

      {/* Checkout Button */}
      <View style={[styles.checkoutContainer, { paddingBottom: Math.max(insets.bottom, 16) }]}>
        <TouchableOpacity style={styles.checkoutButton} onPress={handleCheckout}>
          <Text style={styles.checkoutButtonText}>Proceed to Checkout</Text>
          <View style={styles.checkoutPriceContainer}>
            <Text style={styles.checkoutPrice}>₦{total.toLocaleString()}</Text>
          </View>
        </TouchableOpacity>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#F9FAFB',
  },
  // Empty State
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 24,
    backgroundColor: '#F9FAFB',
  },
  emptyIconContainer: {
    width: 120,
    height: 120,
    borderRadius: 60,
    backgroundColor: COLORS.primaryFaded,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 24,
  },
  emptyIcon: {
    fontSize: 56,
  },
  emptyTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#1F2937',
    marginBottom: 8,
  },
  emptySubtitle: {
    fontSize: 16,
    color: '#6B7280',
    textAlign: 'center',
    marginBottom: 32,
    lineHeight: 24,
  },
  browseButton: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: 40,
    paddingVertical: 16,
    borderRadius: 12,
  },
  browseButtonText: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: 'bold',
  },
  // Scroll View
  scrollView: {
    flex: 1,
  },
  // Cart Header
  cartHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 16,
  },
  cartTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  clearButton: {
    color: COLORS.primary,
    fontSize: 14,
    fontWeight: '600',
  },
  // Cart Items
  itemsContainer: {
    backgroundColor: '#FFFFFF',
    marginHorizontal: 16,
    borderRadius: 16,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    elevation: 2,
  },
  cartItem: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#F3F4F6',
  },
  itemImageContainer: {
    width: 70,
    height: 70,
    borderRadius: 12,
    overflow: 'hidden',
  },
  itemImagePlaceholder: {
    width: '100%',
    height: '100%',
    backgroundColor: '#F3F4F6',
    justifyContent: 'center',
    alignItems: 'center',
  },
  itemImageEmoji: {
    fontSize: 32,
  },
  itemDetails: {
    flex: 1,
    marginLeft: 12,
  },
  itemName: {
    fontSize: 14,
    fontWeight: '600',
    color: '#1F2937',
    marginBottom: 4,
    lineHeight: 20,
  },
  itemPrice: {
    fontSize: 14,
    color: COLORS.primary,
    fontWeight: '700',
  },
  quantityContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F3F4F6',
    borderRadius: 8,
    padding: 4,
  },
  quantityButton: {
    width: 28,
    height: 28,
    borderRadius: 6,
    backgroundColor: '#FFFFFF',
    justifyContent: 'center',
    alignItems: 'center',
  },
  quantityButtonPlus: {
    backgroundColor: COLORS.primary,
  },
  quantityButtonText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#374151',
  },
  quantityButtonPlusText: {
    color: '#FFFFFF',
  },
  quantity: {
    fontSize: 14,
    fontWeight: '600',
    marginHorizontal: 12,
    color: '#1F2937',
    minWidth: 20,
    textAlign: 'center',
  },
  // Promo Code
  promoContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginHorizontal: 16,
    marginTop: 16,
    gap: 12,
  },
  promoInputContainer: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    paddingHorizontal: 16,
    paddingVertical: 14,
    borderWidth: 1,
    borderColor: '#E5E7EB',
  },
  promoIcon: {
    fontSize: 18,
    marginRight: 12,
  },
  promoInput: {
    flex: 1,
    fontSize: 14,
    color: '#1F2937',
    padding: 0,
  },
  applyButton: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: 20,
    paddingVertical: 14,
    borderRadius: 12,
  },
  applyButtonText: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: '600',
  },
  // Order Summary
  summaryContainer: {
    backgroundColor: '#FFFFFF',
    marginHorizontal: 16,
    marginTop: 16,
    padding: 16,
    borderRadius: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    elevation: 2,
  },
  summaryTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#1F2937',
    marginBottom: 16,
  },
  summaryRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 12,
  },
  summaryLabel: {
    fontSize: 14,
    color: '#6B7280',
  },
  summaryValue: {
    fontSize: 14,
    color: '#1F2937',
    fontWeight: '500',
  },
  discountLabel: {
    fontSize: 14,
    color: '#10B981',
  },
  discountValue: {
    fontSize: 14,
    color: '#10B981',
    fontWeight: '500',
  },
  divider: {
    height: 1,
    backgroundColor: '#E5E7EB',
    marginVertical: 12,
  },
  totalLabel: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  totalValue: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  // Checkout Button
  checkoutContainer: {
    padding: 16,
    backgroundColor: '#FFFFFF',
    borderTopWidth: 1,
    borderTopColor: '#E5E7EB',
  },
  checkoutButton: {
    backgroundColor: COLORS.primary,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 16,
    borderRadius: 12,
  },
  checkoutButtonText: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: 'bold',
  },
  checkoutPriceContainer: {
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 8,
  },
  checkoutPrice: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: 'bold',
  },
});

export default CartScreen;
