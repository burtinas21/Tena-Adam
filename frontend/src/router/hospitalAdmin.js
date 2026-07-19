import DashboardLayout from "../layouts/DashboardLayout.vue";

export default [
  {
    path: "/hospital-admin",

    component: DashboardLayout,

    meta: {
      requiresAuth: true,

      roles: ["hospital_admin"],
    },

    children: [
      {
        path: "dashboard",

        name: "hospital-dashboard",

        component: () => import("../views/hospital-admin/Dashboard.vue"),
      },

      {
        path: "departments",

        name: "departments",

        component: () => import("../views/hospital-admin/Departments.vue"),
      },

      {
        path: "facilities",

        name: "facilities",

        component: () => import("../views/hospital-admin/Facilities.vue"),
      },

      {
        path: "operating-hours",

        name: "operating-hours",

        component: () => import("../views/hospital-admin/OpreatingHours.vue"),
      },
      {
        path: "queue",

        name: "Queue",

        component: () => import("../views/hospital-admin/queue.vue"),
      },
      {
        path: "doctors",

        name: "doctors",

        component: () => import("../views/hospital-admin/doctors.vue"),
      },
      {
        path: "appointments",

        name: "Appointments",

        component: () => import("../views/hospital-admin/appointments.vue"),
      },
      {
        path: "leaves",

        name: "Doctor_Leaves",

        component: () => import("../views/hospital-admin/leaves.vue"),
      },
      {
        path: "reports",

        name: "reports",

        component: () => import("../views/hospital-admin/Reports.vue"),
      },
      {
        path: "telemanagment",

        name: "telemanagment",

        component: () =>
          import("../views/hospital-admin/TelehealthManagement.vue"),
      },
      {
        path: "notifications",

        name: "notifications",

        component: () => import("../views/hospital-admin/Notifications.vue"),
      },
      {
        path: "symptoms",

        name: "hospital-admin-symptoms",

        component: () => import("../views/hospital-admin/Symptoms.vue"),
      },
    ],
  },
];
