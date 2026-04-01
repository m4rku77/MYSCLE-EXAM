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
    children: [
      {
        path: '',
        component: Dashboard
      },
      {
        path: '/workout/:id',
        component: WorkoutView
      },
      {
        path: '/workout/:id/track',
        component: WorkoutTrackView
      },
      {
        path: '/statistics',
        component: Statistics
      }
    ]
  },

 
  {
    path: '/login',
    component: () => import('../views/LoginView.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router