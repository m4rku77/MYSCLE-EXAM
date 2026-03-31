import { createRouter, createWebHistory } from "vue-router"
import LoginView from "../views/LoginView.vue"
import HomeView from '../views/HomeView.vue'
import UserDashboardView from "../views/UserDashboardView.vue"
import WorkoutView from "../views/WorkoutView.vue"
import WorkoutTrackView from "../views/WorkoutTrackView.vue"


const routes = [
  {
    path: "/login",
    component: LoginView
  },
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: "/dashboard",
    component: UserDashboardView
  },
  {
    path: "/workout/:id",
    component: WorkoutView
  },
  {
  path: "/workout/:id/track",
  component: WorkoutTrackView
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router