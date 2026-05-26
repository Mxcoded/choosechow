import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
  Dimensions,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { COLORS } from '../../utils/theme';
import { adminService, ReportData } from '../../api';

type AdminReportsScreenProps = {
  navigation: NativeStackNavigationProp<any>;
};

type Period = 'week' | 'month' | 'year';

const { width } = Dimensions.get('window');

export const AdminReportsScreen: React.FC<AdminReportsScreenProps> = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  
  // State
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [selectedPeriod, setSelectedPeriod] = useState<Period>('month');
  const [overview, setOverview] = useState<{
    period: string;
    total_revenue: number;
    total_orders: number;
    average_order_value: number;
    new_users: number;
    new_vendors: number;
  } | null>(null);
  const [revenueReport, setRevenueReport] = useState<ReportData | null>(null);
  const [ordersReport, setOrdersReport] = useState<ReportData | null>(null);
  const [usersReport, setUsersReport] = useState<ReportData | null>(null);

  // Load reports
  const loadReports = async (period: Period) => {
    setIsLoading(true);
    try {
      const [overviewData, revenue, orders, users] = await Promise.all([
        adminService.getReportsOverview({ period }),
        adminService.getRevenueReport({ period }),
        adminService.getOrdersReport({ period }),
        adminService.getUsersReport({ period }),
      ]);
      
      setOverview(overviewData);
      setRevenueReport(revenue);
      setOrdersReport(orders);
      setUsersReport(users);
    } catch (err) {
      console.error('Failed to load reports:', err);
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    loadReports(selectedPeriod);
  }, [selectedPeriod]);

  const onRefresh = async () => {
    setRefreshing(true);
    await loadReports(selectedPeriod);
  };

  const formatCurrency = (amount: number | undefined | null) => {
    if (amount == null) return '₦0';
    if (amount >= 1000000) {
      return `₦${(amount / 1000000).toFixed(1)}M`;
    }
    if (amount >= 1000) {
      return `₦${(amount / 1000).toFixed(0)}K`;
    }
    return `₦${amount.toLocaleString()}`;
  };

  const getChangeColor = (change: number) => {
    if (change > 0) return '#10B981';
    if (change < 0) return '#EF4444';
    return '#6B7280';
  };

  const getChangeIcon = (change: number) => {
    if (change > 0) return '↑';
    if (change < 0) return '↓';
    return '→';
  };

  // Simple bar chart renderer
  const renderBarChart = (data: number[], labels: string[], color: string, maxBars: number = 7) => {
    const displayData = data.slice(-maxBars);
    const displayLabels = labels.slice(-maxBars);
    const maxValue = Math.max(...displayData, 1);
    const barWidth = (width - 80) / maxBars;

    return (
      <View style={styles.chartContainer}>
        <View style={styles.barsContainer}>
          {displayData.map((value, index) => (
            <View key={index} style={[styles.barWrapper, { width: barWidth }]}>
              <View 
                style={[
                  styles.bar, 
                  { 
                    height: Math.max((value / maxValue) * 100, 4),
                    backgroundColor: color,
                  }
                ]} 
              />
              <Text style={styles.barLabel}>{displayLabels[index]?.split(' ')[0]}</Text>
            </View>
          ))}
        </View>
      </View>
    );
  };

  const renderOverviewCard = () => {
    if (!overview) return null;

    return (
      <View style={styles.overviewContainer}>
        <View style={styles.overviewRow}>
          <View style={[styles.overviewCard, { backgroundColor: '#DBEAFE' }]}>
            <Text style={styles.overviewValue}>{formatCurrency(overview.total_revenue)}</Text>
            <Text style={styles.overviewLabel}>Total Revenue</Text>
          </View>
          <View style={[styles.overviewCard, { backgroundColor: '#D1FAE5' }]}>
            <Text style={styles.overviewValue}>{(overview.total_orders ?? 0).toLocaleString()}</Text>
            <Text style={styles.overviewLabel}>Total Orders</Text>
          </View>
        </View>
        <View style={styles.overviewRow}>
          <View style={[styles.overviewCard, { backgroundColor: '#FEF3C7' }]}>
            <Text style={styles.overviewValue}>{formatCurrency(overview.average_order_value)}</Text>
            <Text style={styles.overviewLabel}>Avg Order Value</Text>
          </View>
          <View style={[styles.overviewCard, { backgroundColor: '#EDE9FE' }]}>
            <Text style={styles.overviewValue}>{overview.new_users ?? 0}</Text>
            <Text style={styles.overviewLabel}>New Users</Text>
          </View>
        </View>
      </View>
    );
  };

  const renderReportCard = (
    title: string,
    icon: string,
    report: ReportData | null,
    color: string,
    formatValue: (val: number) => string = (v) => (v ?? 0).toLocaleString()
  ) => {
    if (!report) return null;

    const total = report.total ?? 0;
    const changePercentage = report.change_percentage ?? 0;
    const data = report.data ?? [];
    const labels = report.labels ?? [];

    return (
      <View style={styles.reportCard}>
        <View style={styles.reportHeader}>
          <View style={styles.reportTitleRow}>
            <Text style={styles.reportIcon}>{icon}</Text>
            <Text style={styles.reportTitle}>{title}</Text>
          </View>
          <View style={styles.reportStats}>
            <Text style={styles.reportTotal}>{formatValue(total)}</Text>
            <View style={[styles.changeBadge, { backgroundColor: getChangeColor(changePercentage) + '20' }]}>
              <Text style={[styles.changeText, { color: getChangeColor(changePercentage) }]}>
                {getChangeIcon(changePercentage)} {Math.abs(changePercentage)}%
              </Text>
            </View>
          </View>
        </View>
        {renderBarChart(data, labels, color)}
      </View>
    );
  };

  // Loading state
  if (isLoading && !overview) {
    return (
      <View style={[styles.container, styles.centerContent, { paddingTop: insets.top }]}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.loadingText}>Loading reports...</Text>
      </View>
    );
  }

  return (
    <View style={[styles.container, { paddingTop: insets.top }]}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity style={styles.backBtn} onPress={() => navigation.goBack()}>
          <Text style={styles.backIcon}>←</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Reports & Analytics</Text>
        <View style={styles.headerRight} />
      </View>

      {/* Period Selector */}
      <View style={styles.periodSelector}>
        {(['week', 'month', 'year'] as Period[]).map((period) => (
          <TouchableOpacity
            key={period}
            style={[
              styles.periodOption,
              selectedPeriod === period && styles.periodOptionActive,
            ]}
            onPress={() => setSelectedPeriod(period)}
          >
            <Text style={[
              styles.periodOptionText,
              selectedPeriod === period && styles.periodOptionTextActive,
            ]}>
              {period === 'week' ? 'This Week' : period === 'month' ? 'This Month' : 'This Year'}
            </Text>
          </TouchableOpacity>
        ))}
      </View>

      <ScrollView
        style={styles.scrollContainer}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />
        }
      >
        {/* Overview Cards */}
        {renderOverviewCard()}

        {/* Revenue Report */}
        {renderReportCard('Revenue', '💰', revenueReport, '#3B82F6', formatCurrency)}

        {/* Orders Report */}
        {renderReportCard('Orders', '📦', ordersReport, '#10B981')}

        {/* Users Report */}
        {renderReportCard('New Users', '👥', usersReport, '#8B5CF6')}

        <View style={styles.bottomPadding} />
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
  },
  centerContent: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    marginTop: 16,
    fontSize: 16,
    color: '#6B7280',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    paddingVertical: 12,
    backgroundColor: '#1F2937',
  },
  backBtn: {
    padding: 8,
  },
  backIcon: {
    fontSize: 24,
    color: '#FFFFFF',
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  headerRight: {
    width: 40,
  },
  periodSelector: {
    flexDirection: 'row',
    backgroundColor: '#FFFFFF',
    padding: 12,
    gap: 8,
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  periodOption: {
    flex: 1,
    paddingVertical: 10,
    borderRadius: 8,
    backgroundColor: '#F3F4F6',
    alignItems: 'center',
  },
  periodOptionActive: {
    backgroundColor: COLORS.primary,
  },
  periodOptionText: {
    fontSize: 13,
    fontWeight: '500',
    color: '#6B7280',
  },
  periodOptionTextActive: {
    color: '#FFFFFF',
  },
  scrollContainer: {
    flex: 1,
  },
  overviewContainer: {
    padding: 16,
    gap: 12,
  },
  overviewRow: {
    flexDirection: 'row',
    gap: 12,
  },
  overviewCard: {
    flex: 1,
    borderRadius: 12,
    padding: 16,
    alignItems: 'center',
  },
  overviewValue: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  overviewLabel: {
    fontSize: 12,
    color: '#6B7280',
    marginTop: 4,
  },
  reportCard: {
    backgroundColor: '#FFFFFF',
    marginHorizontal: 16,
    marginBottom: 16,
    borderRadius: 12,
    padding: 16,
  },
  reportHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  reportTitleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  reportIcon: {
    fontSize: 20,
  },
  reportTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#1F2937',
  },
  reportStats: {
    alignItems: 'flex-end',
  },
  reportTotal: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#1F2937',
  },
  changeBadge: {
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 12,
    marginTop: 4,
  },
  changeText: {
    fontSize: 11,
    fontWeight: '600',
  },
  chartContainer: {
    marginTop: 8,
  },
  barsContainer: {
    flexDirection: 'row',
    alignItems: 'flex-end',
    height: 120,
    paddingBottom: 20,
  },
  barWrapper: {
    alignItems: 'center',
    justifyContent: 'flex-end',
    height: '100%',
  },
  bar: {
    width: '60%',
    borderRadius: 4,
    minHeight: 4,
  },
  barLabel: {
    fontSize: 9,
    color: '#9CA3AF',
    marginTop: 4,
    position: 'absolute',
    bottom: 0,
  },
  bottomPadding: {
    height: 100,
  },
});

export default AdminReportsScreen;
