import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  RefreshControl,
  Image,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useAuth, useCart } from '../../contexts';
import { COLORS } from '../../utils/theme';
import { scaleWidth, screenWidth } from '../../utils/dimensions';
import { ChooseChowLogo } from '../../assets';
import { MaterialCommunityIcons } from '@expo/vector-icons';

// Card dimensions based on design (375px width, 2 columns with 16px padding each side and 12px gap)
const CARD_WIDTH = (screenWidth - scaleWidth(40)) / 2;

type HomeScreenProps = {
  navigation: NativeStackNavigationProp<any>;
};

// Category data with icons
const CATEGORIES = [
  { id: 'popular', name: 'Popular', icon: '🔥', isSelected: true },
  { id: 'western', name: 'Western', icon: '🍔' },
  { id: 'local', name: 'Local', icon: '🍲' },
  { id: 'grilled', name: 'Grilled', icon: '🍖' },
  { id: 'drinks', name: 'Drinks', icon: '🥤' },
  { id: 'dessert', name: 'Dessert', icon: '🍰' },
];

// Sample food data (will be replaced with API data)
const SAMPLE_FOODS = [
  { id: 1, name: 'Prime Pancake with egg yoke sauce', category: 'Asian', rating: 3.9, image: null, isFavorite: false },
  { id: 2, name: 'Prime Pancake with egg yoke sauce', category: 'Asian', rating: 3.9, image: null, isFavorite: true },
  { id: 3, name: 'Grilled Chicken Salad', category: 'Western', rating: 4.2, image: null, isFavorite: false },
  { id: 4, name: 'Jollof Rice Special', category: 'Local', rating: 4.5, image: null, isFavorite: true },
];

export const HomeScreen: React.FC<HomeScreenProps> = ({ navigation }) => {
  const { user } = useAuth();
  const { itemCount } = useCart();
  const insets = useSafeAreaInsets();
  const [selectedCategory, setSelectedCategory] = useState('popular');
  const [foods, setFoods] = useState(SAMPLE_FOODS);
  const [isLoading, setIsLoading] = useState(false);
  const [refreshing, setRefreshing] = useState(false);

  const loadData = async () => {
    // TODO: Replace with actual API call
    setIsLoading(false);
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await loadData();
    setRefreshing(false);
  };

  const handleSearch = () => {
    navigation.navigate('Search');
  };

  const toggleFavorite = (id: number) => {
    setFoods(foods.map(food => 
      food.id === id ? { ...food, isFavorite: !food.isFavorite } : food
    ));
  };

  const firstName = user?.name?.split(' ')[0] || 'Foodie';

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={COLORS.primary} />
      </View>
    );
  }

  return (
    <View style={[styles.container, { paddingTop: insets.top }]}>
      {/* Custom Header */}
      <View style={styles.header}>
        <View style={styles.logoContainer}>
          <Image 
            source={ChooseChowLogo} 
            style={styles.headerLogo}
            resizeMode="contain"
          />
          <Text style={styles.logoText}>ChooseChow</Text>
        </View>
        <View style={styles.headerIcons}>
          <TouchableOpacity style={styles.headerIconButton}>
            <MaterialCommunityIcons name="bell-outline" size={24} color="#4B5563" />
            <View style={styles.notificationBadge} />
          </TouchableOpacity>
          <TouchableOpacity 
            style={styles.headerIconButton}
            onPress={() => navigation.navigate('Cart')}
          >
            <MaterialCommunityIcons name="cart-outline" size={24} color="#4B5563" />
            {itemCount > 0 && (
              <View style={styles.cartBadge}>
                <Text style={styles.cartBadgeText}>{itemCount}</Text>
              </View>
            )}
          </TouchableOpacity>
        </View>
      </View>

      <ScrollView
        style={styles.scrollContainer}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />
        }
      >
        {/* Greeting */}
        <View style={styles.greetingContainer}>
          <Text style={styles.greeting}>Hey {firstName} 👋</Text>
        </View>

        {/* Headline */}
        <View style={styles.headlineContainer}>
          <Text style={styles.headline}>
            Find Your <Text style={styles.headlineAccent}>Chow...</Text>
          </Text>
        </View>

        {/* Search Bar */}
        <TouchableOpacity style={styles.searchContainer} onPress={handleSearch} activeOpacity={0.8}>
          <MaterialCommunityIcons name="magnify" size={20} color="#9CA3AF" />
          <Text style={styles.searchPlaceholder}>Search menus, chefs...</Text>
          <View style={styles.filterButton}>
            <MaterialCommunityIcons name="tune-vertical" size={20} color="#6B7280" />
          </View>
        </TouchableOpacity>

        {/* Categories */}
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>Categories</Text>
          <TouchableOpacity onPress={() => navigation.navigate('ChefList')}>
            <Text style={styles.seeAll}>See All</Text>
          </TouchableOpacity>
        </View>
        <ScrollView 
          horizontal 
          showsHorizontalScrollIndicator={false} 
          style={styles.categoriesContainer}
          contentContainerStyle={styles.categoriesContent}
        >
          {CATEGORIES.map((category) => (
            <TouchableOpacity
              key={category.id}
              style={[
                styles.categoryItem,
                selectedCategory === category.id && styles.categoryItemSelected,
              ]}
              onPress={() => setSelectedCategory(category.id)}
            >
              <View style={[
                styles.categoryIconContainer,
                selectedCategory === category.id && styles.categoryIconContainerSelected,
              ]}>
                <Text style={styles.categoryIcon}>{category.icon}</Text>
              </View>
              <Text style={[
                styles.categoryName,
                selectedCategory === category.id && styles.categoryNameSelected,
              ]}>
                {category.name}
              </Text>
            </TouchableOpacity>
          ))}
        </ScrollView>

        {/* Recommended Section */}
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>Recommended</Text>
          <TouchableOpacity onPress={() => navigation.navigate('ChefList')}>
            <Text style={styles.seeAll}>See All</Text>
          </TouchableOpacity>
        </View>

        {/* Food Grid */}
        <View style={styles.foodGrid}>
          {foods.map((food) => (
            <TouchableOpacity
              key={food.id}
              style={styles.foodCard}
              onPress={() => navigation.navigate('ChefDetail', { chefId: food.id })}
              activeOpacity={0.9}
            >
              {/* Food Image */}
              <View style={styles.foodImageContainer}>
                <View style={styles.foodImagePlaceholder}>
                  <MaterialCommunityIcons name="food" size={40} color="#D1D5DB" />
                </View>
                {/* Rating Badge */}
                <View style={styles.ratingBadge}>
                  <Text style={styles.ratingIcon}>⭐</Text>
                  <Text style={styles.ratingText}>{food.rating}</Text>
                </View>
                {/* Favorite Button */}
                <TouchableOpacity 
                  style={styles.favoriteButton}
                  onPress={() => toggleFavorite(food.id)}
                >
                  <MaterialCommunityIcons
                    name={food.isFavorite ? 'heart' : 'heart-outline'}
                    size={18}
                    color={food.isFavorite ? '#EF4444' : '#6B7280'}
                  />
                </TouchableOpacity>
              </View>
              {/* Food Info */}
              <View style={styles.foodInfo}>
                <Text style={styles.foodName} numberOfLines={2}>{food.name}</Text>
                <View style={styles.foodFooter}>
                  <Text style={styles.foodCategory}>{food.category}</Text>
                </View>
              </View>
            </TouchableOpacity>
          ))}
        </View>

        <View style={styles.bottomPadding} />
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FFFFFF',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
  },
  scrollContainer: {
    flex: 1,
  },
  // Header
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 12,
  },
  logoContainer: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  headerLogo: {
    width: 32,
    height: 32,
    marginRight: 8,
  },
  logoText: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  headerIcons: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 16,
  },
  headerIconButton: {
    position: 'relative',
    padding: 4,
  },
  notificationBadge: {
    position: 'absolute',
    top: 2,
    right: 2,
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: COLORS.primary,
  },
  cartBadge: {
    position: 'absolute',
    top: -4,
    right: -4,
    minWidth: 18,
    height: 18,
    borderRadius: 9,
    backgroundColor: COLORS.primary,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 4,
  },
  cartBadgeText: {
    fontSize: 10,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  // Greeting
  greetingContainer: {
    paddingHorizontal: 16,
    paddingTop: 4,
  },
  greeting: {
    fontSize: 15,
    color: '#6B7280',
    fontWeight: '500',
  },
  // Headline
  headlineContainer: {
    paddingHorizontal: 16,
    paddingTop: 4,
    paddingBottom: 20,
  },
  headline: {
    fontSize: 30,
    fontWeight: 'bold',
    color: '#1F2937',
    lineHeight: 38,
  },
  headlineAccent: {
    color: COLORS.primary,
    fontSize: 34,
  },
  // Search
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginHorizontal: 16,
    paddingHorizontal: 16,
    paddingVertical: 12,
    backgroundColor: '#F3F4F6',
    borderRadius: 14,
    marginBottom: 24,
    gap: 10,
  },
  searchPlaceholder: {
    flex: 1,
    fontSize: 15,
    color: '#9CA3AF',
  },
  filterButton: {
    width: 32,
    height: 32,
    borderRadius: 8,
    backgroundColor: '#FFFFFF',
    justifyContent: 'center',
    alignItems: 'center',
  },
  // Section Headers
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 16,
    marginBottom: 14,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  seeAll: {
    fontSize: 14,
    color: COLORS.primary,
    fontWeight: '600',
  },
  // Categories
  categoriesContainer: {
    marginBottom: 24,
  },
  categoriesContent: {
    paddingHorizontal: 16,
    gap: 16,
  },
  categoryItem: {
    alignItems: 'center',
    marginRight: 16,
  },
  categoryItemSelected: {},
  categoryIconContainer: {
    width: 56,
    height: 56,
    borderRadius: 28,
    backgroundColor: '#F3F4F6',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 8,
  },
  categoryIconContainerSelected: {
    backgroundColor: COLORS.primaryFaded,
    borderWidth: 2,
    borderColor: COLORS.primary,
  },
  categoryIcon: {
    fontSize: 24,
  },
  categoryName: {
    fontSize: 12,
    color: '#6B7280',
    fontWeight: '500',
  },
  categoryNameSelected: {
    color: COLORS.primary,
    fontWeight: '600',
  },
  // Food Grid
  foodGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    paddingHorizontal: 12,
    gap: 12,
  },
  foodCard: {
    width: CARD_WIDTH,
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 8,
    elevation: 3,
    marginBottom: 4,
  },
  foodImageContainer: {
    width: '100%',
    height: 140,
    position: 'relative',
  },
  foodImagePlaceholder: {
    width: '100%',
    height: '100%',
    backgroundColor: '#F3F4F6',
    justifyContent: 'center',
    alignItems: 'center',
  },
  ratingBadge: {
    position: 'absolute',
    top: 10,
    left: 10,
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.primary,
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
    gap: 4,
  },
  ratingIcon: {
    fontSize: 10,
  },
  ratingText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  favoriteButton: {
    position: 'absolute',
    top: 10,
    right: 10,
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: 'rgba(255, 255, 255, 0.95)',
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 2,
  },
  foodInfo: {
    padding: 12,
  },
  foodName: {
    fontSize: 14,
    fontWeight: '600',
    color: '#1F2937',
    marginBottom: 6,
    lineHeight: 20,
  },
  foodFooter: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  foodCategory: {
    fontSize: 12,
    color: '#9CA3AF',
  },
  bottomPadding: {
    height: 100,
  },
});

export default HomeScreen;
