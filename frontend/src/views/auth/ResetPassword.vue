
<script setup>
import { ref } from "vue";
import { useRoute } from "vue-router";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/authStore";

const route = useRoute();
const router = useRouter();

const authStore = useAuthStore();

const token = ref(route.query.token || "");
const email = ref(route.query.email || "");

const password = ref("");
const confirmPassword = ref("");

const isPasswordVisible = ref(false);

const loading = ref(false);

const message = ref("");

const errorMessage = ref("");

const togglePasswordVisibility = () => {
  isPasswordVisible.value = !isPasswordVisible.value;
};

const handleResetPassword = async () => {
  message.value = "";

  errorMessage.value = "";

  if (password.value !== confirmPassword.value) {
    errorMessage.value = "Passwords do not match.";

    return;
  }

  try {
    loading.value = true;

    const response = await authStore.resetPassword({
      token: token.value,

      email: email.value,

      password: password.value,

      password_confirmation: confirmPassword.value,
    });

    message.value = response.message || "Password reset successfully.";

    password.value = "";

    confirmPassword.value = "";

    setTimeout(() => {
      router.push("/login");
    }, 2000);
  } catch (error) {
    errorMessage.value =
      error.response?.data?.message || "Unable to reset password.";
  } finally {
    loading.value = false;
  }
};
</script><template>
  <div
    class="min-h-screen bg-[#fafbfe] flex flex-col justify-between items-center p-6 font-sans text-slate-700 select-none relative overflow-hidden"
  >
  
    <div
      class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[radial-gradient(#004494_1px,transparent_1px)] [background-size:16px_16px]"
    ></div>

    <header class="text-center mt-4 relative z-10">
      <div
        class="text-[#004494] font-bold text-2xl tracking-wide flex flex-col items-center gap-0.5"
      >
        <span>Smart Care</span>
        <span
          class="text-[10px] uppercase font-semibold text-slate-400 tracking-[0.2em]"
          >Tena-Adam</span
        >
      </div>
    </header>

    <!-- Main Card Container -->
    <main class="w-full max-w-md my-auto relative z-10">
      <div
        class="bg-white rounded-xl border border-slate-100 p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
      >
        <!-- Header Shield Icon & Title -->
        <div class="text-center mb-6">
          <div
            class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 text-[#004494] mb-3"
          >
            <svg
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 15v2m0-6V9m0 12a9 9 0 110-18 9 9 0 010 18z"
              />
            </svg>
          </div>
          <h1 class="text-xl font-bold text-slate-800 mb-1.5">
            Reset Your Password
          </h1>
          <p
            class="text-xs text-slate-400 max-w-[280px] mx-auto leading-relaxed"
          >
            Create a new secure password for your healthcare account.
          </p>
        </div>

        <div
          v-if="message"
          class="mb-4 rounded bg-green-100 p-3 text-sm text-green-700"
        >
          {{ message }}
        </div>

        <div
          v-if="errorMessage"
          class="mb-4 rounded bg-red-100 p-3 text-sm text-red-700"
        >
          {{ errorMessage }}
        </div>
        <form @submit.prevent="handleResetPassword" class="space-y-4">
          <!-- New Password Input -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-slate-600"
              >New Password</label
            >
            <div class="relative">
              <input
                v-model="password"
                :type="isPasswordVisible ? 'text' : 'password'"
                placeholder="••••••••"
                required
                class="w-full px-3 py-2 border-b border-slate-200 focus:border-blue-500 text-sm focus:outline-none placeholder-slate-300 bg-white pr-10"
              />
              <button
                type="button"
                @click="togglePasswordVisibility"
                class="absolute inset-y-0 right-0 flex items-center pr-2 text-slate-400 hover:text-slate-600"
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
          <div class="space-y-1.5">
            <div class="flex justify-between text-[11px] font-medium">
              <span class="text-slate-400">Password Strength</span>
              <span class="text-emerald-600 font-bold">Strong</span>
            </div>
            <!-- Progress bars indicator items matching the visual style -->
            <div class="grid grid-cols-3 gap-1.5">
              <div class="h-1 rounded bg-emerald-600"></div>
              <div class="h-1 rounded bg-emerald-600"></div>
              <div class="h-1 rounded bg-emerald-600"></div>
            </div>
          </div>

          <!-- Password Dynamic Validation Requirements Box -->
          <div
            class="bg-blue-50/50 border border-blue-100/60 rounded-lg p-4 space-y-2 text-[11px] text-slate-500"
          >
            <span class="font-bold text-slate-700 block mb-1"
              >Password Requirements:</span
            >

            <div class="flex items-center gap-2 text-emerald-600 font-medium">
              <span class="text-xs">✓</span>
              <span>Minimum 8 characters</span>
            </div>
            <div class="flex items-center gap-2 text-emerald-600 font-medium">
              <span class="text-xs">✓</span>
              <span>At least one uppercase letter</span>
            </div>
            <div class="flex items-center gap-2 text-emerald-600 font-medium">
              <span class="text-xs">✓</span>
              <span>At least one lowercase letter</span>
            </div>
            <div class="flex items-center gap-2 text-slate-300">
              <span
                class="inline-block w-2.5 h-2.5 rounded-full border border-slate-300 text-center leading-[0.5]"
              ></span>
              <span>At least one number</span>
            </div>
          </div>

          <!-- Confirm New Password Field -->
          <div class="flex flex-col gap-1.5 pt-1">
            <label class="text-xs font-semibold text-slate-600"
              >Confirm New Password</label
            >
            <input
              v-model="confirmPassword"
              type="password"
              placeholder="••••••••"
              required
              class="w-full px-3 py-2 border border-slate-200 rounded-md shadow-sm text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white"
            />
          </div>
          <div class="pt-4 space-y-3 text-center">
            <button
              type="submit"
              class="w-full bg-[#004494] hover:bg-[#003370] text-white font-medium py-2.5 rounded-md shadow-sm transition-colors text-xs tracking-wide"
            >
            {{ loading ? "Resetting..." : "Reset Password" }}
            </button>

            <a
              href="/login"
              class="inline-flex items-center gap-1.5 text-xs text-blue-600 font-bold hover:text-blue-700 pt-1"
            >
              <span>←</span>
              <span>Back to Login</span>
            </a>
          </div>
        </form>
      </div>
    </main>
    <footer
      class="text-[10px] text-slate-400 font-medium flex items-center gap-1 pb-2 relative z-10"
    >
      <svg
        class="w-3 h-3 text-slate-400"
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
      <span>Protected Healthcare Account</span>
    </footer>
  </div>
</template>


