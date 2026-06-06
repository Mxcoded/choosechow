import React, { useEffect, useRef } from 'react';
import { View, Text, StyleSheet, Animated, Dimensions, Image } from 'react-native';
import { COLORS } from '../utils/theme';
import { ChooseChowLogo } from '../assets';

const { width, height } = Dimensions.get('window');

interface FoodieSplashScreenProps {
  onFinish: () => void;
}

export const FoodieSplashScreen: React.FC<FoodieSplashScreenProps> = ({ onFinish }) => {
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const scaleAnim = useRef(new Animated.Value(0.8)).current;
  const slideUp = useRef(new Animated.Value(30)).current;
  const float1 = useRef(new Animated.Value(0)).current;
  const float2 = useRef(new Animated.Value(0)).current;
  const float3 = useRef(new Animated.Value(0)).current;
  const ringScale = useRef(new Animated.Value(1)).current;
  const steamOpacity = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    Animated.parallel([
      Animated.timing(fadeAnim, {
        toValue: 1, duration: 800, useNativeDriver: true,
      }),
      Animated.spring(scaleAnim, {
        toValue: 1, friction: 7, tension: 35, useNativeDriver: true,
      }),
      Animated.timing(slideUp, {
        toValue: 0, duration: 800, useNativeDriver: true,
      }),
    ]).start();

    Animated.loop(
      Animated.sequence([
        Animated.timing(ringScale, {
          toValue: 1.08, duration: 1200, useNativeDriver: true,
        }),
        Animated.timing(ringScale, {
          toValue: 1, duration: 1200, useNativeDriver: true,
        }),
      ])
    ).start();

    Animated.loop(
      Animated.sequence([
        Animated.timing(float1, {
          toValue: -20, duration: 2000, useNativeDriver: true,
        }),
        Animated.timing(float1, {
          toValue: 0, duration: 2000, useNativeDriver: true,
        }),
      ])
    ).start();

    Animated.loop(
      Animated.sequence([
        Animated.timing(float2, {
          toValue: 15, duration: 2500, useNativeDriver: true,
        }),
        Animated.timing(float2, {
          toValue: 0, duration: 2500, useNativeDriver: true,
        }),
      ])
    ).start();

    Animated.loop(
      Animated.sequence([
        Animated.timing(float3, {
          toValue: -12, duration: 1800, useNativeDriver: true,
        }),
        Animated.timing(float3, {
          toValue: 0, duration: 1800, useNativeDriver: true,
        }),
      ])
    ).start();

    Animated.sequence([
      Animated.delay(600),
      Animated.loop(
        Animated.sequence([
          Animated.timing(steamOpacity, {
            toValue: 0.6, duration: 1000, useNativeDriver: true,
          }),
          Animated.timing(steamOpacity, {
            toValue: 0, duration: 1000, useNativeDriver: true,
          }),
        ])
      ),
    ]).start();

    const timer = setTimeout(() => {
      Animated.timing(fadeAnim, {
        toValue: 0, duration: 400, useNativeDriver: true,
      }).start(() => onFinish());
    }, 2800);

    return () => clearTimeout(timer);
  }, []);

  return (
    <View style={styles.container}>
      <View style={styles.bgTop} />
      <View style={styles.bgBottom} />
      <View style={styles.bgAccent} />

      <View style={styles.foodIconsTop}>
        <Animated.Text style={[styles.floatingIcon, { transform: [{ translateY: float1 }] }]}>🍕</Animated.Text>
        <Animated.Text style={[styles.floatingIcon, styles.floatingIcon2, { transform: [{ translateY: float2 }] }]}>🥗</Animated.Text>
        <Animated.Text style={[styles.floatingIcon, styles.floatingIcon3, { transform: [{ translateY: float3 }] }]}>🌮</Animated.Text>
      </View>

      <Animated.View
        style={[
          styles.content,
          { opacity: fadeAnim, transform: [{ scale: scaleAnim }, { translateY: slideUp }] },
        ]}
      >
        <View style={styles.logoContainer}>
          <Animated.View style={[styles.ringOuter, { transform: [{ scale: ringScale }] }]} />
          <View style={styles.ringMiddle} />
          <View style={styles.logoCircle}>
            <Image
              source={ChooseChowLogo}
              style={styles.logoImage}
              resizeMode="contain"
            />
          </View>
          <Animated.View style={[styles.steamLeft, { opacity: steamOpacity }]}>
            <Text style={styles.steamText}>💨</Text>
          </Animated.View>
          <Animated.View style={[styles.steamRight, { opacity: steamOpacity }]}>
            <Text style={styles.steamText}>💨</Text>
          </Animated.View>
        </View>

        <Text style={styles.brandName}>choosechow</Text>
        <Text style={styles.tagline}>Discover Delicious Meals</Text>
      </Animated.View>

      <View style={styles.plateContainer}>
        <Text style={styles.plateIcon}>🍽️</Text>
      </View>

      <View style={styles.foodIconsBottom}>
        <Text style={styles.bottomIcon}>🍝</Text>
        <Text style={styles.bottomIcon}>🍔</Text>
        <Text style={styles.bottomIcon}>🍰</Text>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FFF8F0',
    justifyContent: 'center',
    alignItems: 'center',
    overflow: 'hidden',
  },
  bgTop: {
    position: 'absolute',
    top: -60,
    left: -80,
    width: width * 1.4,
    height: height * 0.45,
    backgroundColor: '#FFEBEE',
    borderBottomLeftRadius: 350,
    borderBottomRightRadius: 350,
    transform: [{ rotate: '-8deg' }],
  },
  bgBottom: {
    position: 'absolute',
    bottom: -80,
    right: -60,
    width: width * 0.7,
    height: height * 0.25,
    backgroundColor: '#FCE4EC',
    borderTopLeftRadius: 200,
    borderTopRightRadius: 200,
    transform: [{ rotate: '12deg' }],
  },
  bgAccent: {
    position: 'absolute',
    top: height * 0.3,
    left: -width * 0.3,
    width: width * 0.6,
    height: width * 0.6,
    borderRadius: width * 0.3,
    backgroundColor: '#FFF3E0',
    opacity: 0.5,
  },
  foodIconsTop: {
    position: 'absolute',
    top: height * 0.08,
    left: 0,
    right: 0,
    flexDirection: 'row',
    justifyContent: 'space-around',
    paddingHorizontal: 20,
  },
  floatingIcon: {
    fontSize: 28,
  },
  floatingIcon2: {
    marginTop: 20,
  },
  floatingIcon3: {
    marginTop: 10,
  },
  content: {
    alignItems: 'center',
    zIndex: 10,
  },
  logoContainer: {
    position: 'relative',
    width: 170,
    height: 170,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
  },
  ringOuter: {
    position: 'absolute',
    width: 170,
    height: 170,
    borderRadius: 85,
    borderWidth: 2,
    borderColor: 'rgba(229, 57, 53, 0.15)',
  },
  ringMiddle: {
    position: 'absolute',
    width: 148,
    height: 148,
    borderRadius: 74,
    borderWidth: 3,
    borderColor: COLORS.primary,
  },
  logoCircle: {
    width: 126,
    height: 126,
    borderRadius: 63,
    backgroundColor: '#FFFFFF',
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: COLORS.primary,
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.25,
    shadowRadius: 16,
    elevation: 10,
    overflow: 'hidden',
  },
  logoImage: {
    width: 100,
    height: 100,
  },
  steamLeft: {
    position: 'absolute',
    top: -20,
    left: 15,
  },
  steamRight: {
    position: 'absolute',
    top: -18,
    right: 15,
  },
  steamText: {
    fontSize: 20,
  },
  brandName: {
    fontSize: 30,
    fontWeight: '600',
    color: COLORS.primary,
    letterSpacing: 2,
  },
  tagline: {
    fontSize: 14,
    color: COLORS.text.secondary,
    marginTop: 6,
    letterSpacing: 0.5,
  },
  plateContainer: {
    position: 'absolute',
    bottom: height * 0.15,
  },
  plateIcon: {
    fontSize: 48,
  },
  foodIconsBottom: {
    position: 'absolute',
    bottom: height * 0.06,
    flexDirection: 'row',
    gap: 24,
  },
  bottomIcon: {
    fontSize: 24,
  },
});

export default FoodieSplashScreen;
