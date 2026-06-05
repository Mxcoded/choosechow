import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { COLORS } from '../../utils/theme';
import { vendorService, VendorEarnings } from '../../api';

type VendorEarningsProps = {
  navigation: NativeStackNavigationProp<any>;
};

type Period = 'day' | 'week' | 'month' | 'year' | 'all';

const PERIOD_OPTIONS: { key: Period; label: string }[] = [
  { key: 'day', label: 'Today' },
  { key: 'week', label: 'This Week' },
  { key: 'month', label: 'This Month' },
  { key: 'year', label: 'This Year' },
  { key: 'all', label: 'All Time' },
];

// Mock earnings data
const MOCK_EARNINGS: Record<Period, VendorEarnings> = {
  day: {
    period: 'day',
    total_earnings: 45000,
    total_orders: 12,
    average_order_value: 3750,
    pending_payout: 45000,
    daily_breakdown: [
      { date: new Date().toISOString().split('T')[0], day: 'Today', earnings: 45000, orders: 12 },
    ],
  },
  week: {
    period: 'week',
    total_earnings: 285000,
    total_orders: 68,
    average_order_value: 4191,
    pending_payout: 285000,
    daily_breakdown: [
      { date: '2024-01-07', day: 'Sun', earnings: 35000, orders: 8 },
      { date: '2024-01-06', day: 'Sat', earnings: 55000, orders: 14 },
      { date: '2024-01-05', day: 'Fri', earnings: 48000, orders: 12 },
      { date: '2024-01-04', day: 'Thu', earnings: 32000, orders: 8 },
      { date: '2024-01-03', day: 'Wed', earnings: 40000, orders: 10 },
      { date: '2024-01-02', day: 'Tue', earnings: 35000, orders: 8 },
      { date: '2024-01-01', day: 'Mon', earnings: 40000, orders: 8 },
    ],
  },
  month: {
    period: 'month',
    total_earnings: 1250000,
    total_orders: 245,
    average_order_value: 5102,
    pending_payout: 450000,
    daily_breakdown: [],
  },
  year: {
    period: 'year',
    total_earnings: 8500000,
    total_orders: 1520,
    average_order_value: 5592,
    pending_payout: 450000,
    daily_breakdown: [],
  },
  all: {
    period: 'all',
    total_earnings: 12500000,
    total_orders: 2350,
    average_order_value: 5319,
    pending_payout: 450000,
    daily_breakdown: [],
  },
};

export const VendorEarningsScreen: React.FC<VendorEarningsProps> = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [selectedPeriod, setSelectedPeriod] = useState<Period>('week');
  const [earnings, setEarnings] = useState<VendorEarnings | null>(null);

  const loadEarnings = useCallback(async () => {
    try {
      const data = await vendorService.getEarnings(selectedPeriod);
      setEarnings(data);
    } catch (err: any) {
      console.error('Failed to load earnings:', err);
      // Use mock data if API returns 404
      if (err.response?.status === 404 || err.response?.status === 401) {
        setEarnings(MOCK_EARNINGS[selectedPeriod]);
      }
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  }, [selectedPeriod]);

  useEffect(() => {
    setIsLoading(true);
    loadEarnings();
  }, [loadEarnings]);

  const onRefresh = async () => {
    setRefreshing(true);
    await loadEarnings();
  };

  const formatCurrency = (amount: number) => {
    if (amount >= 1000000) {
      return `₦${(amount / 1000000).toFixed(2)}M`;
    }
    if (amount >= 1000) {
      return `₦${(amount / 1000).toFixed(0)}K`;
    }
    return `₦${amount.toLocaleString()}`;
  };

  const formatFullCurrency = (amount: number) => `₦${amount.toLocaleString()}`;

  const getMaxEarnings = () => {
    if (!earnings?.daily_breakdown?.length) return 1;
    return Math.max(...earnings.daily_breakdown.map(d => d.earnings));
  };

  if (isLoading) {
    return (
      <View style={[styles.container, styles.centerContent, { paddingTop: insets.top }]}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.loadingText}>Loading earnings...</Text>
      </View>
    );
  }

  const maxEarnings = getMaxEarnings();

  return (
    <View style={[styles.container, { paddingTop: insets.top }]}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
          <Text style={styles.backIcon}>←</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Earnings</Text>
        <View style={styles.headerRight} />
      </View>

      <ScrollView
        style={styles.scrollContainer}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />
        }
      >
        {/* Period Selector */}
        <ScrollView 
          horizontal 
          showsHorizontalScrollIndicator={false}
          style={styles.periodContainer}
          contentContainerStyle={styles.periodContent}
        >
          {PERIOD_OPTIONS.map((option) => (
            <TouchableOpacity
              key={option.key}
              style={[
                styles.periodChip,
                selectedPeriod === option.key && styles.periodChipActive
              ]}
              onPress={() => setSelectedPeriod(option.key)}
            >
              <Text style={[
                styles.periodChipText,
                selectedPeriod === option.key && styles.periodChipTextActive
              ]}>
                {option.label}
              </Text>
            </TouchableOpacity>
          ))}
        </ScrollView>

        {/* Main Earnings Card */}
        <View style={styles.mainCard}>
          <Text style={styles.mainCardLabel}>Total Earnings</Text>
          <Text style={styles.mainCardValue}>
            {formatFullCurrency(earnings?.total_earnings || 0)}
          </Text>
          <Text style={styles.mainCardPeriod}>
            {PERIOD_OPTIONS.find(p => p.key === selectedPeriod)?.label}
          </Text>
        </View>

        {/* Stats Grid */}
        <View style={styles.statsGrid}>
          <View style={styles.statCard}>
            <Text style={styles.statIcon}>📦</Text>
            <Text style={styles.statValue}>{earnings?.total_orders || 0}</Text>
            <Text style={styles.statLabel}>Total Orders</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statIcon}>💰</Text>
            <Text style={styles.statValue}>
              {formatCurrency(earnings?.average_order_value || 0)}
            </Text>
            <Text style={styles.statLabel}>Avg. Order</Text>
          </View>
          <View style={[styles.statCard, styles.statCardHighlight]}>
            <Text style={styles.statIcon}>⏳</Text>
            <Text style={[styles.statValue, styles.statValueHighlight]}>
              {formatCurrency(earnings?.pending_payout || 0)}
            </Text>
            <Text style={[styles.statLabel, styles.statLabelHighlight]}>Pending Payout</Text>
          </View>
        </View>

        {/* Daily Breakdown Chart */}
        {earnings?.daily_breakdown && earnings.daily_breakdown.length > 0 && (
          <View style={styles.chartSection}>
            <Text style={styles.sectionTitle}>Daily Breakdown</Text>
            <View style={styles.chartContainer}>
              {earnings.daily_breakdown.map((day, index) => (
                <View key={index} style={styles.chartBar}>
                  <Text style={styles.chartAmount}>
                    {formatCurrency(day.earnings)}
                  </Text>
                  <View style={styles.barContainer}>
                    <View 
                      style={[
                        styles.bar, 
                        { height: `${(day.earnings / maxEarnings) * 100}%` }
                      ]} 
                    />
                  </View>
                  <Text style={styles.chartLabel}>{day.day}</Text>
                  <Text style={styles.chartOrders}>{day.orders} orders</Text>
                </View>
              ))}
            </View>
          </View>
        )}

        {/* Quick Actions */}
        <View style={styles.actionsSection}>
          <Text style={styles.sectionTitle}>Quick Actions</Text>
          <View style={styles.actionsGrid}>
            <TouchableOpacity style={styles.actionCard}>
              <View style={[styles.actionIcon, { backgroundColor: '#DBEAFE' }]}>
                <Text style={styles.actionEmoji}>📊</Text>
              </View>
              <Text style={styles.actionLabel}>View Reports</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.actionCard}>
              <View style={[styles.actionIcon, { backgroundColor: '#D1FAE5' }]}>
                <Text style={styles.actionEmoji}>💳</Text>
              </View>
              <Text style={styles.actionLabel}>Request Payout</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.actionCard}>
              <View style={[styles.actionIcon, { backgroundColor: '#FEF3C7' }]}>
                <Text style={styles.actionEmoji}>🏦</Text>
              </View>
              <Text style={styles.actionLabel}>Bank Details</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.actionCard}>
              <View style={[styles.actionIcon, { backgroundColor: '#FEE2E2' }]}>
                <Text style={styles.actionEmoji}>📜</Text>
              </View>
              <Text style={styles.actionLabel}>Transactions</Text>
            </TouchableOpacity>
          </View>
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
  scrollContainer: {
    flex: 1,
  },
  periodContainer: {
    backgroundColor: '#FFFFFF',
    maxHeight: 56,
  },
  periodContent: {
    paddingHorizontal: 12,
    paddingVertical: 12,
    gap: 8,
  },
  periodChip: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
    backgroundColor: '#F3F4F6',
    marginRight: 8,
  },
  periodChipActive: {
    backgroundColor: COLORS.primary,
  },
  periodChipText: {
    fontSize: 14,
    fontWeight: '500',
    color: '#6B7280',
  },
  periodChipTextActive: {
    color: '#FFFFFF',
  },
  mainCard: {
    backgroundColor: COLORS.primary,
    margin: 16,
    padding: 24,
    borderRadius: 16,
    alignItems: 'center',
  },
  mainCardLabel: {
    fontSize: 14,
    color: '#FFE4E6',
    marginBottom: 8,
  },
  mainCardValue: {
    fontSize: 36,
    fontWeight: 'bold',
    color: '#FFFFFF',
    marginBottom: 4,
  },
  mainCardPeriod: {
    fontSize: 14,
    color: '#FFE4E6',
  },
  statsGrid: {
    flexDirection: 'row',
    paddingHorizontal: 16,
    gap: 12,
    marginBottom: 16,
  },
  statCard: {
    flex: 1,
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
    alignItems: 'center',
  },
  statCardHighlight: {
    backgroundColor: '#FEF3C7',
    borderWidth: 1,
    borderColor: '#FCD34D',
  },
  statIcon: {
    fontSize: 24,
    marginBottom: 8,
  },
  statValue: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#1F2937',
    marginBottom: 4,
  },
  statValueHighlight: {
    color: '#B45309',
  },
  statLabel: {
    fontSize: 11,
    color: '#6B7280',
    textAlign: 'center',
  },
  statLabelHighlight: {
    color: '#B45309',
  },
  chartSection: {
    margin: 16,
    marginTop: 0,
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    padding: 16,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#1F2937',
    marginBottom: 16,
  },
  chartContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-end',
    height: 180,
  },
  chartBar: {
    flex: 1,
    alignItems: 'center',
  },
  chartAmount: {
    fontSize: 10,
    color: '#6B7280',
    marginBottom: 4,
  },
  barContainer: {
    flex: 1,
    width: 24,
    backgroundColor: '#F3F4F6',
    borderRadius: 4,
    justifyContent: 'flex-end',
    marginBottom: 8,
  },
  bar: {
    width: '100%',
    backgroundColor: COLORS.primary,
    borderRadius: 4,
    minHeight: 4,
  },
  chartLabel: {
    fontSize: 12,
    fontWeight: '500',
    color: '#374151',
  },
  chartOrders: {
    fontSize: 10,
    color: '#9CA3AF',
    marginTop: 2,
  },
  actionsSection: {
    margin: 16,
    marginTop: 0,
  },
  actionsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
  },
  actionCard: {
    width: '47%',
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
    alignItems: 'center',
  },
  actionIcon: {
    width: 48,
    height: 48,
    borderRadius: 12,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 12,
  },
  actionEmoji: {
    fontSize: 24,
  },
  actionLabel: {
    fontSize: 14,
    fontWeight: '500',
    color: '#374151',
    textAlign: 'center',
  },
  bottomPadding: {
    height: 100,
  },
});

export default VendorEarningsScreen;
