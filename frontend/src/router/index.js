import { createRouter, createWebHistory } from "vue-router";

import authRoutes from "./auth";
import platformRoutes from "./platform";
import hospitalAdminRoutes from "./hospitalAdmin";
import doctorRoutes from "./doctor";
import patientRoutes from "./patient";
import receptionistRoutes from "./receptionist";

import { setupGuards } from "./guards";


const router = createRouter({

  history: createWebHistory(),


  routes: [

    {
      path: "/",
      redirect: "/login",
    },


    ...authRoutes,

    ...platformRoutes,

    ...hospitalAdminRoutes,

    ...doctorRoutes,

    ...patientRoutes,

    ...receptionistRoutes,


    {
      path: "/:pathMatch(.*)*",

      redirect: "/login",

    },

  ],

});


setupGuards(router);


export default router;