<template>
  <div class="bg-[#f0f4fa] dark:bg-[#0f172a] min-h-screen font-sans">
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
      <!-- Page header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Find a Doctor</h1>
          <p class="text-sm text-gray-500 mt-0.5">
            Showing {{ filtered.length }} available specialist{{ filtered.length !== 1 ? "s" : "" }}
          </p>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500 self-end sm:self-auto">
          <span class="text-gray-400">Sort by:</span>
          <select
            v-model="sortBy"
            class="bg-transparent font-semibold text-[#004bb5] focus:outline-none cursor-pointer text-sm border-none"
          >
            <option value="name">Recommended</option>
            <option value="experience">Experience</option>
            <option value="fee">Fee</option>
          </select>
        </div>
      </div>

      <!-- Loading skeleton -->
      <div v-if="loading" class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1 bg-white rounded-xl border border-gray-200 h-80 animate-pulse" />
        <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-5">
          <div v-for="n in 4" :key="n" class="bg-white rounded-xl border border-gray-200 h-64 animate-pulse" />
        </div>
      </div>

      <!-- Error -->
      <div v-else-if="error"
        class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3 mb-4">
        <AlertCircle class="w-4 h-4 flex-shrink-0" /> {{ error }}
      </div>

      <!-- Main layout -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
        <!-- ── Filter sidebar ─────────────────────────────────────── -->
        <aside class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm lg:sticky lg:top-6">
          <h2 class="text-base font-bold text-gray-800 mb-4">Filters</h2>

          <!-- Location -->
          <div class="border-b border-gray-100 pb-4 mb-4">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Location</p>
            <div class="relative">
              <MapPin class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" />
              <input
                v-model="locationFilter"
                type="text"
                placeholder="Addis Ababa"
                class="w-full pl-8 pr-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:outline-none focus:border-blue-400"
              />
            </div>
          </div>

          <!-- Specialization -->
          <div class="border-b border-gray-100 pb-4 mb-4">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Specialization</p>
            <div class="space-y-2">
              <label
                v-for="dept in visibleDepts"
                :key="dept.name"
                class="flex items-center gap-2.5 text-sm text-gray-700 cursor-pointer"
              >
                <input type="checkbox" :value="dept.name" v-model="selectedDepts"
                  class="w-4 h-4 rounded text-[#004bb5] focus:ring-[#004bb5]" />
                <span class="flex-1">{{ dept.name }} <span class="text-gray-400">({{ dept.count }})</span></span>
              </label>
            </div>
            <button v-if="departments.length > 3" @click="showAllDepts = !showAllDepts"
              class="text-xs font-semibold text-[#004bb5] hover:underline mt-2 block">
              {{ showAllDepts ? "Show less" : "Show more..." }}
            </button>
          </div>

          <!-- Telemedicine -->
          <div class="border-b border-gray-100 pb-4 mb-4">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Telemedicine</p>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600">Available Online Only</span>
              <button type="button" @click="teleOnly = !teleOnly"
                :class="teleOnly ? 'bg-[#004bb5]' : 'bg-gray-200'"
                class="relative w-10 h-5 rounded-full transition-colors duration-200 focus:outline-none">
                <span :class="teleOnly ? 'translate-x-5' : 'translate-x-0.5'"
                  class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200" />
              </button>
            </div>
          </div>

          <!-- Availability -->
          <div class="pb-2 mb-4">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Availability</p>
            <div class="space-y-2">
              <label v-for="opt in availabilityOptions" :key="opt.value"
                class="flex items-center gap-2.5 text-sm text-gray-700 cursor-pointer">
                <input type="radio" name="availability" :value="opt.value" v-model="availability"
                  class="w-4 h-4 text-[#004bb5] focus:ring-[#004bb5]" />
                <span>{{ opt.label }}</span>
              </label>
            </div>
          </div>

          <!-- Reset -->
          <button @click="resetFilters"
            class="w-full py-2 border border-blue-200 bg-blue-50 hover:bg-blue-100 text-[#004bb5] rounded-lg text-xs font-bold tracking-wide transition-colors">
            Reset Filters
          </button>
        </aside>

        <!-- ── Doctor cards grid ──────────────────────────────────── -->
        <main class="lg:col-span-3">
          <!-- Navbar search active banner -->
          <div v-if="navbarSearch"
            class="mb-4 flex items-center justify-between bg-blue-50 border border-blue-100 rounded-lg px-4 py-2.5">
            <span class="text-xs text-blue-700 font-medium">
              Searching for "<strong>{{ navbarSearch }}</strong>"
            </span>
            <button @click="clearNavbarSearch" class="text-xs text-blue-500 hover:underline font-semibold ml-3">
              Clear
            </button>
          </div>

          <!-- No results -->
          <div v-if="!filtered.length"
            class="bg-white rounded-xl border border-gray-200 py-16 flex flex-col items-center justify-center text-gray-400">
            <Stethoscope class="w-10 h-10 mb-3 text-gray-300" />
            <p class="text-sm font-medium">No doctors found matching your filters.</p>
            <button @click="resetFilters" class="mt-3 text-xs text-[#004bb5] font-semibold hover:underline">
              Clear filters
            </button>
          </div>

          <!-- Grid -->
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <DoctorCard
              v-for="doctor in filtered"
              :key="doctor.id"
              :doctor="doctor"
            />
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { AlertCircle, MapPin, Stethoscope } from "lucide-vue-next";
import doctorApi from "../../api/doctorApi";
import DoctorCard from "../../components/patient/doctor/DoctorCard.vue";

const route  = useRoute();
const router = useRouter();

// ── State ─────────────────────────────────────────────────────────────────
const rawDoctors    = ref([]);
const loading       = ref(false);
const error         = ref(null);
const locationFilter = ref("");
const selectedDepts  = ref([]);
const teleOnly       = ref(false);
const availability   = ref("any");
const sortBy         = ref("name");
const showAllDepts   = ref(false);

const availabilityOptions = [
  { value: "any",  label: "Any Time" },
  { value: "today", label: "Today" },
  { value: "week",  label: "This Week" },
];

// ── Navbar search (from URL query ?q=...) ────────────────────────────────
const navbarSearch = computed(() => (route.query.q ?? "").toString().trim());

watch(
  () => route.query.q,
  () => { /* reactivity — computed navbarSearch auto-updates */ }
);

function clearNavbarSearch() {
  router.replace({ path: route.path, query: {} });
}

// ── Load ──────────────────────────────────────────────────────────────────
async function load() {
  try {
    loading.value = true;
    error.value   = null;
    const res     = await doctorApi.getAll();
    const list    = res.data?.data ?? res.data ?? [];

    rawDoctors.value = list.map((p) => ({
      id:             p.id,
      name:           `${p.user?.first_name ?? ""} ${p.user?.last_name ?? ""}`.trim(),
      specialty:      p.department?.name ?? "General Practitioner",
      hospital:       p.hospital?.name ?? "—",
      hospitalId:     p.hospital?.id ?? null,
      experience:     p.years_experience ?? 0,
      languages:      ["Amharic", "English"],
      isTelemedicine: p.is_telehealth_available ?? false,
      nextAvailable:  "Check availability",
      avatar:         p.profile_picture_url ?? "",
      initials: (
        (p.user?.first_name?.[0] ?? "") + (p.user?.last_name?.[0] ?? "")
      ).toUpperCase(),
      consultation_fee: p.consultation_fee,
      bio:            p.bio ?? "",
      license_number: p.license_number ?? "",
      _dept:          p.department?.name ?? "",
      _city:          p.hospital?.city ?? "",
    }));
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to load doctors.";
  } finally {
    loading.value = false;
  }
}

onMounted(load);

// ── Department list with counts ────────────────────────────────────────
const departments = computed(() => {
  const map = {};
  rawDoctors.value.forEach((d) => {
    if (d._dept) map[d._dept] = (map[d._dept] ?? 0) + 1;
  });
  return Object.entries(map)
    .map(([name, count]) => ({ name, count }))
    .sort((a, b) => b.count - a.count);
});

const visibleDepts = computed(() =>
  showAllDepts.value ? departments.value : departments.value.slice(0, 3)
);

// ── Filtered + sorted ─────────────────────────────────────────────────
const filtered = computed(() => {
  let list = rawDoctors.value;

  // Navbar search — matches name OR specialty
  if (navbarSearch.value) {
    const q = navbarSearch.value.toLowerCase();
    list = list.filter(
      (d) =>
        d.name.toLowerCase().includes(q) ||
        d.specialty.toLowerCase().includes(q) ||
        d.hospital.toLowerCase().includes(q)
    );
  }

  if (locationFilter.value.trim()) {
    const q = locationFilter.value.toLowerCase();
    list = list.filter(
      (d) =>
        d.hospital.toLowerCase().includes(q) ||
        d._city.toLowerCase().includes(q)
    );
  }
  if (selectedDepts.value.length) {
    list = list.filter((d) => selectedDepts.value.includes(d._dept));
  }
  if (teleOnly.value) {
    list = list.filter((d) => d.isTelemedicine);
  }

  return [...list].sort((a, b) => {
    if (sortBy.value === "fee")        return (a.consultation_fee ?? 0) - (b.consultation_fee ?? 0);
    if (sortBy.value === "experience") return (b.experience ?? 0) - (a.experience ?? 0);
    return a.name.localeCompare(b.name);
  });
});

function resetFilters() {
  locationFilter.value = "";
  selectedDepts.value  = [];
  teleOnly.value       = false;
  availability.value   = "any";
  sortBy.value         = "name";
  clearNavbarSearch();
}
</script>
