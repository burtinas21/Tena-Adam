<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[92vh] flex flex-col">

      <!-- Header -->
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100 flex-shrink-0">
        <div>
          <h3 class="text-base font-bold text-gray-800">
            {{ isEditing ? 'Edit Hospital' : 'Register Hospital' }}
          </h3>
          <p class="text-xs text-gray-400 mt-0.5">
            {{ isEditing ? 'Update hospital information.' : 'Fill in the details to register a hospital.' }}
          </p>
        </div>
        <button @click="$emit('close')" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
          <X class="w-4 h-4" />
        </button>
      </div>

      <!-- Scrollable body -->
      <form @submit.prevent="handleSubmit" class="px-6 py-5 space-y-4 overflow-y-auto flex-1">

        <!-- Error banner -->
        <div v-if="error" class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5">
          <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
          <span>{{ error }}</span>
        </div>

        <div class="grid grid-cols-2 gap-4">

          <!-- Hospital Name -->
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Hospital Name <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.name" type="text" placeholder="e.g. General Hospital" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- Code (create only) -->
          <div v-if="!isEditing">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Code</label>
            <input
              v-model="form.code" type="text" maxlength="20" placeholder="e.g. GH-001"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- Registration Number (create only) -->
          <div v-if="!isEditing">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Reg. Number</label>
            <input
              v-model="form.registration_number" type="text" placeholder="MOH-123456"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- Address -->
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Address <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.address" type="text" placeholder="e.g. Lideta Sub-city, Addis Ababa" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- City -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              City <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.city" type="text" placeholder="Addis Ababa" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- Region -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Region</label>
            <input
              v-model="form.region" type="text" placeholder="Oromia"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- Phone -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone</label>
            <input
              v-model="form.phone" type="text" placeholder="+251 911 000 000"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email</label>
            <input
              v-model="form.email" type="email" placeholder="info@hospital.et"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- Website (create only) -->
          <div class="col-span-2" v-if="!isEditing">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Website</label>
            <input
              v-model="form.website" type="text" placeholder="https://hospital.et"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

        </div>

        <!-- ── Location Picker ─────────────────────────────────────── -->
        <div class="col-span-2">
          <div class="flex items-center justify-between mb-2">
            <label class="block text-xs font-semibold text-gray-700 flex items-center gap-1.5">
              <MapPin class="w-3.5 h-3.5 text-[#004795]" />
              Hospital Location
              <span class="text-[11px] text-gray-400 font-normal">(Phase 1: click map · Phase 2: search address)</span>
            </label>
            <button
              v-if="form.latitude && form.longitude"
              type="button"
              @click="clearLocation"
              class="text-[11px] text-red-400 hover:text-red-600 font-medium transition"
            >
              Clear location
            </button>
          </div>

          <!-- Phase 2: Address search bar -->
          <div class="flex gap-2 mb-2">
            <div class="relative flex-1">
              <Search class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" />
              <input
                v-model="searchQuery"
                @keydown.enter.prevent="searchAddress"
                type="text"
                placeholder="Search address to move map (e.g. Tikur Anbessa Hospital)"
                class="w-full pl-8 pr-3 py-2 border border-gray-200 rounded-lg text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
              />
            </div>
            <button
              type="button"
              @click="searchAddress"
              :disabled="searchLoading || !searchQuery.trim()"
              class="flex items-center gap-1.5 px-3 py-2 bg-[#004795] hover:bg-[#003670] disabled:opacity-50 text-white text-xs font-semibold rounded-lg transition"
            >
              <Loader2 v-if="searchLoading" class="w-3.5 h-3.5 animate-spin" />
              <Search v-else class="w-3.5 h-3.5" />
              Search
            </button>
          </div>

          <!-- Search error -->
          <p v-if="searchError" class="text-[11px] text-red-500 mb-1.5 flex items-center gap-1">
            <AlertCircle class="w-3 h-3" /> {{ searchError }}
          </p>

          <!-- Phase 1: Interactive Leaflet map -->
          <div class="rounded-xl overflow-hidden border border-gray-200 relative" style="height:260px;">
            <!-- Hint overlay (shown until first click) -->
            <div
              v-if="!form.latitude && !form.longitude"
              class="absolute inset-0 z-[1000] pointer-events-none flex items-end justify-center pb-3"
            >
              <span class="bg-[#004795]/90 text-white text-[11px] font-semibold px-3 py-1.5 rounded-full shadow">
                👆 Click on the map to set the hospital location
              </span>
            </div>

            <div ref="mapContainer" class="w-full h-full" />
          </div>

          <!-- Coordinates display -->
          <div class="flex items-center justify-between mt-1.5">
            <span v-if="form.latitude && form.longitude" class="flex items-center gap-1.5 text-[11px] text-emerald-600 font-medium bg-emerald-50 border border-emerald-100 rounded px-2 py-0.5">
              <Navigation class="w-3 h-3" />
              {{ Number(form.latitude).toFixed(6) }}, {{ Number(form.longitude).toFixed(6) }}
              <span class="text-emerald-400 font-normal">— location set ✓</span>
            </span>
            <span v-else class="text-[11px] text-gray-400">No location set yet</span>
            <span v-if="form.latitude && form.longitude" class="text-[11px] text-gray-400">Drag the marker to fine-tune</span>
          </div>
        </div>

      </form>

      <!-- Footer -->
      <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0">
        <button type="button" @click="$emit('close')" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
          Cancel
        </button>
        <button
          @click="handleSubmit"
          :disabled="loading"
          class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2"
        >
          <Loader2 v-if="loading" class="w-3.5 h-3.5 animate-spin" />
          {{ isEditing ? 'Save Changes' : 'Register Hospital' }}
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted } from "vue";
import { X, AlertCircle, Loader2, MapPin, Navigation, Search } from "lucide-vue-next";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

// Fix Leaflet default icon broken in Vite
import markerIcon2x from "leaflet/dist/images/marker-icon-2x.png";
import markerIcon   from "leaflet/dist/images/marker-icon.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: markerIcon2x,
  iconUrl:       markerIcon,
  shadowUrl:     markerShadow,
});

// ── Props / Emits ─────────────────────────────────────────────────────────
const props = defineProps({
  hospital: { type: Object, default: null },
  loading:  { type: Boolean, default: false },
  error:    { type: String, default: null },
});
const emit = defineEmits(["close", "submit"]);

const isEditing = computed(() => !!props.hospital);

// ── Form state ────────────────────────────────────────────────────────────
const form = ref({
  name: "", code: "", address: "", city: "", region: "",
  phone: "", email: "", website: "", registration_number: "",
  latitude: null, longitude: null,
});

// Ethiopia default center — Addis Ababa
const DEFAULT_LAT = 9.0320;
const DEFAULT_LNG = 38.7469;
const DEFAULT_ZOOM = 12;

// ── Leaflet map — declared BEFORE the watch to avoid temporal dead zone ──
const mapContainer = ref(null);
let map    = null;
let marker = null;

watch(
  () => props.hospital,
  (h) => {
    if (h) {
      form.value = {
        name:                h.name                ?? "",
        code:                h.code                ?? "",
        address:             h.address             ?? "",
        city:                h.city                ?? "",
        region:              h.region              ?? "",
        phone:               h.phone               ?? "",
        email:               h.email               ?? "",
        website:             h.website             ?? "",
        registration_number: h.registration_number ?? "",
        latitude:            h.latitude            ?? null,
        longitude:           h.longitude           ?? null,
      };
    } else {
      form.value = {
        name: "", code: "", address: "", city: "", region: "",
        phone: "", email: "", website: "", registration_number: "",
        latitude: null, longitude: null,
      };
    }
    // Sync marker only if the map is already initialised
    if (map) syncMapMarker();
  },
  { immediate: true }
);

function initMap() {
  if (!mapContainer.value || map) return;

  const lat = form.value.latitude  ? Number(form.value.latitude)  : DEFAULT_LAT;
  const lng = form.value.longitude ? Number(form.value.longitude) : DEFAULT_LNG;
  const zoom = form.value.latitude ? 15 : DEFAULT_ZOOM;

  map = L.map(mapContainer.value, {
    center: [lat, lng],
    zoom,
    zoomControl: true,
    scrollWheelZoom: false,
  });

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors',
    maxZoom: 19,
  }).addTo(map);

  // Force Leaflet to recalculate container size in case it was zero at mount
  setTimeout(() => map && map.invalidateSize(), 100);

  // If editing and coords exist, place marker immediately
  if (form.value.latitude && form.value.longitude) {
    placeMarker(Number(form.value.latitude), Number(form.value.longitude));
  }

  // Phase 1: click on map to place / move marker
  map.on("click", (e) => {
    placeMarker(e.latlng.lat, e.latlng.lng);
  });
}

function placeMarker(lat, lng) {
  if (marker) {
    marker.setLatLng([lat, lng]);
  } else {
    marker = L.marker([lat, lng], { draggable: true }).addTo(map);

    // Phase 1: drag marker to fine-tune position
    marker.on("dragend", () => {
      const pos = marker.getLatLng();
      form.value.latitude  = pos.lat;
      form.value.longitude = pos.lng;
    });
  }
  form.value.latitude  = lat;
  form.value.longitude = lng;
}

function syncMapMarker() {
  if (!map) return;
  if (form.value.latitude && form.value.longitude) {
    const lat = Number(form.value.latitude);
    const lng = Number(form.value.longitude);
    placeMarker(lat, lng);
    map.setView([lat, lng], 15);
  } else if (marker) {
    marker.remove();
    marker = null;
  }
}

function clearLocation() {
  form.value.latitude  = null;
  form.value.longitude = null;
  if (marker) {
    marker.remove();
    marker = null;
  }
  if (map) map.setView([DEFAULT_LAT, DEFAULT_LNG], DEFAULT_ZOOM);
}

// ── Phase 2: Address search via Nominatim (free, no API key) ─────────────
const searchQuery   = ref("");
const searchLoading = ref(false);
const searchError   = ref(null);

async function searchAddress() {
  const q = searchQuery.value.trim();
  if (!q) return;

  searchLoading.value = true;
  searchError.value   = null;

  try {
    const params = new URLSearchParams({
      q,
      format:          "json",
      limit:           "1",
      countrycodes:    "et",   // bias toward Ethiopia; remove if international
    });

    const res  = await fetch(`https://nominatim.openstreetmap.org/search?${params}`, {
      headers: { "Accept-Language": "en" },
    });
    const data = await res.json();

    if (!data.length) {
      // Retry without country filter
      const res2  = await fetch(
        `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=1`,
        { headers: { "Accept-Language": "en" } }
      );
      const data2 = await res2.json();
      if (!data2.length) {
        searchError.value = "Address not found. Try a more specific name.";
        return;
      }
      applyGeoResult(data2[0]);
    } else {
      applyGeoResult(data[0]);
    }
  } catch {
    searchError.value = "Search failed. Check your internet connection.";
  } finally {
    searchLoading.value = false;
  }
}

function applyGeoResult(result) {
  const lat = parseFloat(result.lat);
  const lng = parseFloat(result.lon);

  // Move map and place marker
  if (map) map.setView([lat, lng], 16);
  placeMarker(lat, lng);

  // Auto-fill address if empty
  if (!form.value.address && result.display_name) {
    // Use first meaningful part of display_name
    form.value.address = result.display_name.split(",").slice(0, 3).join(",").trim();
  }

  searchError.value = null;
}

// ── Lifecycle ─────────────────────────────────────────────────────────────
onMounted(() => {
  // Use setTimeout so the modal DOM is fully painted before Leaflet tries
  // to measure the container height. nextTick alone is not enough when the
  // parent mounts this component via v-if.
  setTimeout(() => {
    initMap();
  }, 50);
});

onUnmounted(() => {
  if (map) {
    map.remove();
    map    = null;
    marker = null;
  }
});

// ── Submit ────────────────────────────────────────────────────────────────
function handleSubmit() {
  const common = {
    name:      form.value.name,
    address:   form.value.address,
    city:      form.value.city,
    phone:     form.value.phone    || null,
    email:     form.value.email    || null,
    latitude:  form.value.latitude  ? Number(form.value.latitude)  : null,
    longitude: form.value.longitude ? Number(form.value.longitude) : null,
  };

  const payload = isEditing.value
    ? common
    : {
        ...common,
        code:                form.value.code                || null,
        region:              form.value.region              || null,
        website:             form.value.website             || null,
        registration_number: form.value.registration_number || null,
      };

  emit("submit", payload);
}
</script>
