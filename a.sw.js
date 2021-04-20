

self.importScripts("https://storage.googleapis.com/workbox-cdn/releases/3.6.3/workbox-sw.js");


workbox.routing.registerRoute(
    new RegExp('.*\.js'),
    workbox.strategies.cacheFirst()
);


workbox.routing.registerRoute(
    new RegExp('.*\.css'),
    workbox.strategies.cacheFirst({
        cacheName: 'css-cache'
    })
);


workbox.routing.registerRoute(
    new RegExp('.*\.(?:png|jpg|jpeg|svg|gif)'),
    workbox.strategies.cacheFirst({
        cacheName: 'image-cache'
    })
);