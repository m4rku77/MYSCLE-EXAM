# 💪 MYSCLE – Training & Fitness App

https://myscle-exam-amhj.vercel.app/

MYSCLE is a web application designed to help users plan their workouts, stay consistent, and improve their fitness over time.

The platform aims to provide a simple and intuitive environment where individuals can create and manage their own training routines, while trainers can organize, structure, and oversee workout plans for their clients.

The main idea behind this project is to remove unnecessary complexity and focus on what actually matters — clear training structure, consistency, and ease of use. Instead of overwhelming users with too many features, MYSCLE is built to be clean, practical, and efficient for everyday use.

As the project continues to develop, it is planned to evolve into a more complete fitness system, offering better progress tracking, improved trainer-client interaction, and more advanced training tools.

---

## 📸 Application Screenshots

### 🏠 Home View
<img width="840" height="500" alt="image" src="https://github.com/user-attachments/assets/cbb9b4f9-f338-419f-ba11-1c315da34a9c" />


The landing page introduces MYSCLE and provides access to authentication and core platform features.

---

### 👤 User Dashboard
<img width="840" height="500" alt="image" src="https://github.com/user-attachments/assets/8d077b30-6b60-41fc-a30c-a809ab2ad758" />


Users can view their workouts, training plans, and fitness progress from a centralized dashboard.

---

### 🏋️ Trainer View
<img width="840" height="500" alt="image" src="https://github.com/user-attachments/assets/16151caa-a33b-4e95-a551-e309b1654e11" />


Trainers can create and manage workout plans, organize exercises, and oversee their clients' training programs.

---

### ⚙️ Admin View
<img width="840" height="500" alt="image" src="https://github.com/user-attachments/assets/a82c84e4-fd62-4627-9eb8-081401f03287" />


Administrators can manage platform data, users, and system-wide functionality.

---

## 🚀 Vision

The goal of MYSCLE is to become a useful everyday tool for:

- 🏋️‍♂️ Planning workouts
- 📅 Staying consistent with training
- 📈 Tracking progress over time

In the future, the app could grow into a full fitness platform with advanced statistics, personalized plans, trainer-client collaboration, and detailed performance analytics.

---

## 🔧 Current Features

- User registration & login
- Role-based access (User, Trainer, Admin)
- Create and manage workout plans
- Add exercises to training plans
- Dashboard overview
- Workout tracking
- Basic statistics and progress monitoring

---

## 🛠 Tech Stack

- **Backend:** Laravel
- **Frontend:** Vue.js
- **Database:** MySQL
- **Authentication:** Laravel Sanctum / JWT (depending on implementation)
- **Styling:** Tailwind CSS

---

## ⚙️ Installation

### 1. Clone the project

```bash
git clone https://github.com/your-username/MYSCLE-EXAM.git
cd MYSCLE-EXAM
```

### 2. Setup Backend

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### 3. Setup Frontend

```bash
cd frontend
npm install
npm run dev
```

### 4. Access the Application

Backend:

```txt
http://localhost:8000
```

Frontend:

```txt
http://localhost:5173
```

---

## 📁 Project Structure

```txt
MYSCLE-EXAM/
│
├── app/                 # Laravel backend
├── database/            # Migrations & seeders
├── routes/              # API and web routes
├── frontend/            # Vue.js frontend
├── screenshots/         # README images
└── README.md
```

---

## 🎯 Future Improvements

- Advanced workout analytics
- Exercise history tracking
- Progress charts and statistics
- Trainer-client messaging
- Social features and friends system
- Mobile-friendly enhancements
- Personalized training recommendations

---

## 👨‍💻 Author

Developed as a fitness management platform using Laravel, Vue.js, and MySQL.
