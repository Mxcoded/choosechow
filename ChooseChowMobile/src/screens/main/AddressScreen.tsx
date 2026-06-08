import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Alert,
  ActivityIndicator,
  RefreshControl,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { customerService } from '../../api';
import type { Address } from '../../api/customerService';
import { COLORS } from '../../utils/theme';
import MaterialCommunityIcons from '@expo/vector-icons/MaterialCommunityIcons';

type AddressScreenProps = {
  navigation: NativeStackNavigationProp<any>;
};

export const AddressScreen: React.FC<AddressScreenProps> = ({ navigation }) => {
  const [addresses, setAddresses] = useState<Address[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const loadAddresses = async () => {
    try {
      const data = await customerService.getAddresses();
      setAddresses(data);
    } catch {
      Alert.alert('Error', 'Failed to load addresses');
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => { loadAddresses(); }, []);

  const handleSetDefault = async (id: number) => {
    try {
      await customerService.setDefaultAddress(id);
      loadAddresses();
    } catch {
      Alert.alert('Error', 'Failed to set default address');
    }
  };

  const handleDelete = (id: number) => {
    Alert.alert('Delete Address', 'Are you sure?', [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Delete', style: 'destructive',
        onPress: async () => {
          try {
            await customerService.deleteAddress(id);
            loadAddresses();
          } catch { Alert.alert('Error', 'Failed to delete address'); }
        },
      },
    ]);
  };

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color={COLORS.primary} />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <ScrollView
        contentContainerStyle={styles.content}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); loadAddresses(); }} />}
      >
        {addresses.length === 0 ? (
          <View style={styles.empty}>
            <MaterialCommunityIcons name="map-marker-off" size={48} color={COLORS.text.light} />
            <Text style={styles.emptyTitle}>No Addresses</Text>
            <Text style={styles.emptySubtitle}>Add an address for faster checkout</Text>
          </View>
        ) : (
          addresses.map((addr) => (
            <View key={addr.id} style={styles.addressCard}>
              <View style={styles.addressHeader}>
                <View style={styles.addressLabelRow}>
                  <MaterialCommunityIcons name="map-marker" size={18} color={COLORS.primary} />
                  <Text style={styles.labelText}>{addr.label || 'Address'}</Text>
                  {addr.is_default && (
                    <View style={styles.defaultBadge}>
                      <Text style={styles.defaultBadgeText}>Default</Text>
                    </View>
                  )}
                </View>
              </View>
              <Text style={styles.addressText} numberOfLines={2}>
                {addr.address_line_1}{addr.address_line_2 ? `, ${addr.address_line_2}` : ''}
              </Text>
              <Text style={styles.addressCity}>{addr.city}{addr.state ? `, ${addr.state}` : ''}</Text>
              <View style={styles.addressActions}>
                {!addr.is_default && (
                  <TouchableOpacity style={styles.actionBtn} onPress={() => handleSetDefault(addr.id)}>
                    <MaterialCommunityIcons name="check-circle-outline" size={16} color={COLORS.primary} />
                    <Text style={styles.actionText}>Set Default</Text>
                  </TouchableOpacity>
                )}
                <TouchableOpacity style={styles.actionBtn} onPress={() => handleDelete(addr.id)}>
                  <MaterialCommunityIcons name="delete-outline" size={16} color={COLORS.error} />
                  <Text style={[styles.actionText, { color: COLORS.error }]}>Delete</Text>
                </TouchableOpacity>
              </View>
            </View>
          ))
        )}
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: COLORS.background.secondary },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  content: { padding: 20 },
  empty: { alignItems: 'center', paddingVertical: 60 },
  emptyTitle: { fontSize: 18, fontWeight: 'bold', color: COLORS.text.primary, marginTop: 16 },
  emptySubtitle: { fontSize: 14, color: COLORS.text.secondary, marginTop: 4 },
  addressCard: {
    backgroundColor: COLORS.white, padding: 16, borderRadius: 12,
    marginBottom: 12, borderWidth: 1, borderColor: COLORS.border.light,
  },
  addressHeader: { marginBottom: 8 },
  addressLabelRow: { flexDirection: 'row', alignItems: 'center' },
  labelText: { fontSize: 15, fontWeight: '600', color: COLORS.text.primary, marginLeft: 8, flex: 1 },
  defaultBadge: {
    backgroundColor: COLORS.primaryFaded, paddingHorizontal: 10, paddingVertical: 2, borderRadius: 10,
  },
  defaultBadgeText: { fontSize: 11, color: COLORS.primary, fontWeight: '600' },
  addressText: { fontSize: 14, color: COLORS.text.secondary, marginTop: 4 },
  addressCity: { fontSize: 13, color: COLORS.text.light, marginTop: 2 },
  addressActions: { flexDirection: 'row', marginTop: 12, gap: 16 },
  actionBtn: { flexDirection: 'row', alignItems: 'center' },
  actionText: { fontSize: 13, color: COLORS.primary, fontWeight: '500', marginLeft: 4 },
});

export default AddressScreen;
