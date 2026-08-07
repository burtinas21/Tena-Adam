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

      {
        path: "reports",

        name: "platform-reports",

        component: () => import("../views/platform/Reports.vue"),
      },
       {
        path: "analytics",
        name: "platform-analytics",
        component: () => import("../views/platform/Analytics.vue"),
      },
       {
        path: "notification",
        name: "platform-notification",
        component: () => import("../views/platform/Notifications.vue"),
      },
      {
        path: "notifications",
        name: "platform-notifications",
        component: () => import("../views/platform/Notifications.vue"),
      },
      {
        path: "role",
        name: "platform-role",
        component: () => import("../views/platform/RolesandPermission.vue"),
      },
       {
        path: "users",
        name: "platform-users",
        component: () => import("../views/platform/Users.vue"),
      },
       {
        path: "auditlogs",
        name: "auditlogs",
        component: () => import("../views/platform/auditLogs.vue"),
      },

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
      {
        path: "symptoms",
        name: "platform-symptoms",
        component: () => import("../views/hospital-admin/Symptoms.vue"),
      },
      {
        path: "appointments",
        name: "platform-appointments",
        component: () => import("../views/hospital-admin/appointments.vue"),
      },
      {
        path: "telemanagement",
        name: "platform-telemanagement",
        component: () => import("../views/hospital-admin/TelehealthManagement.vue"),
      },
      {
        path: "doctor-leaves",
        name: "platform-doctor-leaves",
        component: () => import("../views/hospital-admin/leaves.vue"),
      },
    ],
  },
];
