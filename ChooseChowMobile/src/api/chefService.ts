import { api, PaginatedResponse } from './client';
import { ENDPOINTS } from './config';
import { Chef, MenuItem, Review, ChefFilters, OpeningHours } from '../types';

export const chefService = {
  // Get list of chefs with optional filters
  getChefs: async (filters?: ChefFilters, page = 1): Promise<PaginatedResponse<Chef>> => {
    const response = await api.get<Chef[]>(ENDPOINTS.CHEFS.LIST, { ...filters, page });
    return response.data as unknown as PaginatedResponse<Chef>;
  },

  // Get chef details by ID
  getChef: async (id: number): Promise<Chef> => {
    const response = await api.get<Chef>(ENDPOINTS.CHEFS.DETAIL(id));
    return response.data.data;
  },

  // Search chefs by name or specialty
  searchChefs: async (query: string, filters?: ChefFilters): Promise<Chef[]> => {
    const response = await api.get<Chef[]>(ENDPOINTS.CHEFS.SEARCH, { q: query, ...filters });
    return response.data.data;
  },

  // Get nearby chefs
  getNearbyChefs: async (latitude: number, longitude: number, radius = 10): Promise<Chef[]> => {
    const response = await api.get<Chef[]>(ENDPOINTS.CHEFS.NEARBY, {
      latitude,
      longitude,
      radius,
    });
    return response.data.data;
  },

  // Get top rated chefs
  getTopRatedChefs: async (limit = 10): Promise<Chef[]> => {
    const response = await api.get<Chef[]>(ENDPOINTS.CHEFS.TOP_RATED, { limit });
    return response.data.data;
  },

  // Get chef's menus
  getChefMenus: async (chefId: number): Promise<MenuItem[]> => {
    const response = await api.get<MenuItem[]>(ENDPOINTS.CHEFS.MENUS(chefId));
    return response.data.data;
  },

  // Get chef's reviews
  getChefReviews: async (chefId: number, page = 1): Promise<PaginatedResponse<Review>> => {
    const response = await api.get<Review[]>(ENDPOINTS.CHEFS.REVIEWS(chefId), { page });
    return response.data as unknown as PaginatedResponse<Review>;
  },

  // Get chef's availability/opening hours
  getChefAvailability: async (chefId: number): Promise<OpeningHours[]> => {
    const response = await api.get<OpeningHours[]>(ENDPOINTS.CHEFS.AVAILABILITY(chefId));
    return response.data.data;
  },
};

export default chefService;
