import {
  LayoutGrid,
  Building2,
  Users,
  ShieldCheck,
  BarChart3,
  ScrollText,
  Bell,
  FileBarChart,
  Stethoscope,
} from "lucide-vue-next";

export default {
  theme: {
    title: "HealthAdmin",

    subtitle: "Enterprise Suite",

    background: "bg-white",
  },

  menu: [
    {
      title: "Dashboard",

      route: "/platform/dashboard",

      icon: LayoutGrid,

      roles: ["platform_admin"],
    },

    {
      title: "Hospital Network",

      route: "/platform/hospitalnetwork",

      icon: Building2,

      roles: ["platform_admin"],
    },

    {
      title: "Hospital Admins",

      route: "/platform/hospital-admins",

      icon: Users,

      roles: ["platform_admin"],
    },

    {
      title: "Doctors",

      route: "/platform/doctor",

      icon: ShieldCheck,

      roles: ["platform_admin"],
    },
    {
      title: "Users",

      route: "/platform/users",

      icon: Users,

      roles: ["platform_admin"],
    },

    // {
    //   title: "Hospital Network",

    //   route: "/platform/hospitalnetwork",

    //   icon: Building2,

    //   roles: ["platform_admin"],
    // },

    {
      title: "Analytics",

      route: "/platform/analytics",

      icon: BarChart3,

      roles: ["platform_admin"],
    },

    {
      title: "Audit Logs",

      route: "/platform/auditlogs",

      icon: ScrollText,

      roles: ["platform_admin"],
    },
    {
      title: "Notifications",

      route: "/platform/notifications",

      icon: Bell,

      roles: ["platform_admin"],
    },
    {
      title: "Reports",

      route: "/platform/reports",

      icon: FileBarChart,

      roles: ["platform_admin"],
    },
    {
      title: "Symptoms",

      route: "/platform/symptoms",

      icon: Stethoscope,

      roles: ["platform_admin"],
    },
  ],
};
