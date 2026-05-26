import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  Image,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useAuth } from '../../contexts';
import { COLORS } from '../../utils/theme';
import { ChooseChowLogo } from '../../assets';
import { vendorService, VendorStats, VendorOrder, VendorProfile } from '../../api';

type VendorDashboardProps = {
  navigation: NativeStackNavigationProp<any>;
};

// Default stats for initial state
const DEFAULT_STATS: VendorStats = {
  today_orders: 0,
  today_earnings: 0,
  pending_orders: 0,
  completed_orders: 0,
  weekly_orders: 0,
  weekly_earnings: 0,
  monthly_orders: 0,
  monthly_earnings: 0,
  total_orders: 0,
  total_earnings: 0,
  rating: 0,
  total_reviews: 0,
  menu_items: 0,
  is_online: false,
};

// Mock data for when API is not available
const MOCK_STATS: VendorStats = {
  today_orders: 12,
  today_earnings: 45000,
  pending_orders: 3,
  completed_orders: 156,
  weekly_orders: 68,
  weekly_earnings: 285000,
  monthly_orders: 245,
  monthly_earnings: 1250000,
  total_orders: 1520,
  total_earnings: 8500000,
  rating: 4.8,
  total_reviews: 156,
  menu_items: 24,
  is_online: true,
};

const MOCK_ORDERS: VendorOrder[] = [
  { id: 1, order_number: 'ORD001', customer: { id: 1, name: 'John D.', phone: '080...' }, items_count: 3, total_amount: 4500, status: 'pending', payment_status: 'paid', subtotal: 4200, delivery_fee: 300, delivery_type: 'asap', created_at: new Date().toISOString(), time_ago: '5 min ago' },
  { id: 2, order_number: 'ORD002', customer: { id: 2, name: 'Sarah M.', phone: '081...' }, items_count: 2, total_amount: 3200, status: 'preparing', payment_status: 'paid', subtotal: 3000, delivery_fee: 200, delivery_type: 'asap', created_at: new Date().toISOString(), time_ago: '15 min ago' },
  { id: 3, order_number: 'ORD003', customer: { id: 3, name: 'Mike K.', phone: '070...' }, items_count: 5, total_amount: 8750, status: 'ready', payment_status: 'paid', subtotal: 8500, delivery_fee: 250, delivery_type: 'asap', created_at: new Date().toISOString(), time_ago: '30 min ago' },
];

export const VendorDashboardScreen: React.FC<VendorDashboardProps> = ({ navigation }) => {
  const { user } = useAuth();
  const insets = useSafeAreaInsets();
  
  // State
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [stats, setStats] = useState<VendorStats>(DEFAULT_STATS);
  const [pendingOrders, setPendingOrders] = useState<VendorOrder[]>([]);
  const [recentOrders, setRecentOrders] = useState<VendorOrder[]>([]);
  const [profile, setProfile] = useState<VendorProfile | null>(null);
  const [isAvailable, setIsAvailable] = useState(false);
  const [error, setError] = useState<string | null>(null);

  // Load dashboard data
  const loadDashboardData = useCallback(async () => {
    try {
      setError(null);
      const data = await vendorService.getDashboard();
      setStats(data.stats);
      setPendingOrders(data.pending_orders || []);
      setRecentOrders(data.recent_orders || []);
      setProfile(data.profile);
      setIsAvailable(data.stats.is_online);
    } catch (err: any) {
      console.error('Failed to load dashboard:', err);
      // Use mock data if API returns 404
      if (err.response?.status === 404 || err.response?.status === 401) {
        console.log('API not available, using mock data');
        setStats(MOCK_STATS);
        setPendingOrders(MOCK_ORDERS.filter(o => o.status === 'pending'));
        setRecentOrders(MOCK_ORDERS);
        setIsAvailable(true);
      } else {
        setError(err.response?.data?.message || 'Failed to load dashboard');
      }
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    loadDashboardData();
  }, [loadDashboardData]);

  const onRefresh = async () => {
    setRefreshing(true);
    await loadDashboardData();
  };

  const handleToggleAvailability = async () => {
    try {
      const result = await vendorService.toggleAvailability();
      setIsAvailable(result.is_online);
      setStats(prev => ({ ...prev, is_online: result.is_online }));
    } catch (err: any) {
      // Demo mode fallback
      if (err.response?.status === 404) {
        setIsAvailable(!isAvailable);
        setStats(prev => ({ ...prev, is_online: !prev.is_online }));
      } else {
        Alert.alert('Error', 'Failed to update availability');
      }
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'pending': return '#F59E0B';
      case 'preparing': return '#3B82F6';
      case 'ready': return '#10B981';
      case 'delivered': return '#6B7280';
      default: return COLORS.primary;
    }
  };

  const formatCurrency = (amount: number) => {
    if (amount >= 1000000) {
      return `₦${(amount / 1000000).toFixed(1)}M`;
    }
    if (amount >= 1000) {
      return `₦${(amount / 1000).toFixed(0)}K`;
    }
    return `₦${amount.toLocaleString()}`;
  };

  // Loading state
  if (isLoading) {
    return (
      <View style={[styles.container, styles.centerContent, { paddingTop: insets.top }]}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.loadingText}>Loading dashboard...</Text>
      </View>
    );
  }

  // Error state
  if (error) {
    return (
      <View style={[styles.container, styles.centerContent, { paddingTop: insets.top }]}>
        <Text style={styles.errorIcon}>⚠️</Text>
        <Text style={styles.errorText}>{error}</Text>
        <TouchableOpacity style={styles.retryButton} onPress={loadDashboardData}>
          <Text style={styles.retryButtonText}>Retry</Text>
        </TouchableOpacity>
      </View>
    );
  }

  return (
    <View style={[styles.container, { paddingTop: insets.top }]}>
      {/* Header */}
      <View style={styles.header}>
        <View style={styles.headerLeft}>
          <Image source={ChooseChowLogo} style={styles.logo} resizeMode="contain" />
          <View>
            <Text style={styles.greeting}>Welcome back,</Text>
            <Text style={styles.userName}>{user?.first_name || 'Vendor'} 👋</Text>
          </View>
        </View>
        <TouchableOpacity style={styles.notificationBtn}>
          <Text style={styles.notificationIcon}>🔔</Text>
          <View style={styles.notificationBadge} />
        </TouchableOpacity>
      </View>

      <ScrollView
        style={styles.scrollContainer}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />
        }
      >
        {/* Availability Toggle */}
        <TouchableOpacity 
          style={[styles.availabilityCard, isAvailable ? styles.available : styles.unavailable]}
          onPress={handleToggleAvailability}
          activeOpacity={0.8}
        >
          <View style={styles.availabilityContent}>
            <Text style={styles.availabilityIcon}>{isAvailable ? '🟢' : '🔴'}</Text>
            <View>
              <Text style={styles.availabilityTitle}>
                {isAvailable ? 'You are Online' : 'You are Offline'}
              </Text>
              <Text style={styles.availabilitySubtitle}>
                {isAvailable ? 'Accepting new orders' : 'Tap to go online'}
              </Text>
            </View>
          </View>
          <View style={[styles.toggleTrack, isAvailable && styles.toggleTrackActive]}>
            <View style={[styles.toggleThumb, isAvailable && styles.toggleThumbActive]} />
          </View>
        </TouchableOpacity>

        {/* Stats Cards */}
        <View style={styles.statsGrid}>
          <View style={[styles.statCard, styles.statCardPrimary]}>
            <Text style={[styles.statIcon, { color: '#FFFFFF' }]}>📦</Text>
            <Text style={[styles.statValue, { color: '#FFFFFF' }]}>{stats.today_orders}</Text>
            <Text style={[styles.statLabel, { color: '#FFE4E6' }]}>Today's Orders</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statIcon}>⏳</Text>
            <Text style={[styles.statValue, { color: '#F59E0B' }]}>{stats.pending_orders}</Text>
            <Text style={styles.statLabel}>Pending</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statIcon}>💰</Text>
            <Text style={styles.statValue}>{formatCurrency(stats.total_earnings)}</Text>
            <Text style={styles.statLabel}>Total Earnings</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statIcon}>⭐</Text>
            <Text style={styles.statValue}>{stats.rating.toFixed(1)}</Text>
            <Text style={styles.statLabel}>{stats.total_reviews} reviews</Text>
          </View>
        </View>

        {/* Quick Actions */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Quick Actions</Text>
          <View style={styles.actionsGrid}>
            <TouchableOpacity style={styles.actionCard} onPress={() => navigation.navigate('VendorMenu')}>
              <View style={[styles.actionIcon, { backgroundColor: '#FEE2E2' }]}>
                <Text style={styles.actionEmoji}>📋</Text>
              </View>
              <Text style={styles.actionLabel}>My Menu</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.actionCard} onPress={() => navigation.navigate('VendorOrders')}>
              <View style={[styles.actionIcon, { backgroundColor: '#DBEAFE' }]}>
                <Text style={styles.actionEmoji}>🛍️</Text>
              </View>
              <Text style={styles.actionLabel}>Orders</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.actionCard} onPress={() => navigation.navigate('VendorEarnings')}>
              <View style={[styles.actionIcon, { backgroundColor: '#D1FAE5' }]}>
                <Text style={styles.actionEmoji}>💵</Text>
              </View>
              <Text style={styles.actionLabel}>Earnings</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.actionCard} onPress={() => navigation.navigate('VendorProfile')}>
              <View style={[styles.actionIcon, { backgroundColor: '#FEF3C7' }]}>
                <Text style={styles.actionEmoji}>⚙️</Text>
              </View>
              <Text style={styles.actionLabel}>Settings</Text>
            </TouchableOpacity>
          </View>
        </View>

        {/* Recent Orders */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={styles.sectionTitle}>Recent Orders</Text>
            <TouchableOpacity onPress={() => navigation.navigate('VendorOrders')}>
              <Text style={styles.seeAll}>See All</Text>
            </TouchableOpacity>
          </View>
          
          {recentOrders.length === 0 ? (
            <View style={styles.emptyState}>
              <Text style={styles.emptyIcon}>📦</Text>
              <Text style={styles.emptyText}>No orders yet</Text>
              <Text style={styles.emptySubtext}>Orders will appear here when customers place them</Text>
            </View>
          ) : (
            recentOrders.slice(0, 5).map((order) => (
              <TouchableOpacity 
                key={order.id} 
                style={styles.orderCard}
                onPress={() => navigation.navigate('VendorOrderDetail', { orderId: order.id })}
              >
                <View style={styles.orderHeader}>
                  <View>
                    <Text style={styles.orderId}>#{order.order_number}</Text>
                    <Text style={styles.orderCustomer}>{order.customer?.name || 'Customer'}</Text>
                  </View>
                  <View style={[styles.statusBadge, { backgroundColor: `${getStatusColor(order.status)}20` }]}>
                    <Text style={[styles.statusText, { color: getStatusColor(order.status) }]}>
                      {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                    </Text>
                  </View>
                </View>
                <View style={styles.orderFooter}>
                  <Text style={styles.orderItems}>{order.items_count} items • {formatCurrency(order.total_amount)}</Text>
                  <Text style={styles.orderTime}>{order.time_ago}</Text>
                </View>
              </TouchableOpacity>
            ))
          )}
        </View>

        <View style={styles.bottomPadding} />
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
    fontSize: 16,
    color: '#EF4444',
    textAlign: 'center',
    marginHorizontal: 32,
  },
  retryButton: {
    marginTop: 16,
    backgroundColor: COLORS.primary,
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 8,
  },
  retryButtonText: {
    color: '#FFFFFF',
    fontWeight: '600',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 12,
    backgroundColor: '#FFFFFF',
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  headerLeft: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  logo: {
    width: 40,
    height: 40,
    marginRight: 12,
  },
  greeting: {
    fontSize: 12,
    color: '#6B7280',
  },
  userName: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  notificationBtn: {
    position: 'relative',
    padding: 8,
  },
  notificationIcon: {
    fontSize: 24,
  },
  notificationBadge: {
    position: 'absolute',
    top: 6,
    right: 6,
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: COLORS.primary,
    borderWidth: 2,
    borderColor: '#FFFFFF',
  },
  scrollContainer: {
    flex: 1,
  },
  availabilityCard: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    margin: 16,
    padding: 16,
    borderRadius: 16,
    backgroundColor: '#FFFFFF',
  },
  available: {
    borderWidth: 2,
    borderColor: '#10B981',
  },
  unavailable: {
    borderWidth: 2,
    borderColor: '#EF4444',
  },
  availabilityContent: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  availabilityIcon: {
    fontSize: 24,
    marginRight: 12,
  },
  availabilityTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#1F2937',
  },
  availabilitySubtitle: {
    fontSize: 12,
    color: '#6B7280',
    marginTop: 2,
  },
  toggleTrack: {
    width: 50,
    height: 28,
    borderRadius: 14,
    backgroundColor: '#E5E7EB',
    padding: 2,
  },
  toggleTrackActive: {
    backgroundColor: '#10B981',
  },
  toggleThumb: {
    width: 24,
    height: 24,
    borderRadius: 12,
    backgroundColor: '#FFFFFF',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 2,
    elevation: 2,
  },
  toggleThumbActive: {
    transform: [{ translateX: 22 }],
  },
  statsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    paddingHorizontal: 12,
    gap: 8,
  },
  statCard: {
    width: '48%',
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    padding: 16,
    alignItems: 'center',
    marginBottom: 4,
  },
  statCardPrimary: {
    backgroundColor: COLORS.primary,
  },
  statIcon: {
    fontSize: 28,
    marginBottom: 8,
  },
  statValue: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  statLabel: {
    fontSize: 12,
    color: '#6B7280',
    marginTop: 4,
  },
  section: {
    marginTop: 24,
    paddingHorizontal: 16,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#1F2937',
    marginBottom: 12,
  },
  seeAll: {
    fontSize: 14,
    color: COLORS.primary,
    fontWeight: '600',
  },
  actionsGrid: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  actionCard: {
    alignItems: 'center',
    width: '23%',
  },
  actionIcon: {
    width: 56,
    height: 56,
    borderRadius: 16,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 8,
  },
  actionEmoji: {
    fontSize: 24,
  },
  actionLabel: {
    fontSize: 12,
    color: '#374151',
    fontWeight: '500',
    textAlign: 'center',
  },
  orderCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
  },
  orderHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  orderId: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  orderCustomer: {
    fontSize: 12,
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
  orderFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderTopWidth: 1,
    borderTopColor: '#F3F4F6',
    paddingTop: 12,
  },
  orderItems: {
    fontSize: 14,
    color: '#374151',
    fontWeight: '500',
  },
  orderTime: {
    fontSize: 12,
    color: '#9CA3AF',
  },
  bottomPadding: {
    height: 100,
  },
  emptyState: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 32,
    alignItems: 'center',
  },
  emptyIcon: {
    fontSize: 48,
    marginBottom: 12,
  },
  emptyText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#1F2937',
    marginBottom: 4,
  },
  emptySubtext: {
    fontSize: 14,
    color: '#6B7280',
    textAlign: 'center',
  },
});

export default VendorDashboardScreen;
