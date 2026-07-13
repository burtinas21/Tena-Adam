import {
  LayoutGrid,
  Building2,
  UserPlus2,
  CalendarDays,
  Users2,
  Video,
  BarChart3,
  Bell,
  Settings,
  LogOut,
} from "lucide-vue-next";

export default {
  theme: {
    title: "Smart Care",

    subtitle: "Hospital Admin",

    background: "bg-[#F0F4FA]",
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
      title: "Doctors",
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
      route: "/hospital-admin/telemedicine",
      icon: Video,
      roles: ["hospital_admin"],
    },

    {
      title: "Reports",
      route: "/hospital-admin/reports",
      icon: BarChart3,
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
      title: "Logout",
      icon: LogOut,
      action: "logout",
      roles: ["hospital_admin"],
    },
  ],
};
