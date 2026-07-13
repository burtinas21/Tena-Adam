<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-5xl mx-auto">

      <!-- Back link -->
      <router-link
        to="/patient/hospitals"
        class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#004bb5] hover:underline mb-5"
      >
        <ChevronLeft class="w-3.5 h-3.5" /> Back to Hospitals
      </router-link>

      <!-- Loading skeleton -->
      <div v-if="loadingHospital" class="space-y-4">
        <div class="h-32 bg-white rounded-xl border border-gray-100 animate-pulse" />
        <div class="h-16 bg-white rounded-xl border border-gray-100 animate-pulse" />
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div v-for="n in 4" :key="n" class="h-40 bg-white rounded-xl border border-gray-100 animate-pulse" />
        </div>
      </div>

      <!-- Error state -->
      <div v-else-if="error"
        class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3 mb-4">
        <AlertCircle class="w-4 h-4 flex-shrink-0" /> {{ error }}
      </div>

      <template v-else-if="hospital">
        <!-- ── Hospital header card ──────────────────────────────────── -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-5">
          <div class="flex items-start gap-4">
            <div class="w-14 h-14 bg-[#004bb5]/10 rounded-xl flex items-center justify-center flex-shrink-0">
              <Building2 class="w-7 h-7 text-[#004bb5]" />
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-xl font-bold text-gray-900">{{ hospital.name }}</h1>
                <span v-if="hospital.is_active"
                  class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-200">
                  Active
                </span>
              </div>
              <div class="flex flex-wrap gap-x-5 gap-y-1 mt-2">
                <span class="flex items-center gap-1 text-xs text-gray-500">
                  <MapPin class="w-3.5 h-3.5 text-gray-400" />
                  {{ hospitalLocation }}
                </span>
                <span v-if="hospital.phone" class="flex items-center gap-1 text-xs text-gray-500">
                  <Phone class="w-3.5 h-3.5 text-gray-400" />
                  {{ hospital.phone }}
                </span>
                <span v-if="hospital.email" class="flex items-center gap-1 text-xs text-gray-500">
                  <Mail class="w-3.5 h-3.5 text-gray-400" />
                  {{ hospital.email }}
                </span>
                <a v-if="hospital.website" :href="hospital.website" target="_blank" rel="noopener"
                  class="flex items-center gap-1 text-xs text-[#004bb5] hover:underline">
                  <Globe class="w-3.5 h-3.5" />
                  Website
                </a>
              </div>
            </div>
            <!-- Book appointment CTA -->
            <router-link
              to="/patient/appointments"
              class="flex-shrink-0 bg-[#004bb5] hover:bg-[#003da1] text-white font-bold text-xs py-2.5 px-5 rounded-lg transition shadow-sm flex items-center gap-2"
            >
              <CalendarPlus class="w-3.5 h-3.5" /> Book Appointment
            </router-link>
          </div>

          <!-- Stats strip -->
          <div class="grid grid-cols-3 gap-3 mt-5 pt-4 border-t border-gray-50">
            <div class="text-center">
              <p class="text-2xl font-bold text-gray-800">{{ doctors.length }}</p>
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">Doctors</p>
            </div>
            <div class="text-center border-x border-gray-100">
              <p class="text-2xl font-bold text-gray-800">{{ hospital.departments?.length ?? 0 }}</p>
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">Departments</p>
            </div>
            <div class="text-center">
              <p class="text-2xl font-bold" :class="hasTelehealth ? 'text-emerald-600' : 'text-gray-400'">
                {{ hasTelehealth ? 'Yes' : 'No' }}
              </p>
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">Telemedicine</p>
            </div>
          </div>
        </div>

        <!-- ── Departments strip ─────────────────────────────────────── -->
        <div v-if="hospital.departments?.length" class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4 mb-5">
          <h2 class="text-sm font-bold text-gray-800 mb-3">Departments</h2>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="dept in hospital.departments"
              :key="dept.id ?? dept.name"
              @click="activeDept = activeDept === (dept.id ?? dept.name) ? null : (dept.id ?? dept.name)"
              :class="activeDept === (dept.id ?? dept.name)
                ? 'bg-[#004bb5] text-white border-[#004bb5]'
                : 'bg-blue-50 text-[#004bb5] border-blue-100 hover:bg-blue-100'"
              class="px-3 py-1 border rounded-full text-xs font-semibold transition"
            >
              {{ dept.name ?? dept }}
            </button>
            <button v-if="activeDept"
              @click="activeDept = null"
              class="px-3 py-1 border border-gray-200 bg-gray-50 text-gray-500 rounded-full text-xs font-semibold hover:bg-gray-100 transition">
              Clear filter
            </button>
          </div>
        </div>

        <!-- ── Doctors section ──────────────────────────────────────── -->
        <div>
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-gray-800">
              Doctors
              <span class="text-sm font-normal text-gray-400 ml-1">({{ filteredDoctors.length }})</span>
            </h2>
            <!-- Search doctors within this hospital -->
            <div class="relative">
              <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" />
              <input
                v-model="doctorSearch"
                type="text"
                placeholder="Search doctor..."
                class="pl-8 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#004bb5] focus:ring-1 focus:ring-[#004bb5]/20 w-48"
              />
            </div>
          </div>

          <!-- Loading doctors skeleton -->
          <div v-if="loadingDoctors" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="n in 4" :key="n" class="h-36 bg-white rounded-xl border border-gray-100 animate-pulse" />
          </div>

          <!-- No doctors -->
          <div v-else-if="!filteredDoctors.length"
            class="bg-white rounded-xl border border-gray-100 py-14 flex flex-col items-center text-gray-400">
            <Stethoscope class="w-9 h-9 mb-2 text-gray-200" />
            <p class="text-sm font-medium text-gray-500">No doctors found</p>
            <p v-if="activeDept || doctorSearch" class="text-xs mt-1">
              Try clearing your filters.
            </p>
          </div>

          <!-- Doctor cards grid -->
          <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div
              v-for="doc in filteredDoctors"
              :key="doc.id"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-col gap-3 hover:shadow-md transition-shadow"
            >
              <!-- Doctor info row -->
              <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-full overflow-hidden bg-[#004bb5]/10 flex items-center justify-center flex-shrink-0 border border-gray-100">
                  <img v-if="doc.profile_picture_url" :src="doc.profile_picture_url"
                    :alt="doc.name" class="w-full h-full object-cover" />
                  <span v-else class="text-sm font-bold text-[#004bb5]">{{ doc.initials }}</span>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-bold text-gray-800 text-sm truncate">Dr. {{ doc.name }}</p>
                  <p class="text-xs text-[#004bb5] font-medium mt-0.5">{{ doc.department }}</p>
                  <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                    <span v-if="doc.years_experience" class="text-[11px] text-gray-500 flex items-center gap-1">
                      <Award class="w-3 h-3 text-gray-400" /> {{ doc.years_experience }}y exp
                    </span>
                    <span v-if="doc.consultation_fee" class="text-[11px] text-gray-500 flex items-center gap-1">
                      <Banknote class="w-3 h-3 text-gray-400" /> {{ doc.consultation_fee }} ETB
                    </span>
                    <span v-if="doc.is_telehealth_available"
                      class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                      <Video class="w-3 h-3" /> Telehealth
                    </span>
                  </div>
                </div>
              </div>

              <!-- Bio snippet -->
              <p v-if="doc.bio" class="text-[11px] text-gray-500 leading-relaxed line-clamp-2">
                {{ doc.bio }}
              </p>

              <!-- Book appointment button -->
              <router-link
                to="/patient/appointments"
                class="w-full bg-[#004bb5] hover:bg-[#003da1] text-white font-bold text-xs py-2.5 rounded-lg transition text-center mt-auto"
              >
                Book Appointment
              </router-link>
            </div>
          </div>
        </div>
      </template>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import {
  Building2, MapPin, Phone, Mail, Globe, ChevronLeft,
  CalendarPlus, AlertCircle, Search, Stethoscope,
  Video, Award, Banknote,
} from "lucide-vue-next";
import hospitalApi from "../../api/hospitalApi";
import doctorApi from "../../api/doctorApi";

const route = useRoute();
const hospitalId = computed(() => route.params.id);

// ── State ─────────────────────────────────────────────────────────────────
const hospital       = ref(null);
const loadingHospital = ref(false);
const loadingDoctors  = ref(false);
const error          = ref(null);
const doctors        = ref([]);
const doctorSearch   = ref("");
const activeDept     = ref(null);   // dept id or name used for filtering

// ── Derived ───────────────────────────────────────────────────────────────
const hospitalLocation = computed(() => {
  const h = hospital.value;
  if (!h) return "—";
  return [h.address, h.city, h.region].filter(Boolean).join(", ");
});

const hasTelehealth = computed(() =>
  doctors.value.some((d) => d.is_telehealth_available)
);

const filteredDoctors = computed(() => {
  let list = doctors.value;

  // Filter by selected department
  if (activeDept.value) {
    list = list.filter(
      (d) => d.department_id === activeDept.value || d.department === activeDept.value
    );
  }

  // Filter by search text
  if (doctorSearch.value.trim()) {
    const q = doctorSearch.value.toLowerCase();
    list = list.filter(
      (d) =>
        d.name.toLowerCase().includes(q) ||
        d.department.toLowerCase().includes(q)
    );
  }

  return list;
});

// ── Load ──────────────────────────────────────────────────────────────────
async function loadHospital() {
  try {
    loadingHospital.value = true;
    error.value = null;
    const res = await hospitalApi.getById(hospitalId.value);
    hospital.value = res.data?.data ?? res.data;
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to load hospital details.";
  } finally {
    loadingHospital.value = false;
  }
}

async function loadDoctors() {
  try {
    loadingDoctors.value = true;
    const res = await doctorApi.getAll();
    const list = res.data?.data ?? res.data ?? [];

    // Filter to only doctors belonging to this hospital
    doctors.value = list
      .filter((p) => p.hospital?.id === hospitalId.value || p.hospital_id === hospitalId.value)
      .map((p) => ({
        id:                    p.id,
        name:                  `${p.user?.first_name ?? ""} ${p.user?.last_name ?? ""}`.trim(),
        department:            p.department?.name ?? "—",
        department_id:         p.department?.id ?? null,
        years_experience:      p.years_experience ?? 0,
        consultation_fee:      p.consultation_fee ?? null,
        bio:                   p.bio ?? null,
        is_telehealth_available: p.is_telehealth_available ?? false,
        profile_picture_url:   p.profile_picture_url ?? null,
        initials: (
          (p.user?.first_name?.[0] ?? "") +
          (p.user?.last_name?.[0] ?? "")
        ).toUpperCase(),
      }));
  } catch {
    /* silent — doctors section will show empty state */
  } finally {
    loadingDoctors.value = false;
  }
}

onMounted(() => {
  Promise.all([loadHospital(), loadDoctors()]);
});
</script>
