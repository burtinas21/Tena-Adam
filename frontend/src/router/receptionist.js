import DashboardLayout from "../layouts/DashboardLayout.vue";

export default [
  {
    path: "/receptionist",
    component: DashboardLayout,
    meta: {
      requiresAuth: true,
      roles: ["receptionist"],
    },
    children: [
      {
        path: "dashboard",
        name: "receptionist-dashboard",
        component: () => import("../views/receptionist/Dashboard.vue"),
      },
      {
        path: "appointments",
        name: "receptionist-appointments",
        component: () => import("../views/receptionist/appointments/CheckInAppointments.vue"),
      },
      {
        path: "queue",
        name: "receptionist-queue",
        component: () => import("../views/receptionist/queue/TodayQueue.vue"),
      },
      {
        // canonical path — sidebar uses this
        path: "registration",
        name: "receptionist-registration",
        component: () => import("../views/receptionist/Registartion.vue"),
      },
      {
        path: "profile",
        name: "receptionist-profile",
        component: () => import("../views/receptionist/Profile.vue"),
      },
    ],
  },
];
