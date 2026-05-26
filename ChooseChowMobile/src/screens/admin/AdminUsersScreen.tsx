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
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { COLORS } from '../../utils/theme';
import { adminService, AdminUser } from '../../api';

type AdminUsersScreenProps = {
  navigation: NativeStackNavigationProp<any>;
};

type FilterStatus = 'all' | 'active' | 'inactive' | 'suspended';
type FilterRole = 'all' | 'customer' | 'chef' | 'admin';

export const AdminUsersScreen: React.FC<AdminUsersScreenProps> = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  
  // State
  const [users, setUsers] = useState<AdminUser[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [statusFilter, setStatusFilter] = useState<FilterStatus>('all');
  const [roleFilter, setRoleFilter] = useState<FilterRole>('all');
  const [currentPage, setCurrentPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);

  // Load users
  const loadUsers = useCallback(async (page: number = 1, append: boolean = false) => {
    try {
      if (page === 1) {
        setError(null);
      }
      
      const params: any = { page };
      if (searchQuery) params.search = searchQuery;
      if (statusFilter !== 'all') params.status = statusFilter;
      if (roleFilter !== 'all') params.role = roleFilter;
      
      const response = await adminService.getUsers(params);
      
      if (append) {
        setUsers(prev => [...prev, ...(response.data || [])]);
      } else {
        setUsers(response.data || []);
      }
      
      setHasMore((response.meta?.current_page || 1) < (response.meta?.last_page || 1));
      setCurrentPage(response.meta?.current_page || 1);
    } catch (err: any) {
      console.error('Failed to load users:', err);
      setError(err.response?.data?.message || 'Failed to load users');
    } finally {
      setIsLoading(false);
      setRefreshing(false);
      setLoadingMore(false);
    }
  }, [searchQuery, statusFilter, roleFilter]);

  useEffect(() => {
    setIsLoading(true);
    loadUsers(1);
  }, [searchQuery, statusFilter, roleFilter]);

  const onRefresh = async () => {
    setRefreshing(true);
    await loadUsers(1);
  };

  const loadMore = () => {
    if (!loadingMore && hasMore) {
      setLoadingMore(true);
      loadUsers(currentPage + 1, true);
    }
  };

  // Toggle user status
  const handleToggleStatus = async (user: AdminUser) => {
    const newStatus = user.status === 'active' ? 'suspend' : 'activate';
    Alert.alert(
      `${newStatus === 'suspend' ? 'Suspend' : 'Activate'} User`,
      `Are you sure you want to ${newStatus} ${user.first_name} ${user.last_name}?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: newStatus === 'suspend' ? 'Suspend' : 'Activate',
          style: newStatus === 'suspend' ? 'destructive' : 'default',
          onPress: async () => {
            try {
              const updatedUser = await adminService.toggleUserStatus(user.id);
              setUsers(prev => prev.map(u => u.id === user.id ? updatedUser : u));
              Alert.alert('Success', `User ${newStatus === 'suspend' ? 'suspended' : 'activated'} successfully`);
            } catch (err: any) {
              Alert.alert('Error', err.response?.data?.message || `Failed to ${newStatus} user`);
            }
          },
        },
      ]
    );
  };

  // Delete user
  const handleDeleteUser = async (user: AdminUser) => {
    Alert.alert(
      'Delete User',
      `Are you sure you want to delete ${user.first_name} ${user.last_name}? This action cannot be undone.`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Delete',
          style: 'destructive',
          onPress: async () => {
            try {
              await adminService.deleteUser(user.id);
              setUsers(prev => prev.filter(u => u.id !== user.id));
              Alert.alert('Success', 'User deleted successfully');
            } catch (err: any) {
              Alert.alert('Error', err.response?.data?.message || 'Failed to delete user');
            }
          },
        },
      ]
    );
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active': return '#10B981';
      case 'inactive': return '#6B7280';
      case 'suspended': return '#EF4444';
      default: return '#6B7280';
    }
  };

  const getRoleColor = (role: string) => {
    switch (role) {
      case 'admin': return '#7C3AED';
      case 'chef': return '#F59E0B';
      case 'customer': return '#3B82F6';
      default: return '#6B7280';
    }
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    });
  };

  const renderUserItem = ({ item: user }: { item: AdminUser }) => (
    <TouchableOpacity 
      style={styles.userCard}
      onPress={() => navigation.navigate('AdminUserDetail', { userId: user.id })}
    >
      <View style={styles.userInfo}>
        {user.avatar_url ? (
          <Image source={{ uri: user.avatar_url }} style={styles.avatar} />
        ) : (
          <View style={styles.avatarPlaceholder}>
            <Text style={styles.avatarText}>
              {user.first_name?.charAt(0) || 'U'}{user.last_name?.charAt(0) || ''}
            </Text>
          </View>
        )}
        <View style={styles.userDetails}>
          <Text style={styles.userName}>{user.first_name} {user.last_name}</Text>
          <Text style={styles.userEmail}>{user.email}</Text>
          <View style={styles.userMeta}>
            <View style={[styles.roleBadge, { backgroundColor: getRoleColor(user.role) + '20' }]}>
              <Text style={[styles.roleText, { color: getRoleColor(user.role) }]}>
                {user.role}
              </Text>
            </View>
            <View style={[styles.statusBadge, { backgroundColor: getStatusColor(user.status) + '20' }]}>
              <Text style={[styles.statusText, { color: getStatusColor(user.status) }]}>
                {user.status}
              </Text>
            </View>
          </View>
          <Text style={styles.joinDate}>Joined {formatDate(user.created_at)}</Text>
        </View>
      </View>
      <View style={styles.userActions}>
        <TouchableOpacity 
          style={[styles.actionBtn, styles.toggleBtn]}
          onPress={() => handleToggleStatus(user)}
        >
          <Text style={styles.actionBtnText}>
            {user.status === 'active' ? '⏸️' : '▶️'}
          </Text>
        </TouchableOpacity>
        <TouchableOpacity 
          style={[styles.actionBtn, styles.deleteBtn]}
          onPress={() => handleDeleteUser(user)}
        >
          <Text style={styles.actionBtnText}>🗑️</Text>
        </TouchableOpacity>
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
          placeholder="Search users..."
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
        <Text style={styles.filterLabel}>Status:</Text>
        <View style={styles.filterOptions}>
          {(['all', 'active', 'inactive', 'suspended'] as FilterStatus[]).map((status) => (
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

      {/* Role Filter */}
      <View style={styles.filterRow}>
        <Text style={styles.filterLabel}>Role:</Text>
        <View style={styles.filterOptions}>
          {(['all', 'customer', 'chef', 'admin'] as FilterRole[]).map((role) => (
            <TouchableOpacity
              key={role}
              style={[
                styles.filterOption,
                roleFilter === role && styles.filterOptionActive,
              ]}
              onPress={() => setRoleFilter(role)}
            >
              <Text style={[
                styles.filterOptionText,
                roleFilter === role && styles.filterOptionTextActive,
              ]}>
                {role.charAt(0).toUpperCase() + role.slice(1)}
              </Text>
            </TouchableOpacity>
          ))}
        </View>
      </View>
    </View>
  );

  // Loading state
  if (isLoading && users.length === 0) {
    return (
      <View style={[styles.container, styles.centerContent, { paddingTop: insets.top }]}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.loadingText}>Loading users...</Text>
      </View>
    );
  }

  // Error state
  if (error && users.length === 0) {
    return (
      <View style={[styles.container, styles.centerContent, { paddingTop: insets.top }]}>
        <Text style={styles.errorIcon}>⚠️</Text>
        <Text style={styles.errorText}>{error}</Text>
        <TouchableOpacity style={styles.retryButton} onPress={() => loadUsers(1)}>
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
        <Text style={styles.headerTitle}>Users Management</Text>
        <View style={styles.headerRight}>
          <Text style={styles.userCount}>{users.length} users</Text>
        </View>
      </View>

      {renderFilters()}

      <FlatList
        data={users}
        renderItem={renderUserItem}
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
            <Text style={styles.emptyIcon}>👥</Text>
            <Text style={styles.emptyTitle}>No users found</Text>
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
  userCount: {
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
    marginBottom: 8,
  },
  filterLabel: {
    fontSize: 14,
    color: '#6B7280',
    marginRight: 12,
    minWidth: 50,
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
  userCard: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
  },
  userInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  avatar: {
    width: 50,
    height: 50,
    borderRadius: 25,
    marginRight: 12,
  },
  avatarPlaceholder: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: '#E5E7EB',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  avatarText: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#6B7280',
  },
  userDetails: {
    flex: 1,
  },
  userName: {
    fontSize: 16,
    fontWeight: '600',
    color: '#1F2937',
  },
  userEmail: {
    fontSize: 12,
    color: '#6B7280',
    marginTop: 2,
  },
  userMeta: {
    flexDirection: 'row',
    gap: 8,
    marginTop: 6,
  },
  roleBadge: {
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 8,
  },
  roleText: {
    fontSize: 10,
    fontWeight: '600',
    textTransform: 'capitalize',
  },
  statusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 8,
  },
  statusText: {
    fontSize: 10,
    fontWeight: '600',
    textTransform: 'capitalize',
  },
  joinDate: {
    fontSize: 11,
    color: '#9CA3AF',
    marginTop: 4,
  },
  userActions: {
    flexDirection: 'row',
    gap: 8,
  },
  actionBtn: {
    width: 36,
    height: 36,
    borderRadius: 18,
    justifyContent: 'center',
    alignItems: 'center',
  },
  toggleBtn: {
    backgroundColor: '#FEF3C7',
  },
  deleteBtn: {
    backgroundColor: '#FEE2E2',
  },
  actionBtnText: {
    fontSize: 14,
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

export default AdminUsersScreen;
