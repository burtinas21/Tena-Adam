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
    {
      title: "Dashboard",
      route: "/receptionist/dashboard",
      icon: LayoutGrid,
      roles: ["receptionist"],
    },
    {
      title: "Appointments",
      route: "/receptionist/appointments",
      icon: CalendarDays,
      roles: ["receptionist"],
    },
    {
      title: "Registration",
      route: "/receptionist/registration",
      icon: UserPlus,
      roles: ["receptionist"],
    },
    {
      title: "Queue",
      route: "/receptionist/queue",
      icon: ListOrdered,
      roles: ["receptionist"],
    },
    {
      title: "Notification",
      route: "/receptionist/notification",
      icon: HelpCircle,
      roles: ["receptionist"],
    },
    {
      title: "Profile",
      route: "/receptionist/profile",
      icon: UserCircle,
      roles: ["receptionist"],
    },
  ],
};
