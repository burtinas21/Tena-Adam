import {
  LayoutGrid,
  UserPlus,
  CalendarDays,
  ListOrdered,
  HelpCircle,
  UserCircle,
} from "lucide-vue-next";

export default {
  theme: {
    title: "Smart Care",
    subtitle: "Receptionist Portal",
    background: "bg-white",
  },

  menu: [
    { titleKey: "nav.dashboard",    title: "Dashboard",    route: "/receptionist/dashboard",    icon: LayoutGrid,  roles: ["receptionist"] },
    { titleKey: "nav.appointments", title: "Appointments", route: "/receptionist/appointments", icon: CalendarDays, roles: ["receptionist"] },
    { titleKey: "nav.registration", title: "Registration", route: "/receptionist/registration", icon: UserPlus,    roles: ["receptionist"] },
    { titleKey: "nav.queue",        title: "Queue",        route: "/receptionist/queue",        icon: ListOrdered, roles: ["receptionist"] },
    { titleKey: "nav.notification", title: "Notification", route: "/receptionist/notification", icon: HelpCircle,  roles: ["receptionist"] },
    { titleKey: "nav.profile",      title: "Profile",      route: "/receptionist/profile",      icon: UserCircle,  roles: ["receptionist"] },
  ],
};
