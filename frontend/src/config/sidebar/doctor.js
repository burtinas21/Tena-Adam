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
      title: "Medical Encounter",
      route: "/doctor/medicalencounter",
      icon: ClipboardList,
      roles: ["doctor"],
    },
    {
      title: "Vitals",
      route: "/doctor/vitals",
      icon: Activity,
      roles: ["doctor"],
    },
    {
      title: "Prescriptions",
      route: "/doctor/prescription",
      icon: Pill,
      roles: ["doctor"],
    },
    {
      title: "Documents",
      route: "/doctor/documents",
      icon: FileArchive,
      roles: ["doctor"],
    },
    {
      title: "Telemedicine",
      route: "/doctor/telehealth",
      icon: Video,
      roles: ["doctor"],
    },
    {
      title: "Profile",
      route: "/doctor/profile",
      icon: Stethoscope,
      roles: ["doctor"],
    },
    {
      title: "Notifications",
      route: "/doctor/notifications",
      icon: Bell,
      roles: ["doctor"],
    },
  ],
};
