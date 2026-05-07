// @ts-ignore - resolved in frontend runtime dependency install
import React from 'react';
// @ts-ignore - resolved in frontend runtime dependency install
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './contexts/AuthContext';

// Pages
import DashboardPage from './pages/DashboardPage';

function AppRoutes() {
  const { user, loading } = useAuth();

  if (loading) {
    return <div>Loading...</div>;
  }

  return (
    <Routes>
      <Route path="/" element={user ? <Navigate to="/dashboard" /> : <div>Please sign in.</div>} />
      <Route path="/dashboard" element={user ? <DashboardPage /> : <Navigate to="/" />} />
      <Route path="*" element={<Navigate to="/" />} />
    </Routes>
  );
}

function App() {
  return (
    <AuthProvider>
      <Router>
        <div className="App">
          <AppRoutes />
        </div>
      </Router>
    </AuthProvider>
  );
}

export default App; 