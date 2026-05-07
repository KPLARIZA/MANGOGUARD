# MangoGuard - IoT-Powered Mango Pest Detection System

A full-stack web-based system designed to support IoT-powered mango pest detection, analytics, and alerting.

## 🖥 System Overview

- **System Name**: MangoGuard
- **Purpose**: Monitor real-time data from smart insect traps via IoT, visualize pest trends, store sensor logs, and manage pest alerts
- **Users**: Admins, Farmers, Researchers

## 🏗 Project Structure

```
MangoGuard/
├── backend/                 # FastAPI Backend
│   ├── app/
│   │   ├── api/
│   │   ├── core/
│   │   ├── models/
│   │   ├── services/
│   │   └── main.py
│   ├── requirements.txt
│   └── Dockerfile
├── frontend/                # React Frontend
│   ├── public/
│   ├── src/
│   │   ├── components/
│   │   ├── pages/
│   │   ├── services/
│   │   └── utils/
│   ├── package.json
│   └── Dockerfile
├── docs/                    # Documentation
├── docker-compose.yml       # Development setup
└── README.md
```

## 🧰 Tech Stack

### Frontend
- **React 18** with TypeScript
- **TailwindCSS** for styling
- **Chart.js** for data visualization
- **Leaflet.js** for interactive maps
- **Firebase Auth** for authentication

### Backend
- **FastAPI** (Python) for API
- **Supabase** for structured data
- **InfluxDB** for time-series sensor data
- **Firebase Admin SDK** for auth verification

### Infrastructure
- **Docker** for containerization
- **Vercel** for frontend deployment
- **Render** for backend deployment
- **InfluxDB Cloud** for time-series data

## 🚀 Quick Start

### Prerequisites
- Node.js 18+
- Python 3.9+
- Docker & Docker Compose
- Firebase project
- Supabase project
- InfluxDB Cloud account

### Development Setup

1. **Clone the repository**
```bash
git clone <repository-url>
cd MangoGuard
```

2. **Backend Setup**
```bash
cd backend
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate
pip install -r requirements.txt
```

3. **Frontend Setup**
```bash
cd frontend
npm install
```

4. **Environment Configuration**
```bash
# Copy environment files
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env
```

5. **Start Development Servers**
```bash
# Backend (FastAPI)
cd backend
uvicorn app.main:app --reload --port 8000

# Frontend (React)
cd frontend
npm start
```

### Docker Setup

```bash
docker-compose up --build
```

## 📁 Features

### 1. Authentication & Authorization
- Firebase Auth integration
- Role-based access control (Admin, Farmer, Researcher)
- JWT token management

### 2. Dashboard
- Real-time pest activity monitoring
- Interactive trap location maps
- Pest trend visualizations
- Trap health monitoring

### 3. Trap Management
- IoT device registration and monitoring
- Battery level tracking
- Status monitoring (online/offline)
- Replacement requests

### 4. Analytics & Reporting
- Pest distribution heatmaps
- Historical trend analysis
- CSV export functionality
- ML classification statistics

### 5. Alert System
- Real-time pest threshold alerts
- Multi-channel notifications (Email, SMS, In-App)
- Customizable alert preferences

## 🔧 Configuration

### Environment Variables

#### Backend (.env)
```env
# Database
SUPABASE_URL=your_supabase_url
SUPABASE_KEY=your_supabase_anon_key
INFLUXDB_URL=your_influxdb_url
INFLUXDB_TOKEN=your_influxdb_token
INFLUXDB_ORG=your_influxdb_org
INFLUXDB_BUCKET=your_influxdb_bucket

# Firebase
FIREBASE_PROJECT_ID=your_firebase_project_id
FIREBASE_PRIVATE_KEY=your_firebase_private_key
FIREBASE_CLIENT_EMAIL=your_firebase_client_email

# Security
SECRET_KEY=your_secret_key
ALGORITHM=HS256
ACCESS_TOKEN_EXPIRE_MINUTES=30
```

#### Frontend (.env)
```env
REACT_APP_API_URL=http://localhost:8000
REACT_APP_FIREBASE_API_KEY=your_firebase_api_key
REACT_APP_FIREBASE_AUTH_DOMAIN=your_firebase_auth_domain
REACT_APP_FIREBASE_PROJECT_ID=your_firebase_project_id
REACT_APP_SUPABASE_URL=your_supabase_url
REACT_APP_SUPABASE_ANON_KEY=your_supabase_anon_key
```

## 📊 Database Schema

### Supabase Tables
- `users` - User profiles and roles
- `traps` - IoT device information
- `detections` - Pest detection records
- `alerts` - Alert configurations and history
- `farms` - Farm locations and details

### InfluxDB Measurements
- `pest_detections` - Time-series sensor data
- `trap_health` - Device health metrics
- `environmental_data` - Weather and environmental factors

## 🚀 Deployment

### Frontend (Vercel)
```bash
cd frontend
vercel --prod
```

### Backend (Render)
1. Connect your GitHub repository
2. Set environment variables
3. Deploy with Python runtime

### Database
- Supabase: Use Supabase Cloud
- InfluxDB: Use InfluxDB Cloud

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Contact the development team
- Check the documentation in `/docs`

## 🔮 Roadmap

- [ ] Mobile app development
- [ ] Advanced ML models
- [ ] Weather integration
- [ ] Multi-language support
- [ ] Advanced analytics dashboard
- [ ] API rate limiting
- [ ] WebSocket real-time updates
