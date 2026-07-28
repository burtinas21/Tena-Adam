import {
  Building2,
  SquarePlus,
  CalendarDays,
  FolderHeart,
  Video,
  LayoutGrid,
  UserCircle,
  Pill,
  ClipboardList,
  FileArchive,
  Bell,
} from "lucide-vue-next";

export default {
  theme: {
    title: "Smart Care",
    subtitle: "Patient Portal",
    background: "bg-white",
  },

  menu: [
    { titleKey: "nav.dashboard",        title: "Dashboard",        route: "/patient/dashboard",       icon: LayoutGrid,   roles: ["patient"] },
    { titleKey: "nav.hospitals",        title: "Hospitals",        route: "/patient/hospitals",       icon: Building2,    roles: ["patient"] },
    { titleKey: "nav.doctors",          title: "Doctors",          route: "/patient/doctors",         icon: SquarePlus,   roles: ["patient"] },
    { titleKey: "nav.appointments",     title: "Appointments",     route: "/patient/appointments",    icon: CalendarDays, roles: ["patient"] },
    { titleKey: "nav.telehealth",       title: "TeleHealth",       route: "/patient/telehealth",      icon: Video,        roles: ["patient"] },
    { titleKey: "nav.symptom_checker",  title: "Symptom Checker",  route: "/patient/symptom",         icon: FolderHeart,  roles: ["patient"] },
    { titleKey: "nav.medical_history",  title: "Medical History",  route: "/patient/medicalhistory",  icon: ClipboardList, roles: ["patient"] },
    { titleKey: "nav.prescriptions",    title: "Prescriptions",    route: "/patient/prescriptions",   icon: Pill,         roles: ["patient"] },
    { titleKey: "nav.documents",        title: "Documents",        route: "/patient/documents",       icon: FileArchive,  roles: ["patient"] },
    { titleKey: "nav.notifications",    title: "Notifications",    route: "/patient/notification",    icon: Bell,         roles: ["patient"] },
    { titleKey: "nav.profile",          title: "Profile",          route: "/patient/profile",         icon: UserCircle,   roles: ["patient"] },
  ],
};
