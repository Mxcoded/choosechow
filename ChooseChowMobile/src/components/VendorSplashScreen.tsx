import React, { useEffect, useRef } from 'react';
import { View, Text, StyleSheet, Animated, Dimensions, Image } from 'react-native';
import { COLORS } from '../utils/theme';
import { ChooseChowLogo } from '../assets';

const { width, height } = Dimensions.get('window');

interface VendorSplashScreenProps {
  onFinish: () => void;
}

export const VendorSplashScreen: React.FC<VendorSplashScreenProps> = ({ onFinish }) => {
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const scaleAnim = useRef(new Animated.Value(0.8)).current;
  const slideUp = useRef(new Animated.Value(30)).current;
  const sparkle1 = useRef(new Animated.Value(0)).current;
  const sparkle2 = useRef(new Animated.Value(0)).current;
  const badgeRotate = useRef(new Animated.Value(0)).current;
  const glowScale = useRef(new Animated.Value(1)).current;

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
        Animated.timing(glowScale, {
          toValue: 1.12, duration: 1500, useNativeDriver: true,
        }),
        Animated.timing(glowScale, {
          toValue: 1, duration: 1500, useNativeDriver: true,
        }),
      ])
    ).start();

    Animated.loop(
      Animated.sequence([
        Animated.timing(sparkle1, {
          toValue: 1, duration: 1200, useNativeDriver: true,
        }),
        Animated.timing(sparkle1, {
          toValue: 0, duration: 1200, useNativeDriver: true,
        }),
      ])
    ).start();

    Animated.loop(
      Animated.sequence([
        Animated.timing(sparkle2, {
          toValue: 1, duration: 1500, useNativeDriver: true,
        }),
        Animated.timing(sparkle2, {
          toValue: 0, duration: 1500, useNativeDriver: true,
        }),
      ])
    ).start();

    Animated.loop(
      Animated.sequence([
        Animated.timing(badgeRotate, {
          toValue: 1, duration: 3000, useNativeDriver: true,
        }),
        Animated.timing(badgeRotate, {
          toValue: 0, duration: 3000, useNativeDriver: true,
        }),
      ])
    ).start();

    const timer = setTimeout(() => {
      Animated.timing(fadeAnim, {
        toValue: 0, duration: 400, useNativeDriver: true,
      }).start(() => onFinish());
    }, 2800);

    return () => clearTimeout(timer);
  }, []);

  const rotateInterpolation = badgeRotate.interpolate({
    inputRange: [0, 1],
    outputRange: ['-3deg', '3deg'],
  });

  return (
    <View style={styles.container}>
      <View style={styles.bgTop} />
      <View style={styles.bgBottom} />
      <View style={styles.bgDarkAccent} />

      <View style={styles.kitchenIconsTop}>
        <Animated.View style={{ opacity: sparkle1 }}>
          <Text style={styles.sparkleIcon}>✨</Text>
        </Animated.View>
      </View>

      <Animated.View
        style={[
          styles.content,
          { opacity: fadeAnim, transform: [{ scale: scaleAnim }, { translateY: slideUp }] },
        ]}
      >
        <View style={styles.logoContainer}>
          <Animated.View style={[styles.glowRing, { transform: [{ scale: glowScale }] }]} />
          <View style={styles.starDecorations}>
            <Text style={styles.starLeft}>★</Text>
            <Text style={styles.starRight}>★</Text>
          </View>
          <View style={styles.shieldOuter}>
            <View style={styles.ringInner} />
            <View style={styles.logoCircle}>
              <Image
                source={ChooseChowLogo}
                style={styles.logoImage}
                resizeMode="contain"
              />
            </View>
          </View>
          <Animated.View style={[styles.chefHat, { transform: [{ rotate: rotateInterpolation }] }]}>
            <Text style={styles.chefHatText}>👨‍🍳</Text>
          </Animated.View>
        </View>

        <Text style={styles.brandName}>choosechow</Text>
        <Text style={styles.tagline}>Grow Your Food Business</Text>
      </Animated.View>

      <View style={styles.kitchenToolsBottom}>
        <Animated.View style={{ opacity: sparkle2 }}>
          <Text style={styles.toolIcon}>🍳</Text>
        </Animated.View>
        <Text style={styles.toolIcon}>🥘</Text>
        <Text style={styles.toolIcon}>🔪</Text>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FCFAFA',
    justifyContent: 'center',
    alignItems: 'center',
    overflow: 'hidden',
  },
  bgTop: {
    position: 'absolute',
    top: -40,
    left: -100,
    width: width * 1.5,
    height: height * 0.4,
    backgroundColor: '#FBE9E7',
    borderBottomLeftRadius: 400,
    borderBottomRightRadius: 400,
    transform: [{ rotate: '-12deg' }],
  },
  bgBottom: {
    position: 'absolute',
    bottom: -60,
    right: -80,
    width: width * 0.9,
    height: height * 0.2,
    backgroundColor: '#FFCCBC',
    borderTopLeftRadius: 250,
    borderTopRightRadius: 250,
    transform: [{ rotate: '8deg' }],
  },
  bgDarkAccent: {
    position: 'absolute',
    bottom: height * 0.05,
    left: -width * 0.15,
    width: width * 0.5,
    height: width * 0.5,
    borderRadius: width * 0.25,
    backgroundColor: '#EF9A9A',
    opacity: 0.15,
  },
  kitchenIconsTop: {
    position: 'absolute',
    top: height * 0.12,
    right: 40,
  },
  sparkleIcon: {
    fontSize: 24,
  },
  content: {
    alignItems: 'center',
    zIndex: 10,
  },
  logoContainer: {
    position: 'relative',
    width: 180,
    height: 180,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
  },
  glowRing: {
    position: 'absolute',
    width: 180,
    height: 180,
    borderRadius: 90,
    backgroundColor: 'rgba(229, 57, 53, 0.08)',
  },
  starDecorations: {
    position: 'absolute',
    top: 8,
    left: 0,
    right: 0,
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingHorizontal: 10,
  },
  starLeft: {
    fontSize: 14,
    color: '#FFC107',
  },
  starRight: {
    fontSize: 14,
    color: '#FFC107',
  },
  shieldOuter: {
    width: 160,
    height: 160,
    borderRadius: 80,
    borderWidth: 3,
    borderColor: COLORS.primary,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    shadowColor: COLORS.primaryDark,
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.2,
    shadowRadius: 14,
    elevation: 8,
  },
  ringInner: {
    position: 'absolute',
    width: 148,
    height: 148,
    borderRadius: 74,
    borderWidth: 1.5,
    borderColor: 'rgba(229, 57, 53, 0.15)',
  },
  logoCircle: {
    width: 126,
    height: 126,
    borderRadius: 63,
    backgroundColor: '#FFFFFF',
    justifyContent: 'center',
    alignItems: 'center',
    overflow: 'hidden',
  },
  logoImage: {
    width: 100,
    height: 100,
  },
  chefHat: {
    position: 'absolute',
    top: -8,
    right: -10,
  },
  chefHatText: {
    fontSize: 32,
  },
  brandName: {
    fontSize: 30,
    fontWeight: '600',
    color: COLORS.primaryDark,
    letterSpacing: 2,
  },
  tagline: {
    fontSize: 14,
    color: COLORS.text.secondary,
    marginTop: 6,
    letterSpacing: 0.5,
  },
  kitchenToolsBottom: {
    position: 'absolute',
    bottom: height * 0.08,
    flexDirection: 'row',
    gap: 28,
    alignItems: 'center',
  },
  toolIcon: {
    fontSize: 28,
  },
});

export default VendorSplashScreen;
