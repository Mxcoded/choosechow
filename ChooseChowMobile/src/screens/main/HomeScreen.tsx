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
import { scaleWidth, scaleFont, screenWidth } from '../../utils/dimensions';
import { ChooseChowLogo } from '../../assets';

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
  const [searchQuery, setSearchQuery] = useState('');
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

  const toggleBookmark = (id: number) => {
    // TODO: Implement bookmark functionality
  };

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
            <Text style={styles.headerIcon}>🔔</Text>
            <View style={styles.notificationBadge} />
          </TouchableOpacity>
          <TouchableOpacity 
            style={styles.headerIconButton}
            onPress={() => navigation.navigate('Cart')}
          >
            <Text style={styles.headerIcon}>🛒</Text>
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
        {/* Headline */}
        <View style={styles.headlineContainer}>
          <Text style={styles.headline}>
            Find what your{"\n"}cooking, <Text style={styles.headlineAccent}>today</Text>
          </Text>
        </View>

        {/* Search Bar */}
        <TouchableOpacity style={styles.searchContainer} onPress={handleSearch} activeOpacity={0.8}>
          <Text style={styles.searchIcon}>🔍</Text>
          <Text style={styles.searchPlaceholder}>Find Chow...</Text>
          <TouchableOpacity style={styles.filterButton}>
            <Text style={styles.filterIcon}>⚙️</Text>
          </TouchableOpacity>
        </TouchableOpacity>

        {/* Categories */}
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
                  <Text style={styles.foodImageEmoji}>🍽️</Text>
                </View>
                {/* Rating Badge */}
                <View style={styles.ratingBadge}>
                  <Text style={styles.ratingIcon}>⭐</Text>
                  <Text style={styles.ratingText}>{food.rating}</Text>
                </View>
                {/* Bookmark Button */}
                <TouchableOpacity 
                  style={styles.bookmarkButton}
                  onPress={() => toggleBookmark(food.id)}
                >
                  <Text style={styles.bookmarkIcon}>🔖</Text>
                </TouchableOpacity>
              </View>
              {/* Food Info */}
              <View style={styles.foodInfo}>
                <Text style={styles.foodName} numberOfLines={2}>{food.name}</Text>
                <View style={styles.foodFooter}>
                  <Text style={styles.foodCategory}>{food.category}</Text>
                  <TouchableOpacity onPress={() => toggleFavorite(food.id)}>
                    <Text style={styles.favoriteIcon}>
                      {food.isFavorite ? '❤️' : '🤍'}
                    </Text>
                  </TouchableOpacity>
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
  headerIcon: {
    fontSize: 22,
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
    top: -2,
    right: -2,
    minWidth: 16,
    height: 16,
    borderRadius: 8,
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
  // Headline
  headlineContainer: {
    paddingHorizontal: 16,
    paddingTop: 8,
    paddingBottom: 16,
  },
  headline: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#1F2937',
    lineHeight: 36,
  },
  headlineAccent: {
    color: COLORS.primary,
  },
  // Search
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginHorizontal: 16,
    paddingHorizontal: 16,
    paddingVertical: 14,
    backgroundColor: '#F3F4F6',
    borderRadius: 12,
    marginBottom: 20,
  },
  searchIcon: {
    fontSize: 18,
    marginRight: 12,
  },
  searchPlaceholder: {
    flex: 1,
    fontSize: 16,
    color: '#9CA3AF',
  },
  filterButton: {
    padding: 4,
  },
  filterIcon: {
    fontSize: 18,
  },
  // Categories
  categoriesContainer: {
    marginBottom: 20,
  },
  categoriesContent: {
    paddingHorizontal: 16,
    gap: 16,
  },
  categoryItem: {
    alignItems: 'center',
    marginRight: 20,
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
    backgroundColor: '#FEE2E2',
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
  foodImageEmoji: {
    fontSize: 48,
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
  bookmarkButton: {
    position: 'absolute',
    bottom: 10,
    right: 10,
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: 'rgba(255, 255, 255, 0.9)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  bookmarkIcon: {
    fontSize: 16,
  },
  foodInfo: {
    padding: 12,
  },
  foodName: {
    fontSize: 14,
    fontWeight: '600',
    color: '#1F2937',
    marginBottom: 8,
    lineHeight: 20,
  },
  foodFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  foodCategory: {
    fontSize: 12,
    color: '#9CA3AF',
  },
  favoriteIcon: {
    fontSize: 18,
  },
  bottomPadding: {
    height: 100,
  },
});

export default HomeScreen;
