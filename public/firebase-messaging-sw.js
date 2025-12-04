importScripts(
    "https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"
);
importScripts(
    "https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js"
);

firebase.initializeApp({
    apiKey: "AIzaSyC2MUvepLCHUVg6ondQ8plEbiutJ2sEYz0",
    authDomain: "meuapppwa-f72da.firebaseapp.com",
    projectId: "meuapppwa-f72da",
    storageBucket: "meuapppwa-f72da.firebasestorage.app",
    messagingSenderId: "629286230886",
    appId: "1:629286230886:web:f5d45aaea590a725bd06a7",
    measurementId: "G-1K1VHKY9XJ",
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log("Mensagem recebida no SW:", payload);
    self.registration.showNotification(payload.data.title, {
        body: payload.data.body,
        icon: "/icon.png",
    });
});
