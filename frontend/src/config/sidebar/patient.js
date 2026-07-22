import {
  Building2,
  SquarePlus,
  CalendarDays,
  FolderHeart,
  Video,
  HelpCircle,
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
    // {
    //   title: "Queue",
    //   route: "/patient/queue-status",
    //   icon: LayoutGrid,
    //   roles: ["patient"],
    // },
    {
      title: "TeleHealth",
      route: "/patient/telehealth",
      icon: Video,
      roles: ["patient"],
    },
    {
      title: "Symptom Checker",
      route: "/patient/symptom",
      icon: FolderHeart,
      roles: ["patient"],
    },
    {
      title: "Medical History",
      route: "/patient/medicalhistory",
      icon: ClipboardList,
      roles: ["patient"],
    },
    {
      title: "Prescriptions",
      route: "/patient/prescriptions",
      icon: Pill,
      roles: ["patient"],
    },
    {
      title: "Documents",
      route: "/patient/documents",
      icon: FileArchive,
      roles: ["patient"],
    },
    {
      title: "Notifications",
      route: "/patient/notification",
      icon: Bell,
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
