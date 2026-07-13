export default [
  {
    path: "/login",
    name: "login",
    component: () => import("../views/auth/Login.vue"),
    meta: {
      guest: true,
    },
  },

  {
    path: "/register",
    name: "register",
    component: () => import("../views/auth/Register.vue"),
    meta: {
      guest: true,
    },
  },

  {
    path: "/forgot-password",
    name: "forgot-password",
    component: () => import("../views/auth/ForgotPassword.vue"),
    meta: {
      guest: true,
    },
  },

  {
    path: "/reset-password",
    name: "reset-password",
    component: () => import("../views/auth/ResetPassword.vue"),
    meta: {
      guest: true,
    },
  },
];