import {
  LayoutGrid,
  Calendar,
  CalendarCheck2,
  FolderHeart,
  Stethoscope,
  Pill,
  Video,
  Bell,
  ClipboardList,
  Activity,
  FileArchive,
} from "lucide-vue-next";

export default {
  theme: {
    title: "HealthPortal",
    subtitle: "Doctor Portal",
    background: "bg-white",
  },

  menu: [
    { titleKey: "nav.dashboard",        title: "Dashboard",         route: "/doctor/dashboard",       icon: LayoutGrid,   roles: ["doctor"] },
    { titleKey: "nav.schedule",         title: "Schedule",          route: "/doctor/schedule",        icon: Calendar,     roles: ["doctor"] },
    { titleKey: "nav.appointments",     title: "Appointments",      route: "/doctor/appointments",    icon: CalendarCheck2, roles: ["doctor"] },
    { titleKey: "nav.queue",            title: "Queue",             route: "/doctor/queue",           icon: FolderHeart,  roles: ["doctor"] },
    { titleKey: "nav.medical_encounter",title: "Medical Encounter", route: "/doctor/medicalencounter",icon: ClipboardList, roles: ["doctor"] },
    { titleKey: "nav.vitals",           title: "Vitals",            route: "/doctor/vitals",          icon: Activity,     roles: ["doctor"] },
    { titleKey: "nav.prescriptions",    title: "Prescriptions",     route: "/doctor/prescription",    icon: Pill,         roles: ["doctor"] },
    { titleKey: "nav.documents",        title: "Documents",         route: "/doctor/documents",       icon: FileArchive,  roles: ["doctor"] },
    { titleKey: "nav.telemedicine",     title: "Telemedicine",      route: "/doctor/telehealth",      icon: Video,        roles: ["doctor"] },
    { titleKey: "nav.profile",          title: "Profile",           route: "/doctor/profile",         icon: Stethoscope,  roles: ["doctor"] },
    { titleKey: "nav.notifications",    title: "Notifications",     route: "/doctor/notifications",   icon: Bell,         roles: ["doctor"] },
  ],
};
