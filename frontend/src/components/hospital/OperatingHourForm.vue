<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 class="text-base font-bold text-gray-800">
            {{ isEditing ? 'Edit Hours' : 'Add Operating Hours' }}
          </h3>
          <p class="text-xs text-gray-400 mt-0.5">
            {{ isEditing ? `Editing ${DAY_LABELS[selectedHour?.day_of_week]}` : 'Set hours for a day of the week.' }}
          </p>
        </div>
        <button @click="$emit('close')" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
          <X class="w-4 h-4" />
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="px-6 py-5 space-y-4">
        <div v-if="error" class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5">
          <AlertCircle class="w-3.5 h-3.5 flex-shrink-0" />{{ error }}
        </div>

        <!-- Day of week (create only) -->
        <div v-if="!isEditing">
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Day <span class="text-red-500">*</span>
          </label>
          <select
            v-model="form.day_of_week"
            required
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
          >
            <option value="" disabled>Select day</option>
            <option
              v-for="(label, index) in DAY_LABELS"
              :key="index"
              :value="index"
              :disabled="usedDays.includes(index)"
            >
              {{ label }}{{ usedDays.includes(index) ? ' (already set)' : '' }}
            </option>
          </select>
        </div>

        <!-- Open time -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Open Time <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.open_time"
            type="time"
            required
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
          />
        </div>

        <!-- Close time -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Close Time <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.close_time"
            type="time"
            required
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
          />
        </div>

        <!-- Holiday toggle -->
        <div class="flex items-center gap-3">
          <button
            type="button"
            @click="form.is_holiday = !form.is_holiday"
            :class="form.is_holiday ? 'bg-red-500' : 'bg-gray-200'"
            class="relative w-10 h-5 rounded-full transition-colors duration-200 focus:outline-none"
          >
            <span
              :class="form.is_holiday ? 'translate-x-5' : 'translate-x-0.5'"
              class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"
            />
          </button>
          <span class="text-sm text-gray-700 font-medium">
            {{ form.is_holiday ? 'Holiday / Closed' : 'Regular Hours' }}
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
            {{ isEditing ? 'Save Changes' : 'Add Hours' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { X, AlertCircle, Loader2 } from "lucide-vue-next";

const DAY_LABELS = [
  "Sunday", "Monday", "Tuesday", "Wednesday",
  "Thursday", "Friday", "Saturday",
];

const props = defineProps({
  hour: { type: Object, default: null },       // null = create mode
  hospitalId: { type: String, required: true },
  usedDays: { type: Array, default: () => [] }, // array of day_of_week ints already set
  loading: { type: Boolean, default: false },
  error: { type: String, default: null },
});
const emit = defineEmits(["close", "submit"]);

const isEditing = computed(() => !!props.hour);
const selectedHour = computed(() => props.hour);

const form = ref({
  day_of_week: "",
  open_time: "08:00",
  close_time: "17:00",
  is_holiday: false,
});

watch(
  () => props.hour,
  (h) => {
    if (h) {
      form.value = {
        day_of_week: h.day_of_week,
        open_time: h.open_time ?? "08:00",
        close_time: h.close_time ?? "17:00",
        is_holiday: h.is_holiday ?? false,
      };
    } else {
      form.value = { day_of_week: "", open_time: "08:00", close_time: "17:00", is_holiday: false };
    }
  },
  { immediate: true }
);

function handleSubmit() {
  const payload = isEditing.value
    ? {
        open_time: form.value.open_time,
        close_time: form.value.close_time,
        is_holiday: form.value.is_holiday,
      }
    : {
        hospital_id: props.hospitalId,
        day_of_week: form.value.day_of_week,
        open_time: form.value.open_time,
        close_time: form.value.close_time,
        is_holiday: form.value.is_holiday,
      };
  emit("submit", payload);
}
</script>
