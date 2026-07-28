import {
  LayoutGrid,
  Building2,
  Users,
  ShieldCheck,
  BarChart3,
  ScrollText,
  Bell,
  FileBarChart,
} from "lucide-vue-next";

export default {
  theme: {
    title: "HealthAdmin",
    subtitle: "Enterprise Suite",
    background: "bg-white",
  },

  menu: [
    { titleKey: "nav.dashboard",       title: "Dashboard",       route: "/platform/dashboard",        icon: LayoutGrid,  roles: ["platform_admin"] },
    { titleKey: "nav.hospital_network",title: "Hospital Network",route: "/platform/hospitalnetwork",  icon: Building2,   roles: ["platform_admin"] },
    { titleKey: "nav.hospital_admins", title: "Hospital Admins", route: "/platform/hospital-admins",  icon: Users,       roles: ["platform_admin"] },
    { titleKey: "nav.doctors",         title: "Doctors",         route: "/platform/doctor",           icon: ShieldCheck, roles: ["platform_admin"] },
    { titleKey: "nav.analytics",       title: "Analytics",       route: "/platform/analytics",        icon: BarChart3,   roles: ["platform_admin"] },
    { titleKey: "nav.audit_logs",      title: "Audit Logs",      route: "/platform/auditlogs",        icon: ScrollText,  roles: ["platform_admin"] },
    { titleKey: "nav.reports",         title: "Reports",         route: "/platform/reports",          icon: FileBarChart, roles: ["platform_admin"] },
    { titleKey: "nav.notifications",   title: "Notifications",   route: "/platform/notifications",    icon: Bell,        roles: ["platform_admin"] },
  ],
};
