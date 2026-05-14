import { createRouter, createWebHistory } from "vue-router";

import UserLayout from "../layouts/UserLayout.vue";
import TrainerLayout from "../layouts/TrainerLayout.vue";
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

import ChooseModeView from "../views/ChooseModeView.vue";
import TrainerDashboard from "../views/trainer/TrainerDashboardView.vue";

import AdminDashboard from "../views/admin/AdminDashboard.vue";
import AdminUserDashboard from "../views/admin/AdminUserDashboard.vue";

import TrainerClient from "../views/trainer/TrainerClient.vue";
import TrainerClientWorkouts from "../views/trainer/TrainerClientWorkouts.vue";
import TrainerClientWorkoutDetail from "../views/trainer/TrainerClientWorkoutDetail.vue";

const routes = [
    { path: "/", component: HomeView },

    {
        path: "/login",
        component: () => import("../views/LoginView.vue"),
        meta: { guest: true },
    },

    {
        path: "/choose-mode",
        component: ChooseModeView,
        meta: { requiresAuth: true },
    },

    // standalone chat routes — no layout, no bottom nav
    {
        path: "/messages/:id",
        component: MessagesChatView,
        meta: { requiresAuth: true, role: "user" },
    },
    {
        path: "/trainer/messages/:id",
        component: MessagesChatView,
        meta: { requiresAuth: true, role: "trainer" },
    },
    {
        path: "/admin/messages/:id",
        component: MessagesChatView,
        meta: { requiresAuth: true, requiresAdmin: true },
    },

    // USER
    {
        path: "/",
        component: UserLayout,
        meta: { requiresAuth: true, role: "user" },
        children: [
            { path: "dashboard", component: Dashboard },
            { path: "workout/:id", component: WorkoutView },
            { path: "workout/:id/track", component: WorkoutTrackView },
            { path: "friends", component: FriendsView },
            { path: "statistics", component: Statistics },
            { path: "profile", component: Profile },
            { path: "create-workout", component: CreateWorkout },
            { path: "user/:id", component: UserProfile },
            { path: "messages", component: Messages },
        ],
    },

    // TRAINER
    {
        path: "/trainer",
        component: TrainerLayout,
        meta: { requiresAuth: true, role: "trainer" },
        children: [
            { path: "", component: TrainerDashboard },
            { path: "messages", component: Messages },
            { path: "profile", component: Profile },
            { path: "user/:id", component: UserProfile },
            { path: "client/:id", component: TrainerClient },
            { path: "client/:id/workouts", component: TrainerClientWorkouts },
            {
                path: "client/:id/workouts/:workoutId",
                component: TrainerClientWorkoutDetail,
            },
        ],
    },

    // ADMIN
    {
        path: "/admin",
        component: MainLayout,
        meta: { requiresAuth: true, requiresAdmin: true },
        children: [
            { path: "", component: AdminDashboard },
            { path: "users", component: AdminUserDashboard },
            { path: "profile", component: Profile },
            { path: "messages", component: Messages },
            { path: "statistics", component: Statistics },
            { path: "friends", component: FriendsView },
        ],
    },

    { path: "/:pathMatch(.*)*", redirect: "/login" },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    const token = localStorage.getItem("token");
    const role = localStorage.getItem("role");

    if (to.meta.requiresAuth && !token) {
        return "/login";
    }

    if (to.meta.requiresAdmin && role !== "admin") {
        if (role === "trainer") return "/trainer";
        return "/dashboard";
    }

    const requiredRole = to.matched.find((r) => r.meta.role)?.meta.role;

    if (requiredRole && requiredRole !== role) {
        if (role === "admin") return "/admin";
        if (role === "trainer") return "/trainer";
        return "/dashboard";
    }

    if (token && to.meta.guest) {
        if (role === "admin") return "/admin";
        if (role === "trainer") return "/trainer";
        return "/dashboard";
    }

    return true;
});

export default router;
