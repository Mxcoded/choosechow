import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  Image,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useAuth } from '../../contexts';
import { COLORS } from '../../utils/theme';
import { scaleWidth, scaleFont } from '../../utils/dimensions';
import { ChooseChowLogo } from '../../assets';

type VendorDashboardProps = {
  navigation: NativeStackNavigationProp<any>;
};

// Sample stats data (will be replaced with API data)
const SAMPLE_STATS = {
  todayOrders: 12,
  pendingOrders: 3,
  totalEarnings: 45000,
  rating: 4.8,
  totalReviews: 156,
};

// Sample recent orders
const SAMPLE_ORDERS = [
  { id: 'ORD001', customer: 'John D.', items: 3, total: 4500, status: 'pending', time: '5 min ago' },
  { id: 'ORD002', customer: 'Sarah M.', items: 2, total: 3200, status: 'preparing', time: '15 min ago' },
  { id: 'ORD003', customer: 'Mike K.', items: 5, total: 8750, status: 'ready', time: '30 min ago' },
];

export const VendorDashboardScreen: React.FC<VendorDashboardProps> = ({ navigation }) => {
  const { user, logout } = useAuth();
  const insets = useSafeAreaInsets();
  const [refreshing, setRefreshing] = useState(false);
  const [isAvailable, setIsAvailable] = useState(true);

  const onRefresh = async () => {
    setRefreshing(true);
    // TODO: Fetch fresh data from API
    setTimeout(() => setRefreshing(false), 1000);
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
    return `₦${amount.toLocaleString()}`;
  };

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
          onPress={() => setIsAvailable(!isAvailable)}
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
            <Text style={styles.statIcon}>📦</Text>
            <Text style={styles.statValue}>{SAMPLE_STATS.todayOrders}</Text>
            <Text style={styles.statLabel}>Today's Orders</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statIcon}>⏳</Text>
            <Text style={[styles.statValue, { color: '#F59E0B' }]}>{SAMPLE_STATS.pendingOrders}</Text>
            <Text style={styles.statLabel}>Pending</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statIcon}>💰</Text>
            <Text style={styles.statValue}>{formatCurrency(SAMPLE_STATS.totalEarnings)}</Text>
            <Text style={styles.statLabel}>Total Earnings</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statIcon}>⭐</Text>
            <Text style={styles.statValue}>{SAMPLE_STATS.rating}</Text>
            <Text style={styles.statLabel}>{SAMPLE_STATS.totalReviews} reviews</Text>
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
          
          {SAMPLE_ORDERS.map((order) => (
            <TouchableOpacity 
              key={order.id} 
              style={styles.orderCard}
              onPress={() => navigation.navigate('VendorOrderDetail', { orderId: order.id })}
            >
              <View style={styles.orderHeader}>
                <View>
                  <Text style={styles.orderId}>#{order.id}</Text>
                  <Text style={styles.orderCustomer}>{order.customer}</Text>
                </View>
                <View style={[styles.statusBadge, { backgroundColor: `${getStatusColor(order.status)}20` }]}>
                  <Text style={[styles.statusText, { color: getStatusColor(order.status) }]}>
                    {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                  </Text>
                </View>
              </View>
              <View style={styles.orderFooter}>
                <Text style={styles.orderItems}>{order.items} items • {formatCurrency(order.total)}</Text>
                <Text style={styles.orderTime}>{order.time}</Text>
              </View>
            </TouchableOpacity>
          ))}
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
});

export default VendorDashboardScreen;
