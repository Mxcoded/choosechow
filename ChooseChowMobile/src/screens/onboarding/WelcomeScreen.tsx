import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  Dimensions,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { COLORS } from '../../utils/theme';

const { width } = Dimensions.get('window');

type WelcomeScreenProps = {
  navigation: NativeStackNavigationProp<any>;
};

type UserRole = 'customer' | 'chef' | null;

export const WelcomeScreen: React.FC<WelcomeScreenProps> = ({ navigation }) => {
  const [selectedRole, setSelectedRole] = useState<UserRole>(null);

  const handleRoleSelect = (role: UserRole) => {
    setSelectedRole(role);
    // Navigate to appropriate registration with role pre-selected
    navigation.navigate('Register', { role });
  };

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <Text style={styles.title}>Welcome</Text>
        <Text style={styles.subtitle}>Please tell us who you are:</Text>
      </View>

      {/* Role Selection Cards */}
      <View style={styles.cardsContainer}>
        {/* Foodie Card */}
        <TouchableOpacity
          style={[
            styles.roleCard,
            selectedRole === 'customer' && styles.roleCardSelected,
          ]}
          onPress={() => handleRoleSelect('customer')}
          activeOpacity={0.8}
        >
          <View style={styles.roleIconContainer}>
            <Text style={styles.roleIcon}>🏪</Text>
            <View style={styles.iconDecoration}>
              <Text style={styles.smallIcon}>🍽️</Text>
            </View>
          </View>
          <View style={styles.roleTextContainer}>
            <Text style={styles.roleLabel}>I am a FOODIE</Text>
          </View>
          {selectedRole === 'customer' && (
            <View style={styles.selectedIndicator} />
          )}
        </TouchableOpacity>

        {/* Vendor/Chef Card */}
        <TouchableOpacity
          style={[
            styles.roleCard,
            selectedRole === 'chef' && styles.roleCardSelected,
          ]}
          onPress={() => handleRoleSelect('chef')}
          activeOpacity={0.8}
        >
          <View style={styles.roleIconContainer}>
            <Text style={styles.roleIcon}>🍝</Text>
            <View style={styles.iconDecoration}>
              <Text style={styles.smallIcon}>🍒</Text>
            </View>
          </View>
          <View style={styles.roleTextContainer}>
            <Text style={styles.roleLabel}>I am a VENDOR</Text>
          </View>
          {selectedRole === 'chef' && (
            <View style={styles.selectedIndicator} />
          )}
        </TouchableOpacity>
      </View>

      {/* Already have account link */}
      <View style={styles.loginContainer}>
        <Text style={styles.loginText}>Already have an account? </Text>
        <TouchableOpacity onPress={() => navigation.navigate('Login')}>
          <Text style={styles.loginLink}>Sign In</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FFFFFF',
    paddingHorizontal: 24,
    paddingTop: 80,
  },
  header: {
    alignItems: 'center',
    marginBottom: 50,
  },
  title: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#1F2937',
    marginBottom: 8,
  },
  subtitle: {
    fontSize: 16,
    color: '#6B7280',
  },
  cardsContainer: {
    flex: 1,
    justifyContent: 'center',
    gap: 20,
  },
  roleCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    borderWidth: 2,
    borderColor: '#E5E7EB',
    padding: 20,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    elevation: 2,
  },
  roleCardSelected: {
    borderColor: COLORS.primary,
    backgroundColor: '#FFF5F5',
  },
  roleIconContainer: {
    width: 120,
    height: 100,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
    position: 'relative',
  },
  roleIcon: {
    fontSize: 64,
  },
  iconDecoration: {
    position: 'absolute',
    top: 0,
    right: 10,
  },
  smallIcon: {
    fontSize: 24,
  },
  roleTextContainer: {
    paddingVertical: 12,
    paddingHorizontal: 24,
    borderWidth: 1,
    borderColor: '#E5E7EB',
    borderRadius: 8,
    backgroundColor: '#FFFFFF',
  },
  roleLabel: {
    fontSize: 14,
    fontWeight: '600',
    color: '#374151',
    letterSpacing: 0.5,
  },
  selectedIndicator: {
    position: 'absolute',
    bottom: -4,
    width: 60,
    height: 4,
    backgroundColor: COLORS.primary,
    borderRadius: 2,
  },
  loginContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    paddingBottom: 50,
  },
  loginText: {
    fontSize: 14,
    color: '#6B7280',
  },
  loginLink: {
    fontSize: 14,
    color: COLORS.primary,
    fontWeight: '600',
  },
});

export default WelcomeScreen;
