import {
  Building2,
  SquarePlus,
  CalendarDays,
  FolderHeart,
  Video,
  HelpCircle,
  LayoutGrid,
  UserCircle,
} from "lucide-vue-next";

export default {
  theme: {
    title: "Smart Care",

    subtitle: "Patient Portal",

    background: "bg-white",
  },

  menu: [
    {
      title: "Dashboard",

      route: "/patient/dashboard",

      icon: LayoutGrid,

      roles: ["patient"],
    },
    {
      title: "Hospitals",

      route: "/patient/hospitals",

      icon: Building2,

      roles: ["patient"],
    },

    {
      title: "Doctors",

      route: "/patient/doctors",

      icon: SquarePlus,

      roles: ["patient"],
    },

    {
      title: "Appointments",

      route: "/patient/appointments",

      icon: CalendarDays,

      roles: ["patient"],
    },
    {
      title: "Queue",

      route: "/patient/queue-status",

      icon: LayoutGrid,

      roles: ["patient"],
    },

    {
      title: "Medical Records",

      route: "/patient/medical-records",

      icon: FolderHeart,

      roles: ["patient"],
    },

    {
      title: "Telemedicine",

      route: "/patient/telemedicine",

      icon: Video,

      roles: ["patient"],
    },

    {
      title: "Help Center",
      route: "/help",
      icon: HelpCircle,
      roles: ["patient"],
    },

    {
      title: "Profile",
      route: "/patient/profile",
      icon: UserCircle,
      roles: ["patient"],
    },
  ],
};
