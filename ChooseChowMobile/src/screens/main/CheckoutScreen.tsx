import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  TextInput,
  Alert,
  ActivityIndicator,
  KeyboardAvoidingView,
  Platform,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useCart } from '../../contexts';
import { orderService, planSubscriptionService, walletService } from '../../api';
import { COLORS } from '../../utils/theme';
import type { SubscriptionStatusResponse } from '../../types';
import { MaterialCommunityIcons } from '@expo/vector-icons';

type CheckoutScreenProps = {
  navigation: NativeStackNavigationProp<any>;
};

type PaymentMethod = 'card' | 'pay_on_delivery' | 'wallet';

export const CheckoutScreen: React.FC<CheckoutScreenProps> = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  const { cart, clearCart } = useCart();
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [notes, setNotes] = useState('');
  const [phoneNumber, setPhoneNumber] = useState('');
  const [deliveryAddress, setDeliveryAddress] = useState('');
  const [deliveryType, setDeliveryType] = useState<'asap' | 'scheduled'>('asap');
  const [paymentMethod, setPaymentMethod] = useState<PaymentMethod>('card');
  const [subscription, setSubscription] = useState<SubscriptionStatusResponse | null | undefined>(undefined);
  const [walletBalance, setWalletBalance] = useState<number | null>(null);

  useEffect(() => {
    (async () => {
      try {
        const [sub, bal] = await Promise.all([
          planSubscriptionService.getStatus(),
          walletService.getBalance().catch(() => null),
        ]);
        setSubscription(sub);
        if (bal) setWalletBalance(bal.balance);
      } catch {
        setSubscription(null);
      }
    })();
  }, []);

  const handlePlaceOrder = async () => {
    if (!cart) {
      Alert.alert('Error', 'Your cart is empty');
      return;
    }

    if (!phoneNumber.trim()) {
      Alert.alert('Required', 'Please enter your phone number');
      return;
    }

    if (!deliveryAddress.trim()) {
      Alert.alert('Required', 'Please enter your delivery address');
      return;
    }

    setIsSubmitting(true);
    try {
      const result = await orderService.createOrder({
        delivery_address: deliveryAddress.trim(),
        phone_number: phoneNumber.trim(),
        payment_method: paymentMethod,
        notes: notes.trim() || undefined,
        delivery_type: deliveryType,
      });

      clearCart();

      if (result.payment?.authorization_url) {
        navigation.navigate('Payment', {
          authorizationUrl: result.payment.authorization_url,
          reference: result.payment.reference,
        });
      } else {
        const orderNumber = result.orders?.[0]?.order_number;
        Alert.alert(
          'Order Placed!',
          `Your order${orderNumber ? ` #${orderNumber}` : ''} has been placed successfully.`,
          [
            {
              text: 'View Order',
              onPress: () => {
                navigation.navigate('MainTabs', { screen: 'Orders' });
              },
            },
          ]
        );
      }
    } catch (error: any) {
      const status = error.response?.status;
      const data = error.response?.data;
      let title = 'Order Failed';
      let message = 'Something went wrong. Please try again.';

      if (!error.response) {
        message = 'Unable to reach the server. Check your internet connection and try again.';
      } else if (status === 422) {
        title = 'Missing Information';
        const errors = data?.errors;
        if (errors) {
          const firstField = Object.values(errors)[0] as string[];
          message = firstField?.[0] || 'Please check your details and try again.';
        } else {
          message = data?.message || 'Please check your details and try again.';
        }
      } else if (status === 400) {
        message = data?.message || 'Invalid request. Please review your order and try again.';
      } else if (status === 500) {
        message = 'Something went wrong on our end. Please try again in a few minutes.';
      } else {
        message = data?.message || 'Failed to place order. Please try again.';
      }

      Alert.alert(title, message);
    } finally {
      setIsSubmitting(false);
    }
  };

  if (!cart || cart.items.length === 0) {
    return (
      <View style={styles.emptyContainer}>
        <Text style={styles.emptyIcon}>🛒</Text>
        <Text style={styles.emptyTitle}>Your Cart is Empty</Text>
        <TouchableOpacity
          style={styles.browseButton}
          onPress={() => navigation.navigate('MainTabs', { screen: 'Home' })}
        >
          <Text style={styles.browseButtonText}>Browse Chefs</Text>
        </TouchableOpacity>
      </View>
    );
  }

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <ScrollView style={styles.scrollView} contentContainerStyle={{ paddingBottom: 24 }} keyboardShouldPersistTaps="handled">
        {/* Delivery Type */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Delivery Type</Text>
          <View style={styles.deliveryTypeRow}>
            <TouchableOpacity
              style={[styles.deliveryTypeOption, deliveryType === 'asap' && styles.deliveryTypeSelected]}
              onPress={() => setDeliveryType('asap')}
            >
              <MaterialCommunityIcons
                name="lightning-bolt"
                size={20}
                color={deliveryType === 'asap' ? COLORS.primary : '#6B7280'}
              />
              <Text style={[styles.deliveryTypeLabel, deliveryType === 'asap' && styles.deliveryTypeLabelSelected]}>
                ASAP
              </Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[styles.deliveryTypeOption, deliveryType === 'scheduled' && styles.deliveryTypeSelected]}
              onPress={() => setDeliveryType('scheduled')}
            >
              <MaterialCommunityIcons
                name="calendar-clock"
                size={20}
                color={deliveryType === 'scheduled' ? COLORS.primary : '#6B7280'}
              />
              <Text style={[styles.deliveryTypeLabel, deliveryType === 'scheduled' && styles.deliveryTypeLabelSelected]}>
                Schedule
              </Text>
            </TouchableOpacity>
          </View>
        </View>

        {/* Phone Number */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Phone Number</Text>
          <TextInput
            style={styles.input}
            placeholder="e.g. 08012345678"
            placeholderTextColor="#9CA3AF"
            value={phoneNumber}
            onChangeText={setPhoneNumber}
            keyboardType="phone-pad"
            maxLength={20}
          />
        </View>

        {/* Delivery Address */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Delivery Address</Text>
          <TextInput
            style={[styles.input, styles.addressInput]}
            placeholder="Enter your full delivery address"
            placeholderTextColor="#9CA3AF"
            value={deliveryAddress}
            onChangeText={setDeliveryAddress}
            multiline
            numberOfLines={3}
          />
        </View>

        {/* Payment Method */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Payment Method</Text>
          
          <TouchableOpacity
            style={[styles.paymentOption, paymentMethod === 'card' && styles.paymentOptionSelected]}
            onPress={() => setPaymentMethod('card')}
          >
            <MaterialCommunityIcons name="credit-card-outline" size={24} color={COLORS.text.primary} style={{ marginRight: 12 }} />
            <View style={styles.radioOuter}>
              {paymentMethod === 'card' && <View style={styles.radioInner} />}
            </View>
            <View style={styles.paymentDetails}>
              <Text style={styles.paymentTitle}>Pay with Card</Text>
              <Text style={styles.paymentSubtitle}>Visa, Mastercard, Verve</Text>
            </View>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.paymentOption, paymentMethod === 'pay_on_delivery' && styles.paymentOptionSelected]}
            onPress={() => setPaymentMethod('pay_on_delivery')}
          >
            <MaterialCommunityIcons name="cash-check" size={24} color={COLORS.text.primary} style={{ marginRight: 12 }} />
            <View style={styles.radioOuter}>
              {paymentMethod === 'pay_on_delivery' && <View style={styles.radioInner} />}
            </View>
            <View style={styles.paymentDetails}>
              <Text style={styles.paymentTitle}>Pay on Delivery</Text>
              <Text style={styles.paymentSubtitle}>Cash or transfer when order arrives</Text>
            </View>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.paymentOption, paymentMethod === 'wallet' && styles.paymentOptionSelected]}
            onPress={() => setPaymentMethod('wallet')}
          >
            <MaterialCommunityIcons name="wallet-outline" size={24} color={COLORS.text.primary} style={{ marginRight: 12 }} />
            <View style={styles.radioOuter}>
              {paymentMethod === 'wallet' && <View style={styles.radioInner} />}
            </View>
            <View style={styles.paymentDetails}>
              <Text style={styles.paymentTitle}>Pay with Wallet</Text>
              <Text style={styles.paymentSubtitle}>
                {walletBalance !== null ? `Balance: ₦${walletBalance.toLocaleString()}` : 'Pay with wallet balance'}
              </Text>
            </View>
          </TouchableOpacity>
        </View>

        {/* Subscription Banner */}
        {subscription === null && (
          <View style={styles.section}>
            <TouchableOpacity
              style={styles.subscribeBanner}
              onPress={() => navigation.navigate('SubscriptionPlans')}
              activeOpacity={0.8}
            >
              <Text style={styles.subscribeIcon}>⭐</Text>
              <View style={styles.subscribeContent}>
                <Text style={styles.subscribeTitle}>Subscribe & Save</Text>
                <Text style={styles.subscribeSubtitle}>
                  Get free delivery and discounts on every order
                </Text>
              </View>
              <Text style={styles.subscribeArrow}>›</Text>
            </TouchableOpacity>
          </View>
        )}

        {/* Order Notes */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Order Notes</Text>
          <TextInput
            style={styles.instructionsInput}
            placeholder="Any special requests? (optional)"
            placeholderTextColor="#9CA3AF"
            value={notes}
            onChangeText={setNotes}
            multiline
            numberOfLines={3}
          />
        </View>

        {/* Order Summary */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Order Summary</Text>
          <View style={styles.summaryCard}>
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>Subtotal</Text>
              <Text style={styles.summaryValue}>₦{cart.subtotal.toLocaleString()}</Text>
            </View>
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>Delivery Fee</Text>
              <Text style={styles.summaryValue}>₦{cart.delivery_fee.toLocaleString()}</Text>
            </View>
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>Service Fee</Text>
              <Text style={styles.summaryValue}>₦{cart.service_fee.toLocaleString()}</Text>
            </View>
            {cart.discount > 0 && (
              <View style={styles.summaryRow}>
                <Text style={styles.discountLabel}>Discount</Text>
                <Text style={styles.discountValue}>-₦{cart.discount.toLocaleString()}</Text>
              </View>
            )}
            {subscription && subscription.is_active && (
              <View style={styles.summaryRow}>
                <Text style={styles.subscriptionBadge}>⭐ {subscription.plan_name} Savings</Text>
              </View>
            )}
            <View style={styles.divider} />
            <View style={styles.summaryRow}>
              <Text style={styles.totalLabel}>Total</Text>
              <Text style={styles.totalValue}>₦{cart.total.toLocaleString()}</Text>
            </View>
          </View>
        </View>
      </ScrollView>

      {/* Place Order Button */}
      <View style={[styles.bottomContainer, { paddingBottom: Math.max(insets.bottom, 16) }]}>
        <TouchableOpacity
          style={[styles.placeOrderButton, isSubmitting && styles.buttonDisabled]}
          onPress={handlePlaceOrder}
          disabled={isSubmitting}
        >
          {isSubmitting ? (
            <ActivityIndicator color="#FFFFFF" />
          ) : (
            <Text style={styles.placeOrderButtonText}>
              Place Order • ₦{cart.total.toLocaleString()}
            </Text>
          )}
        </TouchableOpacity>
      </View>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 24,
  },
  emptyIcon: {
    fontSize: 64,
    marginBottom: 16,
  },
  emptyTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#1F2937',
    marginBottom: 16,
  },
  browseButton: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: 32,
    paddingVertical: 14,
    borderRadius: 12,
  },
  browseButtonText: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: 'bold',
  },
  scrollView: {
    flex: 1,
  },
  section: {
    padding: 16,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#1F2937',
    marginBottom: 12,
  },
  addressCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    padding: 16,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#E5E7EB',
  },
  addressIcon: {
    fontSize: 24,
    marginRight: 12,
  },
  addressDetails: {
    flex: 1,
  },
  addressLabel: {
    fontSize: 16,
    fontWeight: '600',
    color: '#1F2937',
  },
  addressText: {
    fontSize: 14,
    color: '#6B7280',
    marginTop: 2,
  },
  changeText: {
    color: COLORS.primary,
    fontWeight: '600',
  },
  paymentOption: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    padding: 16,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#E5E7EB',
    marginBottom: 12,
  },
  paymentOptionSelected: {
    borderColor: COLORS.primary,
    backgroundColor: COLORS.primaryFaded,
  },
  radioOuter: {
    width: 24,
    height: 24,
    borderRadius: 12,
    borderWidth: 2,
    borderColor: '#D1D5DB',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  radioInner: {
    width: 12,
    height: 12,
    borderRadius: 6,
    backgroundColor: COLORS.primary,
  },
  paymentDetails: {
    flex: 1,
  },
  paymentTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#1F2937',
  },
  paymentSubtitle: {
    fontSize: 13,
    color: '#6B7280',
    marginTop: 2,
  },
  instructionsInput: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
    fontSize: 16,
    color: '#1F2937',
    borderWidth: 1,
    borderColor: '#E5E7EB',
    minHeight: 100,
    textAlignVertical: 'top',
  },
  summaryCard: {
    backgroundColor: '#FFFFFF',
    padding: 16,
    borderRadius: 12,
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
  subscriptionBadge: {
    fontSize: 13,
    color: COLORS.primary,
    fontWeight: '600',
  },
  subscribeBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.primary + '10',
    padding: 16,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: COLORS.primary + '30',
  },
  subscribeIcon: {
    fontSize: 28,
    marginRight: 14,
  },
  subscribeContent: {
    flex: 1,
  },
  subscribeTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  subscribeSubtitle: {
    fontSize: 13,
    color: COLORS.text.secondary,
    marginTop: 2,
  },
  subscribeArrow: {
    fontSize: 24,
    color: COLORS.primary,
    fontWeight: 'bold',
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
  bottomContainer: {
    padding: 16,
    backgroundColor: '#FFFFFF',
    borderTopWidth: 1,
    borderTopColor: '#E5E7EB',
  },
  placeOrderButton: {
    backgroundColor: COLORS.primary,
    padding: 16,
    borderRadius: 12,
    alignItems: 'center',
  },
  buttonDisabled: {
    backgroundColor: COLORS.primaryLight,
  },
  placeOrderButtonText: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: 'bold',
  },
  // Delivery Type
  deliveryTypeRow: {
    flexDirection: 'row',
    gap: 12,
  },
  deliveryTypeOption: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    backgroundColor: '#FFFFFF',
    paddingVertical: 14,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#E5E7EB',
  },
  deliveryTypeSelected: {
    borderColor: COLORS.primary,
    backgroundColor: COLORS.primaryFaded,
  },
  deliveryTypeLabel: {
    fontSize: 15,
    fontWeight: '600',
    color: '#6B7280',
  },
  deliveryTypeLabelSelected: {
    color: COLORS.primary,
  },
  // Input fields
  input: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
    fontSize: 16,
    color: '#1F2937',
    borderWidth: 1,
    borderColor: '#E5E7EB',
  },
  addressInput: {
    minHeight: 80,
    textAlignVertical: 'top',
  },
  bottomPadding: {
    height: 24,
  },
});

export default CheckoutScreen;
