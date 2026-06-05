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
  Image,
  TextInput,
  Modal,
  Switch,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { COLORS } from '../../utils/theme';
import { vendorService, VendorMenuItem, CreateMenuItemData } from '../../api';

type VendorMenuProps = {
  navigation: NativeStackNavigationProp<any>;
};

// Mock menu items for demo mode
const MOCK_MENUS: VendorMenuItem[] = [
  { id: 1, name: 'Jollof Rice', slug: 'jollof-rice', description: 'Delicious Nigerian jollof rice with chicken', price: 2500, category: 'Main Course', preparation_time: 30, is_available: true, is_featured: true, image_url: undefined, cuisines: [{ id: 1, name: 'Nigerian' }], dietary_preferences: [], created_at: new Date().toISOString() },
  { id: 2, name: 'Fried Rice', slug: 'fried-rice', description: 'Savory fried rice with vegetables', price: 2200, category: 'Main Course', preparation_time: 25, is_available: true, is_featured: false, image_url: undefined, cuisines: [{ id: 1, name: 'Nigerian' }], dietary_preferences: [], created_at: new Date().toISOString() },
  { id: 3, name: 'Pepper Soup', slug: 'pepper-soup', description: 'Spicy goat meat pepper soup', price: 3500, category: 'Soups', preparation_time: 45, is_available: false, is_featured: false, image_url: undefined, cuisines: [{ id: 1, name: 'Nigerian' }], dietary_preferences: [], created_at: new Date().toISOString() },
  { id: 4, name: 'Puff Puff', slug: 'puff-puff', description: 'Sweet Nigerian doughnuts', price: 500, category: 'Snacks', preparation_time: 15, is_available: true, is_featured: true, image_url: undefined, cuisines: [{ id: 1, name: 'Nigerian' }], dietary_preferences: [], created_at: new Date().toISOString() },
];

export const VendorMenuScreen: React.FC<VendorMenuProps> = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [menuItems, setMenuItems] = useState<VendorMenuItem[]>([]);
  const [filterAvailable, setFilterAvailable] = useState<boolean | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [showAddModal, setShowAddModal] = useState(false);
  const [editingItem, setEditingItem] = useState<VendorMenuItem | null>(null);
  const [togglingId, setTogglingId] = useState<number | null>(null);

  // Form state
  const [formData, setFormData] = useState<Partial<CreateMenuItemData>>({
    name: '',
    description: '',
    price: 0,
    category: '',
    preparation_time: 30,
    is_available: true,
    is_featured: false,
  });

  const loadMenuItems = useCallback(async () => {
    try {
      const params: { is_available?: boolean; search?: string } = {};
      if (filterAvailable !== null) params.is_available = filterAvailable;
      if (searchQuery) params.search = searchQuery;
      
      const response = await vendorService.getMenus(params);
      setMenuItems(response.data || []);
    } catch (err: any) {
      console.error('Failed to load menus:', err);
      // Use mock data if API returns 404
      if (err.response?.status === 404 || err.response?.status === 401) {
        let filtered = MOCK_MENUS;
        if (filterAvailable !== null) {
          filtered = filtered.filter(m => m.is_available === filterAvailable);
        }
        if (searchQuery) {
          filtered = filtered.filter(m => 
            m.name.toLowerCase().includes(searchQuery.toLowerCase())
          );
        }
        setMenuItems(filtered);
      }
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  }, [filterAvailable, searchQuery]);

  useEffect(() => {
    loadMenuItems();
  }, [loadMenuItems]);

  const onRefresh = async () => {
    setRefreshing(true);
    await loadMenuItems();
  };

  const handleToggleAvailability = async (item: VendorMenuItem) => {
    setTogglingId(item.id);
    try {
      const result = await vendorService.toggleMenuAvailability(item.id);
      setMenuItems(prev => 
        prev.map(m => m.id === item.id ? { ...m, is_available: result.is_available } : m)
      );
    } catch (err: any) {
      // Demo mode fallback
      if (err.response?.status === 404) {
        setMenuItems(prev => 
          prev.map(m => m.id === item.id ? { ...m, is_available: !m.is_available } : m)
        );
      } else {
        Alert.alert('Error', 'Failed to update availability');
      }
    } finally {
      setTogglingId(null);
    }
  };

  const handleDelete = (item: VendorMenuItem) => {
    Alert.alert(
      'Delete Menu Item',
      `Are you sure you want to delete "${item.name}"?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Delete',
          style: 'destructive',
          onPress: async () => {
            try {
              await vendorService.deleteMenu(item.id);
              setMenuItems(prev => prev.filter(m => m.id !== item.id));
              Alert.alert('Success', 'Menu item deleted');
            } catch (err: any) {
              // Demo mode fallback
              if (err.response?.status === 404) {
                setMenuItems(prev => prev.filter(m => m.id !== item.id));
              } else {
                Alert.alert('Error', 'Failed to delete menu item');
              }
            }
          },
        },
      ]
    );
  };

  const openEditModal = (item: VendorMenuItem) => {
    setEditingItem(item);
    setFormData({
      name: item.name,
      description: item.description || '',
      price: item.price,
      category: item.category || '',
      preparation_time: item.preparation_time || 30,
      is_available: item.is_available,
      is_featured: item.is_featured,
    });
    setShowAddModal(true);
  };

  const openAddModal = () => {
    setEditingItem(null);
    setFormData({
      name: '',
      description: '',
      price: 0,
      category: '',
      preparation_time: 30,
      is_available: true,
      is_featured: false,
    });
    setShowAddModal(true);
  };

  const handleSave = async () => {
    if (!formData.name || !formData.price) {
      Alert.alert('Error', 'Please fill in all required fields');
      return;
    }

    try {
      if (editingItem) {
        const result = await vendorService.updateMenu(editingItem.id, formData);
        setMenuItems(prev => 
          prev.map(m => m.id === editingItem.id ? result : m)
        );
        Alert.alert('Success', 'Menu item updated');
      } else {
        const result = await vendorService.createMenu(formData as CreateMenuItemData);
        setMenuItems(prev => [result, ...prev]);
        Alert.alert('Success', 'Menu item created');
      }
      setShowAddModal(false);
    } catch (err: any) {
      // Demo mode fallback
      if (err.response?.status === 404) {
        if (editingItem) {
          setMenuItems(prev => 
            prev.map(m => m.id === editingItem.id ? { ...m, ...formData } as VendorMenuItem : m)
          );
        } else {
          const newItem: VendorMenuItem = {
            id: Date.now(),
            name: formData.name || '',
            slug: (formData.name || '').toLowerCase().replace(/\s+/g, '-'),
            description: formData.description,
            price: formData.price || 0,
            category: formData.category,
            preparation_time: formData.preparation_time,
            is_available: formData.is_available || true,
            is_featured: formData.is_featured || false,
            image_url: undefined,
            cuisines: [],
            dietary_preferences: [],
            created_at: new Date().toISOString(),
          };
          setMenuItems(prev => [newItem, ...prev]);
        }
        setShowAddModal(false);
      } else {
        Alert.alert('Error', 'Failed to save menu item');
      }
    }
  };

  const formatCurrency = (amount: number) => `₦${amount.toLocaleString()}`;

  const renderMenuItem = (item: VendorMenuItem) => {
    const isToggling = togglingId === item.id;

    return (
      <View key={item.id} style={styles.menuCard}>
        <View style={styles.menuImageContainer}>
          {item.image_url ? (
            <Image source={{ uri: item.image_url }} style={styles.menuImage} />
          ) : (
            <View style={styles.menuImagePlaceholder}>
              <Text style={styles.menuImageIcon}>🍽️</Text>
            </View>
          )}
          {item.is_featured && (
            <View style={styles.featuredBadge}>
              <Text style={styles.featuredText}>⭐</Text>
            </View>
          )}
        </View>

        <View style={styles.menuContent}>
          <View style={styles.menuHeader}>
            <Text style={styles.menuName} numberOfLines={1}>{item.name}</Text>
            <Text style={styles.menuPrice}>{formatCurrency(item.price)}</Text>
          </View>

          {item.description && (
            <Text style={styles.menuDescription} numberOfLines={2}>{item.description}</Text>
          )}

          <View style={styles.menuMeta}>
            {item.category && (
              <View style={styles.categoryBadge}>
                <Text style={styles.categoryText}>{item.category}</Text>
              </View>
            )}
            {item.preparation_time && (
              <Text style={styles.prepTime}>⏱️ {item.preparation_time} min</Text>
            )}
          </View>

          <View style={styles.menuActions}>
            <View style={styles.availabilityToggle}>
              <Text style={styles.availabilityLabel}>
                {item.is_available ? '🟢 Available' : '🔴 Unavailable'}
              </Text>
              {isToggling ? (
                <ActivityIndicator size="small" color={COLORS.primary} />
              ) : (
                <Switch
                  value={item.is_available}
                  onValueChange={() => handleToggleAvailability(item)}
                  trackColor={{ false: '#E5E7EB', true: '#86EFAC' }}
                  thumbColor={item.is_available ? '#10B981' : '#9CA3AF'}
                />
              )}
            </View>

            <View style={styles.actionButtons}>
              <TouchableOpacity 
                style={styles.editButton}
                onPress={() => openEditModal(item)}
              >
                <Text style={styles.editButtonText}>✏️</Text>
              </TouchableOpacity>
              <TouchableOpacity 
                style={styles.deleteButton}
                onPress={() => handleDelete(item)}
              >
                <Text style={styles.deleteButtonText}>🗑️</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </View>
    );
  };

  if (isLoading) {
    return (
      <View style={[styles.container, styles.centerContent, { paddingTop: insets.top }]}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.loadingText}>Loading menu...</Text>
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
        <Text style={styles.headerTitle}>My Menu</Text>
        <TouchableOpacity style={styles.addButton} onPress={openAddModal}>
          <Text style={styles.addButtonText}>+ Add</Text>
        </TouchableOpacity>
      </View>

      {/* Search and Filters */}
      <View style={styles.searchContainer}>
        <View style={styles.searchInput}>
          <Text style={styles.searchIcon}>🔍</Text>
          <TextInput
            style={styles.searchTextInput}
            placeholder="Search menu items..."
            value={searchQuery}
            onChangeText={setSearchQuery}
            returnKeyType="search"
            onSubmitEditing={loadMenuItems}
          />
        </View>
      </View>

      <View style={styles.filterContainer}>
        <TouchableOpacity
          style={[styles.filterChip, filterAvailable === null && styles.filterChipActive]}
          onPress={() => setFilterAvailable(null)}
        >
          <Text style={[styles.filterChipText, filterAvailable === null && styles.filterChipTextActive]}>
            All
          </Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.filterChip, filterAvailable === true && styles.filterChipActive]}
          onPress={() => setFilterAvailable(true)}
        >
          <Text style={[styles.filterChipText, filterAvailable === true && styles.filterChipTextActive]}>
            Available
          </Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.filterChip, filterAvailable === false && styles.filterChipActive]}
          onPress={() => setFilterAvailable(false)}
        >
          <Text style={[styles.filterChipText, filterAvailable === false && styles.filterChipTextActive]}>
            Unavailable
          </Text>
        </TouchableOpacity>
      </View>

      {/* Menu List */}
      <FlatList
        data={menuItems}
        keyExtractor={(item) => String(item.id)}
        renderItem={({ item }) => renderMenuItem(item)}
        contentContainerStyle={styles.listContent}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />
        }
        ListEmptyComponent={
          <View style={styles.emptyState}>
            <Text style={styles.emptyIcon}>📋</Text>
            <Text style={styles.emptyText}>No menu items yet</Text>
            <Text style={styles.emptySubtext}>Add your first menu item to start selling</Text>
            <TouchableOpacity style={styles.emptyButton} onPress={openAddModal}>
              <Text style={styles.emptyButtonText}>+ Add Menu Item</Text>
            </TouchableOpacity>
          </View>
        }
      />

      {/* Add/Edit Modal */}
      <Modal
        visible={showAddModal}
        animationType="slide"
        transparent={true}
        onRequestClose={() => setShowAddModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, { paddingBottom: insets.bottom + 16 }]}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>
                {editingItem ? 'Edit Menu Item' : 'Add Menu Item'}
              </Text>
              <TouchableOpacity onPress={() => setShowAddModal(false)}>
                <Text style={styles.modalClose}>✕</Text>
              </TouchableOpacity>
            </View>

            <ScrollView style={styles.modalBody} showsVerticalScrollIndicator={false}>
              <Text style={styles.inputLabel}>Name *</Text>
              <TextInput
                style={styles.textInput}
                placeholder="e.g., Jollof Rice"
                value={formData.name}
                onChangeText={(text) => setFormData(prev => ({ ...prev, name: text }))}
              />

              <Text style={styles.inputLabel}>Description</Text>
              <TextInput
                style={[styles.textInput, styles.textArea]}
                placeholder="Describe your dish..."
                value={formData.description}
                onChangeText={(text) => setFormData(prev => ({ ...prev, description: text }))}
                multiline
                numberOfLines={3}
              />

              <Text style={styles.inputLabel}>Price (₦) *</Text>
              <TextInput
                style={styles.textInput}
                placeholder="e.g., 2500"
                value={formData.price ? String(formData.price) : ''}
                onChangeText={(text) => setFormData(prev => ({ ...prev, price: Number(text) || 0 }))}
                keyboardType="numeric"
              />

              <Text style={styles.inputLabel}>Category</Text>
              <TextInput
                style={styles.textInput}
                placeholder="e.g., Main Course"
                value={formData.category}
                onChangeText={(text) => setFormData(prev => ({ ...prev, category: text }))}
              />

              <Text style={styles.inputLabel}>Preparation Time (minutes)</Text>
              <TextInput
                style={styles.textInput}
                placeholder="e.g., 30"
                value={formData.preparation_time ? String(formData.preparation_time) : ''}
                onChangeText={(text) => setFormData(prev => ({ ...prev, preparation_time: Number(text) || 0 }))}
                keyboardType="numeric"
              />

              <View style={styles.switchRow}>
                <Text style={styles.switchLabel}>Available</Text>
                <Switch
                  value={formData.is_available}
                  onValueChange={(value) => setFormData(prev => ({ ...prev, is_available: value }))}
                  trackColor={{ false: '#E5E7EB', true: '#86EFAC' }}
                  thumbColor={formData.is_available ? '#10B981' : '#9CA3AF'}
                />
              </View>

              <View style={styles.switchRow}>
                <Text style={styles.switchLabel}>Featured</Text>
                <Switch
                  value={formData.is_featured}
                  onValueChange={(value) => setFormData(prev => ({ ...prev, is_featured: value }))}
                  trackColor={{ false: '#E5E7EB', true: '#FCD34D' }}
                  thumbColor={formData.is_featured ? '#F59E0B' : '#9CA3AF'}
                />
              </View>
            </ScrollView>

            <View style={styles.modalFooter}>
              <TouchableOpacity 
                style={styles.cancelButton} 
                onPress={() => setShowAddModal(false)}
              >
                <Text style={styles.cancelButtonText}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.saveButton} onPress={handleSave}>
                <Text style={styles.saveButtonText}>
                  {editingItem ? 'Update' : 'Add Item'}
                </Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
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
  addButton: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 8,
  },
  addButtonText: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: '600',
  },
  searchContainer: {
    padding: 16,
    paddingBottom: 8,
    backgroundColor: '#FFFFFF',
  },
  searchInput: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F3F4F6',
    borderRadius: 12,
    paddingHorizontal: 12,
  },
  searchIcon: {
    fontSize: 18,
    marginRight: 8,
  },
  searchTextInput: {
    flex: 1,
    paddingVertical: 12,
    fontSize: 16,
    color: '#1F2937',
  },
  filterContainer: {
    flexDirection: 'row',
    paddingHorizontal: 16,
    paddingBottom: 12,
    backgroundColor: '#FFFFFF',
    gap: 8,
  },
  filterChip: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
    backgroundColor: '#F3F4F6',
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
  menuCard: {
    flexDirection: 'row',
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    marginBottom: 12,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 4,
    elevation: 2,
  },
  menuImageContainer: {
    width: 100,
    height: 120,
    position: 'relative',
  },
  menuImage: {
    width: '100%',
    height: '100%',
  },
  menuImagePlaceholder: {
    width: '100%',
    height: '100%',
    backgroundColor: '#F3F4F6',
    justifyContent: 'center',
    alignItems: 'center',
  },
  menuImageIcon: {
    fontSize: 32,
  },
  featuredBadge: {
    position: 'absolute',
    top: 8,
    left: 8,
    backgroundColor: '#FEF3C7',
    borderRadius: 8,
    paddingHorizontal: 6,
    paddingVertical: 2,
  },
  featuredText: {
    fontSize: 12,
  },
  menuContent: {
    flex: 1,
    padding: 12,
  },
  menuHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 4,
  },
  menuName: {
    fontSize: 16,
    fontWeight: '600',
    color: '#1F2937',
    flex: 1,
    marginRight: 8,
  },
  menuPrice: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  menuDescription: {
    fontSize: 13,
    color: '#6B7280',
    marginBottom: 8,
  },
  menuMeta: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
    gap: 8,
  },
  categoryBadge: {
    backgroundColor: '#E5E7EB',
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 4,
  },
  categoryText: {
    fontSize: 11,
    color: '#6B7280',
  },
  prepTime: {
    fontSize: 12,
    color: '#9CA3AF',
  },
  menuActions: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  availabilityToggle: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  availabilityLabel: {
    fontSize: 12,
    color: '#6B7280',
  },
  actionButtons: {
    flexDirection: 'row',
    gap: 8,
  },
  editButton: {
    padding: 8,
    backgroundColor: '#DBEAFE',
    borderRadius: 8,
  },
  editButtonText: {
    fontSize: 16,
  },
  deleteButton: {
    padding: 8,
    backgroundColor: '#FEE2E2',
    borderRadius: 8,
  },
  deleteButtonText: {
    fontSize: 16,
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
    marginBottom: 24,
  },
  emptyButton: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 8,
  },
  emptyButtonText: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: '600',
  },
  // Modal styles
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'flex-end',
  },
  modalContent: {
    backgroundColor: '#FFFFFF',
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    maxHeight: '90%',
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  modalClose: {
    fontSize: 24,
    color: '#6B7280',
    padding: 4,
  },
  modalBody: {
    padding: 16,
    maxHeight: 400,
  },
  inputLabel: {
    fontSize: 14,
    fontWeight: '600',
    color: '#374151',
    marginBottom: 8,
    marginTop: 12,
  },
  textInput: {
    backgroundColor: '#F3F4F6',
    borderRadius: 12,
    padding: 14,
    fontSize: 16,
    color: '#1F2937',
  },
  textArea: {
    height: 80,
    textAlignVertical: 'top',
  },
  switchRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 16,
    paddingVertical: 8,
  },
  switchLabel: {
    fontSize: 16,
    color: '#374151',
  },
  modalFooter: {
    flexDirection: 'row',
    padding: 16,
    borderTopWidth: 1,
    borderTopColor: '#E5E7EB',
    gap: 12,
  },
  cancelButton: {
    flex: 1,
    padding: 14,
    borderRadius: 12,
    backgroundColor: '#F3F4F6',
    alignItems: 'center',
  },
  cancelButtonText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#6B7280',
  },
  saveButton: {
    flex: 1,
    padding: 14,
    borderRadius: 12,
    backgroundColor: COLORS.primary,
    alignItems: 'center',
  },
  saveButtonText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#FFFFFF',
  },
});

export default VendorMenuScreen;
