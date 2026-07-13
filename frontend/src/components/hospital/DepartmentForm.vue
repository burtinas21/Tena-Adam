<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">
      <!-- Modal header -->
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 class="text-base font-bold text-gray-800">
            {{ isEditing ? 'Edit Department' : 'New Department' }}
          </h3>
          <p class="text-xs text-gray-400 mt-0.5">
            {{ isEditing ? 'Update department details.' : 'Fill in the details to create a department.' }}
          </p>
        </div>
        <button
          @click="$emit('close')"
          class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
        >
          <X class="w-4 h-4" />
        </button>
      </div>

      <!-- Form body -->
      <form @submit.prevent="handleSubmit" class="px-6 py-5 space-y-4">
        <!-- Error banner -->
        <div
          v-if="error"
          class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5"
        >
          <AlertCircle class="w-3.5 h-3.5 flex-shrink-0" />
          {{ error }}
        </div>

        <!-- Name -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Department Name <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.name"
            type="text"
            maxlength="100"
            placeholder="e.g. Cardiology"
            required
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
          />
        </div>

        <!-- Description -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Description
          </label>
          <textarea
            v-model="form.description"
            rows="3"
            placeholder="Optional description..."
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none"
          />
        </div>

        <!-- Is Active (edit only) -->
        <div v-if="isEditing" class="flex items-center gap-3">
          <button
            type="button"
            @click="form.is_active = !form.is_active"
            :class="form.is_active ? 'bg-[#004795]' : 'bg-gray-200'"
            class="relative w-10 h-5 rounded-full transition-colors duration-200 focus:outline-none"
          >
            <span
              :class="form.is_active ? 'translate-x-5' : 'translate-x-0.5'"
              class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"
            />
          </button>
          <span class="text-sm text-gray-700 font-medium">
            {{ form.is_active ? 'Active' : 'Inactive' }}
          </span>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-2">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2"
          >
            <Loader2 v-if="loading" class="w-3.5 h-3.5 animate-spin" />
            {{ isEditing ? 'Save Changes' : 'Create Department' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { X, AlertCircle, Loader2 } from "lucide-vue-next";

const props = defineProps({
  department: {
    type: Object,
    default: null,
  },
  hospitalId: {
    type: String,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(["close", "submit"]);

const isEditing = computed(() => !!props.department);

const form = ref({
  name: "",
  description: "",
  is_active: true,
});

// Populate form when editing
watch(
  () => props.department,
  (dept) => {
    if (dept) {
      form.value.name = dept.name ?? "";
      form.value.description = dept.description ?? "";
      form.value.is_active = dept.is_active ?? true;
    } else {
      form.value = { name: "", description: "", is_active: true };
    }
  },
  { immediate: true }
);

function handleSubmit() {
  const payload = isEditing.value
    ? {
        name: form.value.name,
        description: form.value.description,
        is_active: form.value.is_active,
      }
    : {
        hospital_id: props.hospitalId,
        name: form.value.name,
        description: form.value.description,
      };

  emit("submit", payload);
}
</script>
