importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');


const firebaseConfig = {
    apiKey: "AIzaSyBcXtu7HGC2eHOaEH8msEBqomBpZ95mXi4",
    authDomain: "ent-center-notification.firebaseapp.com",
    projectId: "ent-center-notification",
    storageBucket: "ent-center-notification.firebasestorage.app",
    messagingSenderId: "381805619576",
    appId: "1:381805619576:web:88fdda76279981230cc08d",
    measurementId: "G-HQJV0L42N6"
};

// Initializing Firebase in Service Worker
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// การรับการแจ้งเตือนใน Background
messaging.onBackgroundMessage(function(payload) {
    console.log('Received background message ', payload);

    // การตั้งค่าการแจ้งเตือน
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon
    };

    // แสดงการแจ้งเตือน
    self.registration.showNotification(notificationTitle, notificationOptions);
});
