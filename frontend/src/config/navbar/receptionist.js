import { Bell, Settings } from "lucide-vue-next";

export default {
  theme: {
    background: "bg-white",

    title: "Receptionist Portal",
  },

  search: {
    placeholder: "Search patient, hospitals...",
  },

  actions: [
    {
      icon: Bell,

      name: "notification",
    },

    {
      icon: Settings,

      name: "settings",
    },
  ],
};
