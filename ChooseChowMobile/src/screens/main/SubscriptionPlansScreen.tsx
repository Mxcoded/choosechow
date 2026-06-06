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
  Modal,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { planSubscriptionService } from '../../api';
import { COLORS } from '../../utils/theme';
import type { SubscriptionPlan, SubscriptionStatusResponse } from '../../types';
import MaterialCommunityIcons from '@expo/vector-icons/MaterialCommunityIcons';

type SubscriptionPlansScreenProps = {
  navigation: NativeStackNavigationProp<any>;
};

const TIER_COLORS: Record<string, string> = {
  basic: '#6B7280',
  plus: '#3B82F6',
  premium: '#E53935',
};

const TIER_ICONS: Record<string, string> = {
  basic: '🌱',
  plus: '⭐',
  premium: '👑',
};

export const SubscriptionPlansScreen: React.FC<SubscriptionPlansScreenProps> = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  const [plans, setPlans] = useState<SubscriptionPlan[]>([]);
  const [currentStatus, setCurrentStatus] = useState<SubscriptionStatusResponse | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [subscribing, setSubscribing] = useState<number | null>(null);
  const [refreshing, setRefreshing] = useState(false);
  const [paymentModal, setPaymentModal] = useState<{ visible: boolean; slug: string; action: 'subscribe' | 'upgrade' }>({ visible: false, slug: '', action: 'subscribe' });

  const loadData = useCallback(async () => {
    try {
      const [plansData, currentData] = await Promise.all([
        planSubscriptionService.getPlans(),
        planSubscriptionService.getStatus(),
      ]);
      setPlans(plansData);
      setCurrentStatus(currentData);
    } catch (error) {
      console.error('Failed to load subscription data:', error);
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

  const processWalletPayment = async (slug: string, action: 'subscribe' | 'upgrade') => {
    setSubscribing(plans.find(p => p.slug === slug)?.id ?? null);
    setPaymentModal({ visible: false, slug: '', action: 'subscribe' });
    try {
      const fn = action === 'upgrade' ? planSubscriptionService.upgrade : planSubscriptionService.subscribe;
      const result = await fn(slug, 'wallet');
      Alert.alert('Success!', (result as any).message);
      loadData();
    } catch (error: any) {
      const msg = error?.response?.data?.message || error?.message || `Failed to ${action}. Please try again.`;
      Alert.alert('Error', msg);
    } finally {
      setSubscribing(null);
    }
  };

  const processPaystackPayment = async (slug: string, action: 'subscribe' | 'upgrade') => {
    setSubscribing(plans.find(p => p.slug === slug)?.id ?? null);
    setPaymentModal({ visible: false, slug: '', action: 'subscribe' });
    try {
      const fn = action === 'upgrade' ? planSubscriptionService.upgrade : planSubscriptionService.subscribe;
      const result = await fn(slug, 'paystack');

      if ((result as any).authorization_url) {
        navigation.navigate('PaymentScreen', {
          authorizationUrl: (result as any).authorization_url,
          reference: (result as any).reference,
          verificationType: action === 'upgrade' ? 'upgrade' : 'subscription',
        });
      } else {
        Alert.alert('Error', 'Could not initialize payment. Please try again.');
      }
    } catch (error: any) {
      const msg = error?.response?.data?.message || error?.message || `Failed to ${action}. Please try again.`;
      Alert.alert('Error', msg);
    } finally {
      setSubscribing(null);
    }
  };

  const handleSubscribe = async (slug: string) => {
    setPaymentModal({ visible: true, slug, action: 'subscribe' });
  };

  const handleUpgrade = async (slug: string) => {
    setPaymentModal({ visible: true, slug, action: 'upgrade' });
  };

  const handleDowngrade = async (slug: string) => {
    Alert.alert(
      'Downgrade Plan',
      'Are you sure you want to downgrade? Some features will be lost.',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Downgrade',
          style: 'destructive',
          onPress: async () => {
            setSubscribing(plans.find(p => p.slug === slug)?.id ?? null);
            try {
              const result = await planSubscriptionService.downgrade(slug);
              Alert.alert('Downgraded', result.message);
              loadData();
            } catch (error: any) {
              Alert.alert('Error', error?.response?.data?.message || 'Failed to downgrade. Please try again.');
            } finally {
              setSubscribing(null);
            }
          },
        },
      ]
    );
  };

  const getAction = (plan: SubscriptionPlan) => {
    if (!currentStatus) return 'subscribe';
    if (plan.slug === currentStatus.tier) return 'current';
    const tierOrder = ['basic', 'plus', 'premium'];
    const currentIdx = tierOrder.indexOf(currentStatus.tier);
    const planIdx = tierOrder.indexOf(plan.slug as string);
    if (planIdx > currentIdx) return 'upgrade';
    return 'downgrade';
  };

  const formatPrice = (price: number) => `₦${price.toLocaleString()}/mo`;

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={COLORS.primary} />
      </View>
    );
  }

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={{ paddingBottom: insets.bottom + 32 }}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
    >
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Choose Your Plan</Text>
        <Text style={styles.headerSubtitle}>
          Unlock benefits like free delivery, discounts, and priority support
        </Text>
      </View>

      {currentStatus && currentStatus.is_active && (
        <TouchableOpacity
          style={styles.currentPlanBanner}
          onPress={() => navigation.navigate('MySubscription')}
        >
          <Text style={styles.currentPlanLabel}>Current Plan</Text>
          <Text style={styles.currentPlanName}>
            {TIER_ICONS[currentStatus.tier]} {currentStatus.plan_name}
          </Text>
          <Text style={styles.currentPlanAction}>Manage Subscription ›</Text>
        </TouchableOpacity>
      )}

      {plans.map((plan) => {
        const action = getAction(plan);
        const tierSlug = plan.slug;
        const tierColor = TIER_COLORS[tierSlug] || COLORS.primary;
        const isPopular = plan.is_popular;

        return (
          <View
            key={plan.id}
            style={[
              styles.planCard,
              action === 'current' && styles.planCardCurrent,
              isPopular && styles.planCardPopular,
            ]}
          >
            {isPopular && (
              <View style={styles.popularBadge}>
                <Text style={styles.popularBadgeText}>Most Popular</Text>
              </View>
            )}

            <View style={styles.planHeader}>
              <Text style={styles.planIcon}>{TIER_ICONS[tierSlug]}</Text>
              <View style={{ flex: 1 }}>
                <Text style={[styles.planName, { color: tierColor }]}>{plan.name}</Text>
                <Text style={styles.planPrice}>{formatPrice(plan.monthly_price)}</Text>
              </View>
            </View>

            <Text style={styles.planDescription}>{plan.description}</Text>

            <View style={styles.featuresList}>
              {plan.features.map((feature, idx) => (
                <View key={idx} style={styles.featureRow}>
                  <Text style={styles.featureCheck}>✓</Text>
                  <Text style={styles.featureText}>{feature}</Text>
                </View>
              ))}
            </View>

            {action === 'subscribe' && (
              <TouchableOpacity
                style={[styles.actionButton, { backgroundColor: tierColor }]}
                onPress={() => handleSubscribe(tierSlug)}
                disabled={subscribing === plan.id}
              >
                {subscribing === plan.id ? (
                  <ActivityIndicator color="#fff" />
                ) : (
                  <Text style={styles.actionButtonText}>Subscribe Now</Text>
                )}
              </TouchableOpacity>
            )}

            {action === 'upgrade' && (
              <TouchableOpacity
                style={[styles.actionButton, { backgroundColor: tierColor }]}
                onPress={() => handleUpgrade(tierSlug)}
                disabled={subscribing === plan.id}
              >
                {subscribing === plan.id ? (
                  <ActivityIndicator color="#fff" />
                ) : (
                  <Text style={styles.actionButtonText}>Upgrade to {plan.name}</Text>
                )}
              </TouchableOpacity>
            )}

            {action === 'downgrade' && (
              <TouchableOpacity
                style={[styles.actionButton, styles.downgradeButton]}
                onPress={() => handleDowngrade(tierSlug)}
                disabled={subscribing === plan.id}
              >
                {subscribing === plan.id ? (
                  <ActivityIndicator color={COLORS.primary} />
                ) : (
                  <Text style={[styles.actionButtonText, styles.downgradeButtonText]}>
                    Downgrade to {plan.name}
                  </Text>
                )}
              </TouchableOpacity>
            )}

            {action === 'current' && (
              <View style={[styles.currentBadge, { backgroundColor: tierColor + '20' }]}>
                <Text style={[styles.currentBadgeText, { color: tierColor }]}>Current Plan</Text>
              </View>
            )}
          </View>
        );
      })}

      <Modal
        visible={paymentModal.visible}
        transparent
        animationType="fade"
        onRequestClose={() => setPaymentModal({ visible: false, slug: '', action: 'subscribe' })}
      >
        <TouchableOpacity
          style={styles.modalOverlay}
          activeOpacity={1}
          onPress={() => setPaymentModal({ visible: false, slug: '', action: 'subscribe' })}
        >
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Choose Payment Method</Text>
            <Text style={styles.modalSubtitle}>
              {paymentModal.action === 'upgrade' ? 'Pay prorated upgrade charge' : 'Pay to activate subscription'}
            </Text>

            <TouchableOpacity
              style={styles.paymentOption}
              onPress={() => processWalletPayment(paymentModal.slug, paymentModal.action)}
            >
              <MaterialCommunityIcons name="wallet" size={24} color={COLORS.primary} />
              <View style={styles.paymentOptionText}>
                <Text style={styles.paymentOptionLabel}>Wallet Balance</Text>
                <Text style={styles.paymentOptionDesc}>Pay with your account wallet</Text>
              </View>
              <MaterialCommunityIcons name="chevron-right" size={20} color={COLORS.text.secondary} />
            </TouchableOpacity>

            <TouchableOpacity
              style={styles.paymentOption}
              onPress={() => processPaystackPayment(paymentModal.slug, paymentModal.action)}
            >
              <MaterialCommunityIcons name="credit-card-outline" size={24} color={COLORS.primary} />
              <View style={styles.paymentOptionText}>
                <Text style={styles.paymentOptionLabel}>Paystack</Text>
                <Text style={styles.paymentOptionDesc}>Pay with card, bank transfer, or USSD</Text>
              </View>
              <MaterialCommunityIcons name="chevron-right" size={20} color={COLORS.text.secondary} />
            </TouchableOpacity>

            <TouchableOpacity
              style={styles.cancelButton}
              onPress={() => setPaymentModal({ visible: false, slug: '', action: 'subscribe' })}
            >
              <Text style={styles.cancelButtonText}>Cancel</Text>
            </TouchableOpacity>
          </View>
        </TouchableOpacity>
      </Modal>
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
  header: {
    padding: 24,
    paddingTop: 16,
  },
  headerTitle: {
    fontSize: 28,
    fontWeight: 'bold',
    color: COLORS.text.primary,
    marginBottom: 8,
  },
  headerSubtitle: {
    fontSize: 15,
    color: COLORS.text.secondary,
    lineHeight: 22,
  },
  currentPlanBanner: {
    backgroundColor: COLORS.primary + '10',
    marginHorizontal: 20,
    marginBottom: 16,
    padding: 16,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: COLORS.primary + '30',
  },
  currentPlanLabel: {
    fontSize: 12,
    color: COLORS.text.secondary,
    fontWeight: '600',
    textTransform: 'uppercase',
    letterSpacing: 1,
  },
  currentPlanName: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.text.primary,
    marginTop: 4,
  },
  currentPlanAction: {
    fontSize: 14,
    color: COLORS.primary,
    fontWeight: '600',
    marginTop: 8,
  },
  planCard: {
    backgroundColor: COLORS.white,
    marginHorizontal: 20,
    marginBottom: 16,
    borderRadius: 16,
    padding: 20,
    borderWidth: 1,
    borderColor: COLORS.border.light,
  },
  planCardCurrent: {
    borderColor: COLORS.primary,
    borderWidth: 2,
  },
  planCardPopular: {
    borderColor: '#3B82F6',
    borderWidth: 2,
  },
  popularBadge: {
    position: 'absolute',
    top: -10,
    right: 20,
    backgroundColor: '#3B82F6',
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 12,
  },
  popularBadgeText: {
    color: '#fff',
    fontSize: 11,
    fontWeight: 'bold',
  },
  planHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  planIcon: {
    fontSize: 32,
    marginRight: 12,
  },
  planName: {
    fontSize: 20,
    fontWeight: 'bold',
  },
  planPrice: {
    fontSize: 14,
    color: COLORS.text.secondary,
    marginTop: 2,
  },
  planDescription: {
    fontSize: 14,
    color: COLORS.text.secondary,
    lineHeight: 20,
    marginBottom: 16,
  },
  featuresList: {
    marginBottom: 20,
  },
  featureRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
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
  actionButton: {
    paddingVertical: 14,
    borderRadius: 12,
    alignItems: 'center',
  },
  actionButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
  downgradeButton: {
    backgroundColor: COLORS.primaryFaded,
    borderWidth: 1,
    borderColor: COLORS.primary,
  },
  downgradeButtonText: {
    color: COLORS.primary,
  },
  currentBadge: {
    paddingVertical: 14,
    borderRadius: 12,
    alignItems: 'center',
  },
  currentBadgeText: {
    fontSize: 16,
    fontWeight: 'bold',
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'flex-end',
  },
  modalContent: {
    backgroundColor: COLORS.white,
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    padding: 24,
    paddingBottom: 40,
  },
  modalTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: COLORS.text.primary,
    marginBottom: 4,
  },
  modalSubtitle: {
    fontSize: 14,
    color: COLORS.text.secondary,
    marginBottom: 24,
  },
  paymentOption: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 16,
    paddingHorizontal: 12,
    borderRadius: 12,
    backgroundColor: COLORS.background.secondary,
    marginBottom: 12,
  },
  paymentOptionText: {
    flex: 1,
    marginLeft: 12,
  },
  paymentOptionLabel: {
    fontSize: 16,
    fontWeight: '600',
    color: COLORS.text.primary,
  },
  paymentOptionDesc: {
    fontSize: 13,
    color: COLORS.text.secondary,
    marginTop: 2,
  },
  cancelButton: {
    alignItems: 'center',
    paddingVertical: 14,
    marginTop: 8,
  },
  cancelButtonText: {
    fontSize: 16,
    color: COLORS.text.secondary,
    fontWeight: '500',
  },
});

export default SubscriptionPlansScreen;
