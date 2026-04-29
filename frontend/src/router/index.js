import { createRouter, createWebHistory } from "vue-router";

import MainLayout from "../layouts/MainLayout.vue";

import HomeView from "../views/HomeView.vue";
import Dashboard from "../views/UserDashboardView.vue";
import WorkoutView from "../views/WorkoutView.vue";
import WorkoutTrackView from "../views/WorkoutTrackView.vue";
import Statistics from "../views/Statistics.vue";
import FriendsView from "../views/FriendsView.vue";
import Profile from "../views/Profile.vue";
import CreateWorkout from "../views/CreateWorkout.vue";
import UserProfile from "../views/UserProfile.vue";
import Messages from "../views/MessagesView.vue";
import MessagesChatView from "../views/MessagesChatView.vue";

import AdminDashboard from "../views/admin/AdminDashboard.vue";
import AdminUserDashboard from "../views/admin/AdminUserDashboard.vue";

const routes = [
    {
        path: "/",
        component: HomeView,
    },

    {
        path: "/messages/:id",
        component: MessagesChatView,
        meta: { requiresAuth: true },
    },

    {
        path: "/",
        component: MainLayout,
        meta: { requiresAuth: true },
        children: [
            { path: "dashboard", component: Dashboard },
            { path: "workout/:id", component: WorkoutView },
            { path: "workout/:id/track", component: WorkoutTrackView },
            { path: "friends", component: FriendsView },
            { path: "statistics", component: Statistics },
            { path: "profile", component: Profile },
            { path: "create-workout", component: CreateWorkout },
            { path: "user/:id", name: "UserProfile", component: UserProfile },

            { path: "messages", component: Messages },
        ],
    },

    {
        path: "/admin",
        component: AdminDashboard,
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: "/admin/users",
        component: AdminUserDashboard,
        meta: { requiresAuth: true, requiresAdmin: true },
    },

    {
        path: "/login",
        component: () => import("../views/LoginView.vue"),
        meta: { guest: true },
    },

    {
        path: "/:pathMatch(.*)*",
        redirect: "/login",
    },
];
const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    const token = localStorage.getItem("token");
    const role = localStorage.getItem("role");

    const requiresAuth = to.matched.some((r) => r.meta.requiresAuth);
    const requiresAdmin = to.matched.some((r) => r.meta.requiresAdmin);
    const isGuest = to.matched.some((r) => r.meta.guest);

    if (requiresAuth && !token) return "/login";

    if (requiresAdmin && role !== "admin") return "/dashboard";

    if (token && isGuest) {
        return role === "admin" ? "/admin" : "/dashboard";
    }

    if (to.path === "/" && token) {
        return role === "admin" ? "/admin" : "/dashboard";
    }

    return true;
});

export default router;
