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
        path: "medicalencounter",

        name: "medicalencounter",

        component: () => import("../views/doctor/MedicalEncounters.vue"),
      },
      {
        path: "telehealth",

        name: "doctor-telehealth",

        component: () => import("../views/doctor/Telehealth.vue"),
      },
      {
        path: "telemedicine",

        name: "telemedicine",

        component: () => import("../views/doctor/TelemedicineActiveCall.vue"),
      },
       {
        path: "prescription",

        name: "prescription",

        component: () => import("../views/doctor/Prescriptions.vue"),
      },
      {
        path: "vitals",
        name: "doctor-vitals",
        component: () => import("../views/doctor/vitals.vue"),
      },
      {
        path: "documents",
        name: "doctor-documents",
        component: () => import("../views/doctor/MedicalDocuments.vue"),
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
      {
        path: "notifications",

        name: "doctor-notifications",

        component: () => import("../views/doctor/Notifications.vue"),
      },
    ],
  },
];
