<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-7xl mx-auto">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Doctor Directory</h1>
          <p class="text-xs text-gray-400 font-medium mt-0.5">
            Manage hospital healthcare providers, availability, and schedules.
          </p>
        </div>
        <div class="flex items-center gap-2 self-end sm:self-auto">
          <!-- <button
            class="flex items-center gap-1.5 bg-white border border-gray-200 text-gray-600 px-3 py-2 rounded-xl text-xs font-semibold shadow-sm hover:bg-gray-50 transition-colors">
            <Download class="w-3.5 h-3.5" /> Export
          </button> -->
          <!-- <button @click="openCreate"
            class="flex items-center gap-1.5 bg-[#004795] hover:bg-[#003670] text-white px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition-colors">
            <Plus class="w-3.5 h-3.5" /> Add Doctor
          </button> -->
        </div>
      </div>

      <!-- Stat cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <StatCard icon="stethoscope" label="Total Doctors"       :value="totalCount" />
        <StatCard icon="user-check"  label="Active Providers"    :value="activeCount" badge="+2 this week" />
        <StatCard icon="video"       label="Telemedicine Enabled" :value="teleCount" />
        <StatCard icon="calendar"    label="Available Today"     :value="availableCount" />
      </div>

      <!-- Search + filter -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-4 border-b border-gray-100">
          <div class="relative flex-1">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
            <input v-model="search" type="text" placeholder="Search doctors by name or ID..."
              class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#004795] focus:ring-1 focus:ring-[#004795]/20" />
          </div>
          <div class="flex items-center gap-2">
            <select v-model="deptFilter"
              class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 bg-white focus:outline-none focus:border-[#004795]">
              <option value="">Department: All</option>
              <option v-for="d in departments" :key="d" :value="d">{{ d }}</option>
            </select>
            <select v-model="statusFilter"
              class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 bg-white focus:outline-none focus:border-[#004795]">
              <option value="">Status: All</option>
              <option value="active">Active</option>
              <option value="telehealth">Telemedicine</option>
            </select>
            <button class="p-2 border border-gray-200 rounded-lg text-gray-400 hover:bg-gray-50 transition">
              <SlidersHorizontal class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Error -->
        <div v-if="error"
          class="mx-5 mt-3 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3">
          <AlertCircle class="w-4 h-4 flex-shrink-0" />{{ error }}
        </div>

        <!-- Loading -->
        <div v-if="loading && !rawDoctors.length" class="p-5 space-y-3">
          <div v-for="n in 4" :key="n" class="h-14 bg-gray-50 rounded-lg animate-pulse" />
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm text-left min-w-[700px]">
            <thead>
              <tr class="border-b border-gray-100">
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Doctor</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Department</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">License & Exp.</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Telemedicine</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-if="!paged.length">
                <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                  <Stethoscope class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                  <p class="text-sm font-medium">No doctors found</p>
                </td>
              </tr>
              <tr v-for="doc in paged" :key="doc.id" class="hover:bg-gray-50/60 transition-colors">
                <!-- Doctor -->
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0 overflow-hidden">
                      <img v-if="doc.profile_picture_url" :src="doc.profile_picture_url"
                        class="w-full h-full object-cover" :alt="doc.fullName" />
                      <span v-else class="text-xs font-bold text-[#004795]">{{ doc.initials }}</span>
                    </div>
                    <div>
                      <p class="font-semibold text-gray-800 text-sm">Dr. {{ doc.fullName }}</p>
                      <p class="text-xs text-gray-400 mt-0.5">{{ doc.department }}</p>
                    </div>
                  </div>
                </td>
                <!-- Department badge -->
                <td class="px-5 py-4">
                  <span class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-lg text-xs font-medium">
                    {{ doc.department || '—' }}
                  </span>
                </td>
                <!-- License & exp -->
                <td class="px-5 py-4">
                  <p class="text-sm font-semibold text-gray-700">{{ doc.license_number || '—' }}</p>
                  <p class="text-xs text-gray-400 mt-0.5">{{ doc.years_experience ?? 0 }} Years</p>
                </td>
                <!-- Status -->
                <td class="px-5 py-4">
                  <span class="flex items-center gap-1.5 text-sm font-medium"
                    :class="doc.is_telehealth_available ? 'text-emerald-600' : 'text-gray-500'">
                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                      :class="doc.is_telehealth_available ? 'bg-emerald-500' : 'bg-gray-400'" />
                    {{ doc.is_telehealth_available ? 'Online' : 'Offline' }}
                  </span>
                </td>
                <!-- Telemedicine icon -->
                <td class="px-5 py-4">
                  <div
                    :class="doc.is_telehealth_available
                      ? 'bg-[#004795] text-white'
                      : 'bg-gray-100 text-gray-400'"
                    class="w-9 h-9 rounded-lg flex items-center justify-center">
                    <Video v-if="doc.is_telehealth_available" class="w-4 h-4" />
                    <VideoOff v-else class="w-4 h-4" />
                  </div>
                </td>
                <!-- Actions -->
                <td class="px-5 py-4 text-right">
                  <div class="flex items-center justify-end gap-1">
                    <button title="Edit"
                      class="p-1.5 rounded-lg text-gray-400 hover:text-[#004795] hover:bg-[#004795]/10 transition">
                      <Pencil class="w-3.5 h-3.5" />
                    </button>
                    <button title="View"
                      class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                      <Eye class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="filtered.length" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-gray-100">
          <p class="text-xs text-gray-500">
            Showing {{ (page - 1) * perPage + 1 }} to {{ Math.min(page * perPage, filtered.length) }} of {{ filtered.length }} doctors
          </p>
          <div class="flex items-center gap-1">
            <button @click="page--" :disabled="page === 1"
              class="w-7 h-7 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 disabled:opacity-40 transition">
              <ChevronLeft class="w-3.5 h-3.5" />
            </button>
            <button v-for="p in visiblePages" :key="p" @click="typeof p === 'number' && (page = p)"
              :class="p === page
                ? 'bg-[#004795] text-white border-[#004795]'
                : p === '...' ? 'border-transparent cursor-default text-gray-400'
                : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'"
              class="w-7 h-7 rounded-lg border text-xs font-semibold transition">
              {{ p }}
            </button>
            <button @click="page++" :disabled="page === totalPages"
              class="w-7 h-7 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 disabled:opacity-40 transition">
              <ChevronRight class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, watch, onMounted, defineComponent, h } from "vue";
import {
  Download, Plus, Search, SlidersHorizontal, AlertCircle,
  Stethoscope, Video, VideoOff, Pencil, Eye,
  ChevronLeft, ChevronRight, UserCheck, Calendar,
} from "lucide-vue-next";
import doctorApi from "../../api/doctorApi";

// ── Inline StatCard component ────────────────────────────────────────────
const StatCard = defineComponent({
  props: {
    icon:  { type: String, required: true },
    label: { type: String, required: true },
    value: { type: Number, default: 0 },
    badge: { type: String, default: null },
  },
  setup(props) {
    const iconMap = {
      stethoscope: Stethoscope,
      'user-check': UserCheck,
      video: Video,
      calendar: Calendar,
    };
    const IconComp = iconMap[props.icon] ?? Stethoscope;
    return () =>
      h("div", { class: "bg-white rounded-xl border border-gray-100 shadow-sm p-5 relative overflow-hidden" }, [
        props.badge
          ? h("span", { class: "absolute top-3 right-3 text-[9px] font-bold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full" }, props.badge)
          : null,
        h("div", { class: "flex items-center gap-3 mb-3" }, [
          h("div", { class: "w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center" },
            h(IconComp, { class: "w-5 h-5 text-blue-500" })
          ),
        ]),
        h("p", { class: "text-3xl font-black text-gray-900 tracking-tight" }, props.value),
        h("p", { class: "text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1" }, props.label),
      ]);
  },
});

// ── State ─────────────────────────────────────────────────────────────────
const rawDoctors   = ref([]);
const loading      = ref(false);
const error        = ref(null);
const search       = ref("");
const deptFilter   = ref("");
const statusFilter = ref("");
const page         = ref(1);
const perPage      = 3;

// ── Load ─────────────────────────────────────────────────────────────────
async function load() {
  try {
    loading.value = true;
    error.value   = null;
    const res  = await doctorApi.getAll();
    const list = res.data?.data ?? res.data ?? [];
    rawDoctors.value = list.map((p) => ({
      ...p,
      fullName:    `${p.user?.first_name ?? ""} ${p.user?.last_name ?? ""}`.trim(),
      initials:    ((p.user?.first_name?.[0] ?? "") + (p.user?.last_name?.[0] ?? "")).toUpperCase(),
      department:  p.department?.name ?? "—",
      hospital:    p.hospital?.name   ?? "—",
    }));
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to load doctors.";
  } finally {
    loading.value = false;
  }
}

onMounted(load);

// ── Stats ─────────────────────────────────────────────────────────────────
const totalCount     = computed(() => rawDoctors.value.length);
const activeCount    = computed(() => rawDoctors.value.length); // all loaded are active
const teleCount      = computed(() => rawDoctors.value.filter((d) => d.is_telehealth_available).length);
const availableCount = computed(() => rawDoctors.value.filter((d) => d.is_telehealth_available).length);

// ── Departments list ──────────────────────────────────────────────────────
const departments = computed(() =>
  [...new Set(rawDoctors.value.map((d) => d.department).filter(Boolean))].sort()
);

// ── Filter + sort ─────────────────────────────────────────────────────────
const filtered = computed(() => {
  let list = rawDoctors.value;
  if (search.value.trim()) {
    const q = search.value.toLowerCase();
    list = list.filter((d) =>
      d.fullName.toLowerCase().includes(q) ||
      d.license_number?.toLowerCase().includes(q) ||
      d.department.toLowerCase().includes(q)
    );
  }
  if (deptFilter.value)   list = list.filter((d) => d.department === deptFilter.value);
  if (statusFilter.value === "telehealth") list = list.filter((d) => d.is_telehealth_available);
  return list;
});

watch([search, deptFilter, statusFilter], () => { page.value = 1; });

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)));
const paged      = computed(() => {
  const s = (page.value - 1) * perPage;
  return filtered.value.slice(s, s + perPage);
});

const visiblePages = computed(() => {
  const t = totalPages.value;
  const p = page.value;
  if (t <= 5) return Array.from({ length: t }, (_, i) => i + 1);
  if (p <= 3) return [1, 2, 3, "...", t];
  if (p >= t - 2) return [1, "...", t - 2, t - 1, t];
  return [1, "...", p - 1, p, p + 1, "...", t];
});

function openCreate() { /* delegate to hospital admin flow */ }
</script>
