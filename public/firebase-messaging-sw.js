importScripts(
    "https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"
);
importScripts(
    "https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js"
);

// Config vinda da query string do registro do SW (definida no .env, por cliente).
const params = new URLSearchParams(location.search);

firebase.initializeApp({
    apiKey: params.get("apiKey"),
    authDomain: params.get("authDomain"),
    projectId: params.get("projectId"),
    storageBucket: params.get("storageBucket"),
    messagingSenderId: params.get("messagingSenderId"),
    appId: params.get("appId"),
    measurementId: params.get("measurementId"),
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log("Mensagem recebida no SW:", payload);
    self.registration.showNotification(payload.data.title, {
        body: payload.data.body,
        icon: payload.data.icon,
        data: {
            url: payload.data.url,
        },
    });
});

self.addEventListener("notificationclick", (event) => {
    event.notification.close();

    event.waitUntil(clients.openWindow(event.notification.data.url));
});
