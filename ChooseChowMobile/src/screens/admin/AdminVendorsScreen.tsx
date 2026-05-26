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
  Image,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RouteProp } from '@react-navigation/native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { COLORS } from '../../utils/theme';
import { adminService, AdminVendor } from '../../api';

type AdminVendorsScreenProps = {
  navigation?: NativeStackNavigationProp<any>;
  route?: RouteProp<{ params: { filter?: string } }, 'params'>;
};

type FilterStatus = 'all' | 'pending' | 'approved' | 'rejected' | 'suspended';

export const AdminVendorsScreen: React.FC<AdminVendorsScreenProps> = ({ navigation, route }) => {
  const insets = useSafeAreaInsets();
  const initialFilter = (route?.params?.filter as FilterStatus) || 'all';
  
  // State
  const [vendors, setVendors] = useState<AdminVendor[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [statusFilter, setStatusFilter] = useState<FilterStatus>(initialFilter);
  const [currentPage, setCurrentPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);

  // Load vendors
  const loadVendors = useCallback(async (page: number = 1, append: boolean = false) => {
    try {
      if (page === 1) {
        setError(null);
      }
      
      const params: any = { page };
      if (searchQuery) params.search = searchQuery;
      if (statusFilter !== 'all') params.status = statusFilter;
      
      const response = await adminService.getVendors(params);
      
      if (append) {
        setVendors(prev => [...prev, ...(response.data || [])]);
      } else {
        setVendors(response.data || []);
      }
      
      setHasMore((response.meta?.current_page || 1) < (response.meta?.last_page || 1));
      setCurrentPage(response.meta?.current_page || 1);
    } catch (err: any) {
      console.error('Failed to load vendors:', err);
      setError(err.response?.data?.message || 'Failed to load vendors');
    } finally {
      setIsLoading(false);
      setRefreshing(false);
      setLoadingMore(false);
    }
  }, [searchQuery, statusFilter]);

  useEffect(() => {
    setIsLoading(true);
    loadVendors(1);
  }, [searchQuery, statusFilter]);

  const onRefresh = async () => {
    setRefreshing(true);
    await loadVendors(1);
  };

  const loadMore = () => {
    if (!loadingMore && hasMore) {
      setLoadingMore(true);
      loadVendors(currentPage + 1, true);
    }
  };

  // Approve vendor
  const handleApproveVendor = async (vendor: AdminVendor) => {
    try {
      const updatedVendor = await adminService.approveVendor(vendor.id);
      setVendors(prev => prev.map(v => v.id === vendor.id ? updatedVendor : v));
      Alert.alert('Success', 'Vendor approved successfully');
    } catch (err: any) {
      Alert.alert('Error', err.response?.data?.message || 'Failed to approve vendor');
    }
  };

  // Reject vendor
  const handleRejectVendor = async (vendor: AdminVendor) => {
    Alert.alert(
      'Reject Vendor',
      `Are you sure you want to reject ${vendor.business_name}?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Reject',
          style: 'destructive',
          onPress: async () => {
            try {
              const updatedVendor = await adminService.rejectVendor(vendor.id);
              setVendors(prev => prev.map(v => v.id === vendor.id ? updatedVendor : v));
              Alert.alert('Success', 'Vendor rejected');
            } catch (err: any) {
              Alert.alert('Error', err.response?.data?.message || 'Failed to reject vendor');
            }
          },
        },
      ]
    );
  };

  // Suspend vendor
  const handleSuspendVendor = async (vendor: AdminVendor) => {
    Alert.alert(
      'Suspend Vendor',
      `Are you sure you want to suspend ${vendor.business_name}?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Suspend',
          style: 'destructive',
          onPress: async () => {
            try {
              const updatedVendor = await adminService.suspendVendor(vendor.id);
              setVendors(prev => prev.map(v => v.id === vendor.id ? updatedVendor : v));
              Alert.alert('Success', 'Vendor suspended');
            } catch (err: any) {
              Alert.alert('Error', err.response?.data?.message || 'Failed to suspend vendor');
            }
          },
        },
      ]
    );
  };

  // Activate vendor
  const handleActivateVendor = async (vendor: AdminVendor) => {
    try {
      const updatedVendor = await adminService.activateVendor(vendor.id);
      setVendors(prev => prev.map(v => v.id === vendor.id ? updatedVendor : v));
      Alert.alert('Success', 'Vendor activated successfully');
    } catch (err: any) {
      Alert.alert('Error', err.response?.data?.message || 'Failed to activate vendor');
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'approved': return '#10B981';
      case 'pending': return '#F59E0B';
      case 'rejected': return '#EF4444';
      case 'suspended': return '#6B7280';
      default: return '#6B7280';
    }
  };

  const getStatusBgColor = (status: string) => {
    switch (status) {
      case 'approved': return '#D1FAE5';
      case 'pending': return '#FEF3C7';
      case 'rejected': return '#FEE2E2';
      case 'suspended': return '#E5E7EB';
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
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    });
  };

  const renderVendorItem = ({ item: vendor }: { item: AdminVendor }) => (
    <TouchableOpacity 
      style={styles.vendorCard}
      onPress={() => navigation?.navigate('AdminVendorDetail', { vendorId: vendor.id })}
    >
      <View style={styles.vendorHeader}>
        {vendor.logo_url ? (
          <Image source={{ uri: vendor.logo_url }} style={styles.vendorLogo} />
        ) : (
          <View style={styles.vendorLogoPlaceholder}>
            <Text style={styles.vendorLogoText}>{vendor.business_name.charAt(0)}</Text>
          </View>
        )}
        <View style={styles.vendorInfo}>
          <Text style={styles.vendorName}>{vendor.business_name}</Text>
          <Text style={styles.vendorEmail}>{vendor.email}</Text>
          <View style={[styles.statusBadge, { backgroundColor: getStatusBgColor(vendor.status) }]}>
            <Text style={[styles.statusText, { color: getStatusColor(vendor.status) }]}>
              {vendor.status}
            </Text>
          </View>
        </View>
      </View>

      <View style={styles.vendorStats}>
        <View style={styles.statItem}>
          <Text style={styles.statValue}>{vendor.total_orders}</Text>
          <Text style={styles.statLabel}>Orders</Text>
        </View>
        <View style={styles.statItem}>
          <Text style={styles.statValue}>{formatCurrency(vendor.total_revenue)}</Text>
          <Text style={styles.statLabel}>Revenue</Text>
        </View>
        <View style={styles.statItem}>
          <Text style={styles.statValue}>⭐ {vendor.rating.toFixed(1)}</Text>
          <Text style={styles.statLabel}>Rating</Text>
        </View>
      </View>

      <View style={styles.vendorFooter}>
        <Text style={styles.joinDate}>Joined {formatDate(vendor.created_at)}</Text>
        <View style={styles.vendorActions}>
          {vendor.status === 'pending' && (
            <>
              <TouchableOpacity 
                style={[styles.actionBtn, styles.approveBtn]}
                onPress={() => handleApproveVendor(vendor)}
              >
                <Text style={styles.approveText}>✓ Approve</Text>
              </TouchableOpacity>
              <TouchableOpacity 
                style={[styles.actionBtn, styles.rejectBtn]}
                onPress={() => handleRejectVendor(vendor)}
              >
                <Text style={styles.rejectText}>✕ Reject</Text>
              </TouchableOpacity>
            </>
          )}
          {vendor.status === 'approved' && (
            <TouchableOpacity 
              style={[styles.actionBtn, styles.suspendBtn]}
              onPress={() => handleSuspendVendor(vendor)}
            >
              <Text style={styles.suspendText}>⏸️ Suspend</Text>
            </TouchableOpacity>
          )}
          {vendor.status === 'suspended' && (
            <TouchableOpacity 
              style={[styles.actionBtn, styles.activateBtn]}
              onPress={() => handleActivateVendor(vendor)}
            >
              <Text style={styles.activateText}>▶️ Activate</Text>
            </TouchableOpacity>
          )}
        </View>
      </View>
    </TouchableOpacity>
  );

  const renderFilters = () => (
    <View style={styles.filtersContainer}>
      {/* Search */}
      <View style={styles.searchContainer}>
        <Text style={styles.searchIcon}>🔍</Text>
        <TextInput
          style={styles.searchInput}
          placeholder="Search vendors..."
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
          {(['all', 'pending', 'approved', 'rejected', 'suspended'] as FilterStatus[]).map((status) => (
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
  if (isLoading && vendors.length === 0) {
    return (
      <View style={[styles.container, styles.centerContent, { paddingTop: insets.top }]}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.loadingText}>Loading vendors...</Text>
      </View>
    );
  }

  // Error state
  if (error && vendors.length === 0) {
    return (
      <View style={[styles.container, styles.centerContent, { paddingTop: insets.top }]}>
        <Text style={styles.errorIcon}>⚠️</Text>
        <Text style={styles.errorText}>{error}</Text>
        <TouchableOpacity style={styles.retryButton} onPress={() => loadVendors(1)}>
          <Text style={styles.retryButtonText}>Retry</Text>
        </TouchableOpacity>
      </View>
    );
  }

  return (
    <View style={[styles.container, { paddingTop: insets.top }]}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity style={styles.backBtn} onPress={() => navigation?.goBack()}>
          <Text style={styles.backIcon}>←</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Vendors Management</Text>
        <View style={styles.headerRight}>
          <Text style={styles.vendorCount}>{vendors.length} vendors</Text>
        </View>
      </View>

      {renderFilters()}

      <FlatList
        data={vendors}
        renderItem={renderVendorItem}
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
            <Text style={styles.emptyIcon}>🏪</Text>
            <Text style={styles.emptyTitle}>No vendors found</Text>
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
  vendorCount: {
    fontSize: 14,
    color: '#9CA3AF',
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
  vendorCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
  },
  vendorHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  vendorLogo: {
    width: 56,
    height: 56,
    borderRadius: 12,
    marginRight: 12,
  },
  vendorLogoPlaceholder: {
    width: 56,
    height: 56,
    borderRadius: 12,
    backgroundColor: '#FEE2E2',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  vendorLogoText: {
    fontSize: 24,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  vendorInfo: {
    flex: 1,
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
    alignSelf: 'flex-start',
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 8,
    marginTop: 6,
  },
  statusText: {
    fontSize: 10,
    fontWeight: '600',
    textTransform: 'capitalize',
  },
  vendorStats: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    backgroundColor: '#F9FAFB',
    borderRadius: 8,
    padding: 12,
    marginBottom: 12,
  },
  statItem: {
    alignItems: 'center',
  },
  statValue: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  statLabel: {
    fontSize: 11,
    color: '#6B7280',
    marginTop: 2,
  },
  vendorFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  joinDate: {
    fontSize: 12,
    color: '#9CA3AF',
  },
  vendorActions: {
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
  suspendBtn: {
    backgroundColor: '#FEF3C7',
  },
  suspendText: {
    fontSize: 12,
    fontWeight: '600',
    color: '#F59E0B',
  },
  activateBtn: {
    backgroundColor: '#DBEAFE',
  },
  activateText: {
    fontSize: 12,
    fontWeight: '600',
    color: '#3B82F6',
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

export default AdminVendorsScreen;
