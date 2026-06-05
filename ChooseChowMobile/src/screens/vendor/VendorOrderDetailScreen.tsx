import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
  Alert,
  Linking,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RouteProp } from '@react-navigation/native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { COLORS } from '../../utils/theme';
import { vendorService, VendorOrder, VendorOrderItem } from '../../api';

type VendorOrderDetailProps = {
  navigation: NativeStackNavigationProp<any>;
  route: RouteProp<{ params: { orderId: number } }, 'params'>;
};

type OrderStatus = 'pending' | 'pending_payment' | 'confirmed' | 'preparing' | 'ready' | 'out_for_delivery' | 'delivered' | 'completed' | 'cancelled';

const STATUS_CONFIG: Record<string, { color: string; bg: string; label: string; icon: string; description: string }> = {
  pending_payment: { color: '#F59E0B', bg: '#FEF3C7', label: 'Awaiting Payment', icon: '💳', description: 'Waiting for payment confirmation' },
  pending: { color: '#F59E0B', bg: '#FEF3C7', label: 'Pending', icon: '⏳', description: 'Waiting for you to start preparing' },
  confirmed: { color: '#8B5CF6', bg: '#EDE9FE', label: 'Confirmed', icon: '✓', description: 'Order confirmed, ready to prepare' },
  preparing: { color: '#3B82F6', bg: '#DBEAFE', label: 'Preparing', icon: '👨‍🍳', description: 'Food is being prepared' },
  ready: { color: '#10B981', bg: '#D1FAE5', label: 'Ready', icon: '✅', description: 'Ready for pickup/delivery' },
  out_for_delivery: { color: '#06B6D4', bg: '#CFFAFE', label: 'Out for Delivery', icon: '🚗', description: 'Order is on the way' },
  delivered: { color: '#6B7280', bg: '#F3F4F6', label: 'Delivered', icon: '📦', description: 'Order has been delivered' },
  completed: { color: '#6B7280', bg: '#F3F4F6', label: 'Completed', icon: '📦', description: 'Order has been delivered' },
  cancelled: { color: '#EF4444', bg: '#FEE2E2', label: 'Cancelled', icon: '❌', description: 'Order was cancelled' },
};

// Mock order for demo
const MOCK_ORDER: VendorOrder & { items: VendorOrderItem[] } = {
  id: 1,
  order_number: 'ORD001',
  customer: { id: 1, name: 'John Doe', phone: '08012345678' },
  items_count: 3,
  total_amount: 4500,
  status: 'pending',
  payment_status: 'paid',
  subtotal: 4200,
  delivery_fee: 300,
  delivery_type: 'asap',
  delivery_address: '123 Victoria Island, Lagos',
  notes: 'Please make it extra spicy',
  created_at: new Date().toISOString(),
  time_ago: '5 min ago',
  items: [
    { id: 1, menu_id: 1, name: 'Jollof Rice', quantity: 2, price: 1500, total: 3000, notes: 'Extra pepper' },
    { id: 2, menu_id: 2, name: 'Fried Plantain', quantity: 1, price: 500, total: 500, notes: undefined },
    { id: 3, menu_id: 3, name: 'Soft Drink', quantity: 1, price: 700, total: 700, notes: undefined },
  ],
};

export const VendorOrderDetailScreen: React.FC<VendorOrderDetailProps> = ({ navigation, route }) => {
  const { orderId } = route.params;
  const insets = useSafeAreaInsets();
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [order, setOrder] = useState<(VendorOrder & { items?: VendorOrderItem[] }) | null>(null);
  const [isUpdating, setIsUpdating] = useState(false);

  const loadOrder = useCallback(async () => {
    try {
      const data = await vendorService.getOrder(orderId);
      setOrder(data);
    } catch (err: any) {
      console.error('Failed to load order:', err);
      // Use mock data if API returns 404
      if (err.response?.status === 404 || err.response?.status === 401) {
        setOrder({ ...MOCK_ORDER, id: orderId });
      } else {
        Alert.alert('Error', 'Failed to load order details');
      }
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  }, [orderId]);

  useEffect(() => {
    loadOrder();
  }, [loadOrder]);

  const onRefresh = async () => {
    setRefreshing(true);
    await loadOrder();
  };

  const handleUpdateStatus = async (newStatus: OrderStatus) => {
    if (!order) return;

    setIsUpdating(true);
    try {
      const result = await vendorService.updateOrderStatus(order.id, newStatus);
      setOrder(result);
      Alert.alert('Success', `Order status updated to ${STATUS_CONFIG[newStatus].label}`);
    } catch (err: any) {
      // Demo mode fallback
      if (err.response?.status === 404) {
        setOrder(prev => prev ? { ...prev, status: newStatus } : null);
      } else {
        Alert.alert('Error', 'Failed to update order status');
      }
    } finally {
      setIsUpdating(false);
    }
  };

  const showStatusOptions = () => {
    if (!order) return;
    
    const availableStatuses = getNextStatuses(order.status, order.payment_status);
    if (availableStatuses.length === 0) {
      if (order.status === 'pending_payment' && order.payment_status !== 'paid') {
        Alert.alert('Awaiting Payment', 'You can update the order status once payment is confirmed.');
      } else {
        Alert.alert('Info', 'No status updates available for this order');
      }
      return;
    }

    Alert.alert(
      'Update Order Status',
      'Select the new status:',
      [
        ...availableStatuses.map(status => ({
          text: `${STATUS_CONFIG[status].icon} ${STATUS_CONFIG[status].label}`,
          onPress: () => handleUpdateStatus(status as OrderStatus),
        })),
        { text: 'Cancel', style: 'cancel' as const },
      ]
    );
  };

  const getNextStatuses = (currentStatus: string, paymentStatus?: string): string[] => {
    // If payment is pending, no status changes allowed until paid
    if (currentStatus === 'pending_payment' && paymentStatus !== 'paid') {
      return [];
    }
    
    switch (currentStatus) {
      case 'pending_payment':
        // If payment is now complete, allow proceeding
        return paymentStatus === 'paid' ? ['preparing', 'cancelled'] : [];
      case 'pending':
      case 'confirmed':
        return ['preparing', 'cancelled'];
      case 'preparing': 
        return ['ready', 'cancelled'];
      case 'ready': 
        return ['out_for_delivery', 'completed'];
      case 'out_for_delivery':
        return ['delivered', 'completed'];
      case 'delivered':
        return ['completed'];
      default: 
        return [];
    }
  };

  const handleCallCustomer = () => {
    if (order?.customer?.phone) {
      Linking.openURL(`tel:${order.customer.phone}`);
    }
  };

  const formatCurrency = (amount: number) => `₦${amount.toLocaleString()}`;

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-NG', {
      weekday: 'short',
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  if (isLoading) {
    return (
      <View style={[styles.container, styles.centerContent, { paddingTop: insets.top }]}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.loadingText}>Loading order...</Text>
      </View>
    );
  }

  if (!order) {
    return (
      <View style={[styles.container, styles.centerContent, { paddingTop: insets.top }]}>
        <Text style={styles.errorIcon}>⚠️</Text>
        <Text style={styles.errorText}>Order not found</Text>
        <TouchableOpacity style={styles.backButton} onPress={() => navigation.goBack()}>
          <Text style={styles.backButtonText}>Go Back</Text>
        </TouchableOpacity>
      </View>
    );
  }

  const statusConfig = STATUS_CONFIG[order.status] || STATUS_CONFIG.pending;
  const canUpdate = getNextStatuses(order.status, order.payment_status).length > 0;

  return (
    <View style={[styles.container, { paddingTop: insets.top }]}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
          <Text style={styles.backIcon}>←</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Order #{order.order_number}</Text>
        <View style={styles.headerRight} />
      </View>

      <ScrollView
        style={styles.scrollContainer}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />
        }
        contentContainerStyle={{ paddingBottom: insets.bottom + 100 }}
      >
        {/* Status Card */}
        <View style={[styles.statusCard, { backgroundColor: statusConfig.bg }]}>
          <View style={styles.statusHeader}>
            <Text style={styles.statusIcon}>{statusConfig.icon}</Text>
            <View style={styles.statusInfo}>
              <Text style={[styles.statusLabel, { color: statusConfig.color }]}>{statusConfig.label}</Text>
              <Text style={styles.statusDescription}>{statusConfig.description}</Text>
            </View>
          </View>
          {canUpdate && (
            <TouchableOpacity 
              style={[styles.updateStatusBtn, { backgroundColor: statusConfig.color }]}
              onPress={showStatusOptions}
              disabled={isUpdating}
            >
              {isUpdating ? (
                <ActivityIndicator size="small" color="#FFFFFF" />
              ) : (
                <Text style={styles.updateStatusText}>Update Status</Text>
              )}
            </TouchableOpacity>
          )}
        </View>

        {/* Order Timeline */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Order Info</Text>
          <View style={styles.infoCard}>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>📅 Placed</Text>
              <Text style={styles.infoValue}>{formatDate(order.created_at)}</Text>
            </View>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>🚚 Delivery Type</Text>
              <Text style={styles.infoValue}>
                {order.delivery_type === 'asap' ? 'ASAP Delivery' : 'Scheduled'}
              </Text>
            </View>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>💳 Payment</Text>
              <View style={[styles.paymentBadge, { 
                backgroundColor: order.payment_status === 'paid' ? '#D1FAE5' : '#FEF3C7' 
              }]}>
                <Text style={[styles.paymentText, { 
                  color: order.payment_status === 'paid' ? '#10B981' : '#F59E0B' 
                }]}>
                  {order.payment_status === 'paid' ? '✓ Paid' : '⏳ Pending'}
                </Text>
              </View>
            </View>
          </View>
        </View>

        {/* Customer Info */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Customer</Text>
          <View style={styles.customerCard}>
            <View style={styles.customerInfo}>
              <View style={styles.customerAvatar}>
                <Text style={styles.customerAvatarText}>
                  {order.customer?.name?.charAt(0) || 'C'}
                </Text>
              </View>
              <View style={styles.customerDetails}>
                <Text style={styles.customerName}>{order.customer?.name || 'Customer'}</Text>
                <Text style={styles.customerPhone}>{order.customer?.phone || 'No phone'}</Text>
              </View>
            </View>
            {order.customer?.phone && (
              <TouchableOpacity style={styles.callButton} onPress={handleCallCustomer}>
                <Text style={styles.callIcon}>📞</Text>
              </TouchableOpacity>
            )}
          </View>
          
          {order.delivery_address && (
            <View style={styles.addressCard}>
              <Text style={styles.addressLabel}>📍 Delivery Address</Text>
              <Text style={styles.addressText}>{order.delivery_address}</Text>
            </View>
          )}
        </View>

        {/* Order Items */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Order Items ({order.items_count})</Text>
          <View style={styles.itemsCard}>
            {(order.items || MOCK_ORDER.items).map((item, index) => (
              <View 
                key={item.id} 
                style={[
                  styles.itemRow,
                  index === (order.items || MOCK_ORDER.items).length - 1 && { borderBottomWidth: 0 }
                ]}
              >
                <View style={styles.itemQuantity}>
                  <Text style={styles.quantityText}>{item.quantity}x</Text>
                </View>
                <View style={styles.itemDetails}>
                  <Text style={styles.itemName}>{item.name}</Text>
                  {item.notes && (
                    <Text style={styles.itemNotes}>📝 {item.notes}</Text>
                  )}
                </View>
                <Text style={styles.itemPrice}>{formatCurrency(item.total)}</Text>
              </View>
            ))}
          </View>
        </View>

        {/* Order Notes */}
        {order.notes && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Special Instructions</Text>
            <View style={styles.notesCard}>
              <Text style={styles.notesText}>{order.notes}</Text>
            </View>
          </View>
        )}

        {/* Price Summary */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Payment Summary</Text>
          <View style={styles.summaryCard}>
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>Subtotal</Text>
              <Text style={styles.summaryValue}>{formatCurrency(order.subtotal)}</Text>
            </View>
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>Delivery Fee</Text>
              <Text style={styles.summaryValue}>{formatCurrency(order.delivery_fee)}</Text>
            </View>
            <View style={[styles.summaryRow, styles.totalRow]}>
              <Text style={styles.totalLabel}>Total</Text>
              <Text style={styles.totalValue}>{formatCurrency(order.total_amount)}</Text>
            </View>
          </View>
        </View>

        {/* Action Buttons */}
        {canUpdate && (
          <View style={styles.actionsSection}>
            {/* Pending Payment with payment completed - allow to start preparing */}
            {order.status === 'pending_payment' && order.payment_status === 'paid' && (
              <>
                <TouchableOpacity 
                  style={styles.primaryButton}
                  onPress={() => handleUpdateStatus('preparing')}
                  disabled={isUpdating}
                >
                  <Text style={styles.primaryButtonText}>👨‍🍳 Start Preparing</Text>
                </TouchableOpacity>
                <TouchableOpacity 
                  style={styles.dangerButton}
                  onPress={() => {
                    Alert.alert(
                      'Cancel Order',
                      'Are you sure you want to cancel this order?',
                      [
                        { text: 'No', style: 'cancel' },
                        { text: 'Yes, Cancel', style: 'destructive', onPress: () => handleUpdateStatus('cancelled') },
                      ]
                    );
                  }}
                  disabled={isUpdating}
                >
                  <Text style={styles.dangerButtonText}>❌ Cancel Order</Text>
                </TouchableOpacity>
              </>
            )}
            {(order.status === 'pending' || order.status === 'confirmed') && (
              <>
                <TouchableOpacity 
                  style={styles.primaryButton}
                  onPress={() => handleUpdateStatus('preparing')}
                  disabled={isUpdating}
                >
                  <Text style={styles.primaryButtonText}>👨‍🍳 Start Preparing</Text>
                </TouchableOpacity>
                <TouchableOpacity 
                  style={styles.dangerButton}
                  onPress={() => {
                    Alert.alert(
                      'Cancel Order',
                      'Are you sure you want to cancel this order?',
                      [
                        { text: 'No', style: 'cancel' },
                        { text: 'Yes, Cancel', style: 'destructive', onPress: () => handleUpdateStatus('cancelled') },
                      ]
                    );
                  }}
                  disabled={isUpdating}
                >
                  <Text style={styles.dangerButtonText}>❌ Cancel Order</Text>
                </TouchableOpacity>
              </>
            )}
            {order.status === 'preparing' && (
              <>
                <TouchableOpacity 
                  style={styles.primaryButton}
                  onPress={() => handleUpdateStatus('ready')}
                  disabled={isUpdating}
                >
                  <Text style={styles.primaryButtonText}>✅ Mark as Ready</Text>
                </TouchableOpacity>
                <TouchableOpacity 
                  style={styles.dangerButton}
                  onPress={() => {
                    Alert.alert(
                      'Cancel Order',
                      'Are you sure you want to cancel this order?',
                      [
                        { text: 'No', style: 'cancel' },
                        { text: 'Yes, Cancel', style: 'destructive', onPress: () => handleUpdateStatus('cancelled') },
                      ]
                    );
                  }}
                  disabled={isUpdating}
                >
                  <Text style={styles.dangerButtonText}>❌ Cancel Order</Text>
                </TouchableOpacity>
              </>
            )}
            {order.status === 'ready' && (
              <>
                <TouchableOpacity 
                  style={styles.primaryButton}
                  onPress={() => handleUpdateStatus('out_for_delivery')}
                  disabled={isUpdating}
                >
                  <Text style={styles.primaryButtonText}>🚗 Out for Delivery</Text>
                </TouchableOpacity>
                <TouchableOpacity 
                  style={[styles.primaryButton, { backgroundColor: '#10B981' }]}
                  onPress={() => handleUpdateStatus('completed')}
                  disabled={isUpdating}
                >
                  <Text style={styles.primaryButtonText}>📦 Mark as Completed</Text>
                </TouchableOpacity>
              </>
            )}
            {order.status === 'out_for_delivery' && (
              <TouchableOpacity 
                style={styles.primaryButton}
                onPress={() => handleUpdateStatus('delivered')}
                disabled={isUpdating}
              >
                <Text style={styles.primaryButtonText}>📦 Mark as Delivered</Text>
              </TouchableOpacity>
            )}
            {order.status === 'delivered' && (
              <TouchableOpacity 
                style={[styles.primaryButton, { backgroundColor: '#10B981' }]}
                onPress={() => handleUpdateStatus('completed')}
                disabled={isUpdating}
              >
                <Text style={styles.primaryButtonText}>✓ Complete Order</Text>
              </TouchableOpacity>
            )}
          </View>
        )}
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
  },
  centerContent: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    marginTop: 16,
    fontSize: 16,
    color: '#6B7280',
  },
  errorIcon: {
    fontSize: 48,
    marginBottom: 16,
  },
  errorText: {
    fontSize: 18,
    color: '#6B7280',
    marginBottom: 24,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    paddingVertical: 12,
    backgroundColor: '#FFFFFF',
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  backBtn: {
    padding: 8,
  },
  backIcon: {
    fontSize: 24,
    color: '#1F2937',
  },
  backButton: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 8,
  },
  backButtonText: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: '600',
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  headerRight: {
    width: 40,
  },
  scrollContainer: {
    flex: 1,
  },
  statusCard: {
    margin: 16,
    padding: 16,
    borderRadius: 16,
  },
  statusHeader: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  statusIcon: {
    fontSize: 40,
    marginRight: 16,
  },
  statusInfo: {
    flex: 1,
  },
  statusLabel: {
    fontSize: 20,
    fontWeight: 'bold',
  },
  statusDescription: {
    fontSize: 14,
    color: '#6B7280',
    marginTop: 4,
  },
  updateStatusBtn: {
    marginTop: 16,
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: 'center',
  },
  updateStatusText: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: '600',
  },
  section: {
    marginHorizontal: 16,
    marginBottom: 16,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#1F2937',
    marginBottom: 12,
  },
  infoCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 4,
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 14,
    paddingHorizontal: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#F3F4F6',
  },
  infoLabel: {
    fontSize: 14,
    color: '#6B7280',
  },
  infoValue: {
    fontSize: 14,
    color: '#1F2937',
    fontWeight: '500',
  },
  paymentBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 8,
  },
  paymentText: {
    fontSize: 12,
    fontWeight: '600',
  },
  customerCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  customerInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  customerAvatar: {
    width: 48,
    height: 48,
    borderRadius: 24,
    backgroundColor: COLORS.primary,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  customerAvatarText: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  customerDetails: {
    flex: 1,
  },
  customerName: {
    fontSize: 16,
    fontWeight: '600',
    color: '#1F2937',
  },
  customerPhone: {
    fontSize: 14,
    color: '#6B7280',
    marginTop: 2,
  },
  callButton: {
    width: 48,
    height: 48,
    borderRadius: 24,
    backgroundColor: '#D1FAE5',
    justifyContent: 'center',
    alignItems: 'center',
  },
  callIcon: {
    fontSize: 20,
  },
  addressCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
    marginTop: 12,
  },
  addressLabel: {
    fontSize: 12,
    color: '#6B7280',
    marginBottom: 8,
  },
  addressText: {
    fontSize: 14,
    color: '#1F2937',
    lineHeight: 20,
  },
  itemsCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
  },
  itemRow: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#F3F4F6',
  },
  itemQuantity: {
    width: 36,
    height: 36,
    borderRadius: 8,
    backgroundColor: '#F3F4F6',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  quantityText: {
    fontSize: 14,
    fontWeight: '600',
    color: '#374151',
  },
  itemDetails: {
    flex: 1,
  },
  itemName: {
    fontSize: 15,
    fontWeight: '500',
    color: '#1F2937',
  },
  itemNotes: {
    fontSize: 12,
    color: '#F59E0B',
    marginTop: 4,
  },
  itemPrice: {
    fontSize: 14,
    fontWeight: '600',
    color: '#1F2937',
  },
  notesCard: {
    backgroundColor: '#FEF3C7',
    borderRadius: 12,
    padding: 16,
    borderLeftWidth: 4,
    borderLeftColor: '#F59E0B',
  },
  notesText: {
    fontSize: 14,
    color: '#92400E',
    lineHeight: 20,
  },
  summaryCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
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
  },
  totalRow: {
    marginTop: 8,
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: '#E5E7EB',
    marginBottom: 0,
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
  actionsSection: {
    margin: 16,
    gap: 12,
  },
  primaryButton: {
    backgroundColor: COLORS.primary,
    paddingVertical: 16,
    borderRadius: 12,
    alignItems: 'center',
  },
  primaryButtonText: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: '600',
  },
  dangerButton: {
    backgroundColor: '#FEE2E2',
    paddingVertical: 16,
    borderRadius: 12,
    alignItems: 'center',
  },
  dangerButtonText: {
    color: '#EF4444',
    fontSize: 16,
    fontWeight: '600',
  },
});

export default VendorOrderDetailScreen;
