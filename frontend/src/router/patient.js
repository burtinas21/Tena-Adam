import DashboardLayout from "../layouts/DashboardLayout.vue";

export default [
  {
    path: "/patient",

    component: DashboardLayout,

    meta: {
      requiresAuth: true,
      roles: ["patient"],
    },

    children: [
      {
        path: "dashboard",

        name: "patient-dashboard",

        component: () => import("../views/patient/Dashboard.vue"),
      },

      {
        path: "appointments",

        name: "appointments",

        component: () => import("../views/patient/Appointments.vue"),
      },

      {
        path: "doctors",

        name: "Doctors",

        component: () => import("../views/patient/doctors.vue"),
      },

      {
        path: "doctors/:id",

        name: "patient-doctor-detail",

        component: () => import("../views/patient/DoctorDetail.vue"),
      },

      {
        path: "hospitals",

        name: "Hospitals",

        component: () => import("../views/patient/hospitals.vue"),
      },

      {
        path: "hospitals/:id",

        name: "patient-hospital-detail",

        component: () => import("../views/patient/HospitalDetail.vue"),
      },
      {
        path: "profile",

        name: "Profile",

        component: () => import("../views/patient/profile/CompleteProfile.vue"),
      },

      {
        path: "telehealth",
        name: "patient-telemedicine",
        component: () => import("../views/patient/Telehealth.vue"),
      },
      {
        path: "queue-status",
        name: "patient-queue-status",
        component: () => import("../views/patient/queue/MyQueueStatus.vue"),
      },
      {
        path: "medicalhistory",
        name: "medicalhistory",
        component: () => import("../views/patient/MedicalHistory.vue"),
      },
      {
        path: "prescriptions",
        name: "patient-prescriptions",
        component: () => import("../views/patient/Prescriptions.vue"),
      },
      {
        path: "documents",
        name: "patient-documents",
        component: () => import("../views/patient/MedicalDocuments.vue"),
      },
      {
        path: "notification",
        name: "patient-notification",
        component: () => import("../views/patient/Notifications.vue"),
      },
      {
        path: "activeconsultation",

        name: "activeconsultation",

        component: () => import("../views/patient/ActiveConsultationSession.vue"),
      },
        {
        path: "symptom",

        name: "symptom",

        component: () => import("../views/patient/SymptomChecker.vue"),
      },
    ],
  },
];
