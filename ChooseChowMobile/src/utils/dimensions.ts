import { Dimensions, PixelRatio } from 'react-native';

// Design reference dimensions (from Figma)
const DESIGN_WIDTH = 480; // Base width for scaling - adjust as needed based on your design
const DESIGN_HEIGHT = 812; // Base height for scaling - adjust as needed based on your design

// Get device dimensions
const { width: SCREEN_WIDTH, height: SCREEN_HEIGHT } = Dimensions.get('window');

// Scale factors
const widthScale = SCREEN_WIDTH / DESIGN_WIDTH;
const heightScale = SCREEN_HEIGHT / DESIGN_HEIGHT;

/**
 * Scale a value horizontally based on screen width
 * Use for horizontal spacing, widths, horizontal padding/margins
 */
export const scaleWidth = (size: number): number => {
  return Math.round(size * widthScale);
};

/**
 * Scale a value vertically based on screen height
 * Use for vertical spacing, heights, vertical padding/margins
 */
export const scaleHeight = (size: number): number => {
  return Math.round(size * heightScale);
};

/**
 * Scale font size - uses width scale for consistency
 * Ensures text doesn't get too small on narrow screens
 */
export const scaleFont = (size: number): number => {
  const newSize = size * widthScale;
  return Math.round(PixelRatio.roundToNearestPixel(newSize));
};

/**
 * Moderate scale - blend between width and height scale
 * Good for elements that should scale proportionally
 * @param size - the design size
 * @param factor - how much to favor height scale (0 = width only, 1 = height only)
 */
export const moderateScale = (size: number, factor: number = 0.5): number => {
  return Math.round(size + (scaleWidth(size) - size) * factor);
};

// Export screen dimensions
export const screenWidth = SCREEN_WIDTH;
export const screenHeight = SCREEN_HEIGHT;

// Common responsive values
export const responsive = {
  // Spacing
  xs: scaleWidth(4),
  sm: scaleWidth(8),
  md: scaleWidth(12),
  lg: scaleWidth(16),
  xl: scaleWidth(20),
  xxl: scaleWidth(24),
  
  // Border radius
  radiusSm: scaleWidth(8),
  radiusMd: scaleWidth(12),
  radiusLg: scaleWidth(16),
  radiusXl: scaleWidth(20),
  radiusFull: scaleWidth(100),
  
  // Icon sizes
  iconSm: scaleWidth(16),
  iconMd: scaleWidth(24),
  iconLg: scaleWidth(32),
  iconXl: scaleWidth(48),
};

export default {
  scaleWidth,
  scaleHeight,
  scaleFont,
  moderateScale,
  screenWidth,
  screenHeight,
  responsive,
  DESIGN_WIDTH,
  DESIGN_HEIGHT,
};
