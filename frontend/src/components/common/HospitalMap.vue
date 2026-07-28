<script setup>
import { LMap, LTileLayer, LMarker } from "@vue-leaflet/vue-leaflet";

import "leaflet/dist/leaflet.css";

const props = defineProps({
  latitude: {
    type: Number,
    required: true,
  },

  longitude: {
    type: Number,
    required: true,
  },
});

const zoom = 15;
</script>

<template>
  <div class="h-80 w-full rounded-xl overflow-hidden">
    <LMap :zoom="zoom" :center="[latitude, longitude]">
      <LTileLayer url="https://tile.openstreetmap.org/{z}/{x}/{y}.png" />

      <LMarker :lat-lng="[latitude, longitude]" />
    </LMap>
  </div>
</template>
