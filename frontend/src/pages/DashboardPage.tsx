import React, { useState, useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';
import { api } from '../services/api';
import { toast } from 'react-hot-toast';

// Components
import PestActivityChart from '../components/charts/PestActivityChart';
import TrapMap from '../components/maps/TrapMap';
import StatsCard from '../components/dashboard/StatsCard';
import RecentAlerts from '../components/dashboard/RecentAlerts';
import TrapHealthOverview from '../components/dashboard/TrapHealthOverview';

// Icons
import { 
  BugAntIcon, 
  ExclamationTriangleIcon, 
  ChartBarIcon,
  MapPinIcon 
} from '@heroicons/react/24/outline';

interface DashboardStats {
  totalTraps: number;
  activeTraps: number;
  totalDetections: number;
  criticalAlerts: number;
  pestTrends: {
    cecidFly: number;
    fruitFly: number;
    leafHopper: number;
  };
}

const DashboardPage: React.FC = () => {
  const { user } = useAuth();
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [loading, setLoading] = useState(true);
  const [selectedTimeRange, setSelectedTimeRange] = useState('week');

  useEffect(() => {
    fetchDashboardData();
  }, [selectedTimeRange]);

  const fetchDashboardData = async () => {
    try {
      setLoading(true);
      const response = await api.get(`/dashboard/stats?time_range=${selectedTimeRange}`);
      setStats(response.data);
    } catch (error) {
      toast.error('Failed to load dashboard data');
      console.error('Error fetching dashboard data:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="bg-white rounded-lg shadow p-6">
        <h1 className="text-2xl font-bold text-gray-900">
          Welcome back, {user?.name}!
        </h1>
        <p className="text-gray-600 mt-1">
          Here's what's happening with your mango farm today.
        </p>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatsCard
          title="Total Traps"
          value={stats?.totalTraps || 0}
          icon={MapPinIcon}
          color="blue"
          trend="+2 this week"
        />
        <StatsCard
          title="Active Traps"
          value={stats?.activeTraps || 0}
          icon={BugAntIcon}
          color="green"
          trend="98% uptime"
        />
        <StatsCard
          title="Total Detections"
          value={stats?.totalDetections || 0}
          icon={ChartBarIcon}
          color="purple"
          trend="+15% from last week"
        />
        <StatsCard
          title="Critical Alerts"
          value={stats?.criticalAlerts || 0}
          icon={ExclamationTriangleIcon}
          color="red"
          trend="3 new alerts"
        />
      </div>

      {/* Main Content Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Pest Activity Chart */}
        <div className="lg:col-span-2 bg-white rounded-lg shadow p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold text-gray-900">Pest Activity Trends</h2>
            <select
              value={selectedTimeRange}
              onChange={(e) => setSelectedTimeRange(e.target.value)}
              className="border border-gray-300 rounded-md px-3 py-1 text-sm"
            >
              <option value="day">Last 24 Hours</option>
              <option value="week">Last Week</option>
              <option value="month">Last Month</option>
            </select>
          </div>
          <PestActivityChart timeRange={selectedTimeRange} />
        </div>

        {/* Recent Alerts */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Recent Alerts</h2>
          <RecentAlerts />
        </div>
      </div>

      {/* Trap Map and Health */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Interactive Trap Map */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Trap Locations</h2>
          <div className="h-80">
            <TrapMap />
          </div>
        </div>

        {/* Trap Health Overview */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Trap Health</h2>
          <TrapHealthOverview />
        </div>
      </div>

      {/* Pest Distribution */}
      <div className="bg-white rounded-lg shadow p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Pest Distribution</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div className="text-center p-4 bg-red-50 rounded-lg">
            <div className="text-2xl font-bold text-red-600">
              {stats?.pestTrends.cecidFly || 0}
            </div>
            <div className="text-sm text-gray-600">Cecid Fly</div>
          </div>
          <div className="text-center p-4 bg-yellow-50 rounded-lg">
            <div className="text-2xl font-bold text-yellow-600">
              {stats?.pestTrends.fruitFly || 0}
            </div>
            <div className="text-sm text-gray-600">Fruit Fly</div>
          </div>
          <div className="text-center p-4 bg-blue-50 rounded-lg">
            <div className="text-2xl font-bold text-blue-600">
              {stats?.pestTrends.leafHopper || 0}
            </div>
            <div className="text-sm text-gray-600">Leaf Hopper</div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default DashboardPage; 