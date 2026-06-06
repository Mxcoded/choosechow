import React, { useState, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  RefreshControl,
} from 'react-native';
import { useFocusEffect } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { orderService } from '../../api';
import { COLORS } from '../../utils/theme';
import type { Order } from '../../types';

type Props = {
  navigation: NativeStackNavigationProp<any>;
};

type TabType = 'active' | 'history';

const STATUS_LABELS: Record<string, string> = {
  pending_payment: 'Awaiting Payment',
  pending: 'Order Received',
  confirmed: 'Confirmed',
  preparing: 'Preparing',
  ready: 'Ready',
  out_for_delivery: 'Out for Delivery',
  delivered: 'Delivered',
  cancelled: 'Cancelled',
};

const STATUS_COLORS: Record<string, string> = {
  pending_payment: '#F59E0B',
  pending: '#3B82F6',
  confirmed: '#10B981',
  preparing: '#F59E0B',
  ready: '#8B5CF6',
  out_for_delivery: '#3B82F6',
  delivered: '#10B981',
  cancelled: '#EF4444',
};

export const OrdersScreen: React.FC<Props> = ({ navigation }) => {
  const [activeTab, setActiveTab] = useState<TabType>('active');
  const [orders, setOrders] = useState<Order[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchOrders = useCallback(async () => {
    try {
      if (activeTab === 'active') {
        const data = await orderService.getActiveOrders();
        setOrders(data);
      } else {
        const data = await orderService.getOrders({});
        setOrders((data as any).data || []);
      }
    } catch {
      setOrders([]);
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  }, [activeTab]);

  useFocusEffect(
    useCallback(() => {
      setIsLoading(true);
      fetchOrders();
    }, [fetchOrders])
  );

  const onRefresh = () => {
    setRefreshing(true);
    fetchOrders();
  };

  const filteredOrders = activeTab === 'active'
    ? orders.filter(o => !['delivered', 'completed', 'cancelled'].includes(o.status))
    : orders.filter(o => ['delivered', 'completed', 'cancelled'].includes(o.status));

  const renderOrderCard = (order: Order) => (
    <View
      key={order.id}
      style={styles.orderCard}
    >
      <View style={styles.orderHeader}>
        <Text style={styles.orderNumber}>#{order.order_number}</Text>
        <View style={[styles.statusBadge, { backgroundColor: (STATUS_COLORS[order.status] || '#9CA3AF') + '20' }]}>
          <View style={[styles.statusDot, { backgroundColor: STATUS_COLORS[order.status] || '#9CA3AF' }]} />
          <Text style={[styles.statusText, { color: STATUS_COLORS[order.status] || '#9CA3AF' }]}>
            {STATUS_LABELS[order.status] || order.status}
          </Text>
        </View>
      </View>

      <View style={styles.orderItems}>
        {order.items?.slice(0, 3).map((item, idx) => (
          <Text key={idx} style={styles.itemText} numberOfLines={1}>
            {item.quantity}x {item.name}
          </Text>
        ))}
        {order.items && order.items.length > 3 && (
          <Text style={styles.moreItems}>+{order.items.length - 3} more items</Text>
        )}
      </View>

      <View style={styles.orderFooter}>
        <Text style={styles.orderTotal}>₦{(order.total_amount || 0).toLocaleString()}</Text>
        <Text style={styles.orderDate}>
          {new Date(order.created_at).toLocaleDateString('en-NG', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
          })}
        </Text>
      </View>
    </View>
  );

  return (
    <View style={styles.container}>
      <View style={styles.tabRow}>
        <TouchableOpacity
          style={[styles.tab, activeTab === 'active' && styles.tabActive]}
          onPress={() => setActiveTab('active')}
        >
          <Text style={[styles.tabText, activeTab === 'active' && styles.tabTextActive]}>Active</Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.tab, activeTab === 'history' && styles.tabActive]}
          onPress={() => setActiveTab('history')}
        >
          <Text style={[styles.tabText, activeTab === 'history' && styles.tabTextActive]}>History</Text>
        </TouchableOpacity>
      </View>

      {isLoading ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color={COLORS.primary} />
        </View>
      ) : filteredOrders.length === 0 ? (
        <View style={styles.centerContainer}>
          <Text style={styles.emptyIcon}>{activeTab === 'active' ? '🛵' : '📋'}</Text>
          <Text style={styles.emptyTitle}>
            {activeTab === 'active' ? 'No Active Orders' : 'No Order History'}
          </Text>
          <Text style={styles.emptySubtitle}>
            {activeTab === 'active'
              ? 'Your current orders will appear here'
              : 'Completed orders will appear here'}
          </Text>
          {activeTab === 'active' && (
            <TouchableOpacity
              style={styles.browseButton}
              onPress={() => navigation.navigate('MainTabs', { screen: 'Home' })}
            >
              <Text style={styles.browseButtonText}>Browse Chefs</Text>
            </TouchableOpacity>
          )}
        </View>
      ) : (
        <ScrollView
          style={styles.scrollView}
          showsVerticalScrollIndicator={false}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />
          }
        >
          <View style={styles.ordersList}>
            {filteredOrders.map(renderOrderCard)}
          </View>
          <View style={{ height: 100 }} />
        </ScrollView>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
  },
  tabRow: {
    flexDirection: 'row',
    backgroundColor: '#FFFFFF',
    paddingHorizontal: 16,
    paddingVertical: 12,
    gap: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  tab: {
    flex: 1,
    paddingVertical: 10,
    borderRadius: 10,
    backgroundColor: '#F3F4F6',
    alignItems: 'center',
  },
  tabActive: {
    backgroundColor: COLORS.primaryFaded,
  },
  tabText: {
    fontSize: 15,
    fontWeight: '600',
    color: '#6B7280',
  },
  tabTextActive: {
    color: COLORS.primary,
  },
  centerContainer: {
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
    marginBottom: 8,
  },
  emptySubtitle: {
    fontSize: 14,
    color: '#6B7280',
    textAlign: 'center',
    marginBottom: 24,
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
  ordersList: {
    padding: 16,
    gap: 12,
  },
  orderCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 14,
    padding: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.06,
    shadowRadius: 6,
    elevation: 2,
  },
  orderHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  orderNumber: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
    gap: 6,
  },
  statusDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
  },
  statusText: {
    fontSize: 12,
    fontWeight: '600',
  },
  orderItems: {
    marginBottom: 12,
  },
  itemText: {
    fontSize: 14,
    color: '#6B7280',
    lineHeight: 20,
  },
  moreItems: {
    fontSize: 13,
    color: '#9CA3AF',
    marginTop: 2,
  },
  orderFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: '#F3F4F6',
  },
  orderTotal: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  orderDate: {
    fontSize: 12,
    color: '#9CA3AF',
  },
});

export default OrdersScreen;
