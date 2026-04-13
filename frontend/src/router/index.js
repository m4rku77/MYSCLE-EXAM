import { createRouter, createWebHistory } from "vue-router"

import MainLayout from "../layouts/MainLayout.vue"

import HomeView from "../views/HomeView.vue"
import Dashboard from "../views/UserDashboardView.vue"
import WorkoutView from "../views/WorkoutView.vue"
import WorkoutTrackView from "../views/WorkoutTrackView.vue"
import Statistics from "../views/Statistics.vue"
import FriendsView from "../views/FriendsView.vue"
import Profile from "../views/Profile.vue"

const routes = [

  // 🔥 PUBLIC HOME PAGE
  {
    path: '/',
    component: HomeView
  },

  // 🔐 APP (AUTH REQUIRED)
  {
    path: '/',
    component: MainLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: 'dashboard',
        component: Dashboard
      },
      {
        path: 'workout/:id',
        component: WorkoutView
      },
      {
        path: 'workout/:id/track',
        component: WorkoutTrackView
      },
      {
        path: 'friends',
        component: FriendsView
      },
      {
        path: 'statistics',
        component: Statistics
      },
      {
        path: 'profile',
        component: Profile
      }
    ]
  },

  {
    path: '/login',
    component: () => import('../views/LoginView.vue'),
    meta: { guest: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to) => {
  const token = localStorage.getItem("token")

  // protect dashboard routes
  if (to.meta.requiresAuth && !token) {
    return "/login"
  }

  // if logged in and tries to go home → send to dashboard
  if (to.path === "/" && token) {
    return "/dashboard"
  }

  return true
})

export default router