<template>
  <div
    class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6"
  >
    <!-- Profile Info -->
    <div class="flex items-center space-x-5">
      <!-- Avatar initials fallback -->
      <div
        class="w-16 h-16 rounded-xl bg-[#004795]/10 flex items-center justify-center flex-shrink-0 ring-2 ring-slate-100"
      >
        <span class="text-xl font-black text-[#004795]">{{ initials }}</span>
      </div>

      <div>
        <div class="flex items-center flex-wrap gap-2">
          <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
            {{ name }}
          </h2>
          <span
            class="text-[10px] font-bold text-cyan-700 bg-cyan-50 px-2 py-0.5 rounded border border-cyan-100/60 uppercase tracking-wider"
          >
            {{ patientId }}
          </span>
          <span
            v-if="status"
            :class="statusClass"
            class="text-[10px] font-bold px-2 py-0.5 rounded border uppercase tracking-wider"
          >
            {{ status }}
          </span>
        </div>

        <div
          class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5 text-xs font-semibold text-slate-500"
        >
          <span class="flex items-center gap-1">
            <User class="w-3.5 h-3.5 text-slate-400" />
            {{ age ? `${age} yrs` : '—' }}
            <span v-if="dob" class="font-normal text-slate-400">({{ dob }})</span>
          </span>
          <span class="flex items-center gap-1">
            <Users2 class="w-3.5 h-3.5 text-slate-400" />
            {{ gender || '—' }}
          </span>
          <span
            v-if="bloodType"
            class="flex items-center gap-1 text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100/40 text-[11px] font-bold"
          >
            <Droplet class="w-3 h-3 fill-rose-500 text-rose-500" />
            {{ bloodType }}
          </span>
          <span v-if="phone" class="flex items-center gap-1">
            <Phone class="w-3.5 h-3.5 text-slate-400" />
            {{ phone }}
          </span>
        </div>

        <!-- Allergies warning if present -->
        <div v-if="allergies" class="mt-2 flex items-center gap-1.5">
          <AlertTriangle class="w-3.5 h-3.5 text-rose-500 flex-shrink-0" />
          <span class="text-[11px] font-bold text-rose-600">Allergies: {{ allergies }}</span>
        </div>
      </div>
    </div>

    <!-- Emergency Contact Block -->
    <div
      v-if="contactName"
      class="bg-slate-50 border border-slate-100 rounded-xl p-4 min-w-[260px]"
    >
      <span
        class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider"
        >Emergency Contact</span
      >
      <div class="mt-2 space-y-1">
        <div
          class="flex items-center space-x-2 text-xs font-bold text-slate-800"
        >
          <Contact2 class="w-3.5 h-3.5 text-slate-400" />
          <span
            >{{ contactName }}
            <span v-if="contactRelation" class="font-normal text-slate-400"
              >({{ contactRelation }})</span
            ></span
          >
        </div>
        <div
          v-if="contactPhone"
          class="flex items-center space-x-2 text-xs font-medium text-slate-500 pl-5"
        >
          <span>{{ contactPhone }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { User, Users2, Droplet, Contact2, Phone, AlertTriangle } from "lucide-vue-next";

const props = defineProps({
  name: { type: String, default: "" },
  patientId: { type: String, default: "" },
  age: { type: [Number, String], default: null },
  dob: { type: String, default: "" },
  gender: { type: String, default: "" },
  bloodType: { type: String, default: "" },
  phone: { type: String, default: "" },
  allergies: { type: String, default: "" },
  contactName: { type: String, default: "" },
  contactRelation: { type: String, default: "" },
  contactPhone: { type: String, default: "" },
  status: { type: String, default: "" },
});

const initials = computed(() => {
  if (!props.name) return "?";
  return props.name
    .split(" ")
    .filter(Boolean)
    .slice(0, 2)
    .map((w) => w[0].toUpperCase())
    .join("");
});

const statusClass = computed(() => {
  const map = {
    in_progress: "text-blue-700 bg-blue-50 border-blue-100",
    completed:   "text-emerald-700 bg-emerald-50 border-emerald-100",
    cancelled:   "text-red-600 bg-red-50 border-red-100",
  };
  return map[props.status] ?? "text-slate-600 bg-slate-50 border-slate-100";
});
</script>
