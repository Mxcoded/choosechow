import React from 'react';
import { View, Text, StyleSheet, Image } from 'react-native';
import { COLORS } from '../utils/theme';
import { ChooseChowLogo } from '../assets';

interface LogoProps {
  size?: 'small' | 'medium' | 'large' | 'xlarge';
  showText?: boolean;
  variant?: 'light' | 'dark';
  imageOnly?: boolean;
}

export const Logo: React.FC<LogoProps> = ({ 
  size = 'medium', 
  showText = true,
  variant = 'dark',
  imageOnly = false,
}) => {
  const dimensions = {
    small: { logo: 40, text: 16, tagline: 10 },
    medium: { logo: 70, text: 24, tagline: 12 },
    large: { logo: 100, text: 32, tagline: 14 },
    xlarge: { logo: 140, text: 36, tagline: 16 },
  };

  const dims = dimensions[size];
  const textColor = variant === 'dark' ? COLORS.primary : COLORS.white;
  const taglineColor = variant === 'dark' ? COLORS.text.secondary : 'rgba(255,255,255,0.9)';

  // If imageOnly, just return the logo image
  if (imageOnly) {
    return (
      <Image 
        source={ChooseChowLogo} 
        style={{ width: dims.logo, height: dims.logo }}
        resizeMode="contain"
      />
    );
  }

  return (
    <View style={styles.container}>
      {/* Logo Image */}
      <View style={styles.logoContainer}>
        <Image 
          source={ChooseChowLogo} 
          style={[
            styles.logoImage,
            { width: dims.logo, height: dims.logo }
          ]}
          resizeMode="contain"
        />
      </View>

      {/* Brand Text */}
      {showText && (
        <View style={styles.textContainer}>
          <Text style={[styles.brandName, { fontSize: dims.text, color: textColor }]}>
            ChooseChow
          </Text>
          {size !== 'small' && (
            <Text style={[styles.tagline, { fontSize: dims.tagline, color: taglineColor }]}>
              Bespoke Cuisine at Your Doorstep
            </Text>
          )}
        </View>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    alignItems: 'center',
  },
  logoContainer: {
    marginBottom: 12,
  },
  logoImage: {
    // dimensions set dynamically
  },
  textContainer: {
    alignItems: 'center',
  },
  brandName: {
    fontWeight: 'bold',
    letterSpacing: 0.5,
  },
  tagline: {
    marginTop: 4,
    fontWeight: '500',
  },
});

export default Logo;
