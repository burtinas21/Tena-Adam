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

  {
    path: "/accept-invitation",
    name: "accept-invitation",
    component: () => import("../views/auth/AcceptInvitation.vue"),
    // No guest: true — this link must work whether the visitor is
    // logged-in (e.g. the platform admin who sent it tests the link)
    // or not (the new hospital admin who has no account yet).
    meta: {},
  },
];