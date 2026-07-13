import { Bell, Settings } from "lucide-vue-next";

export default {
  theme: {
    background: "bg-white",

    title: "Patient Portal",
  },

  search: {
    placeholder: "Search doctors, hospitals...",
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
