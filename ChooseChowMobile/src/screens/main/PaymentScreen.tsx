import React, { useRef, useState, useCallback, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  Alert,
  ActivityIndicator,
  BackHandler,
} from 'react-native';
import { WebView } from 'react-native-webview';
import { useNavigation, useRoute } from '@react-navigation/native';
import { orderService, planSubscriptionService, walletService } from '../../api';
import { COLORS } from '../../utils/theme';

type VerificationType = 'order' | 'subscription' | 'upgrade' | 'wallet_funding';

const VERIFICATION_LABELS: Record<VerificationType, { successMsg: string; navScreen: string }> = {
  order: { successMsg: 'Your order has been confirmed.', navScreen: 'Orders' },
  subscription: { successMsg: 'Your subscription has been activated.', navScreen: 'MySubscription' },
  upgrade: { successMsg: 'Your plan has been upgraded.', navScreen: 'MySubscription' },
  wallet_funding: { successMsg: 'Your wallet has been funded.', navScreen: 'Wallet' },
};

export const PaymentScreen: React.FC = () => {
  const navigation = useNavigation<any>();
  const route = useRoute<any>();
  const { authorizationUrl, reference, verificationType: rawType } = route.params || {};
  const verificationType: VerificationType = rawType === 'wallet_funding' ? 'wallet_funding' : rawType === 'upgrade' ? 'upgrade' : rawType === 'subscription' ? 'subscription' : 'order';
  const webViewRef = useRef<any>(null);
  const verifyingRef = useRef(false);
  const [isVerifying, setIsVerifying] = useState(false);
  const [verified, setVerified] = useState(false);

  const timerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

  useEffect(() => {
    const onBackPress = () => {
      if (!verified && !isVerifying) {
        const cancelMsg = verificationType === 'wallet_funding'
          ? 'Your wallet will not be funded.'
          : verificationType === 'order'
            ? 'Your order will not be processed.'
            : 'Your subscription will not be activated.';
        Alert.alert(
          'Cancel Payment?',
          cancelMsg,
          [
            { text: 'Continue Payment', style: 'cancel' },
            {
              text: 'Cancel',
              style: 'destructive',
              onPress: () => navigation.goBack(),
            },
          ]
        );
        return true;
      }
      return false;
    };

    const subscription = BackHandler.addEventListener('hardwareBackPress', onBackPress);
    return () => subscription.remove();
  }, [verified, isVerifying, navigation, verificationType]);

  const handleVerifyPayment = useCallback(async (ref: string) => {
    if (verifyingRef.current || verified) return;
    verifyingRef.current = true;
    setIsVerifying(true);
    try {
      if (verificationType === 'wallet_funding') {
        await walletService.verifyFunding(ref);
      } else if (verificationType === 'subscription') {
        await planSubscriptionService.verifyPayment(ref);
      } else if (verificationType === 'upgrade') {
        await planSubscriptionService.verifyUpgradePayment(ref);
      } else {
        await orderService.verifyPayment(ref);
      }
      setVerified(true);
      const labels = VERIFICATION_LABELS[verificationType];
      Alert.alert(
        'Payment Successful!',
        labels.successMsg,
        [
          {
            text: 'OK',
            onPress: () => {
              const tabName = verificationType === 'wallet_funding' ? 'Wallet' : verificationType === 'order' ? 'Orders' : 'Subscription';
              navigation.navigate('MainTabs', { screen: tabName });
            },
          },
        ]
      );
    } catch (error: any) {
      Alert.alert(
        'Verification Failed',
        error.response?.data?.message || 'Could not verify payment. Please contact support.'
      );
    } finally {
      setIsVerifying(false);
      verifyingRef.current = false;
    }
  }, [verified, navigation, verificationType]);

  const handleShouldStartLoad = useCallback((request: { url: string; navigationType: string }) => {
    const { url } = request;

    if (!url.includes('paystack.com') && (url.includes('reference=') || url.includes('trxref='))) {
      const match = url.match(/reference=([^&]+)/);
      const ref = match?.[1] || reference;
      if (ref && !verifyingRef.current && !verified) {
        handleVerifyPayment(ref);
      }
      return false;
    }

    return true;
  }, [reference, handleVerifyPayment, verified]);

  const handleNavigationStateChange = useCallback((navState: { url: string }) => {
    const { url } = navState;

    if (!url.includes('paystack.com') && (url.includes('reference=') || url.includes('trxref='))) {
      const match = url.match(/reference=([^&]+)/);
      const ref = match?.[1] || reference;
      if (ref && !verifyingRef.current && !verified) {
        webViewRef.current?.stopLoading();
        handleVerifyPayment(ref);
      }
    }
  }, [reference, handleVerifyPayment, verified]);

  if (isVerifying) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.verifyingText}>Verifying payment...</Text>
      </View>
    );
  }

  if (verified) {
    return (
      <View style={styles.centerContainer}>
        <Text style={styles.successIcon}>✅</Text>
        <Text style={styles.successTitle}>Payment Successful!</Text>
        <Text style={styles.successSubtitle}>{VERIFICATION_LABELS[verificationType]?.successMsg}</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <WebView
        ref={webViewRef}
        source={{ uri: authorizationUrl }}
        onShouldStartLoadWithRequest={handleShouldStartLoad}
        onNavigationStateChange={handleNavigationStateChange}
        startInLoadingState
        renderLoading={() => (
          <View style={styles.loadingOverlay}>
            <ActivityIndicator size="large" color={COLORS.primary} />
            <Text style={styles.loadingText}>Loading payment page...</Text>
          </View>
        )}
        javaScriptEnabled
        domStorageEnabled
        originWhitelist={['*']}
        style={styles.webview}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FFFFFF',
  },
  webview: {
    flex: 1,
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    padding: 24,
  },
  loadingOverlay: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
  },
  loadingText: {
    marginTop: 12,
    fontSize: 15,
    color: '#6B7280',
  },
  verifyingText: {
    marginTop: 16,
    fontSize: 16,
    color: '#6B7280',
    fontWeight: '500',
  },
  successIcon: {
    fontSize: 64,
    marginBottom: 16,
  },
  successTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#1F2937',
    marginBottom: 8,
  },
  successSubtitle: {
    fontSize: 15,
    color: '#6B7280',
    textAlign: 'center',
  },
});

export default PaymentScreen;
