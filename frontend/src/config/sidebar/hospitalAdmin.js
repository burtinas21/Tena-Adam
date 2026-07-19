import {
  LayoutGrid,
  Building2,
  Layers,
  Clock,
  UserPlus2,
  CalendarDays,
  Users2,
  Video,
  BarChart3,
  Bell,
  Settings,
  Stethoscope,
  FileBarChart,
  CalendarX2,
} from "lucide-vue-next";

export default {
  theme: {
    title: "Smart Care",

    subtitle: "Hospital Admin",

    background: "bg-white",
  },

  menu: [
    {
      title: "Dashboard",

      route: "/hospital-admin/dashboard",

      icon: LayoutGrid,

      roles: ["hospital_admin"],
    },

    {
      title: "Departments",

      route: "/hospital-admin/departments",

      icon: Building2,

      roles: ["hospital_admin"],
    },

    {
      title: "Facilities",

      route: "/hospital-admin/facilities",

      icon: Layers,

      roles: ["hospital_admin"],
    },

    {
      title: "Operating Hours",

      route: "/hospital-admin/operating-hours",

      icon: Clock,

      roles: ["hospital_admin"],
    },

    {
      title: "Doctors & Staff",

      route: "/hospital-admin/doctors",

      icon: UserPlus2,

      roles: ["hospital_admin"],
    },

    {
      title: "Appointments",

      route: "/hospital-admin/appointments",

      icon: CalendarDays,

      roles: ["hospital_admin"],
    },

    {
      title: "Queue Management",

      route: "/hospital-admin/queue",

      icon: Users2,

      roles: ["hospital_admin"],
    },

    {
      title: "Telemedicine",

      route: "/hospital-admin/telemanagment",

      icon: Video,

      roles: ["hospital_admin"],
    },

    {
      title: "Doctor Leaves",

      route: "/hospital-admin/leaves",

      icon: CalendarX2,

      roles: ["hospital_admin"],
    },

    {
      title: "Notifications",

      route: "/hospital-admin/notifications",

      icon: Bell,

      roles: ["hospital_admin"],
    },

    {
      title: "Settings",

      route: "/settings",

      icon: Settings,

      roles: ["hospital_admin"],
    },
    {
      title: "Reports & Analytics",

      route: "/hospital-admin/reports",

      icon: FileBarChart,

      roles: ["hospital_admin"],
    },
    {
      title: "Symptoms",

      route: "/hospital-admin/symptoms",

      icon: Stethoscope,

      roles: ["hospital_admin"],
    },
  ],
};
