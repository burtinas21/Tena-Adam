// import { createRouter, createWebHistory } from "vue-router";

// import authRoutes from "./auth";
// import platformRoutes from "./platform";
// import hospitalAdminRoutes from "./hospitalAdmin";
// import doctorRoutes from "./doctor";
// import patientRoutes from "./patient";
// import receptionistRoutes from "./receptionist";

// import { setupGuards } from "./guards";

// const router = createRouter({

//   history: createWebHistory(),

//   routes: [

//     {
//       path: "/",
//       redirect: "/login",
//     },

//     ...authRoutes,

//     ...platformRoutes,

//     ...hospitalAdminRoutes,

//     ...doctorRoutes,

//     ...patientRoutes,

//     ...receptionistRoutes,

//     {
//       path: "/:pathMatch(.*)*",

//       redirect: "/login",

//     },

//   ],

// });

// setupGuards(router);

// export default router;
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
      name: "home",
      component: () => import("@/views/landing/Home.vue"),
      meta: {
        public: true,
      },    
    },

    ...authRoutes,
    ...platformRoutes,

    ...hospitalAdminRoutes,

    ...doctorRoutes,

    ...patientRoutes,

    ...receptionistRoutes,


    {
      path: "/:pathMatch(.*)*",
      name: "not-found",
      redirect: "/",
    },
  ],
});

setupGuards(router);

export default router;
