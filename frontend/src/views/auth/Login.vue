<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/authStore";
import LoginForm from "../../components/auth/LoginForm.vue";

const router = useRouter();
const authStore = useAuthStore();
const email = ref("");
const password = ref("");
const rememberMe = ref(false);
const isPasswordVisible = ref(false);
const loading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

const togglePasswordVisibility = () => {
  isPasswordVisible.value = !isPasswordVisible.value;
};

const handleLogin = async () => {
  errorMessage.value = "";
  successMessage.value = "";

  const loginData = {
    email: email.value,
    password: password.value,
    remember: rememberMe.value,
  };

  try {
    loading.value = true;

    await authStore.login(loginData);

    const role = authStore.user.roles[0].name;

    successMessage.value = "Login successful !";

    setTimeout(() => {
      switch (role) {
        case "platform_admin":
          router.push("/platform/dashboard");
          break;
        case "hospital_admin":
          router.push("/hospital-admin/dashboard");
          break;
        case "doctor":
          router.push("/doctor/dashboard");
          break;
        case "patient":
          router.push("/patient/dashboard");
          break;
          case "receptionist":
          router.push("/receptionist/dashboard");
          break;
        default:
          router.push("/login");
      }
    }, 1000);
  } catch (error) {
    errorMessage.value =
      error.response?.data?.message || "Invalid email or password";
  } finally {
    loading.value = false;
  }
};
</script>
<template>
  <div
    class="min-h-screen bg-slate-50 dark:bg-slate-950 flex font-sans text-slate-700 dark:text-slate-200 select-none transition-colors duration-300"
  >
    <div
      class="w-full lg:w-1/2 flex items-center justify-center p-6 bg-slate-50 dark:bg-slate-950"
    >
      <div
        class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-8 shadow-sm max-w-md w-full"
      >
        <div
          class="flex items-center justify-center gap-2 text-[#004494] dark:text-blue-400 font-bold text-lg tracking-tight mb-4"
        >
          <span
            class="bg-[#004494] text-white rounded-md p-1 flex items-center justify-center text-xs font-black w-6 h-6"
          >
            ✚
          </span>
          <span>Smart Care</span>
        </div>
        <div class="text-center mb-6">
          <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mb-1.5">Sign In</h1>
          <p class="text-xs text-slate-500 dark:text-slate-400">
            Enter your credentials to access your healthcare portal.
          </p>
        </div>

        <!-- Success banner -->
        <div
          v-if="successMessage"
          class="flex items-center gap-2 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400 rounded-lg px-4 py-3 text-sm mb-4"
        >
          <svg
            class="w-4 h-4 flex-shrink-0 text-emerald-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <span class="font-medium">{{ successMessage }}</span>
        </div>

        <!-- Error banner -->
        <div
          v-if="errorMessage"
          class="flex items-center gap-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-400 rounded-lg px-4 py-3 text-sm mb-4"
        >
          <svg
            class="w-4 h-4 flex-shrink-0 text-red-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <span>{{ errorMessage }}</span>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-slate-700 dark:text-slate-300"
              >Email Address</label
            >
            <div class="relative">
              <span
                class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 dark:text-slate-500"
              >
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                  />
                </svg>
              </span>
              <input
                v-model="email"
                type="email"
                placeholder="doctor@hospital.com"
                required
                class="w-full pl-9 pr-3 py-2 border border-slate-300 dark:border-slate-600 rounded-md shadow-sm text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 placeholder-slate-400 dark:placeholder-slate-500 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 transition-colors"
              />
            </div>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Password</label>
            <div class="relative">
              <span
                class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 dark:text-slate-500"
              >
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                  />
                </svg>
              </span>
              <input
                v-model="password"
                :type="isPasswordVisible ? 'text' : 'password'"
                placeholder="••••••••"
                required
                class="w-full pl-9 pr-10 py-2 border border-slate-300 dark:border-slate-600 rounded-md shadow-sm text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 placeholder-slate-400 dark:placeholder-slate-500 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 transition-colors"
              />
              <button
                type="button"
                @click="togglePasswordVisibility"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300"
              >
                <svg
                  v-if="isPasswordVisible"
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"
                  />
                </svg>
                <svg
                  v-else
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                  />
                </svg>
              </button>
            </div>
          </div>
          <div class="flex items-center justify-between text-xs pt-1">
            <label
              class="flex items-center gap-2 cursor-pointer text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200"
            >
              <input
                v-model="rememberMe"
                type="checkbox"
                class="w-3.5 h-3.5 rounded text-blue-600 border-slate-300 dark:border-slate-600 focus:ring-blue-500 cursor-pointer"
              />
              <span>Remember Me</span>
            </label>
            <router-link
              to="/forgot-password"
              class="font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 underline"
            >
              Forgot Password?
            </router-link>
          </div>
          <div class="pt-2">
            <button
              type="submit"
              class="w-full bg-[#004494] hover:bg-[#003370] text-white font-medium py-2.5 rounded-md shadow-sm flex items-center justify-center gap-1.5 transition-colors text-sm"
            >
              <span>Sign In</span>
              <svg
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M14 5l7 7m0 0l-7 7m7-7H3"
                />
              </svg>
            </button>
          </div>

          <div
            class="text-center text-xs text-slate-500 dark:text-slate-400 pt-4 border-t border-slate-100 dark:border-slate-700 flex flex-col gap-3 items-center"
          >
            <div>
              Don't have an account?
              <router-link
                to="/register"
                class="font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 ml-0.5"
              >
                Create Account
              </router-link>
            </div>
            <div
              class="inline-flex items-center gap-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 px-2.5 py-1 rounded-full text-[11px] border border-emerald-100 dark:border-emerald-700 font-medium"
            >
              <svg
                class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                />
              </svg>
              <span>Secure Authentication</span>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div
      class="hidden md:flex lg:w-1/2 bg-[#004bb1] text-white flex-col justify-between p-12 relative overflow-hidden"
    >
      <LoginForm />
    </div>
  </div>
</template>
