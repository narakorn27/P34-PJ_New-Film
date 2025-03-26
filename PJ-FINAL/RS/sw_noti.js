importScripts("https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging-compat.js");

const firebaseConfig = {
    apiKey: "AIzaSyBcXtu7HGC2eHOaEH8msEBqomBpZ95mXi4",
    authDomain: "ent-center-notification.firebaseapp.com",
    projectId: "ent-center-notification",
    storageBucket: "ent-center-notification.firebasestorage.app",
    messagingSenderId: "381805619576",
    appId: "1:381805619576:web:88fdda76279981230cc08d",
    measurementId: "G-HQJV0L42N6"
};

// ✅ Initialize Firebase
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log("📩 ข้อความได้รับขณะอยู่เบื้องหลัง:", payload);
    self.registration.showNotification(payload.notification.title, {
        body: payload.notification.body,
        icon: payload.notification.icon
    });
});
