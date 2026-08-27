<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "../../stores/authStore";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const token = ref(route.query.token || "");
const email = ref(route.query.email || "");

const firstName = ref("");
const role = ref("");
const position = ref("");
const password = ref("");
const confirmPassword = ref("");
const showPassword = ref(false);
const showConfirm = ref(false);

// Page states: checking | invalid | ready | submitting | success
const state = ref("checking");
const message = ref("");
const error = ref("");
const accountType = computed(() => {
  const type = position.value || role.value;

  switch (type) {
    case "doctor":
      return "Doctor";

    case "receptionist":
      return "Receptionist";

    case "hospital_admin":
      return "Hospital Administrator";

    case "platform_admin":
      return "Platform Administrator";

    default:
      return "Staff Member";
  }
});
onMounted(async () => {
  if (!token.value || !email.value) {
    state.value = "invalid";
    message.value = "Invalid invitation link. Please request a new one.";
    return;
  }
  try {
    const res = await authStore.checkInvitation({
      token: token.value,
      email: email.value,
    });

    firstName.value = res.first_name || "";
    role.value = res.role || "";
    position.value = res.position || "";

    state.value = "ready";
  } catch (err) {
    state.value = "invalid";
    message.value =
      err.response?.data?.message ||
      "This invitation link is invalid or has expired.";
  }
});

// ── submit set-password form ────────────────────────────────────────────────
async function handleSubmit() {
  error.value = "";
  if (password.value !== confirmPassword.value) {
    error.value = "Passwords do not match.";
    return;
  }
  if (password.value.length < 8) {
    error.value = "Password must be at least 8 characters.";
    return;
  }
  state.value = "submitting";
  try {
    await authStore.acceptInvitation({
      token: token.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value,
    });
    state.value = "success";
    message.value = "Your account has been activated! Redirecting to login…";
    setTimeout(() => router.push("/login"), 2500);
  } catch (err) {
    state.value = "ready";
    error.value =
      err.response?.data?.message || "Something went wrong. Please try again.";
  }
}

// ── password strength ───────────────────────────────────────────────────────
function strengthLevel(pwd) {
  if (!pwd) return 0;
  let score = 0;
  if (pwd.length >= 8) score++;
  if (/[A-Z]/.test(pwd)) score++;
  if (/[a-z]/.test(pwd)) score++;
  if (/\d/.test(pwd)) score++;
  if (/[^A-Za-z0-9]/.test(pwd)) score++;
  return score;
}
function strengthLabel(pwd) {
  const s = strengthLevel(pwd);
  if (s <= 1) return { text: "Weak", color: "text-red-500" };
  if (s <= 3) return { text: "Fair", color: "text-amber-500" };
  return { text: "Strong", color: "text-emerald-600" };
}
function strengthBars(pwd) {
  const s = strengthLevel(pwd);
  return [
    s >= 2 ? "bg-red-400" : "bg-slate-200",
    s >= 3 ? "bg-amber-400" : "bg-slate-200",
    s >= 5 ? "bg-emerald-500" : "bg-slate-200",
  ];
}
</script>

<template>
  <div
    class="min-h-screen bg-[#fafbfe] flex flex-col justify-between items-center p-6 font-sans text-slate-700 select-none relative overflow-hidden"
  >
    <!-- dot grid bg -->
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

    <main class="w-full max-w-md my-auto relative z-10">
      <div
        class="bg-white rounded-xl border border-slate-100 p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
      >
        <!-- ── Checking ── -->
        <div v-if="state === 'checking'" class="text-center py-8">
          <svg
            class="animate-spin w-8 h-8 text-[#004494] mx-auto mb-3"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            />
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8v8z"
            />
          </svg>
          <p class="text-sm text-slate-500">Verifying your invitation…</p>
        </div>

        <!-- ── Invalid / expired ── -->
        <div v-else-if="state === 'invalid'" class="text-center py-6">
          <div
            class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-red-50 text-red-500 mb-4"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
              />
            </svg>
          </div>
          <h2 class="text-base font-bold text-slate-800 mb-2">
            Invitation Invalid
          </h2>
          <p class="text-sm text-slate-500 mb-6">{{ message }}</p>
          <a
            href="/login"
            class="text-xs font-bold text-[#004494] hover:text-[#003370]"
            >← Back to Login</a
          >
        </div>

        <!-- ── Success ── -->
        <div v-else-if="state === 'success'" class="text-center py-6">
          <div
            class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 mb-4"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 13l4 4L19 7"
              />
            </svg>
          </div>
          <h2 class="text-base font-bold text-slate-800 mb-2">
            Account Activated!
          </h2>
          <p class="text-sm text-slate-500">{{ message }}</p>
        </div>

        <!-- ── Set Password Form ── -->
        <template v-else>
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
                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                />
              </svg>
            </div>
            <h1 class="text-xl font-bold text-slate-800 mb-1">
              Welcome<span v-if="firstName">, {{ firstName }}</span
              >!
            </h1>
            <p
              class="text-xs text-slate-400 max-w-[280px] mx-auto leading-relaxed"
            >
              Create a secure password to activate your
              {{ accountType.toLowerCase() }} account.
            </p>
          </div>

          <!-- Email badge -->
          <div
            class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 mb-4"
          >
            <svg
              class="w-4 h-4 text-slate-400 flex-shrink-0"
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
            <span class="text-xs text-slate-600 font-medium truncate">{{
              email
            }}</span>
          </div>

          <!-- Error banner -->
          <div
            v-if="error"
            class="mb-4 rounded-lg bg-red-50 border border-red-200 px-3 py-2.5 text-sm text-red-700 flex items-start gap-2"
          >
            <svg
              class="w-4 h-4 flex-shrink-0 mt-0.5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
              />
            </svg>
            {{ error }}
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-4">
            <!-- Password -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1.5"
                >New Password</label
              >
              <div class="relative">
                <input
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Min. 8 characters"
                  required
                  class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] pr-10"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                >
                  <svg
                    v-if="showPassword"
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

              <!-- Strength indicator -->
              <div v-if="password" class="mt-2 space-y-1">
                <div class="flex justify-between text-[11px] font-medium">
                  <span class="text-slate-400">Password Strength</span>
                  <span
                    :class="strengthLabel(password).color"
                    class="font-bold"
                    >{{ strengthLabel(password).text }}</span
                  >
                </div>
                <div class="grid grid-cols-3 gap-1.5">
                  <div
                    v-for="(cls, i) in strengthBars(password)"
                    :key="i"
                    :class="cls"
                    class="h-1 rounded transition-colors duration-300"
                  />
                </div>
              </div>
            </div>

            <!-- Confirm Password -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1.5"
                >Confirm Password</label
              >
              <div class="relative">
                <input
                  v-model="confirmPassword"
                  :type="showConfirm ? 'text' : 'password'"
                  placeholder="••••••••"
                  required
                  class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] pr-10"
                  :class="
                    confirmPassword && confirmPassword !== password
                      ? 'border-red-300'
                      : ''
                  "
                />
                <button
                  type="button"
                  @click="showConfirm = !showConfirm"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                >
                  <svg
                    v-if="showConfirm"
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
              <p
                v-if="confirmPassword && confirmPassword !== password"
                class="text-[11px] text-red-500 mt-1 font-medium"
              >
                Passwords do not match.
              </p>
            </div>

            <div class="pt-3">
              <button
                type="submit"
                :disabled="state === 'submitting'"
                class="w-full bg-[#004494] hover:bg-[#003370] text-white font-medium py-2.5 rounded-lg shadow-sm transition-colors text-xs tracking-wide disabled:opacity-60 flex items-center justify-center gap-2"
              >
                <svg
                  v-if="state === 'submitting'"
                  class="animate-spin w-4 h-4"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  />
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v8z"
                  />
                </svg>
                {{
                  state === "submitting" ? "Activating…" : "Activate My Account"
                }}
              </button>
            </div>
          </form>

          <div class="text-center mt-4">
            <a
              href="/login"
              class="inline-flex items-center gap-1.5 text-xs text-blue-600 font-bold hover:text-blue-700"
            >
              <span>←</span>
              <span>Back to Login</span>
            </a>
          </div>
        </template>
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
