import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Image, ViewStyle } from 'react-native';
import { Chef } from '../types';

interface ChefCardProps {
  chef: Chef;
  onPress: () => void;
  style?: ViewStyle;
}

export const ChefCard: React.FC<ChefCardProps> = ({ chef, onPress, style }) => {
  return (
    <TouchableOpacity style={[styles.container, style]} onPress={onPress} activeOpacity={0.8}>
      <View style={styles.imageContainer}>
        {chef.logo_url ? (
          <Image source={{ uri: chef.logo_url }} style={styles.image} resizeMode="cover" />
        ) : (
          <View style={styles.placeholderImage}>
            <Text style={styles.placeholderText}>{chef.business_name.charAt(0)}</Text>
          </View>
        )}
        {chef.is_verified && (
          <View style={styles.verifiedBadge}>
            <Text style={styles.verifiedText}>✓</Text>
          </View>
        )}
      </View>
      
      <View style={styles.content}>
        <Text style={styles.name} numberOfLines={1}>{chef.business_name}</Text>
        
        {chef.specialty && (
          <Text style={styles.specialty} numberOfLines={1}>{chef.specialty}</Text>
        )}
        
        <View style={styles.infoRow}>
          <View style={styles.ratingContainer}>
            <Text style={styles.star}>★</Text>
            <Text style={styles.rating}>{chef.rating.toFixed(1)}</Text>
            <Text style={styles.reviews}>({chef.total_reviews})</Text>
          </View>
        </View>
        
        <View style={styles.footer}>
          {chef.delivery_time && (
            <Text style={styles.deliveryTime}>{chef.delivery_time}</Text>
          )}
          {chef.delivery_fee !== undefined && (
            <Text style={styles.deliveryFee}>
              ₦{chef.delivery_fee.toLocaleString()} delivery
            </Text>
          )}
        </View>
        
        {!chef.is_available && (
          <View style={styles.unavailableBadge}>
            <Text style={styles.unavailableText}>Currently Unavailable</Text>
          </View>
        )}
      </View>
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  container: {
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  imageContainer: {
    height: 120,
    position: 'relative',
  },
  image: {
    width: '100%',
    height: '100%',
  },
  placeholderImage: {
    width: '100%',
    height: '100%',
    backgroundColor: '#FF6B35',
    justifyContent: 'center',
    alignItems: 'center',
  },
  placeholderText: {
    fontSize: 40,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  verifiedBadge: {
    position: 'absolute',
    top: 8,
    right: 8,
    backgroundColor: '#10B981',
    width: 24,
    height: 24,
    borderRadius: 12,
    justifyContent: 'center',
    alignItems: 'center',
  },
  verifiedText: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: 'bold',
  },
  content: {
    padding: 12,
  },
  name: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  specialty: {
    fontSize: 13,
    color: '#6B7280',
    marginTop: 2,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 8,
  },
  ratingContainer: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  star: {
    color: '#FBBF24',
    fontSize: 14,
  },
  rating: {
    fontSize: 14,
    fontWeight: '600',
    color: '#1F2937',
    marginLeft: 4,
  },
  reviews: {
    fontSize: 12,
    color: '#9CA3AF',
    marginLeft: 2,
  },
  footer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginTop: 8,
  },
  deliveryTime: {
    fontSize: 12,
    color: '#6B7280',
  },
  deliveryFee: {
    fontSize: 12,
    color: '#6B7280',
  },
  unavailableBadge: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: 'rgba(0,0,0,0.6)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  unavailableText: {
    color: '#FFFFFF',
    fontSize: 12,
    fontWeight: '600',
  },
});

export default ChefCard;
