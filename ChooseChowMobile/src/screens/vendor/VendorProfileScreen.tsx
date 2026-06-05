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
  TextInput,
  Modal,
  Image,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useAuth } from '../../contexts';
import { COLORS } from '../../utils/theme';
import { vendorService, VendorProfile } from '../../api';

type VendorProfileProps = {
  navigation: NativeStackNavigationProp<any>;
};

// Mock profile data
const MOCK_PROFILE: VendorProfile = {
  id: 1,
  user_id: 1,
  business_name: "Chef's Kitchen",
  slug: 'chefs-kitchen',
  bio: 'Serving delicious Nigerian cuisine with love and passion. We specialize in local dishes made with fresh ingredients.',
  city: 'Lagos',
  is_online: true,
  is_verified: true,
  is_featured: false,
  rating: 4.8,
  total_reviews: 156,
  total_orders: 1520,
  profile_image_url: undefined,
  cover_image_url: undefined,
  kitchen_address: '123 Victoria Island, Lagos',
  minimum_order: 2000,
  delivery_fee: 500,
  delivery_radius_km: 10,
  years_of_experience: 5,
  verification_status: 'approved',
  bank_name: 'First Bank',
  account_number: '****5678',
  account_name: 'John Doe',
  cuisines: [{ id: 1, name: 'Nigerian' }, { id: 2, name: 'Continental' }],
};

export const VendorProfileScreen: React.FC<VendorProfileProps> = ({ navigation }) => {
  const { user, logout } = useAuth();
  const insets = useSafeAreaInsets();
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [profile, setProfile] = useState<VendorProfile | null>(null);
  const [showEditModal, setShowEditModal] = useState(false);
  const [showBankModal, setShowBankModal] = useState(false);
  const [isSaving, setIsSaving] = useState(false);

  // Edit form state
  const [editForm, setEditForm] = useState({
    business_name: '',
    bio: '',
    kitchen_address: '',
    city: '',
    minimum_order: 0,
    delivery_fee: 0,
    delivery_radius_km: 0,
  });

  // Bank form state
  const [bankForm, setBankForm] = useState({
    bank_name: '',
    account_number: '',
    account_name: '',
  });

  const loadProfile = useCallback(async () => {
    try {
      const data = await vendorService.getProfile();
      setProfile(data);
      setEditForm({
        business_name: data.business_name || '',
        bio: data.bio || '',
        kitchen_address: data.kitchen_address || '',
        city: data.city || '',
        minimum_order: data.minimum_order || 0,
        delivery_fee: data.delivery_fee || 0,
        delivery_radius_km: data.delivery_radius_km || 0,
      });
      setBankForm({
        bank_name: data.bank_name || '',
        account_number: '',
        account_name: data.account_name || '',
      });
    } catch (err: any) {
      console.error('Failed to load profile:', err);
      // Use mock data if API returns 404
      if (err.response?.status === 404 || err.response?.status === 401) {
        setProfile(MOCK_PROFILE);
        setEditForm({
          business_name: MOCK_PROFILE.business_name,
          bio: MOCK_PROFILE.bio || '',
          kitchen_address: MOCK_PROFILE.kitchen_address || '',
          city: MOCK_PROFILE.city || '',
          minimum_order: MOCK_PROFILE.minimum_order || 0,
          delivery_fee: MOCK_PROFILE.delivery_fee || 0,
          delivery_radius_km: MOCK_PROFILE.delivery_radius_km || 0,
        });
      }
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    loadProfile();
  }, [loadProfile]);

  const onRefresh = async () => {
    setRefreshing(true);
    await loadProfile();
  };

  const handleSaveProfile = async () => {
    setIsSaving(true);
    try {
      const result = await vendorService.updateProfile(editForm);
      setProfile(result);
      setShowEditModal(false);
      Alert.alert('Success', 'Profile updated successfully');
    } catch (err: any) {
      // Demo mode fallback
      if (err.response?.status === 404) {
        setProfile(prev => prev ? { ...prev, ...editForm } : null);
        setShowEditModal(false);
      } else {
        Alert.alert('Error', 'Failed to update profile');
      }
    } finally {
      setIsSaving(false);
    }
  };

  const handleSaveBankDetails = async () => {
    if (!bankForm.bank_name || !bankForm.account_number || !bankForm.account_name) {
      Alert.alert('Error', 'Please fill in all bank details');
      return;
    }

    setIsSaving(true);
    try {
      await vendorService.updateBankDetails(bankForm);
      setProfile(prev => prev ? { 
        ...prev, 
        bank_name: bankForm.bank_name,
        account_number: '****' + bankForm.account_number.slice(-4),
        account_name: bankForm.account_name,
      } : null);
      setShowBankModal(false);
      Alert.alert('Success', 'Bank details updated successfully');
    } catch (err: any) {
      // Demo mode fallback
      if (err.response?.status === 404) {
        setProfile(prev => prev ? { 
          ...prev, 
          bank_name: bankForm.bank_name,
          account_number: '****' + bankForm.account_number.slice(-4),
          account_name: bankForm.account_name,
        } : null);
        setShowBankModal(false);
      } else {
        Alert.alert('Error', 'Failed to update bank details');
      }
    } finally {
      setIsSaving(false);
    }
  };

  const handleRequestVerification = async () => {
    Alert.alert(
      'Request Verification',
      'Submit your profile for verification? Make sure all required documents are uploaded.',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Submit',
          onPress: async () => {
            try {
              await vendorService.requestVerification();
              setProfile(prev => prev ? { ...prev, verification_status: 'pending' } : null);
              Alert.alert('Success', 'Verification request submitted');
            } catch (err: any) {
              if (err.response?.status === 404) {
                setProfile(prev => prev ? { ...prev, verification_status: 'pending' } : null);
              } else {
                Alert.alert('Error', 'Failed to submit verification request');
              }
            }
          },
        },
      ]
    );
  };

  const handleLogout = () => {
    Alert.alert(
      'Sign Out',
      'Are you sure you want to sign out?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Sign Out',
          style: 'destructive',
          onPress: async () => {
            try {
              await logout();
            } catch (error) {
              console.error('Logout error:', error);
            }
          },
        },
      ]
    );
  };

  const getVerificationBadge = () => {
    if (profile?.is_verified) {
      return { icon: '✓', text: 'Verified', bg: '#D1FAE5', color: '#10B981' };
    }
    switch (profile?.verification_status) {
      case 'pending':
        return { icon: '⏳', text: 'Pending', bg: '#FEF3C7', color: '#F59E0B' };
      case 'rejected':
        return { icon: '✕', text: 'Rejected', bg: '#FEE2E2', color: '#EF4444' };
      default:
        return { icon: '!', text: 'Unverified', bg: '#F3F4F6', color: '#6B7280' };
    }
  };

  const formatCurrency = (amount: number) => `₦${amount.toLocaleString()}`;

  if (isLoading) {
    return (
      <View style={[styles.container, styles.centerContent, { paddingTop: insets.top }]}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.loadingText}>Loading profile...</Text>
      </View>
    );
  }

  const verificationBadge = getVerificationBadge();

  return (
    <View style={[styles.container, { paddingTop: insets.top }]}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
          <Text style={styles.backIcon}>←</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Profile & Settings</Text>
        <TouchableOpacity style={styles.editButton} onPress={() => setShowEditModal(true)}>
          <Text style={styles.editButtonText}>Edit</Text>
        </TouchableOpacity>
      </View>

      <ScrollView
        style={styles.scrollContainer}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />
        }
        contentContainerStyle={{ paddingBottom: insets.bottom + 100 }}
      >
        {/* Profile Header */}
        <View style={styles.profileHeader}>
          <View style={styles.avatarContainer}>
            {profile?.profile_image_url ? (
              <Image source={{ uri: profile.profile_image_url }} style={styles.avatar} />
            ) : (
              <View style={styles.avatarPlaceholder}>
                <Text style={styles.avatarText}>
                  {profile?.business_name?.charAt(0) || user?.first_name?.charAt(0) || 'V'}
                </Text>
              </View>
            )}
            <TouchableOpacity style={styles.cameraButton}>
              <Text style={styles.cameraIcon}>📷</Text>
            </TouchableOpacity>
          </View>
          <Text style={styles.businessName}>{profile?.business_name || 'Your Business'}</Text>
          <Text style={styles.email}>{user?.email || ''}</Text>
          
          {/* Verification Badge */}
          <View style={[styles.verificationBadge, { backgroundColor: verificationBadge.bg }]}>
            <Text style={[styles.verificationText, { color: verificationBadge.color }]}>
              {verificationBadge.icon} {verificationBadge.text}
            </Text>
          </View>

          {/* Stats Row */}
          <View style={styles.statsRow}>
            <View style={styles.statItem}>
              <Text style={styles.statValue}>⭐ {profile?.rating?.toFixed(1) || '0.0'}</Text>
              <Text style={styles.statLabel}>{profile?.total_reviews || 0} reviews</Text>
            </View>
            <View style={styles.statDivider} />
            <View style={styles.statItem}>
              <Text style={styles.statValue}>📦 {profile?.total_orders || 0}</Text>
              <Text style={styles.statLabel}>Orders</Text>
            </View>
          </View>
        </View>

        {/* Business Info Section */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Business Information</Text>
          <View style={styles.infoCard}>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>📍 Address</Text>
              <Text style={styles.infoValue}>{profile?.kitchen_address || 'Not set'}</Text>
            </View>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>🏙️ City</Text>
              <Text style={styles.infoValue}>{profile?.city || 'Not set'}</Text>
            </View>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>💰 Min Order</Text>
              <Text style={styles.infoValue}>{formatCurrency(profile?.minimum_order || 0)}</Text>
            </View>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>🚚 Delivery Fee</Text>
              <Text style={styles.infoValue}>{formatCurrency(profile?.delivery_fee || 0)}</Text>
            </View>
            <View style={[styles.infoRow, { borderBottomWidth: 0 }]}>
              <Text style={styles.infoLabel}>📏 Delivery Radius</Text>
              <Text style={styles.infoValue}>{profile?.delivery_radius_km || 0} km</Text>
            </View>
          </View>
        </View>

        {/* Bio Section */}
        {profile?.bio && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>About</Text>
            <View style={styles.bioCard}>
              <Text style={styles.bioText}>{profile.bio}</Text>
            </View>
          </View>
        )}

        {/* Bank Details Section */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={styles.sectionTitle}>Bank Details</Text>
            <TouchableOpacity onPress={() => setShowBankModal(true)}>
              <Text style={styles.sectionAction}>Edit</Text>
            </TouchableOpacity>
          </View>
          <View style={styles.infoCard}>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>🏦 Bank</Text>
              <Text style={styles.infoValue}>{profile?.bank_name || 'Not set'}</Text>
            </View>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>💳 Account</Text>
              <Text style={styles.infoValue}>{profile?.account_number || '••••••••••'}</Text>
            </View>
            <View style={[styles.infoRow, { borderBottomWidth: 0 }]}>
              <Text style={styles.infoLabel}>👤 Name</Text>
              <Text style={styles.infoValue}>{profile?.account_name || 'Not set'}</Text>
            </View>
          </View>
        </View>

        {/* Verification Section */}
        {!profile?.is_verified && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Verification</Text>
            <View style={styles.verificationCard}>
              <Text style={styles.verificationCardText}>
                Get verified to build trust with customers and access more features.
              </Text>
              <TouchableOpacity 
                style={styles.verifyButton}
                onPress={handleRequestVerification}
                disabled={profile?.verification_status === 'pending'}
              >
                <Text style={styles.verifyButtonText}>
                  {profile?.verification_status === 'pending' 
                    ? 'Verification Pending' 
                    : 'Request Verification'}
                </Text>
              </TouchableOpacity>
            </View>
          </View>
        )}

        {/* Settings Menu */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Settings</Text>
          <View style={styles.menuCard}>
            <TouchableOpacity style={styles.menuItem}>
              <Text style={styles.menuIcon}>🕐</Text>
              <Text style={styles.menuLabel}>Operating Hours</Text>
              <Text style={styles.menuArrow}>›</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.menuItem}>
              <Text style={styles.menuIcon}>📄</Text>
              <Text style={styles.menuLabel}>Documents</Text>
              <Text style={styles.menuArrow}>›</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.menuItem}>
              <Text style={styles.menuIcon}>🔔</Text>
              <Text style={styles.menuLabel}>Notifications</Text>
              <Text style={styles.menuArrow}>›</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.menuItem}>
              <Text style={styles.menuIcon}>❓</Text>
              <Text style={styles.menuLabel}>Help & Support</Text>
              <Text style={styles.menuArrow}>›</Text>
            </TouchableOpacity>
            <TouchableOpacity style={[styles.menuItem, { borderBottomWidth: 0 }]}>
              <Text style={styles.menuIcon}>ℹ️</Text>
              <Text style={styles.menuLabel}>About ChooseChow</Text>
              <Text style={styles.menuArrow}>›</Text>
            </TouchableOpacity>
          </View>
        </View>

        {/* Logout Button */}
        <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
          <Text style={styles.logoutButtonText}>Sign Out</Text>
        </TouchableOpacity>
      </ScrollView>

      {/* Edit Profile Modal */}
      <Modal
        visible={showEditModal}
        animationType="slide"
        transparent={true}
        onRequestClose={() => setShowEditModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, { paddingBottom: insets.bottom + 16 }]}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Edit Profile</Text>
              <TouchableOpacity onPress={() => setShowEditModal(false)}>
                <Text style={styles.modalClose}>✕</Text>
              </TouchableOpacity>
            </View>

            <ScrollView style={styles.modalBody} showsVerticalScrollIndicator={false}>
              <Text style={styles.inputLabel}>Business Name</Text>
              <TextInput
                style={styles.textInput}
                value={editForm.business_name}
                onChangeText={(text) => setEditForm(prev => ({ ...prev, business_name: text }))}
                placeholder="Enter business name"
              />

              <Text style={styles.inputLabel}>Bio</Text>
              <TextInput
                style={[styles.textInput, styles.textArea]}
                value={editForm.bio}
                onChangeText={(text) => setEditForm(prev => ({ ...prev, bio: text }))}
                placeholder="Tell customers about your business"
                multiline
                numberOfLines={3}
              />

              <Text style={styles.inputLabel}>Kitchen Address</Text>
              <TextInput
                style={styles.textInput}
                value={editForm.kitchen_address}
                onChangeText={(text) => setEditForm(prev => ({ ...prev, kitchen_address: text }))}
                placeholder="Enter your kitchen address"
              />

              <Text style={styles.inputLabel}>City</Text>
              <TextInput
                style={styles.textInput}
                value={editForm.city}
                onChangeText={(text) => setEditForm(prev => ({ ...prev, city: text }))}
                placeholder="Enter city"
              />

              <Text style={styles.inputLabel}>Minimum Order (₦)</Text>
              <TextInput
                style={styles.textInput}
                value={editForm.minimum_order ? String(editForm.minimum_order) : ''}
                onChangeText={(text) => setEditForm(prev => ({ ...prev, minimum_order: Number(text) || 0 }))}
                placeholder="Enter minimum order amount"
                keyboardType="numeric"
              />

              <Text style={styles.inputLabel}>Delivery Fee (₦)</Text>
              <TextInput
                style={styles.textInput}
                value={editForm.delivery_fee ? String(editForm.delivery_fee) : ''}
                onChangeText={(text) => setEditForm(prev => ({ ...prev, delivery_fee: Number(text) || 0 }))}
                placeholder="Enter delivery fee"
                keyboardType="numeric"
              />

              <Text style={styles.inputLabel}>Delivery Radius (km)</Text>
              <TextInput
                style={styles.textInput}
                value={editForm.delivery_radius_km ? String(editForm.delivery_radius_km) : ''}
                onChangeText={(text) => setEditForm(prev => ({ ...prev, delivery_radius_km: Number(text) || 0 }))}
                placeholder="Enter delivery radius"
                keyboardType="numeric"
              />
            </ScrollView>

            <View style={styles.modalFooter}>
              <TouchableOpacity 
                style={styles.cancelButton} 
                onPress={() => setShowEditModal(false)}
              >
                <Text style={styles.cancelButtonText}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity 
                style={[styles.saveButton, isSaving && styles.saveButtonDisabled]} 
                onPress={handleSaveProfile}
                disabled={isSaving}
              >
                {isSaving ? (
                  <ActivityIndicator size="small" color="#FFFFFF" />
                ) : (
                  <Text style={styles.saveButtonText}>Save Changes</Text>
                )}
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>

      {/* Bank Details Modal */}
      <Modal
        visible={showBankModal}
        animationType="slide"
        transparent={true}
        onRequestClose={() => setShowBankModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, { paddingBottom: insets.bottom + 16 }]}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Bank Details</Text>
              <TouchableOpacity onPress={() => setShowBankModal(false)}>
                <Text style={styles.modalClose}>✕</Text>
              </TouchableOpacity>
            </View>

            <ScrollView style={styles.modalBody} showsVerticalScrollIndicator={false}>
              <Text style={styles.inputLabel}>Bank Name</Text>
              <TextInput
                style={styles.textInput}
                value={bankForm.bank_name}
                onChangeText={(text) => setBankForm(prev => ({ ...prev, bank_name: text }))}
                placeholder="e.g., First Bank"
              />

              <Text style={styles.inputLabel}>Account Number</Text>
              <TextInput
                style={styles.textInput}
                value={bankForm.account_number}
                onChangeText={(text) => setBankForm(prev => ({ ...prev, account_number: text }))}
                placeholder="Enter 10-digit account number"
                keyboardType="numeric"
                maxLength={10}
              />

              <Text style={styles.inputLabel}>Account Name</Text>
              <TextInput
                style={styles.textInput}
                value={bankForm.account_name}
                onChangeText={(text) => setBankForm(prev => ({ ...prev, account_name: text }))}
                placeholder="Enter account holder name"
              />
            </ScrollView>

            <View style={styles.modalFooter}>
              <TouchableOpacity 
                style={styles.cancelButton} 
                onPress={() => setShowBankModal(false)}
              >
                <Text style={styles.cancelButtonText}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity 
                style={[styles.saveButton, isSaving && styles.saveButtonDisabled]} 
                onPress={handleSaveBankDetails}
                disabled={isSaving}
              >
                {isSaving ? (
                  <ActivityIndicator size="small" color="#FFFFFF" />
                ) : (
                  <Text style={styles.saveButtonText}>Save</Text>
                )}
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
  editButton: {
    padding: 8,
  },
  editButtonText: {
    color: COLORS.primary,
    fontSize: 16,
    fontWeight: '600',
  },
  scrollContainer: {
    flex: 1,
  },
  profileHeader: {
    backgroundColor: '#FFFFFF',
    alignItems: 'center',
    paddingVertical: 24,
    paddingHorizontal: 16,
  },
  avatarContainer: {
    position: 'relative',
    marginBottom: 16,
  },
  avatar: {
    width: 100,
    height: 100,
    borderRadius: 50,
  },
  avatarPlaceholder: {
    width: 100,
    height: 100,
    borderRadius: 50,
    backgroundColor: COLORS.primary,
    justifyContent: 'center',
    alignItems: 'center',
  },
  avatarText: {
    fontSize: 40,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  cameraButton: {
    position: 'absolute',
    bottom: 0,
    right: 0,
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: '#FFFFFF',
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 2,
    borderColor: '#E5E7EB',
  },
  cameraIcon: {
    fontSize: 16,
  },
  businessName: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#1F2937',
    marginBottom: 4,
  },
  email: {
    fontSize: 14,
    color: '#6B7280',
    marginBottom: 12,
  },
  verificationBadge: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 16,
    marginBottom: 16,
  },
  verificationText: {
    fontSize: 12,
    fontWeight: '600',
  },
  statsRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  statItem: {
    alignItems: 'center',
    paddingHorizontal: 24,
  },
  statValue: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  statLabel: {
    fontSize: 12,
    color: '#6B7280',
    marginTop: 2,
  },
  statDivider: {
    width: 1,
    height: 32,
    backgroundColor: '#E5E7EB',
  },
  section: {
    marginTop: 16,
    paddingHorizontal: 16,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#1F2937',
    marginBottom: 12,
  },
  sectionAction: {
    fontSize: 14,
    color: COLORS.primary,
    fontWeight: '600',
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
    textAlign: 'right',
    flex: 1,
    marginLeft: 12,
  },
  bioCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
  },
  bioText: {
    fontSize: 14,
    color: '#374151',
    lineHeight: 22,
  },
  verificationCard: {
    backgroundColor: COLORS.primaryFaded,
    borderRadius: 12,
    padding: 16,
    alignItems: 'center',
  },
  verificationCardText: {
    fontSize: 14,
    color: COLORS.primaryDark,
    textAlign: 'center',
    marginBottom: 12,
  },
  verifyButton: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 8,
  },
  verifyButtonText: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: '600',
  },
  menuCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
  },
  menuItem: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#F3F4F6',
  },
  menuIcon: {
    fontSize: 20,
    marginRight: 12,
  },
  menuLabel: {
    flex: 1,
    fontSize: 15,
    color: '#1F2937',
  },
  menuArrow: {
    fontSize: 20,
    color: '#9CA3AF',
  },
  logoutButton: {
    margin: 16,
    backgroundColor: '#FEE2E2',
    padding: 16,
    borderRadius: 12,
    alignItems: 'center',
  },
  logoutButtonText: {
    color: '#EF4444',
    fontSize: 16,
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
  saveButtonDisabled: {
    backgroundColor: '#9CA3AF',
  },
  saveButtonText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#FFFFFF',
  },
});

export default VendorProfileScreen;
