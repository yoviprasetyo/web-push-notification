
self.addEventListener('push', function(event) {

  const title = 'Web Push Notifications';
  const options = {
    body: event.data.text(),
  };

  const notificationPromise = self.registration.showNotification(title, options);
  event.waitUntil(notificationPromise);
});
  
self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  
  event.waitUntil(
    clients.openWindow('http://localhost:8000/')
  );
});
  