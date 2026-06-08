import React, { useState, useEffect, useCallback } from 'react';
import {
  View, Text, StyleSheet, ScrollView, TouchableOpacity,
  RefreshControl, Image, ActivityIndicator, Alert, TextInput, Modal,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { COLORS } from '../../utils/theme';
import MaterialCommunityIcons from '@expo/vector-icons/MaterialCommunityIcons';
import { subscriptionService, Subscriber } from '../../api';
import { VendorStackParamList } from '../../navigation/types';

type Props = {
  navigation: NativeStackNavigationProp<VendorStackParamList, 'VendorSubscribers'>;
};

export const VendorSubscribersScreen: React.FC<Props> = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  const [subscribers, setSubscribers] = useState<Subscriber[]>([]);
  const [totalCount, setTotalCount] = useState(0);
  const [thisMonthCount, setThisMonthCount] = useState(0);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [showNotifyModal, setShowNotifyModal] = useState(false);
  const [notifyTitle, setNotifyTitle] = useState('');
  const [notifyMessage, setNotifyMessage] = useState('');
  const [notifyType, setNotifyType] = useState<'new_menu' | 'promotion' | 'announcement'>('announcement');
  const [sendingNotify, setSendingNotify] = useState(false);

  const fetchSubscribers = useCallback(async (pageNum = 1, isRefresh = false) => {
    try {
      const params: any = { page: pageNum, per_page: 20 };
      if (search.trim()) params.search = search.trim();
      const [subsRes, countRes] = await Promise.all([
        subscriptionService.getSubscribers(params),
        subscriptionService.getSubscriberCount(),
      ]);
      const newSubs = subsRes.data || [];
      if (isRefresh || pageNum === 1) {
        setSubscribers(newSubs);
      } else {
        setSubscribers(prev => [...prev, ...newSubs]);
      }
      setTotalCount(countRes.total);
      setThisMonthCount(countRes.this_month);
      setHasMore(newSubs.length >= 20);
      setPage(pageNum);
    } catch (error) {
      console.error('Failed to fetch subscribers:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [search]);

  useEffect(() => {
    fetchSubscribers();
  }, [fetchSubscribers]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchSubscribers(1, true);
  };

  const loadMore = () => {
    if (!loading && hasMore) {
      fetchSubscribers(page + 1);
    }
  };

  const handleSendNotification = async () => {
    if (!notifyTitle.trim() || !notifyMessage.trim()) {
      Alert.alert('Error', 'Please enter both title and message');
      return;
    }
    setSendingNotify(true);
    try {
      const result = await subscriptionService.notifySubscribers({
        title: notifyTitle.trim(),
        message: notifyMessage.trim(),
        type: notifyType,
      });
      setShowNotifyModal(false);
      setNotifyTitle('');
      setNotifyMessage('');
      Alert.alert('Success', `Notification sent to ${result.sent_count} subscriber(s)`);
    } catch (error) {
      Alert.alert('Error', 'Failed to send notification. Please try again.');
    } finally {
      setSendingNotify(false);
    }
  };

  const renderSubscriberItem = (sub: Subscriber) => (
    <View key={sub.id} style={styles.subscriberItem}>
      <View style={styles.subscriberAvatar}>
        {sub.avatar_url ? (
          <Image source={{ uri: sub.avatar_url }} style={styles.avatarImage} />
        ) : (
          <Text style={styles.avatarLetter}>
            {(sub.name || 'U').charAt(0).toUpperCase()}
          </Text>
        )}
      </View>
      <View style={styles.subscriberInfo}>
        <Text style={styles.subscriberName}>{sub.name}</Text>
        <Text style={styles.subscriberEmail}>{sub.email}</Text>
        <Text style={styles.subscriberDate}>
          Subscribed {new Date(sub.subscribed_at).toLocaleDateString()}
        </Text>
      </View>
      <View style={styles.subscriberPrefs}>
        {sub.notify_new_menu && (
          <View style={styles.prefBadge}>
            <MaterialCommunityIcons name="food" size={12} color={COLORS.primary} />
          </View>
        )}
        {sub.notify_promotions && (
          <View style={styles.prefBadge}>
            <MaterialCommunityIcons name="sale" size={12} color={COLORS.success} />
          </View>
        )}
      </View>
    </View>
  );

  if (loading) {
    return (
      <View style={[styles.container, styles.centered]}>
        <ActivityIndicator size="large" color={COLORS.primary} />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      {/* Header Stats */}
      <View style={[styles.statsContainer, { paddingTop: insets.top + 16 }]}>
        <View style={styles.statCard}>
          <Text style={styles.statNumber}>{totalCount}</Text>
          <Text style={styles.statLabel}>Total</Text>
        </View>
        <View style={styles.statCard}>
          <Text style={styles.statNumber}>{thisMonthCount}</Text>
          <Text style={styles.statLabel}>This Month</Text>
        </View>
        <TouchableOpacity
          style={styles.notifyButton}
          onPress={() => setShowNotifyModal(true)}
          activeOpacity={0.7}
        >
          <MaterialCommunityIcons name="bell-ring" size={20} color={COLORS.white} />
          <Text style={styles.notifyButtonText}>Notify All</Text>
        </TouchableOpacity>
      </View>

      {/* Search */}
      <View style={styles.searchContainer}>
        <MaterialCommunityIcons name="magnify" size={20} color={COLORS.gray[500]} />
        <TextInput
          style={styles.searchInput}
          placeholder="Search subscribers..."
          placeholderTextColor={COLORS.gray[400]}
          value={search}
          onChangeText={setSearch}
          onSubmitEditing={() => fetchSubscribers(1, true)}
        />
        {search.length > 0 && (
          <TouchableOpacity onPress={() => { setSearch(''); fetchSubscribers(1, true); }}>
            <MaterialCommunityIcons name="close" size={18} color={COLORS.gray[500]} />
          </TouchableOpacity>
        )}
      </View>

      {/* Subscriber List */}
      {subscribers.length === 0 ? (
        <View style={styles.emptyContainer}>
          <MaterialCommunityIcons name="account-multiple-outline" size={64} color={COLORS.gray[300]} />
          <Text style={styles.emptyTitle}>No Subscribers Yet</Text>
          <Text style={styles.emptySubtitle}>
            Customers who subscribe to your updates will appear here
          </Text>
        </View>
      ) : (
        <ScrollView
          showsVerticalScrollIndicator={false}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />
          }
          onScroll={({ nativeEvent }) => {
            const { contentOffset, contentSize, layoutMeasurement } = nativeEvent;
            if (contentOffset.y + layoutMeasurement.height >= contentSize.height - 100) {
              loadMore();
            }
          }}
          scrollEventThrottle={400}
          contentContainerStyle={{ paddingBottom: insets.bottom + 100 }}
        >
          {subscribers.map(renderSubscriberItem)}
          {hasMore && (
            <ActivityIndicator size="small" color={COLORS.primary} style={{ marginVertical: 16 }} />
          )}
        </ScrollView>
      )}

      {/* Notify All Modal */}
      <Modal visible={showNotifyModal} animationType="slide" transparent>
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, { paddingBottom: insets.bottom + 24 }]}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Notify Subscribers</Text>
              <TouchableOpacity onPress={() => setShowNotifyModal(false)}>
                <MaterialCommunityIcons name="close" size={24} color={COLORS.text.primary} />
              </TouchableOpacity>
            </View>

            <Text style={styles.modalLabel}>Notification Type</Text>
            <View style={styles.typeSelector}>
              {(['announcement', 'promotion', 'new_menu'] as const).map((type) => (
                <TouchableOpacity
                  key={type}
                  style={[
                    styles.typeOption,
                    notifyType === type && styles.typeOptionActive,
                  ]}
                  onPress={() => setNotifyType(type)}
                >
                  <Text style={[
                    styles.typeOptionText,
                    notifyType === type && styles.typeOptionTextActive,
                  ]}>
                    {type === 'new_menu' ? 'New Menu' : type.charAt(0).toUpperCase() + type.slice(1)}
                  </Text>
                </TouchableOpacity>
              ))}
            </View>

            <Text style={styles.modalLabel}>Title</Text>
            <TextInput
              style={styles.modalInput}
              placeholder="e.g. New Menu Available!"
              placeholderTextColor={COLORS.gray[400]}
              value={notifyTitle}
              onChangeText={setNotifyTitle}
              maxLength={255}
            />

            <Text style={styles.modalLabel}>Message</Text>
            <TextInput
              style={[styles.modalInput, styles.modalTextArea]}
              placeholder="Write your message..."
              placeholderTextColor={COLORS.gray[400]}
              value={notifyMessage}
              onChangeText={setNotifyMessage}
              multiline
              maxLength={1000}
            />

            <Text style={styles.modalHint}>
              This notification will be sent to {totalCount} subscriber(s)
            </Text>

            <TouchableOpacity
              style={[styles.sendButton, sendingNotify && styles.sendButtonDisabled]}
              onPress={handleSendNotification}
              disabled={sendingNotify}
              activeOpacity={0.7}
            >
              {sendingNotify ? (
                <ActivityIndicator size="small" color={COLORS.white} />
              ) : (
                <>
                  <MaterialCommunityIcons name="send" size={18} color={COLORS.white} />
                  <Text style={styles.sendButtonText}>Send Notification</Text>
                </>
              )}
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background.secondary,
  },
  centered: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  statsContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingBottom: 16,
    backgroundColor: COLORS.white,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.border.light,
    gap: 12,
  },
  statCard: {
    flex: 1,
    backgroundColor: COLORS.background.secondary,
    borderRadius: 12,
    padding: 14,
    alignItems: 'center',
  },
  statNumber: {
    fontSize: 24,
    fontWeight: 'bold',
    color: COLORS.text.primary,
  },
  statLabel: {
    fontSize: 12,
    color: COLORS.text.secondary,
    marginTop: 2,
  },
  notifyButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.primary,
    paddingHorizontal: 14,
    paddingVertical: 12,
    borderRadius: 12,
    gap: 6,
  },
  notifyButtonText: {
    color: COLORS.white,
    fontSize: 13,
    fontWeight: '600',
  },
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.white,
    margin: 16,
    paddingHorizontal: 14,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: COLORS.border.light,
    gap: 8,
  },
  searchInput: {
    flex: 1,
    paddingVertical: 12,
    fontSize: 15,
    color: COLORS.text.primary,
  },
  subscriberItem: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.white,
    marginHorizontal: 16,
    marginBottom: 8,
    padding: 14,
    borderRadius: 12,
    gap: 12,
  },
  subscriberAvatar: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: COLORS.primaryFaded,
    justifyContent: 'center',
    alignItems: 'center',
  },
  avatarImage: {
    width: 44,
    height: 44,
    borderRadius: 22,
  },
  avatarLetter: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  subscriberInfo: {
    flex: 1,
  },
  subscriberName: {
    fontSize: 15,
    fontWeight: '600',
    color: COLORS.text.primary,
  },
  subscriberEmail: {
    fontSize: 12,
    color: COLORS.text.secondary,
    marginTop: 1,
  },
  subscriberDate: {
    fontSize: 11,
    color: COLORS.gray[400],
    marginTop: 2,
  },
  subscriberPrefs: {
    flexDirection: 'row',
    gap: 4,
  },
  prefBadge: {
    width: 24,
    height: 24,
    borderRadius: 12,
    backgroundColor: COLORS.background.secondary,
    justifyContent: 'center',
    alignItems: 'center',
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 40,
  },
  emptyTitle: {
    fontSize: 18,
    fontWeight: '600',
    color: COLORS.text.primary,
    marginTop: 16,
  },
  emptySubtitle: {
    fontSize: 14,
    color: COLORS.text.secondary,
    textAlign: 'center',
    marginTop: 8,
    lineHeight: 20,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'flex-end',
  },
  modalContent: {
    backgroundColor: COLORS.white,
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    padding: 24,
    maxHeight: '85%',
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20,
  },
  modalTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: COLORS.text.primary,
  },
  modalLabel: {
    fontSize: 14,
    fontWeight: '600',
    color: COLORS.text.primary,
    marginBottom: 8,
    marginTop: 4,
  },
  typeSelector: {
    flexDirection: 'row',
    gap: 8,
    marginBottom: 16,
  },
  typeOption: {
    flex: 1,
    paddingVertical: 10,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: COLORS.border.light,
    alignItems: 'center',
  },
  typeOptionActive: {
    borderColor: COLORS.primary,
    backgroundColor: COLORS.primaryFaded,
  },
  typeOptionText: {
    fontSize: 13,
    color: COLORS.text.secondary,
    fontWeight: '500',
  },
  typeOptionTextActive: {
    color: COLORS.primary,
    fontWeight: '700',
  },
  modalInput: {
    borderWidth: 1,
    borderColor: COLORS.border.light,
    borderRadius: 10,
    padding: 14,
    fontSize: 15,
    color: COLORS.text.primary,
    marginBottom: 12,
  },
  modalTextArea: {
    height: 100,
    textAlignVertical: 'top',
  },
  modalHint: {
    fontSize: 12,
    color: COLORS.text.secondary,
    marginBottom: 16,
    textAlign: 'center',
  },
  sendButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: COLORS.primary,
    paddingVertical: 14,
    borderRadius: 12,
    gap: 8,
  },
  sendButtonDisabled: {
    opacity: 0.7,
  },
  sendButtonText: {
    color: COLORS.white,
    fontSize: 16,
    fontWeight: '700',
  },
});

export default VendorSubscribersScreen;
