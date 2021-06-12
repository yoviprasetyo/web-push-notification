<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Push Notifications</title>
  </head>
  <body>
    <h1>Learn Web Push Notifications</h1>

    <div>
      <span>Push Messaging Status:</span>
      <span id="push-status"></span>
    </div>

    <div>
      <span>Service Worker Status (sw.js):</span>
      <span id="sw-status">Not Registered Yet</span>
    </div>

    <div>
      <span>Subscription Status:</span>
      <span id="subscription-status"></span>
    </div>

    <div>
      <span>JSON:</span>
      <span id="json-container"></span>
    </div>

    <div>
      <ol>
        <li>Open This file in Code Editor</li>
        <li>Get Public and Private Keys at <a href="https://web-push-codelab.glitch.me/" target="_blank">https://web-push-codelab.glitch.me/</a></li>
        <li>Copy the Public key value to <code>const publicKey = 'copy-here'</code></li>
        <li><button id="notif-btn">Click</button> and then Allow Notifications</li>
        <li>Send a Push Messages</li>
      </ol>
    </div>

    <div>
      
    </div>
    <script>

    // Document Element
    const pushStatus    = document.querySelector('#push-status');
    const swStatus      = document.querySelector('#sw-status');
    const subStatus     = document.querySelector('#subscription-status');
    const notifBtn      = document.querySelector('#notif-btn');
    const jsonContainer = document.querySelector('#json-container');

    // Public Key
    const publicKey     = 'copy-here';

    // Variable that change
    var swRegistration  = null;
    var isSubscribed    = false;

    function registerSw() {
      if ('serviceWorker' in navigator && 'PushManager' in window) {
        pushStatus.innerHTML  = 'Supported'

        navigator.serviceWorker.register('sw.js')
        .then(function(swReg) {
          swStatus.innerHTML  = 'Registered';
          swRegistration      = swReg;
          getSubscription();
          updateNotificationStatus();
          UI();
        })
        .catch(function(error) {
          swStatus.innerHTML  = 'Service Worker Error' + error;
        });
      } else {
        pushStatus.innerHTML = 'Not Supported'
      }
    }

    function urlBase64ToUint8Array(base64String) {
      const padding     = '='.repeat((4 - base64String.length % 4) % 4);
      const base64      = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
      const rawData = window.atob(base64);
      const outputArray = new Uint8Array(rawData.length);
      for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
      }
      return outputArray;
    }

    function updateSubscription(status) {
      if( status ) {
        isSubscribed = true;
        subStatus.innerHTML = 'Subscribed!';
      } else {
        isSubscribed = false;
        subStatus.innerHTML = 'Not Subscribed!';
      }
    }

    function updateSubscriptionOnServer(subscription) {
      jsonContainer.textContent = ''
      if (subscription) {
        jsonContainer.textContent = JSON.stringify(subscription);
      }
    }

    function updateNotificationStatus() {
      if (Notification.permission === 'denied') {
        pushStatus.innerHTML = 'Blocked';
        updateSubscriptionOnServer(null);
        return;
      }
    }

    function unsubscribeUser(event) {
      swRegistration.pushManager.getSubscription()
      .then(function(subscription) {
        if( subscription ) {
          return subscription.unsubscribe();
        }
      })
      .then(function() {
        updateSubscriptionOnServer(null);
        updateSubscription(false);
        updateNotificationStatus();
      });

      UI();
    }

    function subscribeUser(event) {
      const applicationServerKey = urlBase64ToUint8Array(publicKey)

      swRegistration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: applicationServerKey
      })
      .then(function(subscription) {
        updateSubscription(true);
        updateSubscriptionOnServer(subscription);
        updateNotificationStatus();
      })
      .catch(function(err) {
        updateNotificationStatus();
      });

      UI();
    }

    function addButtonToUnSubscribe() {
      notifBtn.addEventListener('click', unsubscribeUser);
    }

    function addButtonToSubscribe() {
      notifBtn.addEventListener('click', subscribeUser);
    }

    function UI() {
      if( !isSubscribed ) {
        addButtonToSubscribe()
      } else {
        addButtonToUnSubscribe()
      }
    }

    function getSubscription() {
      swRegistration.pushManager.getSubscription()
      .then(function(subscription) {
        isSubscribed = !(subscription === null);
    
        if (isSubscribed) {
          subStatus.innerHTML = 'Subscribed';
        } else {
          subStatus.innerHTML = 'Not Subscribed';
        }
      });
    }

    registerSw();
    </script>
  </body>
</html>