import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TextInput,
  TouchableOpacity,
  ActivityIndicator,
  RefreshControl,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RouteProp } from '@react-navigation/native';
import { chefService } from '../../api';
import { Chef } from '../../types';
import ChefCard from '../../components/ChefCard';
import { COLORS } from '../../utils/theme';

type ChefListScreenProps = {
  navigation: NativeStackNavigationProp<any>;
  route: RouteProp<any>;
};

export const ChefListScreen: React.FC<ChefListScreenProps> = ({ navigation, route }) => {
  const { search, cuisine, sortBy } = route.params || {};
  
  const [chefs, setChefs] = useState<Chef[]>([]);
  const [searchQuery, setSearchQuery] = useState(search || '');
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);

  useEffect(() => {
    loadChefs();
  }, []);

  const loadChefs = async (pageNum = 1, refresh = false) => {
    if (pageNum === 1) setIsLoading(true);
    
    try {
      const response = await chefService.getChefs(
        { sort_by: sortBy as any },
        pageNum
      );
      
      const data = response?.data || [];
      
      if (refresh || pageNum === 1) {
        setChefs(data);
      } else {
        setChefs(prev => [...prev, ...data]);
      }
      
      const meta = response?.meta;
      setHasMore(meta ? meta.current_page < meta.last_page : false);
      setPage(pageNum);
    } catch (error) {
      console.error('Failed to load chefs:', error);
      if (pageNum === 1) {
        setChefs([]);
      }
      setHasMore(false);
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  };

  const handleSearch = async () => {
    if (!searchQuery.trim()) {
      loadChefs(1, true);
      return;
    }
    
    setIsLoading(true);
    try {
      const results = await chefService.searchChefs(searchQuery);
      setChefs(results || []);
      setHasMore(false);
    } catch (error) {
      console.error('Search failed:', error);
      setChefs([]);
    } finally {
      setIsLoading(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadChefs(1, true);
  };

  const loadMore = () => {
    if (!isLoading && hasMore) {
      loadChefs(page + 1);
    }
  };

  const renderChefItem = ({ item }: { item: Chef }) => (
    <ChefCard
      chef={item}
      onPress={() => navigation.navigate('ChefDetail', { chefId: item.id })}
      style={styles.chefCard}
    />
  );

  const renderFooter = () => {
    if (!hasMore) return null;
    return (
      <View style={styles.footer}>
        <ActivityIndicator size="small" color={COLORS.primary} />
      </View>
    );
  };

  if (isLoading && chefs.length === 0) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={COLORS.primary} />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      {/* Search Bar */}
      <View style={styles.searchContainer}>
        <TextInput
          style={styles.searchInput}
          placeholder="Search chefs..."
          placeholderTextColor="#9CA3AF"
          value={searchQuery}
          onChangeText={setSearchQuery}
          onSubmitEditing={handleSearch}
          returnKeyType="search"
        />
        <TouchableOpacity style={styles.searchButton} onPress={handleSearch}>
          <Text style={styles.searchButtonText}>Search</Text>
        </TouchableOpacity>
      </View>

      {/* Results */}
      {chefs.length === 0 ? (
        <View style={styles.emptyContainer}>
          <Text style={styles.emptyIcon}>🍽️</Text>
          <Text style={styles.emptyTitle}>No Chefs Found</Text>
          <Text style={styles.emptySubtitle}>
            Try adjusting your search or filters
          </Text>
        </View>
      ) : (
        <FlatList
          data={chefs}
          renderItem={renderChefItem}
          keyExtractor={(item) => item.id.toString()}
          numColumns={2}
          columnWrapperStyle={styles.row}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />
          }
          onEndReached={loadMore}
          onEndReachedThreshold={0.5}
          ListFooterComponent={renderFooter}
        />
      )}
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
  searchContainer: {
    flexDirection: 'row',
    padding: 16,
    backgroundColor: '#FFFFFF',
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
    gap: 12,
  },
  searchInput: {
    flex: 1,
    backgroundColor: '#F3F4F6',
    borderRadius: 12,
    padding: 12,
    fontSize: 16,
    color: '#1F2937',
  },
  searchButton: {
    backgroundColor: COLORS.primary,
    borderRadius: 12,
    paddingHorizontal: 20,
    justifyContent: 'center',
  },
  searchButtonText: {
    color: '#FFFFFF',
    fontWeight: '600',
  },
  listContent: {
    padding: 12,
  },
  row: {
    justifyContent: 'space-between',
  },
  chefCard: {
    width: '48%',
    marginBottom: 12,
  },
  footer: {
    padding: 20,
    alignItems: 'center',
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 24,
  },
  emptyIcon: {
    fontSize: 64,
    marginBottom: 16,
  },
  emptyTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#1F2937',
    marginBottom: 8,
  },
  emptySubtitle: {
    fontSize: 14,
    color: '#6B7280',
    textAlign: 'center',
  },
});

export default ChefListScreen;
