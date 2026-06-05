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
  FlatList,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { COLORS } from '../../utils/theme';
import { vendorService, VendorOrder } from '../../api';

type VendorOrdersProps = {
  navigation: NativeStackNavigationProp<any>;
};

const STATUS_FILTERS = ['all', 'pending', 'preparing', 'ready', 'completed', 'cancelled'];

const STATUS_CONFIG: Record<string, { color: string; bg: string; label: string; icon: string }> = {
  pending: { color: '#F59E0B', bg: '#FEF3C7', label: 'Pending', icon: '⏳' },
  preparing: { color: '#3B82F6', bg: '#DBEAFE', label: 'Preparing', icon: '👨‍🍳' },
  ready: { color: '#10B981', bg: '#D1FAE5', label: 'Ready', icon: '✅' },
  completed: { color: '#6B7280', bg: '#F3F4F6', label: 'Completed', icon: '📦' },
  cancelled: { color: '#EF4444', bg: '#FEE2E2', label: 'Cancelled', icon: '❌' },
};

// Mock orders for demo mode
const MOCK_ORDERS: VendorOrder[] = [
  { id: 1, order_number: 'ORD001', customer: { id: 1, name: 'John D.', phone: '080...' }, items_count: 3, total_amount: 4500, status: 'pending', payment_status: 'paid', subtotal: 4200, delivery_fee: 300, delivery_type: 'asap', created_at: new Date().toISOString(), time_ago: '5 min ago' },
  { id: 2, order_number: 'ORD002', customer: { id: 2, name: 'Sarah M.', phone: '081...' }, items_count: 2, total_amount: 3200, status: 'preparing', payment_status: 'paid', subtotal: 3000, delivery_fee: 200, delivery_type: 'asap', created_at: new Date().toISOString(), time_ago: '15 min ago' },
  { id: 3, order_number: 'ORD003', customer: { id: 3, name: 'Mike K.', phone: '070...' }, items_count: 5, total_amount: 8750, status: 'ready', payment_status: 'paid', subtotal: 8500, delivery_fee: 250, delivery_type: 'asap', created_at: new Date().toISOString(), time_ago: '30 min ago' },
  { id: 4, order_number: 'ORD004', customer: { id: 4, name: 'Jane O.', phone: '090...' }, items_count: 1, total_amount: 2000, status: 'completed', payment_status: 'paid', subtotal: 1800, delivery_fee: 200, delivery_type: 'scheduled', created_at: new Date().toISOString(), time_ago: '1 hour ago' },
];

export const VendorOrdersScreen: React.FC<VendorOrdersProps> = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [orders, setOrders] = useState<VendorOrder[]>([]);
  const [selectedStatus, setSelectedStatus] = useState('all');
  const [updatingOrderId, setUpdatingOrderId] = useState<number | null>(null);

  const loadOrders = useCallback(async () => {
    try {
      const params = selectedStatus !== 'all' ? { status: selectedStatus } : {};
      const response = await vendorService.getOrders(params);
      setOrders(response.data || []);
    } catch (err: any) {
      console.error('Failed to load orders:', err);
      // Use mock data if API returns 404
      if (err.response?.status === 404 || err.response?.status === 401) {
        const filtered = selectedStatus === 'all' 
          ? MOCK_ORDERS 
          : MOCK_ORDERS.filter(o => o.status === selectedStatus);
        setOrders(filtered);
      }
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  }, [selectedStatus]);

  useEffect(() => {
    loadOrders();
  }, [loadOrders]);

  const onRefresh = async () => {
    setRefreshing(true);
    await loadOrders();
  };

  const handleUpdateStatus = async (orderId: number, newStatus: VendorOrder['status']) => {
    setUpdatingOrderId(orderId);
    try {
      await vendorService.updateOrderStatus(orderId, newStatus);
      setOrders(prev => 
        prev.map(order => 
          order.id === orderId ? { ...order, status: newStatus } : order
        )
      );
      Alert.alert('Success', `Order status updated to ${STATUS_CONFIG[newStatus].label}`);
    } catch (err: any) {
      // Demo mode fallback
      if (err.response?.status === 404) {
        setOrders(prev => 
          prev.map(order => 
            order.id === orderId ? { ...order, status: newStatus } : order
          )
        );
      } else {
        Alert.alert('Error', 'Failed to update order status');
      }
    } finally {
      setUpdatingOrderId(null);
    }
  };

  const showStatusOptions = (order: VendorOrder) => {
    const availableStatuses = getNextStatuses(order.status);
    if (availableStatuses.length === 0) return;

    Alert.alert(
      'Update Order Status',
      `Order #${order.order_number}`,
      [
        ...availableStatuses.map(status => ({
          text: `${STATUS_CONFIG[status].icon} ${STATUS_CONFIG[status].label}`,
          onPress: () => handleUpdateStatus(order.id, status as VendorOrder['status']),
        })),
        { text: 'Cancel', style: 'cancel' as const },
      ]
    );
  };

  const getNextStatuses = (currentStatus: string): string[] => {
    switch (currentStatus) {
      case 'pending': return ['preparing', 'cancelled'];
      case 'preparing': return ['ready', 'cancelled'];
      case 'ready': return ['completed'];
      default: return [];
    }
  };

  const formatCurrency = (amount: number) => `₦${amount.toLocaleString()}`;

  const renderOrderCard = (order: VendorOrder) => {
    const config = STATUS_CONFIG[order.status] || STATUS_CONFIG.pending;
    const isUpdating = updatingOrderId === order.id;
    const canUpdate = getNextStatuses(order.status).length > 0;

    return (
      <TouchableOpacity
        key={order.id}
        style={styles.orderCard}
        onPress={() => navigation.navigate('VendorOrderDetail', { orderId: order.id })}
        activeOpacity={0.7}
      >
        <View style={styles.orderHeader}>
          <View>
            <Text style={styles.orderNumber}>#{order.order_number}</Text>
            <Text style={styles.customerName}>{order.customer?.name || 'Customer'}</Text>
          </View>
          <View style={[styles.statusBadge, { backgroundColor: config.bg }]}>
            <Text style={[styles.statusText, { color: config.color }]}>
              {config.icon} {config.label}
            </Text>
          </View>
        </View>

        <View style={styles.orderDetails}>
          <View style={styles.orderInfo}>
            <Text style={styles.orderLabel}>Items</Text>
            <Text style={styles.orderValue}>{order.items_count}</Text>
          </View>
          <View style={styles.orderInfo}>
            <Text style={styles.orderLabel}>Total</Text>
            <Text style={styles.orderValueBold}>{formatCurrency(order.total_amount)}</Text>
          </View>
          <View style={styles.orderInfo}>
            <Text style={styles.orderLabel}>Type</Text>
            <Text style={styles.orderValue}>{order.delivery_type === 'asap' ? 'ASAP' : 'Scheduled'}</Text>
          </View>
        </View>

        <View style={styles.orderFooter}>
          <Text style={styles.orderTime}>{order.time_ago}</Text>
          {canUpdate && (
            <TouchableOpacity
              style={[styles.updateButton, isUpdating && styles.updateButtonDisabled]}
              onPress={() => showStatusOptions(order)}
              disabled={isUpdating}
            >
              {isUpdating ? (
                <ActivityIndicator size="small" color="#FFFFFF" />
              ) : (
                <Text style={styles.updateButtonText}>Update Status</Text>
              )}
            </TouchableOpacity>
          )}
        </View>
      </TouchableOpacity>
    );
  };

  if (isLoading) {
    return (
      <View style={[styles.container, styles.centerContent, { paddingTop: insets.top }]}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.loadingText}>Loading orders...</Text>
      </View>
    );
  }

  return (
    <View style={[styles.container, { paddingTop: insets.top }]}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
          <Text style={styles.backIcon}>←</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Orders</Text>
        <View style={styles.headerRight} />
      </View>

      {/* Status Filter */}
      <ScrollView 
        horizontal 
        showsHorizontalScrollIndicator={false}
        style={styles.filterContainer}
        contentContainerStyle={styles.filterContent}
      >
        {STATUS_FILTERS.map(status => (
          <TouchableOpacity
            key={status}
            style={[
              styles.filterChip,
              selectedStatus === status && styles.filterChipActive
            ]}
            onPress={() => setSelectedStatus(status)}
          >
            <Text style={[
              styles.filterChipText,
              selectedStatus === status && styles.filterChipTextActive
            ]}>
              {status === 'all' ? 'All' : STATUS_CONFIG[status]?.label || status}
            </Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      {/* Orders List */}
      <FlatList
        data={orders}
        keyExtractor={(item) => String(item.id)}
        renderItem={({ item }) => renderOrderCard(item)}
        contentContainerStyle={styles.listContent}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />
        }
        ListEmptyComponent={
          <View style={styles.emptyState}>
            <Text style={styles.emptyIcon}>📦</Text>
            <Text style={styles.emptyText}>No orders found</Text>
            <Text style={styles.emptySubtext}>
              {selectedStatus === 'all' 
                ? 'Orders will appear here when customers place them'
                : `No ${STATUS_CONFIG[selectedStatus]?.label.toLowerCase()} orders`}
            </Text>
          </View>
        }
      />
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
  backButton: {
    padding: 8,
  },
  backIcon: {
    fontSize: 24,
    color: '#1F2937',
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  headerRight: {
    width: 40,
  },
  filterContainer: {
    backgroundColor: '#FFFFFF',
    maxHeight: 56,
  },
  filterContent: {
    paddingHorizontal: 12,
    paddingVertical: 12,
    gap: 8,
  },
  filterChip: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
    backgroundColor: '#F3F4F6',
    marginRight: 8,
  },
  filterChipActive: {
    backgroundColor: COLORS.primary,
  },
  filterChipText: {
    fontSize: 14,
    fontWeight: '500',
    color: '#6B7280',
  },
  filterChipTextActive: {
    color: '#FFFFFF',
  },
  listContent: {
    padding: 16,
    paddingBottom: 100,
  },
  orderCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 4,
    elevation: 2,
  },
  orderHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  orderNumber: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  customerName: {
    fontSize: 14,
    color: '#6B7280',
    marginTop: 2,
  },
  statusBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 12,
    fontWeight: '600',
  },
  orderDetails: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 12,
    borderTopWidth: 1,
    borderTopColor: '#F3F4F6',
    borderBottomWidth: 1,
    borderBottomColor: '#F3F4F6',
  },
  orderInfo: {
    alignItems: 'center',
  },
  orderLabel: {
    fontSize: 12,
    color: '#9CA3AF',
    marginBottom: 4,
  },
  orderValue: {
    fontSize: 14,
    color: '#374151',
    fontWeight: '500',
  },
  orderValueBold: {
    fontSize: 14,
    color: '#1F2937',
    fontWeight: 'bold',
  },
  orderFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 12,
  },
  orderTime: {
    fontSize: 12,
    color: '#9CA3AF',
  },
  updateButton: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 8,
    minWidth: 100,
    alignItems: 'center',
  },
  updateButtonDisabled: {
    backgroundColor: '#9CA3AF',
  },
  updateButtonText: {
    color: '#FFFFFF',
    fontSize: 12,
    fontWeight: '600',
  },
  emptyState: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 48,
    alignItems: 'center',
    marginTop: 24,
  },
  emptyIcon: {
    fontSize: 48,
    marginBottom: 16,
  },
  emptyText: {
    fontSize: 18,
    fontWeight: '600',
    color: '#1F2937',
    marginBottom: 8,
  },
  emptySubtext: {
    fontSize: 14,
    color: '#6B7280',
    textAlign: 'center',
  },
});

export default VendorOrdersScreen;
