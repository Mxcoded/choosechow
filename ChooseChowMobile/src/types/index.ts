// User Role Type
export type UserRole = 'customer' | 'chef' | 'admin';

// Spatie Role object (from Laravel)
export interface SpatieRole {
  id: number;
  name: string;
  guard_name?: string;
}

// Raw API User response (before processing)
export interface ApiUser {
  id: number;
  first_name: string;
  last_name: string;
  name?: string;
  email: string;
  phone?: string;
  avatar_url?: string;
  email_verified_at?: string;
  created_at: string;
  default_address?: Address;
  // Spatie roles - can be array of strings or role objects
  roles?: (string | SpatieRole)[];
  // Some Laravel APIs also include a single role field
  role?: string;
}

// Processed User type for the app
export interface User {
  id: number;
  first_name: string;
  last_name: string;
  name?: string; // Full name (computed)
  email: string;
  phone?: string;
  avatar_url?: string;
  email_verified_at?: string;
  created_at: string;
  default_address?: Address;
  role: UserRole; // Primary role for navigation (extracted from Spatie)
  roles?: string[]; // All role names (for permissions)
}

// Helper function to extract primary role from Spatie roles
export const extractPrimaryRole = (apiUser: ApiUser): UserRole => {
  // Priority: admin > chef > customer
  const roleNames: string[] = [];
  
  // Handle roles array (Spatie format)
  if (apiUser.roles && Array.isArray(apiUser.roles)) {
    apiUser.roles.forEach(role => {
      if (typeof role === 'string') {
        roleNames.push(role.toLowerCase());
      } else if (role && typeof role === 'object' && role.name) {
        roleNames.push(role.name.toLowerCase());
      }
    });
  }
  
  // Also check single role field
  if (apiUser.role) {
    roleNames.push(apiUser.role.toLowerCase());
  }
  
  // Determine primary role with priority
  if (roleNames.includes('admin') || roleNames.includes('super-admin')) {
    return 'admin';
  }
  if (roleNames.includes('chef') || roleNames.includes('vendor')) {
    return 'chef';
  }
  // Default to customer
  return 'customer';
};

// Helper to convert API user to app User
export const normalizeUser = (apiUser: ApiUser): User => {
  const roleNames: string[] = [];
  
  if (apiUser.roles && Array.isArray(apiUser.roles)) {
    apiUser.roles.forEach(role => {
      if (typeof role === 'string') {
        roleNames.push(role);
      } else if (role && typeof role === 'object' && role.name) {
        roleNames.push(role.name);
      }
    });
  }
  
  return {
    id: apiUser.id,
    first_name: apiUser.first_name,
    last_name: apiUser.last_name,
    name: apiUser.name,
    email: apiUser.email,
    phone: apiUser.phone,
    avatar_url: apiUser.avatar_url,
    email_verified_at: apiUser.email_verified_at,
    created_at: apiUser.created_at,
    default_address: apiUser.default_address,
    role: extractPrimaryRole(apiUser),
    roles: roleNames,
  };
};

export interface Address {
  id: number;
  user_id: number;
  label: string;
  street_address: string;
  apartment?: string;
  city: string;
  state: string;
  postal_code?: string;
  country: string;
  latitude?: number;
  longitude?: number;
  is_default: boolean;
  delivery_instructions?: string;
}

// Chef Types
export interface Chef {
  id: number;
  user_id: number;
  business_name: string;
  slug: string;
  description?: string;
  specialty?: string;
  logo_url?: string;
  banner_url?: string;
  rating: number;
  total_reviews: number;
  total_orders: number;
  minimum_order?: number;
  delivery_fee?: number;
  delivery_time?: string;
  is_available: boolean;
  is_verified: boolean;
  cuisines: Cuisine[];
  address?: Address;
  opening_hours?: OpeningHours[];
}

export interface OpeningHours {
  day: string;
  open_time: string;
  close_time: string;
  is_closed: boolean;
}

// Menu Types
export interface MenuItem {
  id: number;
  chef_id: number;
  name: string;
  slug: string;
  description?: string;
  price: number;
  sale_price?: number;
  image_url?: string;
  category?: string;
  is_available: boolean;
  is_featured: boolean;
  preparation_time?: number;
  calories?: number;
  dietary_info?: string[];
  allergens?: string[];
  ingredients?: string[];
  customizations?: MenuCustomization[];
}

export interface MenuCustomization {
  id: number;
  name: string;
  type: 'single' | 'multiple';
  required: boolean;
  options: CustomizationOption[];
}

export interface CustomizationOption {
  id: number;
  name: string;
  price: number;
}

// Cuisine Types
export interface Cuisine {
  id: number;
  name: string;
  slug: string;
  image_url?: string;
  description?: string;
}

export interface DietaryPreference {
  id: number;
  name: string;
  icon?: string;
}

// Cart Types
export interface Cart {
  id: number;
  user_id: number;
  chef_id?: number;
  chef?: Chef;
  items: CartItem[];
  subtotal: number;
  delivery_fee: number;
  service_fee: number;
  discount: number;
  total: number;
  coupon?: Coupon;
}

export interface CartItem {
  id: number;
  cart_id: number;
  menu_id: number;
  menu?: MenuItem;
  quantity: number;
  unit_price: number;
  total_price: number;
  special_instructions?: string;
  customizations?: SelectedCustomization[];
}

export interface SelectedCustomization {
  customization_id: number;
  option_ids: number[];
}

export interface Coupon {
  id: number;
  code: string;
  discount_type: 'percentage' | 'fixed';
  discount_value: number;
  minimum_order?: number;
}

// Order Types
export interface Order {
  id: number;
  order_number: string;
  user_id: number;
  chef_id: number;
  chef?: Chef;
  status: OrderStatus;
  payment_status: PaymentStatus;
  items: OrderItem[];
  delivery_address?: Address;
  subtotal: number;
  delivery_fee: number;
  service_fee: number;
  discount: number;
  total: number;
  special_instructions?: string;
  scheduled_for?: string;
  estimated_delivery?: string;
  delivered_at?: string;
  created_at: string;
  updated_at: string;
}

export type OrderStatus = 
  | 'pending_payment'
  | 'pending'
  | 'confirmed'
  | 'preparing'
  | 'ready'
  | 'out_for_delivery'
  | 'delivered'
  | 'cancelled';

export type PaymentStatus = 
  | 'pending'
  | 'paid'
  | 'failed'
  | 'refunded';

export interface OrderItem {
  id: number;
  order_id: number;
  menu_id: number;
  menu_name: string;
  quantity: number;
  unit_price: number;
  total_price: number;
  special_instructions?: string;
}

export interface OrderTracking {
  order_id: number;
  status: OrderStatus;
  timeline: TrackingEvent[];
  driver?: Driver;
  estimated_arrival?: string;
}

export interface TrackingEvent {
  status: OrderStatus;
  timestamp: string;
  description: string;
}

export interface Driver {
  id: number;
  name: string;
  phone: string;
  avatar_url?: string;
  current_location?: {
    latitude: number;
    longitude: number;
  };
}

// Review Types
export interface Review {
  id: number;
  user_id: number;
  user?: User;
  chef_id: number;
  order_id?: number;
  rating: number;
  comment?: string;
  images?: string[];
  chef_reply?: string;
  created_at: string;
}

// Payment Types
export interface PaymentIntent {
  reference: string;
  authorization_url: string;
  access_code: string;
}

export interface PaymentMethod {
  id: number;
  type: 'card' | 'bank';
  last_four?: string;
  brand?: string;
  bank_name?: string;
  is_default: boolean;
}

// Notification Types
export interface Notification {
  id: number;
  type: string;
  title: string;
  message: string;
  data?: Record<string, unknown>;
  read_at?: string;
  created_at: string;
}

// Authentication Types
export interface LoginCredentials {
  email: string;
  password: string;
  device_name?: string;
}

export interface RegisterData {
  first_name: string;
  last_name: string;
  email: string;
  phone: string;
  password: string;
  password_confirmation: string;
  role?: 'customer' | 'chef';
}

export interface AuthResponse {
  user: User;
  token: string;
  token_type: string;
}

// Subscription Plan Types
export type SubscriptionTier = 'none' | 'basic' | 'plus' | 'premium';
export type SubscriptionStatus = 'active' | 'cancelled' | 'expired' | 'past_due' | 'trialing' | 'none';

export interface SubscriptionPlan {
  id: number;
  name: string;
  slug: string;
  description: string;
  monthly_price: number;
  features: string[];
  is_popular?: boolean;
}

export interface SubscriptionStatusResponse {
  tier: SubscriptionTier;
  status: SubscriptionStatus;
  is_active: boolean;
  plan_name: string | null;
  plan_price: number;
  started_at: string | null;
  renews_at: string | null;
  cancelled_at: string | null;
  ends_at: string | null;
  free_delivery_used: number;
  free_delivery_limit: number;
  has_dedicated_rider: boolean;
  benefits: string[];
}

export type Period = 'monthly' | 'yearly';

// Search/Filter Types
export interface ChefFilters {
  cuisine_id?: number;
  min_rating?: number;
  max_delivery_fee?: number;
  is_available?: boolean;
  sort_by?: 'rating' | 'delivery_time' | 'distance' | 'popularity';
  latitude?: number;
  longitude?: number;
  radius?: number;
}

export interface MenuFilters {
  chef_id?: number;
  cuisine_id?: number;
  min_price?: number;
  max_price?: number;
  dietary?: string[];
  is_available?: boolean;
  search?: string;
}
