import React, { useEffect, useRef } from 'react';
import { View, Text, StyleSheet, Animated, Dimensions, Image } from 'react-native';
import { COLORS } from '../utils/theme';
import { ChooseChowLogo } from '../assets';

const { width, height } = Dimensions.get('window');

interface SplashScreenProps {
  onFinish: () => void;
}

export const SplashScreen: React.FC<SplashScreenProps> = ({ onFinish }) => {
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const scaleAnim = useRef(new Animated.Value(0.8)).current;
  const pulseAnim = useRef(new Animated.Value(1)).current;

  useEffect(() => {
    // Animate in
    Animated.parallel([
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 800,
        useNativeDriver: true,
      }),
      Animated.spring(scaleAnim, {
        toValue: 1,
        friction: 8,
        tension: 40,
        useNativeDriver: true,
      }),
    ]).start();

    // Pulse animation for the ring
    Animated.loop(
      Animated.sequence([
        Animated.timing(pulseAnim, {
          toValue: 1.1,
          duration: 1000,
          useNativeDriver: true,
        }),
        Animated.timing(pulseAnim, {
          toValue: 1,
          duration: 1000,
          useNativeDriver: true,
        }),
      ])
    ).start();

    // Auto finish after delay
    const timer = setTimeout(() => {
      Animated.timing(fadeAnim, {
        toValue: 0,
        duration: 400,
        useNativeDriver: true,
      }).start(() => {
        onFinish();
      });
    }, 2500);

    return () => clearTimeout(timer);
  }, []);

  return (
    <View style={styles.container}>
      {/* Background wave decoration */}
      <View style={styles.waveTop} />
      <View style={styles.waveBottom} />

      <Animated.View
        style={[
          styles.content,
          {
            opacity: fadeAnim,
            transform: [{ scale: scaleAnim }],
          },
        ]}
      >
        {/* Logo Circle */}
        <View style={styles.logoContainer}>
          {/* Animated outer ring */}
          <Animated.View 
            style={[
              styles.logoRingOuter,
              { transform: [{ scale: pulseAnim }] }
            ]} 
          />
          <View style={styles.logoRing} />
          <View style={styles.logoCircle}>
            <Image 
              source={ChooseChowLogo} 
              style={styles.logoImage}
              resizeMode="contain"
            />
          </View>
        </View>

        {/* Brand Name */}
        <Text style={styles.brandName}>choosechow</Text>
        
        {/* Small decoration */}
        <Text style={styles.decoration}>🌸</Text>
      </Animated.View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FFFFFF',
    justifyContent: 'center',
    alignItems: 'center',
  },
  waveTop: {
    position: 'absolute',
    top: -50,
    left: -100,
    width: width * 1.5,
    height: height * 0.5,
    backgroundColor: '#FFF0F0',
    borderBottomLeftRadius: 300,
    borderBottomRightRadius: 300,
    transform: [{ rotate: '-10deg' }],
  },
  waveBottom: {
    position: 'absolute',
    bottom: -100,
    right: -100,
    width: width * 0.8,
    height: height * 0.3,
    backgroundColor: '#FFF5F5',
    borderTopLeftRadius: 200,
    borderTopRightRadius: 200,
    transform: [{ rotate: '15deg' }],
  },
  content: {
    alignItems: 'center',
  },
  logoContainer: {
    position: 'relative',
    width: 160,
    height: 160,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 20,
  },
  logoRingOuter: {
    position: 'absolute',
    width: 160,
    height: 160,
    borderRadius: 80,
    borderWidth: 2,
    borderColor: 'rgba(229, 57, 53, 0.2)',
  },
  logoRing: {
    position: 'absolute',
    width: 140,
    height: 140,
    borderRadius: 70,
    borderWidth: 3,
    borderColor: COLORS.primary,
  },
  logoCircle: {
    width: 120,
    height: 120,
    borderRadius: 60,
    backgroundColor: '#FFFFFF',
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: COLORS.primary,
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.3,
    shadowRadius: 16,
    elevation: 10,
    overflow: 'hidden',
  },
  logoImage: {
    width: 100,
    height: 100,
  },
  brandName: {
    fontSize: 28,
    fontWeight: '600',
    color: COLORS.primary,
    letterSpacing: 2,
    marginTop: 8,
  },
  decoration: {
    fontSize: 16,
    marginTop: 8,
  },
});

export default SplashScreen;
