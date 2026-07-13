import DashboardLayout from "../layouts/DashboardLayout.vue";

export default [
  {
    path: "/doctor",

    component: DashboardLayout,

    meta: {
      requiresAuth: true,
      roles: ["doctor"],
    },

    children: [
      {
        path: "dashboard",

        name: "doctor-dashboard",

        component: () => import("../views/doctor/Dashboard.vue"),
      },

      {
        path: "appointments",

        name: "doctor-appointments",

        component: () => import("../views/doctor/appointment.vue"),
      },

      {
        path: "queue",

        name: "Doctor_Queue",

        component: () => import("../views/doctor/queue/MyQueue.vue"),
      },

      {
        path: "schedule",

        name: "doctor-schedule",

        component: () => import("../views/doctor/schedule.vue"),
      },
      {
        path: "profile",

        name: "doctor-profile",

        component: () => import("../views/doctor/profile/MyProfile.vue"),
      },
    ],
  },
];
