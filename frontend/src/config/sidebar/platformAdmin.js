import {
  LayoutGrid,
  Building2,
  Users,
  ShieldCheck,
  BarChart3,
  ScrollText,
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
  ],
};
