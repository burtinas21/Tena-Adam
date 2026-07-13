import DashboardLayout from "../layouts/DashboardLayout.vue";

export default [
  {
    path: "/platform",
    component: DashboardLayout,

    meta: {
      requiresAuth: true,
      roles: ["platform_admin"],
    },

    children: [
      {
        path: "dashboard",
        name: "platform-dashboard",
        component: () => import("../views/platform/Dashboard.vue"),
      },

      // {
      //   path: "hospitals",
      //   name: "platform-hospitals",
      //   component: () => import("../views/platform/Hospitals.vue"),
      // },

      {
        path: "hospital-admins",
        name: "platform-hospital-admins",
        component: () => import("../views/platform/HospitalAdmins.vue"),
      },

      {
        path: "doctor",
        name: "doctor",
        component: () => import("../views/platform/doctor.vue"),
      },
      {
        path: "hospitalnetwork",
        name: "hospitalnetwork",
        component: () => import("../views/platform/hospitalnetwork.vue"),
      },
    ],
  },
];
