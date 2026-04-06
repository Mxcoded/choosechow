import React from 'react';
import { ActivityIndicator, View, Text, StyleSheet } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { useAuth } from '../contexts';

// Auth Screens
import LoginScreen from '../screens/auth/LoginScreen';
import RegisterScreen from '../screens/auth/RegisterScreen';
import ForgotPasswordScreen from '../screens/auth/ForgotPasswordScreen';

// Main Screens
import HomeScreen from '../screens/main/HomeScreen';
import CartScreen from '../screens/main/CartScreen';
import ChefListScreen from '../screens/main/ChefListScreen';
import ChefDetailScreen from '../screens/main/ChefDetailScreen';
import CheckoutScreen from '../screens/main/CheckoutScreen';

// Stack Navigator Types
export type AuthStackParamList = {
  Login: undefined;
  Register: undefined;
  ForgotPassword: undefined;
};

export type MainStackParamList = {
  MainTabs: undefined;
  ChefDetail: { chefId: number };
  ChefList: { search?: string; cuisine?: string; sortBy?: string };
  MenuDetail: { menuId: number };
  Checkout: undefined;
  OrderDetail: { orderId: number };
  OrderTracking: { orderId: number };
};

export type MainTabParamList = {
  Home: undefined;
  Search: undefined;
  Cart: undefined;
  Orders: undefined;
  Profile: undefined;
};

const AuthStack = createNativeStackNavigator<AuthStackParamList>();
const MainStack = createNativeStackNavigator<MainStackParamList>();
const Tab = createBottomTabNavigator<MainTabParamList>();

// Placeholder screens (you can expand these later)
const SearchScreen = () => (
  <View style={styles.placeholder}>
    <Text style={styles.placeholderText}>🔍</Text>
    <Text style={styles.placeholderTitle}>Search</Text>
    <Text style={styles.placeholderSubtitle}>Find your favorite chefs and dishes</Text>
  </View>
);

const OrdersScreen = () => (
  <View style={styles.placeholder}>
    <Text style={styles.placeholderText}>📋</Text>
    <Text style={styles.placeholderTitle}>My Orders</Text>
    <Text style={styles.placeholderSubtitle}>Track your order history</Text>
  </View>
);

const ProfileScreen = ({ navigation }: any) => {
  const { user, logout } = useAuth();
  
  return (
    <View style={styles.profileContainer}>
      <View style={styles.profileHeader}>
        <View style={styles.avatar}>
          <Text style={styles.avatarText}>{user?.name?.charAt(0) || 'U'}</Text>
        </View>
        <Text style={styles.profileName}>{user?.name || 'User'}</Text>
        <Text style={styles.profileEmail}>{user?.email || ''}</Text>
      </View>
      
      <View style={styles.profileMenu}>
        <MenuItem title="Edit Profile" icon="👤" />
        <MenuItem title="My Addresses" icon="📍" />
        <MenuItem title="Payment Methods" icon="💳" />
        <MenuItem title="Notifications" icon="🔔" />
        <MenuItem title="Help & Support" icon="❓" />
        <MenuItem title="About" icon="ℹ️" />
      </View>
      
      <View style={styles.logoutContainer}>
        <Text style={styles.logoutButton} onPress={() => logout()}>
          Sign Out
        </Text>
      </View>
    </View>
  );
};

const MenuItem = ({ title, icon }: { title: string; icon: string }) => (
  <View style={styles.menuItem}>
    <Text style={styles.menuIcon}>{icon}</Text>
    <Text style={styles.menuTitle}>{title}</Text>
    <Text style={styles.menuArrow}>›</Text>
  </View>
);

// Tab Navigator
const TabNavigator = () => {
  const { itemCount } = require('../contexts').useCart();
  
  return (
    <Tab.Navigator
      screenOptions={{
        tabBarActiveTintColor: '#FF6B35',
        tabBarInactiveTintColor: '#9CA3AF',
        tabBarStyle: {
          backgroundColor: '#FFFFFF',
          borderTopWidth: 1,
          borderTopColor: '#E5E7EB',
          paddingBottom: 8,
          paddingTop: 8,
          height: 60,
        },
        tabBarLabelStyle: {
          fontSize: 12,
          fontWeight: '600',
        },
        headerShown: true,
        headerStyle: {
          backgroundColor: '#FFFFFF',
        },
        headerTintColor: '#1F2937',
        headerTitleStyle: {
          fontWeight: 'bold',
        },
      }}
    >
      <Tab.Screen
        name="Home"
        component={HomeScreen}
        options={{
          tabBarLabel: 'Home',
          tabBarIcon: ({ color }) => <Text style={{ fontSize: 22 }}>🏠</Text>,
          headerTitle: 'ChooseChow',
          headerTitleStyle: { color: '#FF6B35', fontWeight: 'bold', fontSize: 22 },
        }}
      />
      <Tab.Screen
        name="Search"
        component={SearchScreen}
        options={{
          tabBarLabel: 'Search',
          tabBarIcon: ({ color }) => <Text style={{ fontSize: 22 }}>🔍</Text>,
          headerTitle: 'Search Chefs',
        }}
      />
      <Tab.Screen
        name="Cart"
        component={CartScreen}
        options={{
          tabBarLabel: 'Cart',
          tabBarIcon: ({ color }) => <Text style={{ fontSize: 22 }}>🛒</Text>,
          tabBarBadge: itemCount > 0 ? itemCount : undefined,
          tabBarBadgeStyle: { backgroundColor: '#FF6B35' },
          headerTitle: 'My Cart',
        }}
      />
      <Tab.Screen
        name="Orders"
        component={OrdersScreen}
        options={{
          tabBarLabel: 'Orders',
          tabBarIcon: ({ color }) => <Text style={{ fontSize: 22 }}>📋</Text>,
          headerTitle: 'My Orders',
        }}
      />
      <Tab.Screen
        name="Profile"
        component={ProfileScreen}
        options={{
          tabBarLabel: 'Profile',
          tabBarIcon: ({ color }) => <Text style={{ fontSize: 22 }}>👤</Text>,
          headerTitle: 'Profile',
        }}
      />
    </Tab.Navigator>
  );
};

// Auth Navigator
const AuthNavigator = () => (
  <AuthStack.Navigator
    screenOptions={{
      headerShown: false,
    }}
  >
    <AuthStack.Screen name="Login" component={LoginScreen} />
    <AuthStack.Screen name="Register" component={RegisterScreen} />
    <AuthStack.Screen name="ForgotPassword" component={ForgotPasswordScreen} />
  </AuthStack.Navigator>
);

// Main Navigator (authenticated)
const MainNavigator = () => (
  <MainStack.Navigator
    screenOptions={{
      headerStyle: {
        backgroundColor: '#FFFFFF',
      },
      headerTintColor: '#1F2937',
      headerBackTitleVisible: false,
    }}
  >
    <MainStack.Screen
      name="MainTabs"
      component={TabNavigator}
      options={{ headerShown: false }}
    />
    <MainStack.Screen
      name="ChefList"
      component={ChefListScreen}
      options={{ headerTitle: 'Chefs' }}
    />
    <MainStack.Screen
      name="ChefDetail"
      component={ChefDetailScreen}
      options={{ headerTitle: 'Chef Details' }}
    />
    <MainStack.Screen
      name="Checkout"
      component={CheckoutScreen}
      options={{ headerTitle: 'Checkout' }}
    />
  </MainStack.Navigator>
);

// Root Navigator
export const AppNavigator: React.FC = () => {
  const { isAuthenticated, isLoading } = useAuth();

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#FF6B35" />
        <Text style={styles.loadingText}>Loading...</Text>
      </View>
    );
  }

  return (
    <NavigationContainer>
      {isAuthenticated ? <MainNavigator /> : <AuthNavigator />}
    </NavigationContainer>
  );
};

const styles = StyleSheet.create({
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
  },
  loadingText: {
    marginTop: 16,
    fontSize: 16,
    color: '#6B7280',
  },
  placeholder: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#F9FAFB',
    padding: 24,
  },
  placeholderText: {
    fontSize: 64,
    marginBottom: 16,
  },
  placeholderTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#1F2937',
    marginBottom: 8,
  },
  placeholderSubtitle: {
    fontSize: 16,
    color: '#6B7280',
    textAlign: 'center',
  },
  profileContainer: {
    flex: 1,
    backgroundColor: '#F9FAFB',
  },
  profileHeader: {
    backgroundColor: '#FFFFFF',
    alignItems: 'center',
    paddingVertical: 32,
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  avatar: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: '#FF6B35',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
  },
  avatarText: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  profileName: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  profileEmail: {
    fontSize: 14,
    color: '#6B7280',
    marginTop: 4,
  },
  profileMenu: {
    backgroundColor: '#FFFFFF',
    marginTop: 16,
  },
  menuItem: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  menuIcon: {
    fontSize: 20,
    marginRight: 16,
  },
  menuTitle: {
    flex: 1,
    fontSize: 16,
    color: '#1F2937',
  },
  menuArrow: {
    fontSize: 20,
    color: '#9CA3AF',
  },
  logoutContainer: {
    marginTop: 32,
    alignItems: 'center',
  },
  logoutButton: {
    fontSize: 16,
    color: '#EF4444',
    fontWeight: '600',
  },
});

export default AppNavigator;
