import React, { useRef, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  Dimensions,
  TouchableOpacity,
  FlatList,
  Animated,
  ViewToken,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { COLORS } from '../../utils/theme';

const { width, height } = Dimensions.get('window');

type OnboardingScreenProps = {
  navigation: NativeStackNavigationProp<any>;
};

interface OnboardingSlide {
  id: string;
  illustration: string;
  title: string;
  subtitle?: string;
}

const slides: OnboardingSlide[] = [
  {
    id: '1',
    illustration: '🍳🥕🔪',
    title: 'Easily prepare your\nfavourite dishes',
  },
  {
    id: '2',
    illustration: '👨‍🍳📚🍲',
    title: 'Easy Learning from\nprofessional chefs',
  },
  {
    id: '3',
    illustration: '🍯🥘✨',
    title: 'Cooking made easy',
  },
  {
    id: '4',
    illustration: '🏪🛒🍽️',
    title: 'Order Food and\nIngredients',
  },
];

const OnboardingSlideItem: React.FC<{ item: OnboardingSlide }> = ({ item }) => {
  return (
    <View style={styles.slideContainer}>
      {/* Background wave decoration */}
      <View style={styles.waveBackground} />
      
      {/* Illustration */}
      <View style={styles.illustrationContainer}>
        <Text style={styles.illustration}>{item.illustration}</Text>
      </View>

      {/* Title */}
      <Text style={styles.slideTitle}>{item.title}</Text>
    </View>
  );
};

export const OnboardingScreen: React.FC<OnboardingScreenProps> = ({ navigation }) => {
  const [currentIndex, setCurrentIndex] = useState(0);
  const flatListRef = useRef<FlatList>(null);
  const scrollX = useRef(new Animated.Value(0)).current;

  const viewableItemsChanged = useRef(
    ({ viewableItems }: { viewableItems: ViewToken[] }) => {
      if (viewableItems[0]) {
        setCurrentIndex(Number(viewableItems[0].index) || 0);
      }
    }
  ).current;

  const viewConfig = useRef({ viewAreaCoveragePercentThreshold: 50 }).current;

  const handleNext = () => {
    if (currentIndex < slides.length - 1) {
      flatListRef.current?.scrollToIndex({ index: currentIndex + 1 });
    } else {
      // Go to Welcome/Role selection screen
      navigation.replace('Welcome');
    }
  };

  const handleSkip = () => {
    navigation.replace('Welcome');
  };

  return (
    <View style={styles.container}>
      <FlatList
        ref={flatListRef}
        data={slides}
        renderItem={({ item }) => <OnboardingSlideItem item={item} />}
        horizontal
        showsHorizontalScrollIndicator={false}
        pagingEnabled
        bounces={false}
        keyExtractor={(item) => item.id}
        onScroll={Animated.event(
          [{ nativeEvent: { contentOffset: { x: scrollX } } }],
          { useNativeDriver: false }
        )}
        onViewableItemsChanged={viewableItemsChanged}
        viewabilityConfig={viewConfig}
        scrollEventThrottle={32}
      />

      {/* Pagination dots */}
      <View style={styles.paginationContainer}>
        {slides.map((_, index) => (
          <View
            key={index}
            style={[
              styles.dot,
              currentIndex === index ? styles.dotActive : styles.dotInactive,
            ]}
          />
        ))}
      </View>

      {/* Navigation buttons */}
      <View style={styles.buttonContainer}>
        <TouchableOpacity onPress={handleSkip} style={styles.skipButton}>
          <Text style={styles.skipText}>Skip</Text>
        </TouchableOpacity>

        <TouchableOpacity onPress={handleNext} style={styles.nextButton}>
          <Text style={styles.nextText}>
            {currentIndex === slides.length - 1 ? 'Get Started' : 'Next'}
          </Text>
        </TouchableOpacity>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FFFFFF',
  },
  slideContainer: {
    width,
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 40,
  },
  waveBackground: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    height: height * 0.35,
    backgroundColor: '#FFF5F5',
    borderBottomLeftRadius: 100,
    borderBottomRightRadius: 100,
  },
  illustrationContainer: {
    width: 200,
    height: 200,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 60,
  },
  illustration: {
    fontSize: 72,
    textAlign: 'center',
  },
  slideTitle: {
    fontSize: 24,
    fontWeight: '600',
    color: '#1F2937',
    textAlign: 'center',
    lineHeight: 34,
  },
  paginationContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 20,
  },
  dot: {
    height: 4,
    borderRadius: 2,
    marginHorizontal: 4,
  },
  dotActive: {
    width: 24,
    backgroundColor: COLORS.primary,
  },
  dotInactive: {
    width: 8,
    backgroundColor: '#E5E7EB',
  },
  buttonContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 30,
    paddingBottom: 50,
  },
  skipButton: {
    padding: 12,
  },
  skipText: {
    fontSize: 16,
    color: '#9CA3AF',
    fontWeight: '500',
  },
  nextButton: {
    padding: 12,
  },
  nextText: {
    fontSize: 16,
    color: COLORS.primary,
    fontWeight: '600',
  },
});

export default OnboardingScreen;
