<template>
  <main
    class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200"
  >
    <div class="max-w-3xl mx-auto">
      <!-- Back -->
      <router-link
        to="/patient/doctors"
        class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#004bb5] hover:underline mb-5"
      >
        <ChevronLeft class="w-3.5 h-3.5" /> Back to Doctors
      </router-link>

      <!-- Loading skeleton -->
      <div v-if="loading" class="space-y-4">
        <div
          class="h-40 bg-white rounded-xl border border-gray-100 animate-pulse"
        />
        <div
          class="h-32 bg-white rounded-xl border border-gray-100 animate-pulse"
        />
        <div
          class="h-24 bg-white rounded-xl border border-gray-100 animate-pulse"
        />
      </div>

      <!-- Error -->
      <div
        v-else-if="error"
        class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
      >
        <AlertCircle class="w-4 h-4 flex-shrink-0" /> {{ error }}
      </div>

      <template v-else-if="doctor">
        <!-- ── Profile card ─────────────────────────────────────────── -->
        <div
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-5"
        >
          <div class="flex flex-col sm:flex-row gap-5 items-start">
            <!-- Avatar -->
            <div
              class="w-24 h-24 rounded-2xl bg-[#004bb5]/10 border border-blue-100 overflow-hidden flex items-center justify-center flex-shrink-0 relative"
            >
              <img
                v-if="doctor.profile_picture_url && !imgError"
                :src="doctor.profile_picture_url"
                :alt="fullName"
                class="w-full h-full object-cover"
                @error="imgError = true"
              />
              <span v-else class="text-2xl font-bold text-[#004bb5]">{{
                initials
              }}</span>
              <!-- Online dot -->
              <span
                class="absolute bottom-1.5 right-1.5 w-3 h-3 rounded-full border-2 border-white"
                :class="
                  doctor.is_telehealth_available
                    ? 'bg-emerald-400'
                    : 'bg-gray-300'
                "
              />
            </div>

            <!-- Core info -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-3 flex-wrap">
                <div>
                  <h1 class="text-xl font-bold text-gray-900">
                    Dr. {{ fullName }}
                  </h1>
                  <p class="text-sm font-semibold text-[#004bb5] mt-0.5">
                    {{ doctor.department?.name ?? "General Practitioner" }}
                  </p>
                </div>
                <span
                  v-if="doctor.is_verified"
                  class="flex items-center gap-1 text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full flex-shrink-0"
                >
                  <ShieldCheck class="w-3 h-3" /> Verified
                </span>
              </div>

              <!-- Meta row -->
              <div
                class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1.5 mt-3"
              >
                <span class="flex items-center gap-2 text-xs text-gray-600">
                  <Building2 class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" />
                  {{ doctor.hospital?.name ?? "—" }}
                </span>
                <span class="flex items-center gap-2 text-xs text-gray-600">
                  <Briefcase class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" />
                  {{ doctor.years_experience ?? 0 }}+ Years Experience
                </span>
                <span
                  v-if="doctor.license_number"
                  class="flex items-center gap-2 text-xs text-gray-600"
                >
                  <BadgeCheck class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" />
                  License: {{ doctor.license_number }}
                </span>
                <span
                  v-if="doctor.consultation_fee"
                  class="flex items-center gap-2 text-xs text-gray-600"
                >
                  <Banknote class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" />
                  {{ doctor.consultation_fee }} ETB / consultation
                </span>
              </div>

              <!-- Badges -->
              <div class="flex flex-wrap gap-2 mt-3">
                <span
                  v-if="doctor.is_telehealth_available"
                  class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-0.5 rounded-full"
                >
                  <Monitor class="w-3 h-3" /> Telemedicine Available
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1 text-xs font-medium text-orange-600 bg-orange-50 border border-orange-100 px-2.5 py-0.5 rounded-full"
                >
                  <UserCheck class="w-3 h-3" /> In-person only
                </span>
              </div>
            </div>
          </div>

          <!-- Book button -->
          <div class="mt-5 pt-4 border-t border-gray-50">
            <button
              @click="bookAppointment"
              class="inline-flex items-center gap-2 bg-[#004bb5] hover:bg-[#003da1] text-white font-bold text-sm py-2.5 px-6 rounded-lg transition shadow-sm"
            >
              <CalendarPlus class="w-4 h-4" /> Book Appointment
            </button>
          </div>
        </div>
        <div
          v-if="doctor.bio"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-5"
        >
          <h2
            class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2"
          >
            <User class="w-4 h-4 text-[#004bb5]" /> About
          </h2>
          <p class="text-sm text-gray-600 leading-relaxed">{{ doctor.bio }}</p>
        </div>
        <div
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-5"
        >
          <h2
            class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2"
          >
            <Info class="w-4 h-4 text-[#004bb5]" /> Professional Details
          </h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <DetailItem label="Full Name" :value="`Dr. ${fullName}`" />
            <DetailItem
              label="Specialization"
              :value="doctor.department?.name ?? '—'"
            />
            <DetailItem
              label="Hospital"
              :value="doctor.hospital?.name ?? '—'"
            />
            <DetailItem
              label="Experience"
              :value="`${doctor.years_experience ?? 0} years`"
            />
            <DetailItem
              label="Consultation Fee"
              :value="
                doctor.consultation_fee ? `${doctor.consultation_fee} ETB` : '—'
              "
            />
            <DetailItem
              label="License No."
              :value="doctor.license_number ?? '—'"
            />
            <DetailItem label="Practice Since" :value="practiceStart" />
            <DetailItem
              label="Telemedicine"
              :value="
                doctor.is_telehealth_available ? 'Available' : 'Not available'
              "
            />
          </div>
        </div>

        <!-- ── Languages ───────────────────────────────────────────── -->
        <div
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-5"
        >
          <h2
            class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2"
          >
            <Languages class="w-4 h-4 text-[#004bb5]" /> Languages
          </h2>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="lang in languages"
              :key="lang"
              class="px-3 py-1 bg-blue-50 text-[#004bb5] border border-blue-100 rounded-full text-xs font-semibold"
            >
              {{ lang }}
            </span>
          </div>
        </div>
      </template>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, defineComponent, h } from "vue";
import { useRoute, useRouter } from "vue-router";
import {
  ChevronLeft,
  AlertCircle,
  Building2,
  Briefcase,
  Monitor,
  UserCheck,
  CalendarPlus,
  ShieldCheck,
  BadgeCheck,
  Banknote,
  User,
  Info,
  Languages,
} from "lucide-vue-next";
import doctorApi from "../../api/doctorApi";

// ── Tiny helper component ─────────────────────────────────────────────────
const DetailItem = defineComponent({
  props: { label: String, value: String },
  setup: (p) => () =>
    h("div", {}, [
      h(
        "p",
        {
          class:
            "text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5",
        },
        p.label,
      ),
      h("p", { class: "text-sm font-semibold text-gray-800" }, p.value || "—"),
    ]),
});

const route = useRoute();
const router = useRouter();
const doctorId = computed(() => route.params.id);

const doctor = ref(null);
const loading = ref(false);
const error = ref(null);
const imgError = ref(false);

const fullName = computed(() => {
  const u = doctor.value?.user;
  if (!u) return "—";
  return `${u.first_name ?? ""} ${u.last_name ?? ""}`.trim();
});

const initials = computed(() => {
  const u = doctor.value?.user;
  if (!u) return "?";
  return ((u.first_name?.[0] ?? "") + (u.last_name?.[0] ?? "")).toUpperCase();
});

const practiceStart = computed(() => {
  const d = doctor.value?.practice_start_date;
  if (!d) return "—";
  return new Date(d).toLocaleDateString("en-ET", {
    year: "numeric",
    month: "long",
  });
});

// Default languages — backend doesn't store them yet so we show sensible defaults
const languages = computed(() => ["Amharic", "English"]);

function bookAppointment() {
  router.push({
    name: "appointments",
    query: {
      doctor_id: doctorId.value,
      hospital_id: doctor.value?.hospital?.id ?? "",
    },
  });
}

async function load() {
  try {
    loading.value = true;
    error.value = null;
    const res = await doctorApi.getById(doctorId.value);
    doctor.value = res.data?.data ?? res.data;
  } catch (err) {
    error.value =
      err.response?.data?.message || "Failed to load doctor profile.";
  } finally {
    loading.value = false;
  }
}

onMounted(load);
</script>
