import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

export const echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});
import { echo } from "@/plugins/echo";

echo.channel("queue." + doctorId)
    .listen(".queue.updated", (e) => {
        console.log("Queue updated:", e.queue);
    });
    Echo.channel('reception.queue')
    .listen('.queue.updated', (e) => {
        refreshAllQueues();
    });