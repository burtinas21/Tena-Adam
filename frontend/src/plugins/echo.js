import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

const key     = import.meta.env.VITE_PUSHER_APP_KEY;
const cluster = import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "mt1";
const host    = import.meta.env.VITE_PUSHER_HOST;
const port    = import.meta.env.VITE_PUSHER_PORT    ? Number(import.meta.env.VITE_PUSHER_PORT)    : undefined;
const wsPort  = import.meta.env.VITE_PUSHER_WS_PORT ? Number(import.meta.env.VITE_PUSHER_WS_PORT) : undefined;
const scheme  = import.meta.env.VITE_PUSHER_SCHEME  ?? "https";

// Build Echo config — works for both Pusher cloud and a self-hosted Reverb/Soketi server
const config = {
    broadcaster: "pusher",
    key,
    cluster,
    forceTLS: scheme === "https",
    // If a custom host is set (e.g. Reverb / Soketi), use it; otherwise fall back to Pusher cloud
    ...(host ? {
        wsHost:  host,
        wsPort:  wsPort ?? port ?? (scheme === "https" ? 443 : 80),
        enabledTransports: ["ws", "wss"],
        disableStats: true,
    } : {}),
};

export const echo = new Echo(config);
