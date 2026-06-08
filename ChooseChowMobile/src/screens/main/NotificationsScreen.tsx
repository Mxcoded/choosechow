import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Alert,
  ActivityIndicator,
  RefreshControl,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { customerService } from '../../api';
import type { Notification } from '../../api/customerService';
import { COLORS } from '../../utils/theme';
import MaterialCommunityIcons from '@expo/vector-icons/MaterialCommunityIcons';

const TYPE_ICONS: Record<string, string> = {
  order: 'food',
  promotion: 'tag-outline',
  system: 'cog-outline',
  review: 'star-outline',
};

const TYPE_COLORS: Record<string, string> = {
  order: COLORS.primary,
  promotion: '#F59E0B',
  system: COLORS.text.secondary,
  review: '#8B5CF6',
};

type NotificationsScreenProps = {
  navigation: NativeStackNavigationProp<any>;
};

export const NotificationsScreen: React.FC<NotificationsScreenProps> = ({ navigation }) => {
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const loadNotifications = useCallback(async () => {
    try {
      const result = await customerService.getNotifications();
      setNotifications(result.data ?? []);
    } catch {
      // silent
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => { loadNotifications(); }, [loadNotifications]);

  const handleMarkRead = async (id: number) => {
    try {
      await customerService.markNotificationAsRead(id);
      setNotifications((prev) =>
        prev.map((n) => (n.id === id ? { ...n, read_at: new Date().toISOString() } : n))
      );
    } catch { /* silent */ }
  };

  const handleMarkAllRead = async () => {
    try {
      await customerService.markAllNotificationsAsRead();
      setNotifications((prev) =>
        prev.map((n) => ({ ...n, read_at: n.read_at || new Date().toISOString() }))
      );
    } catch { Alert.alert('Error', 'Failed to mark all as read'); }
  };

  const unreadCount = notifications.filter((n) => !n.read_at).length;

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color={COLORS.primary} />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      {unreadCount > 0 && (
        <TouchableOpacity style={styles.markAllBar} onPress={handleMarkAllRead}>
          <MaterialCommunityIcons name="check-all" size={18} color={COLORS.primary} />
          <Text style={styles.markAllText}>Mark all as read ({unreadCount})</Text>
        </TouchableOpacity>
      )}
      <ScrollView
        contentContainerStyle={styles.content}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); loadNotifications(); }} />}
      >
        {notifications.length === 0 ? (
          <View style={styles.empty}>
            <MaterialCommunityIcons name="bell-off-outline" size={48} color={COLORS.text.light} />
            <Text style={styles.emptyTitle}>No Notifications</Text>
            <Text style={styles.emptySubtitle}>You're all caught up</Text>
          </View>
        ) : (
          notifications.map((n) => {
            const isUnread = !n.read_at;
            return (
              <TouchableOpacity
                key={n.id}
                style={[styles.notifCard, isUnread && styles.notifUnread]}
                onPress={() => handleMarkRead(n.id)}
                activeOpacity={0.7}
              >
                <View style={[styles.notifIcon, { backgroundColor: (TYPE_COLORS[n.type] || COLORS.text.secondary) + '20' }]}>
                  <MaterialCommunityIcons
                    name={(TYPE_ICONS[n.type] || 'bell') as any}
                    size={20}
                    color={TYPE_COLORS[n.type] || COLORS.text.secondary}
                  />
                </View>
                <View style={styles.notifContent}>
                  <View style={styles.notifHeader}>
                    <Text style={[styles.notifTitle, isUnread && styles.notifTitleUnread]} numberOfLines={1}>
                      {n.title}
                    </Text>
                    {isUnread && <View style={styles.unreadDot} />}
                  </View>
                  <Text style={styles.notifMessage} numberOfLines={2}>{n.message}</Text>
                  <Text style={styles.notifTime}>{n.time_ago || new Date(n.created_at).toLocaleDateString()}</Text>
                </View>
              </TouchableOpacity>
            );
          })
        )}
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: COLORS.background.secondary },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  content: { padding: 16 },
  empty: { alignItems: 'center', paddingVertical: 60 },
  emptyTitle: { fontSize: 18, fontWeight: 'bold', color: COLORS.text.primary, marginTop: 16 },
  emptySubtitle: { fontSize: 14, color: COLORS.text.secondary, marginTop: 4 },
  markAllBar: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center',
    paddingVertical: 12, backgroundColor: COLORS.primaryFaded, gap: 6,
  },
  markAllText: { fontSize: 14, color: COLORS.primary, fontWeight: '600' },
  notifCard: {
    flexDirection: 'row', backgroundColor: COLORS.white, padding: 14, borderRadius: 12,
    marginBottom: 10, borderWidth: 1, borderColor: COLORS.border.light,
  },
  notifUnread: { backgroundColor: COLORS.primaryFaded, borderColor: COLORS.primary + '30' },
  notifIcon: {
    width: 40, height: 40, borderRadius: 20,
    justifyContent: 'center', alignItems: 'center', marginRight: 12,
  },
  notifContent: { flex: 1 },
  notifHeader: { flexDirection: 'row', alignItems: 'center' },
  notifTitle: { fontSize: 15, fontWeight: '500', color: COLORS.text.primary, flex: 1 },
  notifTitleUnread: { fontWeight: '700' },
  unreadDot: { width: 8, height: 8, borderRadius: 4, backgroundColor: COLORS.primary, marginLeft: 8 },
  notifMessage: { fontSize: 13, color: COLORS.text.secondary, marginTop: 4, lineHeight: 18 },
  notifTime: { fontSize: 11, color: COLORS.text.light, marginTop: 6 },
});

export default NotificationsScreen;
