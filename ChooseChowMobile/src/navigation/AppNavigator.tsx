import React from 'react';
import { ActivityIndicator, View, Text, StyleSheet, TouchableOpacity, ScrollView, Platform, Alert } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { useSafeAreaInsets, EdgeInsets } from 'react-native-safe-area-context';
import { useAuth } from '../contexts';
import { COLORS } from '../utils/theme';
import MaterialCommunityIcons from '@expo/vector-icons/MaterialCommunityIcons';

// Base tab bar height (without safe area)
const BASE_TAB_BAR_HEIGHT = 56;
// Minimum bottom padding for Android gesture navigation
const ANDROID_MIN_BOTTOM_PADDING = 8;

// Auth Screens
import LoginScreen from '../screens/auth/LoginScreen';
import RegisterScreen from '../screens/auth/RegisterScreen';
import ForgotPasswordScreen from '../screens/auth/ForgotPasswordScreen';

// Onboarding Screens
import { OnboardingScreen, WelcomeScreen } from '../screens/onboarding';

// Main Screens
import HomeScreen from '../screens/main/HomeScreen';
import CartScreen from '../screens/main/CartScreen';
import ChefListScreen from '../screens/main/ChefListScreen';
import ChefDetailScreen from '../screens/main/ChefDetailScreen';
import CheckoutScreen from '../screens/main/CheckoutScreen';
import PaymentScreen from '../screens/main/PaymentScreen';
import OrdersScreen from '../screens/main/OrdersScreen';
import SubscriptionPlansScreen from '../screens/main/SubscriptionPlansScreen';
import MySubscriptionScreen from '../screens/main/MySubscriptionScreen';
import WalletScreen from '../screens/main/WalletScreen';
import EditProfileScreen from '../screens/main/EditProfileScreen';
import AddressScreen from '../screens/main/AddressScreen';
import NotificationsScreen from '../screens/main/NotificationsScreen';

// Vendor Screens
import { 
  VendorDashboardScreen,
  VendorOrdersScreen,
  VendorMenuScreen,
  VendorEarningsScreen,
  VendorProfileScreen,
  VendorOrderDetailScreen,
  VendorSubscribersScreen,
} from '../screens/vendor';

// Admin Screens
import { 
  AdminDashboardScreen, 
  AdminUsersScreen, 
  AdminVendorsScreen,
  AdminOrdersScreen,
  AdminPayoutsScreen,
  AdminReportsScreen,
} from '../screens/admin';

// Import navigation types
import { 
  AuthStackParamList, 
  MainStackParamList, 
  MainTabParamList,
  VendorStackParamList,
  VendorTabParamList,
  AdminStackParamList,
  AdminTabParamList,
} from './types';

// Re-export types for convenience
export type { AuthStackParamList, MainStackParamList, MainTabParamList, VendorStackParamList, AdminStackParamList };

const AuthStack = createNativeStackNavigator<AuthStackParamList>();
const MainStack = createNativeStackNavigator<MainStackParamList>();
const Tab = createBottomTabNavigator<MainTabParamList>();
const VendorStack = createNativeStackNavigator<VendorStackParamList>();
const VendorTab = createBottomTabNavigator<VendorTabParamList>();
const AdminStack = createNativeStackNavigator<AdminStackParamList>();
const AdminTab = createBottomTabNavigator<AdminTabParamList>();

// Search Screen
const SearchScreen = ({ navigation }: any) => (
  <View style={styles.placeholder}>
    <Text style={styles.placeholderText}>🔍</Text>
    <Text style={styles.placeholderTitle}>Search Chefs</Text>
    <Text style={styles.placeholderSubtitle}>Find your favorite chefs and dishes</Text>
    <TouchableOpacity 
      style={styles.actionButton}
      onPress={() => navigation.navigate('ChefList')}
    >
      <Text style={styles.actionButtonText}>Browse All Chefs</Text>
    </TouchableOpacity>
  </View>
);

// Orders Screen (imported from screens/main/OrdersScreen)

// Profile Screen
const ProfileScreen = ({ navigation }: any) => {
  const { user, logout } = useAuth();
  const insets = useSafeAreaInsets();
  
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
          }
        },
      ]
    );
  };
  
  const menuItems = [
    { title: 'Edit Profile', icon: 'account-outline', onPress: () => navigation.navigate('EditProfile') },
    { title: 'My Addresses', icon: 'map-marker-outline', onPress: () => navigation.navigate('Addresses') },
    { title: 'Wallet', icon: 'wallet-outline', onPress: () => navigation.navigate('Wallet') },
    { title: 'Subscription', icon: 'crown-outline', onPress: () => navigation.navigate('MySubscription') },
    { title: 'Notifications', icon: 'bell-outline', onPress: () => navigation.navigate('Notifications') },
    { title: 'Help & Support', icon: 'help-circle-outline', onPress: () => {} },
    { title: 'About ChooseChow', icon: 'information-outline', onPress: () => {} },
  ];
  
  return (
    <ScrollView 
      style={styles.profileContainer}
      contentContainerStyle={{ paddingBottom: insets.bottom + 100 }}
    >
      <View style={styles.profileHeader}>
        <View style={styles.avatar}>
          <Text style={styles.avatarText}>{user?.first_name?.charAt(0) || 'U'}</Text>
        </View>
        <Text style={styles.profileName}>
          {user?.first_name && user?.last_name 
            ? `${user.first_name} ${user.last_name}` 
            : user?.name || 'User'}
        </Text>
        <Text style={styles.profileEmail}>{user?.email || ''}</Text>
      </View>
      
      <View style={styles.profileMenu}>
        {menuItems.map((item, index) => (
          <TouchableOpacity 
            key={index} 
            style={[
              styles.menuItem,
              index === menuItems.length - 1 && { borderBottomWidth: 0 }
            ]} 
            onPress={item.onPress}
            activeOpacity={0.7}
          >
            <MaterialCommunityIcons name={item.icon as any} size={22} color={COLORS.text.primary} style={styles.menuIcon} />
            <Text style={styles.menuTitle}>{item.title}</Text>
            <Text style={styles.menuArrow}>›</Text>
          </TouchableOpacity>
        ))}
      </View>
      
      <TouchableOpacity 
        style={styles.logoutContainer} 
        onPress={handleLogout} 
        activeOpacity={0.7}
      >
        <Text style={styles.logoutButton}>Sign Out</Text>
      </TouchableOpacity>
    </ScrollView>
  );
};

// Helper function to calculate tab bar dimensions based on safe area insets
const getTabBarDimensions = (insets: EdgeInsets) => {
  // Use actual safe area insets for both platforms
  // This properly handles gesture navigation on Android (OneUI, etc.)
  const bottomInset = insets.bottom;
  
  // Calculate bottom padding: use safe area inset, with minimum for Android
  const bottomPadding = Platform.select({
    ios: Math.max(bottomInset, 8),
    android: Math.max(bottomInset, ANDROID_MIN_BOTTOM_PADDING),
    default: 8,
  }) || 8;
  
  // Total tab bar height = base height + bottom padding for safe area
  const tabBarHeight = BASE_TAB_BAR_HEIGHT + bottomPadding;
  
  return { bottomPadding, tabBarHeight };
};

// Tab Navigator
const TabNavigator = () => {
  const { itemCount } = require('../contexts').useCart();
  const insets = useSafeAreaInsets();
  
  // Get dynamic tab bar dimensions based on device safe areas
  const { bottomPadding, tabBarHeight } = getTabBarDimensions(insets);
  
  return (
    <Tab.Navigator
      screenOptions={{
        tabBarActiveTintColor: COLORS.primary,
        tabBarInactiveTintColor: COLORS.gray[500],
        tabBarStyle: {
          backgroundColor: COLORS.white,
          borderTopWidth: 1,
          borderTopColor: COLORS.border.light,
          // Use dynamic padding based on safe area insets
          paddingBottom: bottomPadding,
          paddingTop: 8,
          height: tabBarHeight,
          // Proper elevation for Android
          elevation: 8,
          // Shadow for iOS
          shadowColor: '#000',
          shadowOffset: { width: 0, height: -2 },
          shadowOpacity: 0.1,
          shadowRadius: 4,
        },
        tabBarLabelStyle: {
          fontSize: 11,
          fontWeight: '600',
          marginTop: 2,
          marginBottom: 0,
        },
        tabBarIconStyle: {
          marginTop: 4,
          marginBottom: 0,
        },
        tabBarItemStyle: {
          paddingTop: 4,
          paddingBottom: 0,
          justifyContent: 'center',
          alignItems: 'center',
        },
        headerShown: true,
        headerStyle: {
          backgroundColor: COLORS.white,
          elevation: 2,
          shadowColor: '#000',
          shadowOffset: { width: 0, height: 2 },
          shadowOpacity: 0.1,
          shadowRadius: 2,
        },
        headerTintColor: COLORS.text.primary,
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
          tabBarIcon: ({ focused }) => (
            <View style={[styles.tabIconContainer, focused && styles.tabIconActive]}>
              <MaterialCommunityIcons name={focused ? 'home' : 'home-outline'} size={24} color={focused ? COLORS.primary : COLORS.gray[500]} />
            </View>
          ),
          headerShown: false,
        }}
      />
      <Tab.Screen
        name="Search"
        component={SearchScreen}
        options={{
          tabBarLabel: 'Search',
          tabBarIcon: ({ focused }) => (
            <View style={[styles.tabIconContainer, focused && styles.tabIconActive]}>
              <MaterialCommunityIcons name={focused ? 'magnify' : 'magnify'} size={24} color={focused ? COLORS.primary : COLORS.gray[500]} />
            </View>
          ),
          headerTitle: 'Search Chefs',
          headerTitleStyle: { color: COLORS.primary },
        }}
      />
      <Tab.Screen
        name="Cart"
        component={CartScreen}
        options={{
          tabBarLabel: 'Cart',
          tabBarIcon: ({ focused }) => (
            <View style={[styles.tabIconContainer, focused && styles.tabIconActive]}>
              <MaterialCommunityIcons name={focused ? 'cart' : 'cart-outline'} size={24} color={focused ? COLORS.primary : COLORS.gray[500]} />
            </View>
          ),
          tabBarBadge: itemCount > 0 ? itemCount : undefined,
          tabBarBadgeStyle: { backgroundColor: COLORS.primary, fontSize: 10, minWidth: 18, height: 18 },
          headerTitle: 'My Cart',
          headerTitleStyle: { color: COLORS.primary },
        }}
      />
      <Tab.Screen
        name="Orders"
        component={OrdersScreen}
        options={{
          tabBarLabel: 'Orders',
          tabBarIcon: ({ focused }) => (
            <View style={[styles.tabIconContainer, focused && styles.tabIconActive]}>
              <MaterialCommunityIcons name={focused ? 'clipboard-text' : 'clipboard-text-outline'} size={24} color={focused ? COLORS.primary : COLORS.gray[500]} />
            </View>
          ),
          headerTitle: 'My Orders',
          headerTitleStyle: { color: COLORS.primary },
        }}
      />
      <Tab.Screen
        name="Profile"
        component={ProfileScreen}
        options={{
          tabBarLabel: 'Profile',
          tabBarIcon: ({ focused }) => (
            <View style={[styles.tabIconContainer, focused && styles.tabIconActive]}>
              <MaterialCommunityIcons name={focused ? 'account' : 'account-outline'} size={24} color={focused ? COLORS.primary : COLORS.gray[500]} />
            </View>
          ),
          headerTitle: 'Profile',
          headerTitleStyle: { color: COLORS.primary },
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
    initialRouteName="Onboarding"
  >
    <AuthStack.Screen name="Onboarding" component={OnboardingScreen} />
    <AuthStack.Screen name="Welcome" component={WelcomeScreen} />
    <AuthStack.Screen name="Login" component={LoginScreen} />
    <AuthStack.Screen name="Register" component={RegisterScreen} />
    <AuthStack.Screen name="ForgotPassword" component={ForgotPasswordScreen} />
  </AuthStack.Navigator>
);

// Main Navigator (Customer/Foodie - authenticated)
const MainNavigator = () => (
  <MainStack.Navigator
    screenOptions={{
      headerStyle: {
        backgroundColor: COLORS.white,
      },
      headerTintColor: COLORS.primary,
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
    <MainStack.Screen
      name="Payment"
      component={PaymentScreen}
      options={{ headerTitle: 'Complete Payment', headerBackVisible: false }}
    />
    <MainStack.Screen
      name="SubscriptionPlans"
      component={SubscriptionPlansScreen}
      options={{ headerTitle: 'Subscription Plans' }}
    />
    <MainStack.Screen
      name="MySubscription"
      component={MySubscriptionScreen}
      options={{ headerTitle: 'My Subscription' }}
    />
    <MainStack.Screen
      name="Wallet"
      component={WalletScreen}
      options={{ headerTitle: 'My Wallet' }}
    />
    <MainStack.Screen
      name="EditProfile"
      component={EditProfileScreen}
      options={{ headerTitle: 'Edit Profile' }}
    />
    <MainStack.Screen
      name="Addresses"
      component={AddressScreen}
      options={{ headerTitle: 'My Addresses' }}
    />
    <MainStack.Screen
      name="Notifications"
      component={NotificationsScreen}
      options={{ headerTitle: 'Notifications' }}
    />
  </MainStack.Navigator>
);

// Vendor Tab Navigator
const VendorTabNavigator = () => {
  const insets = useSafeAreaInsets();
  const { bottomPadding, tabBarHeight } = getTabBarDimensions(insets);
  
  return (
    <VendorTab.Navigator
      screenOptions={{
        tabBarActiveTintColor: COLORS.primary,
        tabBarInactiveTintColor: COLORS.gray[500],
        tabBarStyle: {
          backgroundColor: COLORS.white,
          borderTopWidth: 1,
          borderTopColor: COLORS.border.light,
          paddingBottom: bottomPadding,
          paddingTop: 8,
          height: tabBarHeight,
          elevation: 8,
          shadowColor: '#000',
          shadowOffset: { width: 0, height: -2 },
          shadowOpacity: 0.1,
          shadowRadius: 4,
        },
        tabBarLabelStyle: { fontSize: 11, fontWeight: '600', marginTop: 2 },
        headerShown: false,
      }}
    >
      <VendorTab.Screen
        name="VendorDashboard"
        component={VendorDashboardScreen}
        options={{
          tabBarLabel: 'Dashboard',
          tabBarIcon: ({ focused }) => (
            <View style={[styles.tabIconContainer, focused && styles.tabIconActive]}>
              <MaterialCommunityIcons name={focused ? 'view-dashboard' : 'view-dashboard-outline'} size={24} color={focused ? COLORS.primary : COLORS.gray[500]} />
            </View>
          ),
        }}
      />
      <VendorTab.Screen
        name="VendorOrders"
        component={VendorOrdersScreen}
        options={{
          tabBarLabel: 'Orders',
          tabBarIcon: ({ focused }) => (
            <View style={[styles.tabIconContainer, focused && styles.tabIconActive]}>
              <MaterialCommunityIcons name={focused ? 'package-variant-closed' : 'package-variant-closed'} size={24} color={focused ? COLORS.primary : COLORS.gray[500]} />
            </View>
          ),
        }}
      />
      <VendorTab.Screen
        name="VendorMenu"
        component={VendorMenuScreen}
        options={{
          tabBarLabel: 'Menu',
          tabBarIcon: ({ focused }) => (
            <View style={[styles.tabIconContainer, focused && styles.tabIconActive]}>
              <MaterialCommunityIcons name={focused ? 'silverware-fork-knife' : 'silverware-fork-knife'} size={24} color={focused ? COLORS.primary : COLORS.gray[500]} />
            </View>
          ),
        }}
      />
      <VendorTab.Screen
        name="VendorProfile"
        component={VendorProfileScreen}
        options={{
          tabBarLabel: 'Profile',
          tabBarIcon: ({ focused }) => (
            <View style={[styles.tabIconContainer, focused && styles.tabIconActive]}>
              <MaterialCommunityIcons name={focused ? 'account' : 'account-outline'} size={24} color={focused ? COLORS.primary : COLORS.gray[500]} />
            </View>
          ),
        }}
      />
    </VendorTab.Navigator>
  );
};

// Vendor Navigator
const VendorNavigator = () => (
  <VendorStack.Navigator screenOptions={{ headerShown: false }}>
    <VendorStack.Screen name="VendorTabs" component={VendorTabNavigator} />
    <VendorStack.Screen name="VendorEarnings" component={VendorEarningsScreen} />
    <VendorStack.Screen name="VendorOrderDetail" component={VendorOrderDetailScreen} />
    <VendorStack.Screen name="VendorSubscribers" component={VendorSubscribersScreen} />
  </VendorStack.Navigator>
);

// Admin Profile/Settings Placeholder
const AdminSettingsScreen = ({ navigation }: any) => {
  const { user, logout } = useAuth();
  const insets = useSafeAreaInsets();
  
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
          }
        },
      ]
    );
  };
  
  return (
    <ScrollView style={[styles.profileContainer, { backgroundColor: '#1F2937' }]} contentContainerStyle={{ paddingBottom: insets.bottom + 100 }}>
      <View style={[styles.profileHeader, { backgroundColor: '#374151', borderBottomColor: '#4B5563' }]}>
        <View style={[styles.avatar, { backgroundColor: '#10B981' }]}>
          <Text style={styles.avatarText}>{user?.first_name?.charAt(0) || 'A'}</Text>
        </View>
        <Text style={[styles.profileName, { color: '#FFFFFF' }]}>
          {user?.first_name && user?.last_name 
            ? `${user.first_name} ${user.last_name}` 
            : user?.name || 'Admin'}
        </Text>
        <Text style={[styles.profileEmail, { color: '#9CA3AF' }]}>{user?.email || ''}</Text>
        <View style={[styles.roleBadge, { backgroundColor: '#10B981' }]}>
          <Text style={styles.roleBadgeText}>👑 Administrator</Text>
        </View>
      </View>
      
      <TouchableOpacity style={[styles.logoutContainer, { backgroundColor: '#374151' }]} onPress={handleLogout} activeOpacity={0.7}>
        <Text style={[styles.logoutButton, { color: '#EF4444' }]}>Sign Out</Text>
      </TouchableOpacity>
    </ScrollView>
  );
};

// Admin Tab Navigator
const AdminTabNavigator = () => {
  const insets = useSafeAreaInsets();
  const { bottomPadding, tabBarHeight } = getTabBarDimensions(insets);
  
  return (
    <AdminTab.Navigator
      screenOptions={{
        tabBarActiveTintColor: '#10B981',
        tabBarInactiveTintColor: '#6B7280',
        tabBarStyle: {
          backgroundColor: '#1F2937',
          borderTopWidth: 1,
          borderTopColor: '#374151',
          paddingBottom: bottomPadding,
          paddingTop: 8,
          height: tabBarHeight,
          elevation: 8,
        },
        tabBarLabelStyle: { fontSize: 11, fontWeight: '600', marginTop: 2 },
        headerShown: false,
      }}
    >
      <AdminTab.Screen
        name="AdminDashboard"
        component={AdminDashboardScreen}
        options={{
          tabBarLabel: 'Dashboard',
          tabBarIcon: ({ focused }) => (
            <View style={[styles.tabIconContainer, focused && styles.tabIconActive]}>
              <MaterialCommunityIcons name={focused ? 'view-dashboard' : 'view-dashboard-outline'} size={24} color={focused ? COLORS.primary : COLORS.gray[500]} />
            </View>
          ),
        }}
      />
      <AdminTab.Screen
        name="AdminUsers"
        component={AdminUsersScreen}
        options={{
          tabBarLabel: 'Users',
          tabBarIcon: ({ focused }) => (
            <View style={[styles.tabIconContainer, focused && styles.tabIconActive]}>
              <MaterialCommunityIcons name={focused ? 'account-group' : 'account-group-outline'} size={24} color={focused ? COLORS.primary : COLORS.gray[500]} />
            </View>
          ),
        }}
      />
      <AdminTab.Screen
        name="AdminVendors"
        component={AdminVendorsScreen}
        options={{
          tabBarLabel: 'Vendors',
          tabBarIcon: ({ focused }) => (
            <View style={[styles.tabIconContainer, focused && styles.tabIconActive]}>
              <MaterialCommunityIcons name={focused ? 'store' : 'store-outline'} size={24} color={focused ? COLORS.primary : COLORS.gray[500]} />
            </View>
          ),
        }}
      />
      <AdminTab.Screen
        name="AdminSettings"
        component={AdminSettingsScreen}
        options={{
          tabBarLabel: 'Settings',
          tabBarIcon: ({ focused }) => (
            <View style={[styles.tabIconContainer, focused && styles.tabIconActive]}>
              <MaterialCommunityIcons name={focused ? 'cog' : 'cog-outline'} size={24} color={focused ? COLORS.primary : COLORS.gray[500]} />
            </View>
          ),
        }}
      />
    </AdminTab.Navigator>
  );
};

// Admin Navigator
const AdminNavigator = () => (
  <AdminStack.Navigator screenOptions={{ headerShown: false }}>
    <AdminStack.Screen name="AdminTabs" component={AdminTabNavigator} />
    <AdminStack.Screen name="AdminOrders" component={AdminOrdersScreen} />
    <AdminStack.Screen name="AdminPayouts" component={AdminPayoutsScreen} />
    <AdminStack.Screen name="AdminReports" component={AdminReportsScreen} />
  </AdminStack.Navigator>
);

// Helper function to get the right navigator based on user role
const getNavigatorForRole = (role: string | undefined) => {
  switch (role) {
    case 'chef':
      return <VendorNavigator />;
    case 'admin':
      return <AdminNavigator />;
    case 'customer':
    default:
      return <MainNavigator />;
  }
};

// Root Navigator
export const AppNavigator: React.FC = () => {
  const { isAuthenticated, isLoading, user } = useAuth();

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.loadingText}>Loading...</Text>
      </View>
    );
  }

  return (
    <NavigationContainer>
      {isAuthenticated ? getNavigatorForRole(user?.role) : <AuthNavigator />}
    </NavigationContainer>
  );
};

const styles = StyleSheet.create({
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: COLORS.white,
  },
  loadingText: {
    marginTop: 16,
    fontSize: 16,
    color: COLORS.text.secondary,
  },
  placeholder: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: COLORS.background.secondary,
    padding: 24,
  },
  placeholderText: {
    fontSize: 64,
    marginBottom: 16,
  },
  placeholderTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: COLORS.text.primary,
    marginBottom: 8,
  },
  placeholderSubtitle: {
    fontSize: 16,
    color: COLORS.text.secondary,
    textAlign: 'center',
    marginBottom: 24,
  },
  actionButton: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: 32,
    paddingVertical: 14,
    borderRadius: 12,
  },
  actionButtonText: {
    color: COLORS.white,
    fontSize: 16,
    fontWeight: 'bold',
  },
  profileContainer: {
    flex: 1,
    backgroundColor: COLORS.background.secondary,
  },
  profileHeader: {
    backgroundColor: COLORS.white,
    alignItems: 'center',
    paddingVertical: 32,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.border.light,
  },
  avatar: {
    width: 90,
    height: 90,
    borderRadius: 45,
    backgroundColor: COLORS.primary,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
    borderWidth: 3,
    borderColor: COLORS.primaryLight,
  },
  avatarText: {
    fontSize: 36,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  profileName: {
    fontSize: 22,
    fontWeight: 'bold',
    color: COLORS.text.primary,
  },
  profileEmail: {
    fontSize: 14,
    color: COLORS.text.secondary,
    marginTop: 4,
  },
  profileMenu: {
    backgroundColor: COLORS.white,
    marginTop: 16,
    borderRadius: 12,
    marginHorizontal: 16,
    overflow: 'hidden',
  },
  menuItem: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.border.light,
    backgroundColor: COLORS.white,
  },
  menuIcon: {
    marginRight: 16,
    width: 24,
    textAlign: 'center',
  },
  menuTitle: {
    flex: 1,
    fontSize: 16,
    color: COLORS.text.primary,
  },
  menuArrow: {
    fontSize: 22,
    color: COLORS.gray[400],
  },
  logoutContainer: {
    marginTop: 24,
    marginHorizontal: 16,
    backgroundColor: COLORS.primaryFaded,
    padding: 16,
    borderRadius: 12,
    alignItems: 'center',
  },
  logoutButton: {
    fontSize: 16,
    color: COLORS.primary,
    fontWeight: '700',
  },
  tabIconContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    width: 28,
    height: 28,
  },
  tabIconActive: {
    transform: [{ scale: 1.05 }],
  },
  roleBadge: {
    marginTop: 12,
    backgroundColor: COLORS.primary,
    paddingHorizontal: 16,
    paddingVertical: 6,
    borderRadius: 16,
  },
  roleBadgeText: {
    color: '#FFFFFF',
    fontSize: 12,
    fontWeight: '600',
  },
});

export default AppNavigator;
