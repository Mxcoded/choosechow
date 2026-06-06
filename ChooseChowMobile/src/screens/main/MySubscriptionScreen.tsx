import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  RefreshControl,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { planSubscriptionService } from '../../api';
import { COLORS } from '../../utils/theme';
import type { SubscriptionStatusResponse } from '../../types';

type MySubscriptionScreenProps = {
  navigation: NativeStackNavigationProp<any>;
};

const STATUS_COLORS: Record<string, string> = {
  active: '#10B981',
  cancelled: '#EF4444',
  expired: '#6B7280',
  past_due: '#F59E0B',
  trialing: '#3B82F6',
};

const STATUS_LABELS: Record<string, string> = {
  active: 'Active',
  cancelled: 'Cancelled',
  expired: 'Expired',
  past_due: 'Past Due',
  trialing: 'Trial',
};

const TIER_ICONS: Record<string, string> = {
  basic: '🌱',
  plus: '⭐',
  premium: '👑',
};

export const MySubscriptionScreen: React.FC<MySubscriptionScreenProps> = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  const [subscription, setSubscription] = useState<SubscriptionStatusResponse | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [isCancelling, setIsCancelling] = useState(false);
  const [refreshing, setRefreshing] = useState(false);

  const loadData = useCallback(async () => {
    try {
      const currentData = await planSubscriptionService.getStatus();
      setSubscription(currentData);
    } catch (error) {
      console.error('Failed to load subscription:', error);
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    loadData();
  }, [loadData]);

  const onRefresh = () => {
    setRefreshing(true);
    loadData();
  };

  const handleCancel = () => {
    Alert.alert(
      'Cancel Subscription',
      'Your subscription will remain active until the end of the current billing period. After that, you will lose access to all subscription benefits.',
      [
        { text: 'Keep Subscription', style: 'cancel' },
        {
          text: 'Cancel',
          style: 'destructive',
          onPress: async () => {
            setIsCancelling(true);
            try {
              const result = await planSubscriptionService.cancel();
              Alert.alert('Cancelled', result.message);
              loadData();
            } catch (error: any) {
              Alert.alert('Error', error?.response?.data?.message || 'Failed to cancel subscription.');
            } finally {
              setIsCancelling(false);
            }
          },
        },
      ]
    );
  };

  const formatDate = (dateStr: string | null) => {
    if (!dateStr) return '—';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
  };

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={COLORS.primary} />
      </View>
    );
  }

  if (!subscription) {
    return (
      <ScrollView
        style={styles.container}
        contentContainerStyle={{ flex: 1, justifyContent: 'center', alignItems: 'center', padding: 24 }}
      >
        <Text style={styles.emptyIcon}>📋</Text>
        <Text style={styles.emptyTitle}>No Active Subscription</Text>
        <Text style={styles.emptySubtitle}>Subscribe to a plan to unlock benefits like free delivery and discounts.</Text>
        <TouchableOpacity
          style={styles.browseButton}
          onPress={() => navigation.navigate('SubscriptionPlans')}
        >
          <Text style={styles.browseButtonText}>View Plans</Text>
        </TouchableOpacity>
      </ScrollView>
    );
  }

  const hasBenefits = subscription.benefits && subscription.benefits.length > 0;
  const statusColor = STATUS_COLORS[subscription.status] || COLORS.gray[500];

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={{ paddingBottom: insets.bottom + 32 }}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
    >
      <View style={styles.heroSection}>
        <Text style={styles.heroIcon}>{TIER_ICONS[subscription.tier]}</Text>
        <Text style={styles.heroPlanName}>{subscription.plan_name}</Text>
        <View style={[styles.statusBadge, { backgroundColor: statusColor + '20' }]}>
          <View style={[styles.statusDot, { backgroundColor: statusColor }]} />
          <Text style={[styles.statusText, { color: statusColor }]}>
            {STATUS_LABELS[subscription.status] || subscription.status}
          </Text>
        </View>
        <Text style={styles.heroPrice}>₦{subscription.plan_price.toLocaleString()}/mo</Text>
      </View>

      <View style={styles.infoCard}>
        <View style={styles.infoRow}>
          <Text style={styles.infoLabel}>Start Date</Text>
          <Text style={styles.infoValue}>{formatDate(subscription.started_at)}</Text>
        </View>
        <View style={styles.infoRow}>
          <Text style={styles.infoLabel}>Renewal Date</Text>
          <Text style={styles.infoValue}>{formatDate(subscription.renews_at)}</Text>
        </View>
        {subscription.cancelled_at && (
          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>Cancelled At</Text>
            <Text style={styles.infoValue}>{formatDate(subscription.cancelled_at)}</Text>
          </View>
        )}
        {subscription.ends_at && (
          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>Benefits Until</Text>
            <Text style={styles.infoValue}>{formatDate(subscription.ends_at)}</Text>
          </View>
        )}
        <View style={styles.infoRow}>
          <Text style={styles.infoLabel}>Free Deliveries Used</Text>
          <Text style={styles.infoValue}>{subscription.free_delivery_used} / {subscription.free_delivery_limit}</Text>
        </View>
      </View>

      {hasBenefits && (
        <View style={styles.featuresCard}>
          <Text style={styles.sectionTitle}>Plan Benefits</Text>
          {subscription.benefits.map((feature, idx) => (
            <View key={idx} style={styles.featureRow}>
              <Text style={styles.featureCheck}>✓</Text>
              <Text style={styles.featureText}>{feature}</Text>
            </View>
          ))}
        </View>
      )}

      <View style={styles.actionsSection}>
        {(subscription.tier === 'basic' || subscription.tier === 'plus' || subscription.tier === 'none') && subscription.is_active && (
          <TouchableOpacity
            style={styles.upgradeButton}
            onPress={() => navigation.navigate('SubscriptionPlans')}
          >
            <Text style={styles.upgradeButtonText}>Upgrade Plan</Text>
          </TouchableOpacity>
        )}

        {subscription.is_active && (
          <TouchableOpacity
            style={styles.cancelButton}
            onPress={handleCancel}
            disabled={isCancelling}
          >
            {isCancelling ? (
              <ActivityIndicator color={COLORS.primary} />
            ) : (
              <Text style={styles.cancelButtonText}>Cancel Subscription</Text>
            )}
          </TouchableOpacity>
        )}
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background.secondary,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: COLORS.background.secondary,
  },
  emptyIcon: {
    fontSize: 64,
    marginBottom: 16,
  },
  emptyTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: COLORS.text.primary,
    marginBottom: 8,
  },
  emptySubtitle: {
    fontSize: 15,
    color: COLORS.text.secondary,
    textAlign: 'center',
    marginBottom: 24,
    lineHeight: 22,
  },
  browseButton: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: 32,
    paddingVertical: 14,
    borderRadius: 12,
  },
  browseButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
  heroSection: {
    backgroundColor: COLORS.white,
    alignItems: 'center',
    paddingVertical: 32,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.border.light,
  },
  heroIcon: {
    fontSize: 48,
    marginBottom: 12,
  },
  heroPlanName: {
    fontSize: 26,
    fontWeight: 'bold',
    color: COLORS.text.primary,
  },
  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 14,
    paddingVertical: 6,
    borderRadius: 16,
    marginTop: 10,
  },
  statusDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    marginRight: 6,
  },
  statusText: {
    fontSize: 14,
    fontWeight: '600',
  },
  heroPrice: {
    fontSize: 16,
    color: COLORS.text.secondary,
    marginTop: 8,
  },
  infoCard: {
    backgroundColor: COLORS.white,
    marginHorizontal: 16,
    marginTop: 16,
    borderRadius: 12,
    padding: 16,
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.border.light,
  },
  infoLabel: {
    fontSize: 14,
    color: COLORS.text.secondary,
  },
  infoValue: {
    fontSize: 14,
    color: COLORS.text.primary,
    fontWeight: '600',
  },
  featuresCard: {
    backgroundColor: COLORS.white,
    marginHorizontal: 16,
    marginTop: 12,
    borderRadius: 12,
    padding: 16,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.text.primary,
    marginBottom: 14,
  },
  featureRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 10,
  },
  featureCheck: {
    fontSize: 14,
    color: '#10B981',
    marginRight: 10,
    fontWeight: 'bold',
  },
  featureText: {
    fontSize: 14,
    color: COLORS.text.primary,
    flex: 1,
  },
  actionsSection: {
    marginHorizontal: 16,
    marginTop: 20,
  },
  upgradeButton: {
    backgroundColor: COLORS.primary,
    paddingVertical: 14,
    borderRadius: 12,
    alignItems: 'center',
    marginBottom: 12,
  },
  upgradeButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
  cancelButton: {
    backgroundColor: COLORS.primaryFaded,
    paddingVertical: 14,
    borderRadius: 12,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: COLORS.primary,
  },
  cancelButtonText: {
    color: COLORS.primary,
    fontSize: 16,
    fontWeight: 'bold',
  },
  historyCard: {
    backgroundColor: COLORS.white,
    marginHorizontal: 16,
    marginTop: 16,
    borderRadius: 12,
    padding: 16,
  },
  historyItem: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginBottom: 14,
  },
  historyDot: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: COLORS.primary,
    marginRight: 12,
    marginTop: 4,
  },
  historyAction: {
    fontSize: 14,
    color: COLORS.text.primary,
    fontWeight: '600',
  },
  historyDate: {
    fontSize: 12,
    color: COLORS.text.secondary,
    marginTop: 2,
  },
  historyAmount: {
    fontSize: 14,
    color: COLORS.text.primary,
    fontWeight: '600',
  },
});

export default MySubscriptionScreen;
