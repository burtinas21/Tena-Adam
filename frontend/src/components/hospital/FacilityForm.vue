<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 class="text-base font-bold text-gray-800">
            {{ isEditing ? 'Edit Facility' : 'New Facility' }}
          </h3>
          <p class="text-xs text-gray-400 mt-0.5">
            {{ isEditing ? 'Update facility details.' : 'Fill in the details to add a facility.' }}
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

        <!-- Name -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Facility Name <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.name"
            type="text"
            placeholder="e.g. Operating Room 1"
            required
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
          />
        </div>

        <!-- Type (create only) -->
        <div v-if="!isEditing">
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Type <span class="text-red-500">*</span>
          </label>
          <select
            v-model="form.type"
            required
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
          >
            <option value="" disabled>Select type</option>
            <option v-for="t in TYPES" :key="t" :value="t" class="capitalize">{{ t }}</option>
          </select>
        </div>

        <!-- Status -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Status</label>
          <select
            v-model="form.status"
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
          >
            <option v-for="s in STATUSES" :key="s" :value="s" class="capitalize">{{ s }}</option>
          </select>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Description</label>
          <textarea
            v-model="form.description"
            rows="3"
            placeholder="Optional description..."
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none"
          />
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
            {{ isEditing ? 'Save Changes' : 'Add Facility' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { X, AlertCircle, Loader2 } from "lucide-vue-next";

const TYPES = ["room", "bed", "clinic", "lab", "pharmacy"];
const STATUSES = ["available", "occupied", "maintenance", "reserved"];

const props = defineProps({
  facility: { type: Object, default: null },
  hospitalId: { type: String, required: true },
  loading: { type: Boolean, default: false },
  error: { type: String, default: null },
});
const emit = defineEmits(["close", "submit"]);

const isEditing = computed(() => !!props.facility);
const form = ref({ name: "", type: "", status: "available", description: "" });

watch(
  () => props.facility,
  (f) => {
    if (f) {
      form.value = {
        name: f.name ?? "",
        type: f.type ?? "",
        status: f.status ?? "available",
        description: f.description ?? "",
      };
    } else {
      form.value = { name: "", type: "", status: "available", description: "" };
    }
  },
  { immediate: true }
);

function handleSubmit() {
  const payload = isEditing.value
    ? {
        name: form.value.name,
        status: form.value.status,
        description: form.value.description,
      }
    : {
        hospital_id: props.hospitalId,
        name: form.value.name,
        type: form.value.type,
        status: form.value.status,
        description: form.value.description,
      };
  emit("submit", payload);
}
</script>
