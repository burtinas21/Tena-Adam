<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/authStore";

const router = useRouter();
const authStore = useAuthStore();

const firstName = ref("");
const lastName = ref("");
const email = ref("");
const phone = ref("");
const password = ref("");
const confirmPassword = ref("");
const dob = ref("");
const gender = ref("");
const agreeToTerms = ref(false);

const loading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

const clearForm = () => {
  firstName.value = "";
  lastName.value = "";
  email.value = "";
  phone.value = "";
  password.value = "";
  confirmPassword.value = "";
  dob.value = "";
  gender.value = "";
  agreeToTerms.value = false;
};

const handleSubmit = async () => {
  errorMessage.value = "";
  successMessage.value = "";

  if (password.value !== confirmPassword.value) {
    errorMessage.value = "Passwords do not match";
    return;
  }

  if (!agreeToTerms.value) {
    errorMessage.value = "Please accept the Terms of Service to continue";
    return;
  }

  const data = {
    first_name: firstName.value,
    last_name: lastName.value,
    email: email.value,
    phone: phone.value,
    password: password.value,
    password_confirmation: confirmPassword.value,
    date_of_birth: dob.value,
    gender: gender.value,
    accept_terms: agreeToTerms.value,
  };

  try {
    loading.value = true;

    await authStore.register(data);

    successMessage.value = "Account created successfully!";
    clearForm();

    setTimeout(() => {
      router.push("/login");
    }, 2000);
  } catch (error) {
    if (error.response?.data?.errors) {
      errorMessage.value = Object.values(error.response.data.errors)
        .flat()
        .join(" ");
    } else {
      errorMessage.value =
        error.response?.data?.message || "Registration failed. Please try again.";
    }
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div
    class="bg-white rounded-xl border border-slate-200 p-8 shadow-sm max-w-2xl w-full"
  >
    <!-- Title -->
    <div class="text-center mb-4">
      <h1 class="text-2xl font-semibold text-slate-800 mb-2">
        Patient Registration
      </h1>

      <!-- Success banner -->
      <div
        v-if="successMessage"
        class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg px-4 py-3 text-sm text-left mb-4"
      >
        <svg class="w-4 h-4 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="font-medium">{{ successMessage }}</span>
      </div>

      <!-- Error banner -->
      <div
        v-if="errorMessage"
        class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm text-left mb-4"
      >
        <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ errorMessage }}</span>
      </div>

      <p class="text-sm text-slate-500 font-semibold">
        Create your secure healthcare account to access your medical records and
        appointments.
      </p>
    </div>

    <!-- Form -->
    <form @submit.prevent="handleSubmit" class="space-y-5">
      <!-- Personal Information -->
      <div
        class="flex items-center gap-2 text-slate-800 font-medium text-sm border-b border-slate-100 pb-2 mb-4"
      >
        <svg
          class="w-4 h-4 text-slate-500"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
          />
        </svg>

        <span>Personal Information</span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- First Name -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-slate-700"> First Name </label>

          <input
            v-model="firstName"
            type="text"
            required
            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white"
          />
        </div>

        <!-- Last Name -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-slate-700"> Last Name </label>

          <input
            v-model="lastName"
            type="text"
            required
            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white"
          />
        </div>

        <!-- Email -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-slate-700">
            Email Address
          </label>

          <div class="relative">
            <span
              class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"
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
              placeholder="name@example.com"
              required
              class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 placeholder-slate-400 bg-white"
            />
          </div>
        </div>

        <!-- Phone -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-slate-700"> Phone Number </label>

          <div class="relative">
            <span
              class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"
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
                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                />
              </svg>
            </span>

            <input
              v-model="phone"
              type="tel"
              placeholder="+1 (555) 000-0000"
              required
              class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 placeholder-slate-400 bg-white"
            />
          </div>
        </div>
        <!-- Password -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-slate-700"> Password </label>

          <div class="relative">
            <span
              class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"
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
              type="password"
              placeholder="••••••••"
              required
              class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 placeholder-slate-400 bg-white"
            />
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-slate-700">
            Confirm Password
          </label>

          <div class="relative">
            <span
              class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"
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
                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                />
              </svg>
            </span>

            <input
              v-model="confirmPassword"
              type="password"
              placeholder="••••••••"
              required
              class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 placeholder-slate-400 bg-white"
            />
          </div>
        </div>

        <!-- Date of Birth -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-slate-700">
            Date of Birth
          </label>

          <input
            v-model="dob"
            type="date"
            required
            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-500 bg-white"
          />
        </div>

        <!-- Gender -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-slate-700">
            Gender (Optional)
          </label>

          <select
            v-model="gender"
            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-600"
          >
            <option value="">Select Gender</option>
            <option value="Male">Male</option>

            <option value="Female">Female</option>

            <option value="Other">Other</option>
          </select>
        </div>
      </div>

      <!-- Terms Agreement -->
      <div class="flex items-start gap-2.5 pt-1">
        <input
          id="terms"
          v-model="agreeToTerms"
          type="checkbox"
          class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 mt-0.5 cursor-pointer"
        />

        <label
          for="terms"
          class="text-xs text-slate-500 leading-tight cursor-pointer font-bold"
        >
          I agree to the

          <a href="#" class="text-blue-600 underline hover:text-blue-700">
            Terms of Service
          </a>

          and

          <a href="#" class="text-blue-600 underline hover:text-blue-700">
            Privacy Policy </a
          >.
        </label>
      </div>

      <!-- Form Footer -->
      <div
        class="flex items-center justify-between pt-4 border-t border-slate-100 text-xs"
      >
        <div class="flex items-center gap-2">
          <span class="text-slate-500 text-sm font-semibold">
            Already Have an Account?
          </span>

          <RouterLink
            to="/login"
            class="text-slate-700 hover:text-slate-900 underline text-sm font-semibold"
          >
            Sign In
          </RouterLink>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="bg-[#004494] hover:bg-[#003370] disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium px-4 py-2.5 rounded-md shadow-sm flex items-center gap-1.5 transition-colors"
        >
          <span>
            {{ loading ? "Registering..." : "Register Account" }}
          </span>

          <svg
            class="w-3.5 h-3.5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
            />
          </svg>
        </button>
      </div>
      <!-- End Form -->
    </form>
  </div>
</template>

<style scoped>
input:focus,
select:focus,
button:focus {
  outline: none;
}

/* Smooth transitions */
input,
select,
button,
a {
  transition: all 0.2s ease-in-out;
}

/* Placeholder color */
input::placeholder {
  color: rgb(148 163 184);
}

/* Disabled button */
button:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}
</style>
