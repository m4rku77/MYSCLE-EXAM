import { createRouter, createWebHistory } from "vue-router"

import MainLayout from "../layouts/MainLayout.vue"

import Dashboard from "../views/UserDashboardView.vue"
import WorkoutView from "../views/WorkoutView.vue"
import WorkoutTrackView from "../views/WorkoutTrackView.vue"
import Statistics from "../views/Statistics.vue"

const routes = [
  {
    path: '/',
    component: MainLayout,
    meta: { requiresAuth: true }, 
    children: [
      {
        path: '',
        redirect: '/dashboard'
      },
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
        path: 'statistics',
        component: Statistics
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

  if (to.path === "/login" && token) {
    return "/dashboard"
  }

  return true
})

export default router