import { createRouter, createWebHistory } from "vue-router"
import LoginView from "../views/LoginView.vue"
import HomeView from '../views/HomeView.vue'
import UserDashboardView from "../views/UserDashboardView.vue"


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
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router