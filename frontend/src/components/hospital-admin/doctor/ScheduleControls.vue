<template>
  <!-- Modal backdrop -->
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
      <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100">
        <div>
          <h3 class="text-sm font-bold text-gray-800">
            {{ isEditing ? 'Edit Schedule' : 'Add Schedule' }}
          </h3>
          <p class="text-xs text-gray-400 mt-0.5">
            {{ isEditing ? 'Update working hours for this day.' : 'Set working hours for a day.' }}
          </p>
        </div>
        <button @click="$emit('close')" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
          <X class="w-4 h-4" />
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="px-6 py-4 space-y-4">
        <div v-if="error" class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5">
          <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ error }}
        </div>

        <!-- Day (create only) -->
        <div v-if="!isEditing">
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Day of Week <span class="text-red-500">*</span>
          </label>
          <select
            v-model.number="form.day_of_week"
            required
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
          >
            <option value="" disabled>Select day</option>
            <option
              v-for="(label, i) in DAY_LABELS"
              :key="i"
              :value="i"
              :disabled="usedDays.includes(i)"
            >
              {{ label }}{{ usedDays.includes(i) ? ' (already set)' : '' }}
            </option>
          </select>
        </div>

        <!-- Working hours -->
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Start Time <span class="text-red-500">*</span></label>
            <input
              v-model="form.start_time" type="time" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">End Time <span class="text-red-500">*</span></label>
            <input
              v-model="form.end_time" type="time" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>
        </div>

        <!-- Lunch break -->
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Lunch Start</label>
            <input
              v-model="form.lunch_start" type="time"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Lunch End</label>
            <input
              v-model="form.lunch_end" type="time"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>
        </div>

        <!-- Slot duration -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Slot Duration (minutes)</label>
          <input
            v-model.number="form.slot_duration_min" type="number" min="5" max="240"
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
          />
        </div>

        <!-- Is available toggle -->
        <div class="flex items-center gap-3">
          <button
            type="button"
            @click="form.is_available = !form.is_available"
            :class="form.is_available ? 'bg-[#004795]' : 'bg-gray-200'"
            class="relative w-10 h-5 rounded-full transition-colors duration-200"
          >
            <span
              :class="form.is_available ? 'translate-x-5' : 'translate-x-0.5'"
              class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"
            />
          </button>
          <span class="text-sm text-gray-700 font-medium">
            {{ form.is_available ? 'Available' : 'Unavailable' }}
          </span>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
            Cancel
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2"
          >
            <Loader2 v-if="loading" class="w-3.5 h-3.5 animate-spin" />
            {{ isEditing ? 'Save Changes' : 'Add Schedule' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { X, AlertCircle, Loader2 } from "lucide-vue-next";

const DAY_LABELS = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

const props = defineProps({
  schedule: { type: Object, default: null },
  doctorId:  { type: String, default: "" },   // optional: empty when doctor manages own schedule
  usedDays:  { type: Array, default: () => [] },
  loading:   { type: Boolean, default: false },
  error:     { type: String, default: null },
});
const emit = defineEmits(["close", "submit"]);

const isEditing = computed(() => !!props.schedule);

const form = ref({
  day_of_week: "",
  start_time: "08:30",
  end_time: "16:30",
  lunch_start: "12:00",
  lunch_end: "13:00",
  slot_duration_min: 30,
  is_available: true,
});

watch(() => props.schedule, (s) => {
  if (s) {
    form.value = {
      day_of_week:       s.day_of_week,
      start_time:        s.start_time ?? "08:30",
      end_time:          s.end_time ?? "16:30",
      lunch_start:       s.lunch_start ?? "12:00",
      lunch_end:         s.lunch_end ?? "13:00",
      slot_duration_min: s.slot_duration_min ?? 30,
      is_available:      s.is_available ?? true,
    };
  } else {
    form.value = {
      day_of_week: "", start_time: "08:30", end_time: "16:30",
      lunch_start: "12:00", lunch_end: "13:00",
      slot_duration_min: 30, is_available: true,
    };
  }
}, { immediate: true });

function handleSubmit() {
  const payload = {
    day_of_week:       form.value.day_of_week,
    start_time:        form.value.start_time,
    end_time:          form.value.end_time,
    lunch_start:       form.value.lunch_start || null,
    lunch_end:         form.value.lunch_end || null,
    slot_duration_min: form.value.slot_duration_min,
    is_available:      form.value.is_available,
  };

  // Only add doctor_id when hospital_admin is creating for a specific doctor
  if (!isEditing.value && props.doctorId) {
    payload.doctor_id = props.doctorId;
  }

  emit("submit", payload);
}
</script>
