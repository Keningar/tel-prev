self.addEventListener('push', function(e) {
  const payload = e.data.json();
  const options = {
    "body":payload.notification.body,
    "icon":payload.notification.icon
  };
  e.waitUntil(
    self.registration.showNotification(payload.notification.title, options)
  );
});