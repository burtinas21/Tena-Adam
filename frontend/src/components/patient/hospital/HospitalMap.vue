<template>
  <div class="relative w-full" :style="{ height: height }">
    <l-map
      ref="mapRef"
      :zoom="zoom"
      :center="[lat, lng]"
      :use-global-leaflet="false"
      class="w-full h-full rounded-b-xl z-0"
      :options="{ zoomControl: true, scrollWheelZoom: false, attributionControl: true }"
    >
      <l-tile-layer
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        attribution='&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
        layer-type="base"
        name="OpenStreetMap"
      />
      <l-marker :lat-lng="[lat, lng]">
        <l-tooltip :options="{ permanent: true, direction: 'top', offset: [0, -10] }">
          {{ label }}
        </l-tooltip>
      </l-marker>
    </l-map>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { LMap, LTileLayer, LMarker, LTooltip } from "@vue-leaflet/vue-leaflet";
import "leaflet/dist/leaflet.css";

// Fix Leaflet default marker icon broken in Vite/Webpack builds
import L from "leaflet";
import markerIcon2x from "leaflet/dist/images/marker-icon-2x.png";
import markerIcon from "leaflet/dist/images/marker-icon.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: markerIcon2x,
  iconUrl: markerIcon,
  shadowUrl: markerShadow,
});

const props = defineProps({
  lat:    { type: Number, required: true },
  lng:    { type: Number, required: true },
  label:  { type: String, default: "" },
  zoom:   { type: Number, default: 15 },
  height: { type: String, default: "260px" },
});
</script>
