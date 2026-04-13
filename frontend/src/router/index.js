import { createRouter, createWebHistory } from "vue-router"

import MainLayout from "../layouts/MainLayout.vue"

import HomeView from "../views/HomeView.vue"
import Dashboard from "../views/UserDashboardView.vue"
import WorkoutView from "../views/WorkoutView.vue"
import WorkoutTrackView from "../views/WorkoutTrackView.vue"
import Statistics from "../views/Statistics.vue"
import FriendsView from "../views/FriendsView.vue"
import Profile from "../views/Profile.vue"
import CreateWorkout from "../views/CreateWorkout.vue"
import UserProfile from "../views/UserProfile.vue"

const routes = [

  {
    path: '/',
    component: HomeView
  },

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
      },
      {
        path: '/create-workout',
        component: CreateWorkout
      },
      {
        path: "/user/:id",
        name: "UserProfile",
        component: UserProfile
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

  if (to.meta.requiresAuth && !token) {
    return "/login"
  }

  if (to.path === "/" && token) {
    return "/dashboard"
  }

  return true
})

export default router