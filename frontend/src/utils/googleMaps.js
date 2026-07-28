import { Loader } from "@googlemaps/js-api-loader";


const loader = new Loader({

    apiKey: import.meta.env.VITE_GOOGLE_MAPS_KEY,

    version: "weekly",

    libraries: [
        "places"
    ]

});


export default loader;