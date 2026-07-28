<script setup>

import GoogleMap from "../components/common/GoogleMap.vue";
const hospital={

name:"Tena Adam Hospital",

location:{

latitude:9.0320,

longitude:38.7469

}

};


</script>


<template>


<div>

<h1 class="text-xl font-bold">

{{hospital.name}}

</h1>



<GoogleMap

:latitude="hospital.location.latitude"

:longitude="hospital.location.longitude"

:hospitalName="hospital.name"

/>


</div>


</template>