import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
  Alert,
  TextInput,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { COLORS } from '../../utils/theme';
import { adminService, AdminPayout, PayoutStats } from '../../api';

type AdminPayoutsScreenProps = {
  navigation: NativeStackNavigationProp<any>;
};

type FilterStatus = 'all' | 'pending' | 'approved' | 'rejected';

export const AdminPayoutsScreen: React.FC<AdminPayoutsScreenProps> = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  
  // State
  const [payouts, setPayouts] = useState<AdminPayout[]>([]);
  const [stats, setStats] = useState<PayoutStats | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [statusFilter, setStatusFilter] = useState<FilterStatus>('all');
  const [currentPage, setCurrentPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);

  // Load stats
  const loadStats = async () => {
    try {
      const data = await adminService.getPayoutStats();
      setStats(data);
    } catch (err) {
      console.error('Failed to load payout stats:', err);
    }
  };

  // Load payouts
  const loadPayouts = useCallback(async (page: number = 1, append: boolean = false) => {
    try {
      if (page === 1) {
        setError(null);
      }
      
      const params: any = { page };
      if (searchQuery) params.search = searchQuery;
      if (statusFilter !== 'all') params.status = statusFilter;
      
      const response = await adminService.getPayouts(params);
      
      if (append) {
        setPayouts(prev => [...prev, ...(response.data || [])]);
      } else {
        setPayouts(response.data || []);
      }
      
      setHasMore((response.meta?.current_page || 1) < (response.meta?.last_page || 1));
      setCurrentPage(response.meta?.current_page || 1);
    } catch (err: any) {
      console.error('Failed to load payouts:', err);
      setError(err.response?.data?.message || 'Failed to load payouts');
    } finally {
      setIsLoading(false);
      setRefreshing(false);
      setLoadingMore(false);
    }
  }, [searchQuery, statusFilter]);

  useEffect(() => {
    setIsLoading(true);
    loadStats();
    loadPayouts(1);
  }, [searchQuery, statusFilter]);

  const onRefresh = async () => {
    setRefreshing(true);
    await loadStats();
    await loadPayouts(1);
  };

  const loadMore = () => {
    if (!loadingMore && hasMore) {
      setLoadingMore(true);
      loadPayouts(currentPage + 1, true);
    }
  };

  // Approve payout
  const handleApprovePayout = async (payout: AdminPayout) => {
    Alert.alert(
      'Approve Payout',
      `Approve payout of ₦${payout.amount.toLocaleString()} to ${payout.vendor_name}?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Approve',
          onPress: async () => {
            try {
              const updated = await adminService.approvePayout(payout.id);
              setPayouts(prev => prev.map(p => p.id === payout.id ? updated : p));
              loadStats();
              Alert.alert('Success', 'Payout approved successfully');
            } catch (err: any) {
              Alert.alert('Error', err.response?.data?.message || 'Failed to approve payout');
            }
          },
        },
      ]
    );
  };

  // Reject payout
  const handleRejectPayout = async (payout: AdminPayout) => {
    Alert.alert(
      'Reject Payout',
      `Reject payout of ₦${payout.amount.toLocaleString()} from ${payout.vendor_name}? Funds will be returned to their wallet.`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Reject',
          style: 'destructive',
          onPress: async () => {
            try {
              const updated = await adminService.rejectPayout(payout.id, 'Rejected by admin');
              setPayouts(prev => prev.map(p => p.id === payout.id ? updated : p));
              loadStats();
              Alert.alert('Success', 'Payout rejected. Funds returned to vendor wallet.');
            } catch (err: any) {
              Alert.alert('Error', err.response?.data?.message || 'Failed to reject payout');
            }
          },
        },
      ]
    );
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'approved': return '#10B981';
      case 'pending': return '#F59E0B';
      case 'rejected': return '#EF4444';
      default: return '#6B7280';
    }
  };

  const getStatusBgColor = (status: string) => {
    switch (status) {
      case 'approved': return '#D1FAE5';
      case 'pending': return '#FEF3C7';
      case 'rejected': return '#FEE2E2';
      default: return '#E5E7EB';
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

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  const renderStatsCard = () => {
    if (!stats) return null;
    
    return (
      <View style={styles.statsContainer}>
        <View style={styles.statsRow}>
          <View style={[styles.statCard, { backgroundColor: '#FEF3C7' }]}>
            <Text style={styles.statValue}>{stats.pending_count}</Text>
            <Text style={styles.statLabel}>Pending</Text>
            <Text style={styles.statAmount}>{formatCurrency(stats.pending_amount)}</Text>
          </View>
          <View style={[styles.statCard, { backgroundColor: '#D1FAE5' }]}>
            <Text style={styles.statValue}>{stats.approved_count}</Text>
            <Text style={styles.statLabel}>Approved</Text>
            <Text style={styles.statAmount}>{formatCurrency(stats.approved_amount)}</Text>
          </View>
          <View style={[styles.statCard, { backgroundColor: '#FEE2E2' }]}>
            <Text style={styles.statValue}>{stats.rejected_count}</Text>
            <Text style={styles.statLabel}>Rejected</Text>
            <Text style={styles.statAmount}>{formatCurrency(stats.rejected_amount)}</Text>
          </View>
        </View>
      </View>
    );
  };

  const renderPayoutItem = ({ item: payout }: { item: AdminPayout }) => (
    <View style={styles.payoutCard}>
      <View style={styles.payoutHeader}>
        <View>
          <Text style={styles.vendorName}>{payout.vendor_name}</Text>
          <Text style={styles.vendorEmail}>{payout.vendor_email}</Text>
        </View>
        <View style={[styles.statusBadge, { backgroundColor: getStatusBgColor(payout.status) }]}>
          <Text style={[styles.statusText, { color: getStatusColor(payout.status) }]}>
            {payout.status}
          </Text>
        </View>
      </View>

      <View style={styles.payoutDetails}>
        <View style={styles.amountRow}>
          <Text style={styles.amountLabel}>Amount:</Text>
          <Text style={styles.amountValue}>₦{payout.amount.toLocaleString()}</Text>
        </View>
        {payout.bank_name && (
          <View style={styles.bankInfo}>
            <Text style={styles.bankText}>
              {payout.bank_name} ••••{payout.account_number}
            </Text>
          </View>
        )}
        {payout.rejection_reason && (
          <View style={styles.rejectionReason}>
            <Text style={styles.rejectionText}>Reason: {payout.rejection_reason}</Text>
          </View>
        )}
      </View>

      <View style={styles.payoutFooter}>
        <Text style={styles.payoutDate}>{formatDate(payout.created_at)}</Text>
        {payout.status === 'pending' && (
          <View style={styles.payoutActions}>
            <TouchableOpacity 
              style={[styles.actionBtn, styles.approveBtn]}
              onPress={() => handleApprovePayout(payout)}
            >
              <Text style={styles.approveText}>✓ Approve</Text>
            </TouchableOpacity>
            <TouchableOpacity 
              style={[styles.actionBtn, styles.rejectBtn]}
              onPress={() => handleRejectPayout(payout)}
            >
              <Text style={styles.rejectText}>✕ Reject</Text>
            </TouchableOpacity>
          </View>
        )}
      </View>
    </View>
  );

  const renderFilters = () => (
    <View style={styles.filtersContainer}>
      {/* Search */}
      <View style={styles.searchContainer}>
        <Text style={styles.searchIcon}>🔍</Text>
        <TextInput
          style={styles.searchInput}
          placeholder="Search by vendor name..."
          placeholderTextColor="#9CA3AF"
          value={searchQuery}
          onChangeText={setSearchQuery}
        />
        {searchQuery.length > 0 && (
          <TouchableOpacity onPress={() => setSearchQuery('')}>
            <Text style={styles.clearIcon}>✕</Text>
          </TouchableOpacity>
        )}
      </View>

      {/* Status Filter */}
      <View style={styles.filterRow}>
        <View style={styles.filterOptions}>
          {(['all', 'pending', 'approved', 'rejected'] as FilterStatus[]).map((status) => (
            <TouchableOpacity
              key={status}
              style={[
                styles.filterOption,
                statusFilter === status && styles.filterOptionActive,
              ]}
              onPress={() => setStatusFilter(status)}
            >
              <Text style={[
                styles.filterOptionText,
                statusFilter === status && styles.filterOptionTextActive,
              ]}>
                {status.charAt(0).toUpperCase() + status.slice(1)}
              </Text>
            </TouchableOpacity>
          ))}
        </View>
      </View>
    </View>
  );

  // Loading state
  if (isLoading && payouts.length === 0) {
    return (
      <View style={[styles.container, styles.centerContent, { paddingTop: insets.top }]}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.loadingText}>Loading payouts...</Text>
      </View>
    );
  }

  // Error state
  if (error && payouts.length === 0) {
    return (
      <View style={[styles.container, styles.centerContent, { paddingTop: insets.top }]}>
        <Text style={styles.errorIcon}>⚠️</Text>
        <Text style={styles.errorText}>{error}</Text>
        <TouchableOpacity style={styles.retryButton} onPress={() => loadPayouts(1)}>
          <Text style={styles.retryButtonText}>Retry</Text>
        </TouchableOpacity>
      </View>
    );
  }

  return (
    <View style={[styles.container, { paddingTop: insets.top }]}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity style={styles.backBtn} onPress={() => navigation.goBack()}>
          <Text style={styles.backIcon}>←</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Payouts Management</Text>
        <View style={styles.headerRight}>
          <Text style={styles.payoutCount}>{stats?.total_requests || 0} total</Text>
        </View>
      </View>

      {renderStatsCard()}
      {renderFilters()}

      <FlatList
        data={payouts}
        renderItem={renderPayoutItem}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.listContent}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />
        }
        onEndReached={loadMore}
        onEndReachedThreshold={0.5}
        ListFooterComponent={() => (
          loadingMore ? (
            <View style={styles.loadingMore}>
              <ActivityIndicator size="small" color={COLORS.primary} />
            </View>
          ) : null
        )}
        ListEmptyComponent={() => (
          <View style={styles.emptyState}>
            <Text style={styles.emptyIcon}>💳</Text>
            <Text style={styles.emptyTitle}>No payouts found</Text>
            <Text style={styles.emptyText}>Try adjusting your filters</Text>
          </View>
        )}
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
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    paddingVertical: 12,
    backgroundColor: '#1F2937',
  },
  backBtn: {
    padding: 8,
  },
  backIcon: {
    fontSize: 24,
    color: '#FFFFFF',
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  headerRight: {
    padding: 8,
  },
  payoutCount: {
    fontSize: 14,
    color: '#9CA3AF',
  },
  statsContainer: {
    padding: 16,
    backgroundColor: '#FFFFFF',
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  statsRow: {
    flexDirection: 'row',
    gap: 12,
  },
  statCard: {
    flex: 1,
    borderRadius: 12,
    padding: 12,
    alignItems: 'center',
  },
  statValue: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  statLabel: {
    fontSize: 12,
    color: '#6B7280',
    marginTop: 2,
  },
  statAmount: {
    fontSize: 11,
    color: '#374151',
    marginTop: 4,
    fontWeight: '500',
  },
  filtersContainer: {
    backgroundColor: '#FFFFFF',
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F3F4F6',
    borderRadius: 12,
    paddingHorizontal: 12,
    marginBottom: 12,
  },
  searchIcon: {
    fontSize: 16,
    marginRight: 8,
  },
  searchInput: {
    flex: 1,
    paddingVertical: 10,
    fontSize: 14,
    color: '#1F2937',
  },
  clearIcon: {
    fontSize: 14,
    color: '#9CA3AF',
    padding: 4,
  },
  filterRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  filterOptions: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  filterOption: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 16,
    backgroundColor: '#F3F4F6',
  },
  filterOptionActive: {
    backgroundColor: COLORS.primary,
  },
  filterOptionText: {
    fontSize: 12,
    color: '#6B7280',
  },
  filterOptionTextActive: {
    color: '#FFFFFF',
    fontWeight: '600',
  },
  listContent: {
    padding: 16,
  },
  payoutCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
  },
  payoutHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  vendorName: {
    fontSize: 16,
    fontWeight: '600',
    color: '#1F2937',
  },
  vendorEmail: {
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
    fontSize: 11,
    fontWeight: '600',
    textTransform: 'capitalize',
  },
  payoutDetails: {
    backgroundColor: '#F9FAFB',
    borderRadius: 8,
    padding: 12,
    marginBottom: 12,
  },
  amountRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  amountLabel: {
    fontSize: 14,
    color: '#6B7280',
  },
  amountValue: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  bankInfo: {
    marginTop: 8,
    paddingTop: 8,
    borderTopWidth: 1,
    borderTopColor: '#E5E7EB',
  },
  bankText: {
    fontSize: 13,
    color: '#374151',
  },
  rejectionReason: {
    marginTop: 8,
    paddingTop: 8,
    borderTopWidth: 1,
    borderTopColor: '#E5E7EB',
  },
  rejectionText: {
    fontSize: 12,
    color: '#EF4444',
    fontStyle: 'italic',
  },
  payoutFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  payoutDate: {
    fontSize: 12,
    color: '#9CA3AF',
  },
  payoutActions: {
    flexDirection: 'row',
    gap: 8,
  },
  actionBtn: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 8,
  },
  approveBtn: {
    backgroundColor: '#D1FAE5',
  },
  approveText: {
    fontSize: 12,
    fontWeight: '600',
    color: '#10B981',
  },
  rejectBtn: {
    backgroundColor: '#FEE2E2',
  },
  rejectText: {
    fontSize: 12,
    fontWeight: '600',
    color: '#EF4444',
  },
  loadingMore: {
    paddingVertical: 20,
    alignItems: 'center',
  },
  emptyState: {
    alignItems: 'center',
    paddingVertical: 40,
  },
  emptyIcon: {
    fontSize: 48,
    marginBottom: 16,
  },
  emptyTitle: {
    fontSize: 18,
    fontWeight: '600',
    color: '#1F2937',
    marginBottom: 8,
  },
  emptyText: {
    fontSize: 14,
    color: '#6B7280',
  },
});

export default AdminPayoutsScreen;
