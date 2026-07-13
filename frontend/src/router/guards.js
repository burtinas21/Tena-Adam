import { useAuthStore } from "../stores/authStore";

export function setupGuards(router) {
  router.beforeEach((to) => {
    const authStore = useAuthStore();
    const token = authStore.token;

    // Always allow login page
    if (to.path === "/login") {
      return true;
    }

    // Protected routes — redirect to login if not authenticated
    if (to.meta.requiresAuth && !token) {
      return "/login";
    }

    // Guest pages — redirect authenticated users to their dashboard
    if (to.meta.guest && token) {
      const role = authStore.user?.roles?.[0]?.name;
      switch (role) {
        case "platform_admin":  return "/platform/dashboard";
        case "hospital_admin":  return "/hospital-admin/dashboard";
        case "doctor":          return "/doctor/dashboard";
        case "patient":         return "/patient/dashboard";
        case "receptionist":    return "/receptionist/dashboard";
        default:                return "/login";
      }
    }

    return true;
  });
}
