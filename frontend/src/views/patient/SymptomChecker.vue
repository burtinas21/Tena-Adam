<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-3xl mx-auto">

      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Symptom Checker</h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Select your symptom and we'll recommend the right department for you
        </p>
      </div>

      <!-- Error banner -->
      <div
        v-if="store.error"
        class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
      >
        <AlertCircle class="w-4 h-4 flex-shrink-0" /> {{ store.error }}
      </div>

      <!-- Step 1: Search & Select symptom -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
        <h2 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
          <span class="w-5 h-5 rounded-full bg-[#004795] text-white text-xs flex items-center justify-center font-bold">1</span>
          What are you experiencing?
        </h2>

        <!-- Search input -->
        <div class="relative mb-4">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search symptoms (e.g. Fever, Headache, Cough)..."
            class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
          />
        </div>

        <!-- Loading skeleton -->
        <div v-if="store.loading" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
          <div
            v-for="n in 6" :key="n"
            class="h-10 bg-gray-100 rounded-lg animate-pulse"
          />
        </div>

        <!-- Symptoms grid -->
        <div v-else-if="filteredSymptoms.length" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
          <button
            v-for="symptom in filteredSymptoms"
            :key="symptom.id"
            type="button"
            @click="selectSymptom(symptom)"
            :class="
              selectedSymptom?.id === symptom.id
                ? 'bg-[#004795] text-white border-[#004795] shadow-sm'
                : 'bg-white text-gray-700 border-gray-200 hover:border-[#004795] hover:text-[#004795]'
            "
            class="px-3 py-2 text-xs font-semibold rounded-lg border transition text-left flex items-center gap-2"
          >
            <span
              class="w-1.5 h-1.5 rounded-full flex-shrink-0"
              :class="selectedSymptom?.id === symptom.id ? 'bg-white' : 'bg-[#004795]'"
            />
            {{ symptom.name }}
          </button>
        </div>

        <!-- Empty state -->
        <div v-else class="py-8 text-center text-gray-400 text-sm">
          <Search class="w-8 h-8 mx-auto mb-2 text-gray-300" />
          No symptoms found matching "{{ searchQuery }}"
        </div>

        <!-- Category filter pills -->
        <div v-if="categories.length" class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-50">
          <button
            type="button"
            @click="selectedCategory = null"
            :class="!selectedCategory ? 'bg-[#004795] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
            class="px-3 py-1 text-xs font-semibold rounded-full transition"
          >
            All
          </button>
          <button
            v-for="cat in categories"
            :key="cat"
            type="button"
            @click="selectedCategory = cat"
            :class="selectedCategory === cat ? 'bg-[#004795] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
            class="px-3 py-1 text-xs font-semibold rounded-full transition capitalize"
          >
            {{ cat }}
          </button>
        </div>
      </div>

      <!-- Step 2: Recommendation -->
      <transition name="fade">
        <div v-if="selectedSymptom" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
          <h2 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-5 h-5 rounded-full bg-[#004795] text-white text-xs flex items-center justify-center font-bold">2</span>
            Recommended Department for
            <span class="text-[#004795]">{{ selectedSymptom.name }}</span>
          </h2>

          <!-- Loading recommendations -->
          <div v-if="store.recommendationsLoading" class="flex items-center gap-3 text-sm text-gray-500 py-4">
            <Loader2 class="w-4 h-4 animate-spin text-[#004795]" />
            Analyzing your symptom...
          </div>

          <!-- No recommendation -->
          <div
            v-else-if="!store.recommendations || !store.recommendations.primary"
            class="flex items-start gap-3 bg-amber-50 border border-amber-100 rounded-xl p-4 text-xs text-amber-800"
          >
            <AlertCircle class="w-4 h-4 flex-shrink-0 mt-0.5 text-amber-500" />
            <div>
              <p class="font-semibold mb-0.5">No department mapped yet</p>
              <p>This symptom hasn't been mapped to a department yet. Please consult with a general practitioner or contact the hospital directly.</p>
            </div>
          </div>

          <!-- Recommendation cards -->
          <div v-else class="space-y-3">
            <!-- Primary recommendation -->
            <div class="flex items-start justify-between gap-4 bg-blue-50 border border-blue-100 rounded-xl p-4">
              <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-[#004795] flex items-center justify-center flex-shrink-0">
                  <Building2 class="w-4 h-4 text-white" />
                </div>
                <div>
                  <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Primary Department</p>
                  <p class="text-sm font-bold text-gray-800">{{ store.recommendations.primary.department }}</p>
                  <div class="flex items-center gap-2 mt-1.5">
                    <span
                      class="text-xs font-semibold px-2 py-0.5 rounded-full border"
                      :class="evidenceClass(store.recommendations.primary.evidence_level)"
                    >
                      {{ store.recommendations.primary.evidence_level }} evidence
                    </span>
                    <span class="text-xs text-gray-500">
                      Relevance: {{ store.recommendations.primary.relevance_score }}%
                    </span>
                  </div>
                </div>
              </div>
              <button
                @click="openBooking"
                class="flex-shrink-0 bg-[#004795] hover:bg-[#003670] text-white text-xs font-bold px-4 py-2 rounded-lg transition flex items-center gap-1.5"
              >
                <CalendarDays class="w-3.5 h-3.5" />
                Book
              </button>
            </div>

            <!-- Alternative departments -->
            <div
              v-if="store.recommendations.alternatives?.length"
              class="space-y-2"
            >
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Also consider</p>
              <div
                v-for="alt in store.recommendations.alternatives"
                :key="alt.department"
                class="flex items-center justify-between gap-3 bg-gray-50 border border-gray-100 rounded-xl px-4 py-3"
              >
                <div class="flex items-center gap-3">
                  <Building2 class="w-4 h-4 text-gray-400 flex-shrink-0" />
                  <div>
                    <p class="text-sm font-semibold text-gray-700">{{ alt.department }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                      <span
                        class="text-xs font-semibold px-2 py-0.5 rounded-full border"
                        :class="evidenceClass(alt.evidence_level)"
                      >
                        {{ alt.evidence_level }} evidence
                      </span>
                      <span class="text-xs text-gray-400">{{ alt.relevance_score }}%</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Symptom description -->
            <div
              v-if="selectedSymptom.description"
              class="bg-gray-50 rounded-xl px-4 py-3 text-xs text-gray-600"
            >
              <span class="font-semibold text-gray-700">About this symptom: </span>
              {{ selectedSymptom.description }}
            </div>
          </div>
        </div>
      </transition>

      <!-- Step 3: Book Appointment Modal -->
      <div
        v-if="showBooking"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
        @click.self="showBooking = false"
      >
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md flex flex-col max-h-[90vh]">
          <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100">
            <div>
              <h3 class="text-sm font-bold text-gray-800">Book Appointment</h3>
              <p class="text-xs text-gray-400 mt-0.5">
                Department: <span class="text-[#004795] font-semibold">{{ store.recommendations?.appointment_suggestion?.department_name }}</span>
              </p>
            </div>
            <button @click="showBooking = false" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
              <X class="w-4 h-4" />
            </button>
          </div>

          <div class="px-6 py-4 space-y-4 overflow-y-auto flex-1">
            <div
              v-if="bookError"
              class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5"
            >
              <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ bookError }}
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                Date & Time <span class="text-red-500">*</span>
              </label>
              <input
                v-model="bookForm.scheduled_at"
                type="datetime-local"
                :min="minDateTime"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
              />
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
            <button
              @click="showBooking = false"
              class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
            >
              Cancel
            </button>
            <button
              @click="handleBook"
              :disabled="!bookForm.scheduled_at || bookSaving"
              class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-50 flex items-center gap-2"
            >
              <Loader2 v-if="bookSaving" class="w-3.5 h-3.5 animate-spin" />
              Confirm Booking
            </button>
          </div>
        </div>
      </div>

      <!-- Success toast -->
      <transition name="slide-up">
        <div
          v-if="successMsg"
          class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-emerald-600 text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-lg flex items-center gap-2"
        >
          <CheckCircle class="w-4 h-4" /> {{ successMsg }}
        </div>
      </transition>

    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import {
  Search,
  Building2,
  CalendarDays,
  AlertCircle,
  Loader2,
  X,
  CheckCircle,
} from "lucide-vue-next";
import { useSymptomStore } from "../../stores/symptomStore";
import api from "../../api/axios";

const store = useSymptomStore();
const router = useRouter();

const searchQuery = ref("");
const selectedCategory = ref(null);
const selectedSymptom = ref(null);

const showBooking = ref(false);
const bookSaving = ref(false);
const bookError = ref(null);
const successMsg = ref("");
const bookForm = ref({ scheduled_at: "" });

// Minimum datetime = now (ISO string trimmed to minutes)
const minDateTime = computed(() => {
  const d = new Date();
  d.setMinutes(d.getMinutes() + 30);
  return d.toISOString().slice(0, 16);
});

// Unique categories from loaded symptoms
const categories = computed(() => {
  const cats = store.symptoms
    .map((s) => s.category)
    .filter(Boolean);
  return [...new Set(cats)].sort();
});

// Filtered symptoms based on search + category
const filteredSymptoms = computed(() => {
  let list = store.symptoms;
  if (selectedCategory.value) {
    list = list.filter((s) => s.category === selectedCategory.value);
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(
      (s) =>
        s.name.toLowerCase().includes(q) ||
        (s.description ?? "").toLowerCase().includes(q)
    );
  }
  return list;
});

async function selectSymptom(symptom) {
  selectedSymptom.value = symptom;
  bookForm.value.scheduled_at = "";
  bookError.value = null;
  showBooking.value = false;

  // Load recommendations
  await store.fetchRecommendations(symptom.id);

  // Log analytic silently (fire-and-forget)
  if (store.recommendations?.appointment_suggestion?.department_id) {
    store.logAnalytic({
      symptom_id: symptom.id,
      recommended_department_id:
        store.recommendations.appointment_suggestion.department_id,
      selected_by_patient: true,
    });
  }
}

function openBooking() {
  bookError.value = null;
  bookForm.value.scheduled_at = "";
  showBooking.value = true;
}

async function handleBook() {
  bookError.value = null;
  bookSaving.value = true;
  try {
    // Get the authenticated patient's profile id
    const profileRes = await api.get("/patient/profile");
    const patientId = (profileRes.data?.data ?? profileRes.data)?.id;

    if (!patientId) {
      bookError.value = "Patient profile not found. Please complete your profile first.";
      return;
    }

    await store.fetchRecommendations(selectedSymptom.value.id); // ensure fresh
    const symptomId = selectedSymptom.value.id;

    const res = await api.post(`/symptom-mappings/${symptomId}/create-appointment`, {
      patient_id: patientId,
      scheduled_at: bookForm.value.scheduled_at,
    });

    showBooking.value = false;
    successMsg.value =
      res.data?.message ||
      `Appointment booked with ${store.recommendations?.appointment_suggestion?.department_name}!`;
    setTimeout(() => (successMsg.value = ""), 4000);
  } catch (err) {
    const errors = err.response?.data?.errors;
    bookError.value = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Failed to book appointment.";
  } finally {
    bookSaving.value = false;
  }
}

function evidenceClass(level) {
  return (
    {
      high: "bg-emerald-50 text-emerald-700 border-emerald-200",
      medium: "bg-amber-50 text-amber-700 border-amber-200",
      low: "bg-gray-50 text-gray-500 border-gray-200",
    }[level] ?? "bg-gray-50 text-gray-500 border-gray-200"
  );
}

onMounted(() => {
  store.fetchAll();
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(8px);
}

.slide-up-enter-active,
.slide-up-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.slide-up-enter-from,
.slide-up-leave-to {
  opacity: 0;
  transform: translateX(-50%) translateY(16px);
}
</style>
