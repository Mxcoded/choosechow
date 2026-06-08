import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  RefreshControl,
  TextInput,
  Modal,
  Alert,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { walletService } from '../../api';
import type { WalletBalance, WalletTransaction } from '../../api/walletService';
import { COLORS } from '../../utils/theme';
import MaterialCommunityIcons from '@expo/vector-icons/MaterialCommunityIcons';

type WalletScreenProps = {
  navigation: NativeStackNavigationProp<any>;
};

export const WalletScreen: React.FC<WalletScreenProps> = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  const [balance, setBalance] = useState<WalletBalance | null>(null);
  const [transactions, setTransactions] = useState<WalletTransaction[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [fundModal, setFundModal] = useState(false);
  const [fundAmount, setFundAmount] = useState('');
  const [funding, setFunding] = useState(false);

  const loadData = useCallback(async () => {
    try {
      const [bal, txns] = await Promise.all([
        walletService.getBalance(),
        walletService.getTransactions(1, 50),
      ]);
      setBalance(bal);
      setTransactions(txns.data ?? []);
    } catch (error) {
      console.error('Failed to load wallet data:', error);
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    loadData();
  }, [loadData]);

  useEffect(() => {
    const unsubscribe = navigation.addListener('focus', () => {
      loadData();
    });
    return unsubscribe;
  }, [navigation, loadData]);

  const onRefresh = () => {
    setRefreshing(true);
    loadData();
  };

  const handleFund = async () => {
    const amount = parseFloat(fundAmount);
    if (!amount || amount < 100) {
      Alert.alert('Invalid Amount', 'Minimum funding amount is ₦100.');
      return;
    }
    setFunding(true);
    try {
      const result = await walletService.fund(amount);
      setFundModal(false);
      setFundAmount('');
      navigation.navigate('Payment', {
        authorizationUrl: result.authorization_url,
        reference: result.reference,
        verificationType: 'wallet_funding',
      });
    } catch (error: any) {
      Alert.alert('Error', error?.response?.data?.message || 'Failed to initialize funding.');
    } finally {
      setFunding(false);
    }
  };

  const getTypeIcon = (type: string) => {
    switch (type) {
      case 'wallet_topup': return 'wallet-plus' as const;
      case 'subscription_payment': return 'credit-card-refund' as const;
      case 'order_payment': return 'cart-outline' as const;
      case 'subscription_credit': return 'gift' as const;
      case 'payout': return 'cash-minus' as const;
      case 'refund': return 'cash-refund' as const;
      case 'earning': return 'cash-plus' as const;
      default: return 'swap-horizontal' as const;
    }
  };

  const getTypeLabel = (type: string): string => {
    switch (type) {
      case 'wallet_topup': return 'Wallet Funding';
      case 'subscription_payment': return 'Subscription Payment';
      case 'order_payment': return 'Order Payment';
      case 'subscription_credit': return 'Premium Credit';
      case 'payout': return 'Withdrawal';
      case 'refund': return 'Refund';
      case 'earning': return 'Earnings';
      default: return type;
    }
  };

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
      <View style={styles.balanceCard}>
        <Text style={styles.balanceLabel}>Wallet Balance</Text>
        <Text style={styles.balanceAmount}>{balance?.formatted_balance ?? '₦0.00'}</Text>
        <TouchableOpacity style={styles.fundButton} onPress={() => setFundModal(true)}>
          <MaterialCommunityIcons name="plus" size={18} color="#fff" />
          <Text style={styles.fundButtonText}>Fund Wallet</Text>
        </TouchableOpacity>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Transaction History</Text>
        {transactions.length === 0 ? (
          <View style={styles.emptyState}>
            <MaterialCommunityIcons name="wallet-outline" size={48} color={COLORS.text.light} />
            <Text style={styles.emptyText}>No transactions yet</Text>
          </View>
        ) : (
          transactions.map((txn) => (
            <View key={txn.id} style={styles.transactionRow}>
              <View style={styles.transactionIcon}>
                <MaterialCommunityIcons
                  name={getTypeIcon(txn.type)}
                  size={20}
                  color={txn.type === 'subscription_payment' || txn.type === 'payout' ? COLORS.error : COLORS.success}
                />
              </View>
              <View style={styles.transactionInfo}>
                <Text style={styles.transactionType}>{getTypeLabel(txn.type)}</Text>
                {txn.description && (
                  <Text style={styles.transactionDesc} numberOfLines={1}>{txn.description}</Text>
                )}
                <Text style={styles.transactionDate}>
                  {new Date(txn.created_at).toLocaleDateString('en-NG', { day: 'numeric', month: 'short', year: 'numeric' })}
                </Text>
              </View>
              <Text style={[
                styles.transactionAmount,
                (txn.type === 'subscription_payment' || txn.type === 'payout' || txn.type === 'order_payment') && styles.negativeAmount,
              ]}>
                {txn.formatted_amount}
              </Text>
            </View>
          ))
        )}
      </View>

      <Modal
        visible={fundModal}
        transparent
        animationType="fade"
        onRequestClose={() => setFundModal(false)}
      >
        <TouchableOpacity
          style={styles.modalOverlay}
          activeOpacity={1}
          onPress={() => !funding && setFundModal(false)}
        >
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Fund Wallet</Text>
            <Text style={styles.modalSubtitle}>Enter amount to add to your wallet</Text>

            <View style={styles.inputContainer}>
              <Text style={styles.currencyPrefix}>₦</Text>
              <TextInput
                style={styles.amountInput}
                placeholder="0.00"
                placeholderTextColor={COLORS.text.light}
                keyboardType="decimal-pad"
                value={fundAmount}
                onChangeText={setFundAmount}
                autoFocus
              />
            </View>

            <View style={styles.quickAmounts}>
              {[1000, 2000, 5000, 10000].map((amount) => (
                <TouchableOpacity
                  key={amount}
                  style={[styles.quickAmount, parseFloat(fundAmount) === amount && styles.quickAmountActive]}
                  onPress={() => setFundAmount(amount.toString())}
                >
                  <Text style={[styles.quickAmountText, parseFloat(fundAmount) === amount && styles.quickAmountTextActive]}>
                    ₦{amount.toLocaleString()}
                  </Text>
                </TouchableOpacity>
              ))}
            </View>

            <TouchableOpacity
              style={[styles.confirmButton, funding && styles.confirmButtonDisabled]}
              onPress={handleFund}
              disabled={funding}
            >
              {funding ? (
                <ActivityIndicator color="#fff" />
              ) : (
                <Text style={styles.confirmButtonText}>
                  Fund ₦{parseFloat(fundAmount || '0').toLocaleString()}
                </Text>
              )}
            </TouchableOpacity>

            <TouchableOpacity
              style={styles.cancelButton}
              onPress={() => { setFundModal(false); setFundAmount(''); }}
              disabled={funding}
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
  balanceCard: {
    backgroundColor: COLORS.primary,
    margin: 20,
    borderRadius: 16,
    padding: 24,
    alignItems: 'center',
  },
  balanceLabel: {
    fontSize: 14,
    color: 'rgba(255,255,255,0.8)',
    fontWeight: '500',
  },
  balanceAmount: {
    fontSize: 36,
    fontWeight: 'bold',
    color: '#fff',
    marginTop: 8,
  },
  fundButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255,255,255,0.2)',
    paddingHorizontal: 20,
    paddingVertical: 10,
    borderRadius: 20,
    marginTop: 16,
  },
  fundButtonText: {
    color: '#fff',
    fontSize: 15,
    fontWeight: '600',
    marginLeft: 6,
  },
  section: {
    paddingHorizontal: 20,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.text.primary,
    marginBottom: 12,
  },
  emptyState: {
    alignItems: 'center',
    paddingVertical: 40,
  },
  emptyText: {
    fontSize: 15,
    color: COLORS.text.light,
    marginTop: 12,
  },
  transactionRow: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.white,
    padding: 14,
    borderRadius: 12,
    marginBottom: 8,
    borderWidth: 1,
    borderColor: COLORS.border.light,
  },
  transactionIcon: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: COLORS.background.secondary,
    justifyContent: 'center',
    alignItems: 'center',
  },
  transactionInfo: {
    flex: 1,
    marginLeft: 12,
  },
  transactionType: {
    fontSize: 14,
    fontWeight: '600',
    color: COLORS.text.primary,
  },
  transactionDesc: {
    fontSize: 12,
    color: COLORS.text.secondary,
    marginTop: 2,
  },
  transactionDate: {
    fontSize: 11,
    color: COLORS.text.light,
    marginTop: 2,
  },
  transactionAmount: {
    fontSize: 15,
    fontWeight: 'bold',
    color: COLORS.success,
  },
  negativeAmount: {
    color: COLORS.error,
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
    fontSize: 22,
    fontWeight: 'bold',
    color: COLORS.text.primary,
    marginBottom: 4,
  },
  modalSubtitle: {
    fontSize: 14,
    color: COLORS.text.secondary,
    marginBottom: 24,
  },
  inputContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.background.secondary,
    borderRadius: 12,
    paddingHorizontal: 16,
    marginBottom: 16,
  },
  currencyPrefix: {
    fontSize: 24,
    fontWeight: 'bold',
    color: COLORS.text.primary,
    marginRight: 8,
  },
  amountInput: {
    flex: 1,
    fontSize: 24,
    fontWeight: 'bold',
    color: COLORS.text.primary,
    paddingVertical: 16,
  },
  quickAmounts: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    marginBottom: 24,
  },
  quickAmount: {
    paddingHorizontal: 16,
    paddingVertical: 10,
    borderRadius: 20,
    borderWidth: 1,
    borderColor: COLORS.border.medium,
    backgroundColor: COLORS.background.secondary,
  },
  quickAmountActive: {
    borderColor: COLORS.primary,
    backgroundColor: COLORS.primaryFaded,
  },
  quickAmountText: {
    fontSize: 14,
    color: COLORS.text.secondary,
    fontWeight: '600',
  },
  quickAmountTextActive: {
    color: COLORS.primary,
  },
  confirmButton: {
    backgroundColor: COLORS.primary,
    paddingVertical: 16,
    borderRadius: 12,
    alignItems: 'center',
    marginBottom: 8,
  },
  confirmButtonDisabled: {
    opacity: 0.6,
  },
  confirmButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
  cancelButton: {
    alignItems: 'center',
    paddingVertical: 12,
  },
  cancelButtonText: {
    fontSize: 15,
    color: COLORS.text.secondary,
    fontWeight: '500',
  },
});

export default WalletScreen;
