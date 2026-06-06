import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  Image,
  TouchableOpacity,
  ActivityIndicator,
  FlatList,
  Alert,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RouteProp } from '@react-navigation/native';
import { chefService, subscriptionService } from '../../api';
import { Chef, MenuItem } from '../../types';
import { useCart, useAuth } from '../../contexts';
import { COLORS } from '../../utils/theme';
import { CartToast } from '../../components/CartToast';

type ChefDetailScreenProps = {
  navigation: NativeStackNavigationProp<any>;
  route: RouteProp<{ ChefDetail: { chefId: number } }, 'ChefDetail'>;
};

const MenuItemCard: React.FC<{
  item: MenuItem;
  onAddToCart: () => void;
}> = ({ item, onAddToCart }) => (
  <View style={styles.menuItem}>
    <View style={styles.menuImageContainer}>
      {item.image_url ? (
        <Image source={{ uri: item.image_url }} style={styles.menuImage} />
      ) : (
        <View style={styles.menuImagePlaceholder}>
          <Text style={styles.menuImageText}>{item.name.charAt(0)}</Text>
        </View>
      )}
    </View>
    <View style={styles.menuDetails}>
      <Text style={styles.menuName}>{item.name}</Text>
      {item.description && (
        <Text style={styles.menuDescription} numberOfLines={2}>{item.description}</Text>
      )}
      <View style={styles.menuFooter}>
        <Text style={styles.menuPrice}>₦{item.price.toLocaleString()}</Text>
        <TouchableOpacity
          style={[styles.addButton, !item.is_available && styles.addButtonDisabled]}
          onPress={onAddToCart}
          disabled={!item.is_available}
        >
          <Text style={styles.addButtonText}>
            {item.is_available ? '+ Add' : 'Unavailable'}
          </Text>
        </TouchableOpacity>
      </View>
    </View>
  </View>
);

export const ChefDetailScreen: React.FC<ChefDetailScreenProps> = ({ navigation, route }) => {
  const { chefId } = route.params;
  const [chef, setChef] = useState<Chef | null>(null);
  const [menus, setMenus] = useState<MenuItem[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [isSubscribed, setIsSubscribed] = useState(false);
  const [isSubscribing, setIsSubscribing] = useState(false);
  const { addToCart, clearCart, cart } = useCart();
  const { isAuthenticated } = useAuth();
  const [toastItem, setToastItem] = useState<{ visible: boolean; name: string }>({ visible: false, name: '' });

  useEffect(() => {
    loadChefData();
  }, [chefId]);

  const loadChefData = async () => {
    try {
      const [chefData, menuData] = await Promise.all([
        chefService.getChef(chefId),
        chefService.getChefMenus(chefId),
      ]);
      setChef(chefData);
      setMenus(menuData);
      
      // Check subscription status if authenticated
      if (isAuthenticated) {
        try {
          const subStatus = await subscriptionService.isSubscribed(chefId);
          setIsSubscribed(subStatus.subscribed);
        } catch {
          // Subscription check failed, ignore
        }
      }
    } catch (error) {
      console.error('Failed to load chef data:', error);
      Alert.alert('Error', 'Failed to load chef details');
    } finally {
      setIsLoading(false);
    }
  };

  const handleSubscriptionToggle = async () => {
    if (!isAuthenticated) {
      Alert.alert('Sign In Required', 'Please sign in to subscribe to this chef.');
      return;
    }

    setIsSubscribing(true);
    try {
      if (isSubscribed) {
        await subscriptionService.unsubscribeFromChef(chefId);
        setIsSubscribed(false);
        Alert.alert('Unsubscribed', `You will no longer receive updates from ${chef?.business_name}`);
      } else {
        await subscriptionService.subscribeToChef(chefId);
        setIsSubscribed(true);
        Alert.alert('Subscribed!', `You will now receive updates when ${chef?.business_name} adds new menu items!`);
      }
    } catch (error: any) {
      // Demo mode fallback
      if (error.response?.status === 404) {
        setIsSubscribed(!isSubscribed);
      } else {
        Alert.alert('Error', 'Failed to update subscription');
      }
    } finally {
      setIsSubscribing(false);
    }
  };

  const handleAddToCart = async (menuItem: MenuItem) => {
    if (cart && cart.chef_id && cart.chef_id !== chefId) {
      Alert.alert(
        'Different Chef',
        'Your cart contains items from another chef. Would you like to clear it and add this item?',
        [
          { text: 'Cancel', style: 'cancel' },
          {
            text: 'Clear & Add',
            onPress: async () => {
              try {
                await clearCart();
                await addToCart(menuItem.id, 1);
                setToastItem({ visible: true, name: menuItem.name });
              } catch {
                Alert.alert('Error', 'Failed to add item to cart');
              }
            },
          },
        ]
      );
      return;
    }

    try {
      await addToCart(menuItem.id, 1);
      setToastItem({ visible: true, name: menuItem.name });
    } catch {
      Alert.alert('Error', 'Failed to add item to cart');
    }
  };

  const handleViewCart = () => {
    setToastItem({ visible: false, name: '' });
    navigation.navigate('MainTabs', { screen: 'Cart' });
  };

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={COLORS.primary} />
      </View>
    );
  }

  if (!chef) {
    return (
      <View style={styles.errorContainer}>
        <Text style={styles.errorText}>Chef not found</Text>
      </View>
    );
  }

  return (
    <View style={{ flex: 1 }}>
    <ScrollView style={styles.container}>
      {/* Header Image */}
      <View style={styles.headerImage}>
        {chef.banner_url ? (
          <Image source={{ uri: chef.banner_url }} style={styles.bannerImage} />
        ) : (
          <View style={styles.bannerPlaceholder}>
            <Text style={styles.bannerText}>{chef.business_name.charAt(0)}</Text>
          </View>
        )}
      </View>

      {/* Chef Info */}
      <View style={styles.chefInfo}>
        <View style={styles.logoContainer}>
          {chef.logo_url ? (
            <Image source={{ uri: chef.logo_url }} style={styles.logo} />
          ) : (
            <View style={styles.logoPlaceholder}>
              <Text style={styles.logoText}>{chef.business_name.charAt(0)}</Text>
            </View>
          )}
        </View>

        <Text style={styles.chefName}>{chef.business_name}</Text>
        {chef.specialty && <Text style={styles.specialty}>{chef.specialty}</Text>}

        <View style={styles.statsRow}>
          <View style={styles.stat}>
            <Text style={styles.statValue}>★ {chef.rating.toFixed(1)}</Text>
            <Text style={styles.statLabel}>{chef.total_reviews} reviews</Text>
          </View>
          <View style={styles.statDivider} />
          <View style={styles.stat}>
            <Text style={styles.statValue}>{chef.delivery_time || '30-45 min'}</Text>
            <Text style={styles.statLabel}>Delivery</Text>
          </View>
          <View style={styles.statDivider} />
          <View style={styles.stat}>
            <Text style={styles.statValue}>₦{(chef.delivery_fee || 500).toLocaleString()}</Text>
            <Text style={styles.statLabel}>Delivery Fee</Text>
          </View>
        </View>

        {chef.description && (
          <Text style={styles.description}>{chef.description}</Text>
        )}

        {/* Subscribe Button */}
        <TouchableOpacity
          style={[
            styles.subscribeButton,
            isSubscribed && styles.subscribedButton
          ]}
          onPress={handleSubscriptionToggle}
          disabled={isSubscribing}
        >
          {isSubscribing ? (
            <ActivityIndicator size="small" color={isSubscribed ? COLORS.primary : '#FFFFFF'} />
          ) : (
            <>
              <Text style={styles.subscribeIcon}>{isSubscribed ? '🔔' : '🔕'}</Text>
              <Text style={[
                styles.subscribeText,
                isSubscribed && styles.subscribedText
              ]}>
                {isSubscribed ? 'Subscribed' : 'Subscribe for Updates'}
              </Text>
            </>
          )}
        </TouchableOpacity>

        {!chef.is_available && (
          <View style={styles.unavailableBanner}>
            <Text style={styles.unavailableText}>Currently Unavailable</Text>
          </View>
        )}
      </View>

      {/* Menu Section */}
      <View style={styles.menuSection}>
        <Text style={styles.sectionTitle}>Menu</Text>
        {menus.length === 0 ? (
          <Text style={styles.noMenuText}>No menu items available</Text>
        ) : (
          menus.map((item) => (
            <MenuItemCard
              key={item.id}
              item={item}
              onAddToCart={() => handleAddToCart(item)}
            />
          ))
        )}
      </View>

      <View style={styles.bottomPadding} />
    </ScrollView>
      <CartToast
        visible={toastItem.visible}
        itemName={toastItem.name}
        onViewCart={handleViewCart}
        onDismiss={() => setToastItem({ visible: false, name: '' })}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  errorText: {
    fontSize: 16,
    color: '#6B7280',
  },
  headerImage: {
    height: 200,
  },
  bannerImage: {
    width: '100%',
    height: '100%',
  },
  bannerPlaceholder: {
    width: '100%',
    height: '100%',
    backgroundColor: COLORS.primary,
    justifyContent: 'center',
    alignItems: 'center',
  },
  bannerText: {
    fontSize: 64,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  chefInfo: {
    backgroundColor: '#FFFFFF',
    padding: 20,
    marginTop: -40,
    marginHorizontal: 16,
    borderRadius: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  logoContainer: {
    alignSelf: 'center',
    marginTop: -50,
    marginBottom: 12,
  },
  logo: {
    width: 80,
    height: 80,
    borderRadius: 40,
    borderWidth: 3,
    borderColor: '#FFFFFF',
  },
  logoPlaceholder: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: COLORS.primary,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 3,
    borderColor: '#FFFFFF',
  },
  logoText: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  chefName: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#1F2937',
    textAlign: 'center',
  },
  specialty: {
    fontSize: 14,
    color: '#6B7280',
    textAlign: 'center',
    marginTop: 4,
  },
  statsRow: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 16,
    paddingTop: 16,
    borderTopWidth: 1,
    borderTopColor: '#E5E7EB',
  },
  stat: {
    alignItems: 'center',
    paddingHorizontal: 16,
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
    height: 30,
    backgroundColor: '#E5E7EB',
  },
  description: {
    fontSize: 14,
    color: '#6B7280',
    marginTop: 16,
    lineHeight: 22,
  },
  subscribeButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: COLORS.primary,
    paddingVertical: 14,
    paddingHorizontal: 24,
    borderRadius: 12,
    marginTop: 16,
    gap: 8,
  },
  subscribedButton: {
    backgroundColor: '#D1FAE5',
    borderWidth: 1,
    borderColor: '#10B981',
  },
  subscribeIcon: {
    fontSize: 18,
  },
  subscribeText: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: '600',
  },
  subscribedText: {
    color: '#10B981',
  },
  unavailableBanner: {
    backgroundColor: '#FEE2E2',
    padding: 12,
    borderRadius: 8,
    marginTop: 16,
  },
  unavailableText: {
    color: '#DC2626',
    textAlign: 'center',
    fontWeight: '600',
  },
  menuSection: {
    padding: 16,
  },
  sectionTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#1F2937',
    marginBottom: 16,
  },
  noMenuText: {
    fontSize: 14,
    color: '#6B7280',
    textAlign: 'center',
    padding: 20,
  },
  menuItem: {
    flexDirection: 'row',
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 12,
    marginBottom: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 2,
    elevation: 1,
  },
  menuImageContainer: {
    width: 80,
    height: 80,
    borderRadius: 8,
    overflow: 'hidden',
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
  menuImageText: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#9CA3AF',
  },
  menuDetails: {
    flex: 1,
    marginLeft: 12,
  },
  menuName: {
    fontSize: 16,
    fontWeight: '600',
    color: '#1F2937',
  },
  menuDescription: {
    fontSize: 13,
    color: '#6B7280',
    marginTop: 4,
  },
  menuFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 8,
  },
  menuPrice: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  addButton: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 8,
  },
  addButtonDisabled: {
    backgroundColor: '#D1D5DB',
  },
  addButtonText: {
    color: '#FFFFFF',
    fontWeight: '600',
    fontSize: 14,
  },
  bottomPadding: {
    height: 40,
  },
});

export default ChefDetailScreen;
