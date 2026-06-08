import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TextInput,
  TouchableOpacity,
  Alert,
  ActivityIndicator,
  KeyboardAvoidingView,
  Platform,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { customerService } from '../../api';
import { COLORS } from '../../utils/theme';
import MaterialCommunityIcons from '@expo/vector-icons/MaterialCommunityIcons';

type EditProfileScreenProps = {
  navigation: NativeStackNavigationProp<any>;
};

export const EditProfileScreen: React.FC<EditProfileScreenProps> = ({ navigation }) => {
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [firstName, setFirstName] = useState('');
  const [lastName, setLastName] = useState('');
  const [phone, setPhone] = useState('');

  useEffect(() => {
    (async () => {
      try {
        const profile = await customerService.getProfile();
        setFirstName(profile.first_name || '');
        setLastName(profile.last_name || '');
        setPhone(profile.phone || '');
      } catch {
        Alert.alert('Error', 'Failed to load profile');
      } finally {
        setLoading(false);
      }
    })();
  }, []);

  const handleSave = async () => {
    if (!firstName.trim() || !lastName.trim()) {
      Alert.alert('Required', 'First name and last name are required');
      return;
    }
    setSaving(true);
    try {
      await customerService.updateProfile({
        first_name: firstName.trim(),
        last_name: lastName.trim(),
        phone: phone.trim() || undefined,
      });
      Alert.alert('Saved', 'Profile updated successfully', [
        { text: 'OK', onPress: () => navigation.goBack() },
      ]);
    } catch (error: any) {
      Alert.alert('Error', error.response?.data?.message || 'Failed to update profile');
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color={COLORS.primary} />
      </View>
    );
  }

  return (
    <KeyboardAvoidingView style={styles.container} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView contentContainerStyle={styles.content}>
        <View style={styles.avatarSection}>
          <View style={styles.avatar}>
            <MaterialCommunityIcons name="account" size={48} color="#fff" />
          </View>
          <TouchableOpacity style={styles.changePhotoBtn}>
            <Text style={styles.changePhotoText}>Change Photo</Text>
          </TouchableOpacity>
        </View>

        <View style={styles.field}>
          <Text style={styles.label}>First Name</Text>
          <TextInput
            style={styles.input}
            value={firstName}
            onChangeText={setFirstName}
            placeholder="First name"
            placeholderTextColor={COLORS.text.light}
          />
        </View>

        <View style={styles.field}>
          <Text style={styles.label}>Last Name</Text>
          <TextInput
            style={styles.input}
            value={lastName}
            onChangeText={setLastName}
            placeholder="Last name"
            placeholderTextColor={COLORS.text.light}
          />
        </View>

        <View style={styles.field}>
          <Text style={styles.label}>Phone Number</Text>
          <TextInput
            style={styles.input}
            value={phone}
            onChangeText={setPhone}
            placeholder="e.g. 08012345678"
            placeholderTextColor={COLORS.text.light}
            keyboardType="phone-pad"
            maxLength={20}
          />
        </View>

        <TouchableOpacity
          style={[styles.saveButton, saving && styles.saveButtonDisabled]}
          onPress={handleSave}
          disabled={saving}
        >
          {saving ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.saveText}>Save Changes</Text>
          )}
        </TouchableOpacity>
      </ScrollView>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: COLORS.background.secondary },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  content: { padding: 20 },
  avatarSection: { alignItems: 'center', marginBottom: 32 },
  avatar: {
    width: 96, height: 96, borderRadius: 48,
    backgroundColor: COLORS.primary,
    justifyContent: 'center', alignItems: 'center',
  },
  changePhotoBtn: { marginTop: 12 },
  changePhotoText: { color: COLORS.primary, fontWeight: '600', fontSize: 15 },
  field: { marginBottom: 20 },
  label: { fontSize: 14, fontWeight: '600', color: COLORS.text.primary, marginBottom: 8 },
  input: {
    backgroundColor: COLORS.white, borderRadius: 12, padding: 16,
    fontSize: 16, color: COLORS.text.primary, borderWidth: 1, borderColor: COLORS.border.light,
  },
  saveButton: {
    backgroundColor: COLORS.primary, padding: 16, borderRadius: 12,
    alignItems: 'center', marginTop: 12,
  },
  saveButtonDisabled: { opacity: 0.6 },
  saveText: { color: '#fff', fontSize: 16, fontWeight: 'bold' },
});

export default EditProfileScreen;
