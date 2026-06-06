import React, { useRef, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  Alert,
  ActivityIndicator,
  BackHandler,
} from 'react-native';
import { WebView, WebViewNavigation } from 'react-native-webview';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RouteProp, useFocusEffect } from '@react-navigation/native';
import { orderService } from '../../api';
import { COLORS } from '../../utils/theme';
import type { MainStackParamList } from '../../navigation/types';

type PaymentScreenProps = {
  navigation: NativeStackNavigationProp<MainStackParamList, 'Payment'>;
  route: RouteProp<MainStackParamList, 'Payment'>;
};

export const PaymentScreen: React.FC<PaymentScreenProps> = ({ navigation, route }) => {
  const { authorizationUrl, reference } = route.params;
  const webViewRef = useRef<WebView>(null);
  const [isVerifying, setIsVerifying] = useState(false);
  const [verified, setVerified] = useState(false);

  useFocusEffect(
    React.useCallback(() => {
      const onBackPress = () => {
        if (!verified && !isVerifying) {
          Alert.alert(
            'Cancel Payment?',
            'Are you sure you want to cancel this payment? Your order will not be processed.',
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

      BackHandler.addEventListener('hardwareBackPress', onBackPress);
      return () => BackHandler.removeEventListener('hardwareBackPress', onBackPress);
    }, [verified, isVerifying, navigation])
  );

  const handleVerifyPayment = async (ref: string) => {
    if (isVerifying || verified) return;
    setIsVerifying(true);
    try {
      await orderService.verifyPayment(ref);
      setVerified(true);
      Alert.alert(
        'Payment Successful!',
        'Your order has been confirmed.',
        [
          {
            text: 'View Orders',
            onPress: () => {
              navigation.navigate('MainTabs', { screen: 'Orders' });
            },
          },
        ]
      );
    } catch (error: any) {
      Alert.alert(
        'Verification Failed',
        error.response?.data?.message || 'Could not verify payment. Your order may still be pending.'
      );
    } finally {
      setIsVerifying(false);
    }
  };

  const handleNavigationStateChange = (navState: WebViewNavigation) => {
    const { url } = navState;

    if (url.includes('reference=') || url.includes('trxref=')) {
      const match = url.match(/reference=([^&]+)/);
      const ref = match?.[1] || reference;
      if (ref && webViewRef.current) {
        webViewRef.current.stopLoading();
        handleVerifyPayment(ref);
      }
    }
  };

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
        <Text style={styles.successSubtitle}>Your order is being processed.</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <WebView
        ref={webViewRef}
        source={{ uri: authorizationUrl }}
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
        sharedCookiesEnabled
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
