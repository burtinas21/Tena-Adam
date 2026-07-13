import { Bell, History, HelpCircle } from "lucide-vue-next";

export default {
  theme: {
    background: "bg-[#F5F7FB]",

    title: "HealthSync Pro",
  },

  search: {
    placeholder: "Search patients, ID...",
  },

  actions: [
    {
      icon: Bell,

      name: "notification",
    },

    {
      icon: History,

      name: "history",
    },

    {
      icon: HelpCircle,

      name: "help",
    },
  ],
};
