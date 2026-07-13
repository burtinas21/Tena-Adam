import {
  LayoutGrid,
  Calendar,
  CalendarCheck2,
  FolderHeart,
  Stethoscope,
  Pill,
  Video,
  Bell,
  Settings,
} from "lucide-vue-next";

export default {
  theme: {
    title: "HealthPortal",

    subtitle: "Doctor Portal",

    background: "bg-white",
  },

  menu: [
    {
      title: "Dashboard",

      route: "/doctor/dashboard",

      icon: LayoutGrid,

      roles: ["doctor"],
    },

    {
      title: "Schedule",

      route: "/doctor/schedule",

      icon: Calendar,

      roles: ["doctor"],
    },

    {
      title: "Appointments",

      route: "/doctor/appointments",

      icon: CalendarCheck2,

      roles: ["doctor"],
    },

    {
      title: "Queue",

      route: "/doctor/queue",

      icon: FolderHeart,

      roles: ["doctor"],
    },

    {
      title: "Profile",

      route: "/doctor/profile",

      icon: Stethoscope,

      roles: ["doctor"],
    },

    {
      title: "Prescriptions",

      route: "/doctor/prescriptions",

      icon: Pill,

      roles: ["doctor"],
    },

    {
      title: "Telemedicine",

      route: "/doctor/telemedicine",

      icon: Video,

      roles: ["doctor"],
    },

    {
      title: "Notifications",

      route: "/doctor/notifications",

      icon: Bell,

      roles: ["doctor"],
    },

    {
      title: "Settings",
      route: "/doctor/settings",
      icon: Settings,
      roles: ["doctor"],
    },
  ],
};
