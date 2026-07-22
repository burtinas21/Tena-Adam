import { storeToRefs } from "pinia";
import { useThemeStore } from "../stores/themeStore";

export function useTheme() {
  const store = useThemeStore();

  const { dark } = storeToRefs(store);

  return {
    dark,

    toggleTheme: store.toggleTheme,
  };
}
