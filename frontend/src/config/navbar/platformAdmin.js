import {
  LayoutGrid,
  Building2,
  Users,
  ShieldCheck,
  KeyRound,
  BarChart3,
  ScrollText,
  LogOut,
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
      title: "Hospitals",

      route: "/platform/hospitals",

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
      title: "Users",

      route: "/platform/users",

      icon: Users,

      roles: ["platform_admin"],
    },

    {
      title: "Roles",

      route: "/platform/roles",

      icon: ShieldCheck,

      roles: ["platform_admin"],
    },

    {
      title: "Permissions",

      route: "/platform/permissions",

      icon: KeyRound,

      roles: ["platform_admin"],
    },

    {
      title: "Statistics",

      route: "/platform/statistics",

      icon: BarChart3,

      roles: ["platform_admin"],
    },

    {
      title: "Audit Logs",

      route: "/platform/audit",

      icon: ScrollText,

      roles: ["platform_admin"],
    },

    {
      title: "Logout",

      icon: LogOut,

      action: "logout",

      roles: ["platform_admin"],
    },
  ],
};
