function getParam(name) {
  const queryString = location.search;
  const urlParams = new URLSearchParams(queryString);
  // console.log(urlParams);
  if (name) {
      return urlParams.get(name);
  }
  return urlParams;
}

// Names of the two caches used in this version of the service worker.
// Change to v2, etc. when you update any of the local resources, which will
// in turn trigger the install event again.
const PRECACHE = 'precache-v1';
const TINY_MCE = 'tinymce-v5.7.1';
const HOME_VERSION = 'home-t=' + (getParam('t') ? getParam('t') : '');
const RUNTIME = 'runtime';

const HOME_URL = [
  './', // Alias for index.html
  './dev',
  '/assets/blog.css',
  '/assets/styles.css',
  '/assets/display-messages.css',
  '/assets/bg1.jpg',
  '/scripts/blog-ui-ajax.js',
  '/scripts/display-messages.js'
];
const TINY_MCE_URL = [
  '/tinymce/tinymce.min.js',
  '/tinymce/icons/default/icons.min.js',
  '/tinymce/plugins/quickbars/plugin.min.js',
  '/tinymce/skins/ui/oxide/content.inline.min.css',
  '/tinymce/skins/ui/oxide/skin.min.css',
  '/tinymce/themes/silver/theme.min.js'
];
// A list of local resources we always want to be cached.
const PRECACHE_URLS = [
  // 'index.php',
  // '/sw.js',
  '/favicon.ico',
  '/icons/export.png',
  '/icons/export_dark.png',
  '/icons/DarkMode.png',
  '/icons/hamburger.png',
  '/icons/hamburger_dark.png',
  '/icons/moon.png',
  '/icons/moon_dark.png',
  '/icons/info.png',
  '/icons/cross.png',
  '/assets/roboto.woff2'
];

// The install handler takes care of precaching the resources we always need.
self.addEventListener('install', event => {
  // console.log(version);
  event.waitUntil(
    caches.open(PRECACHE)
      .then(cache => cache.addAll(PRECACHE_URLS))
      .then(self.skipWaiting())
  );
  event.waitUntil(
    caches.open(HOME_VERSION)
    .then(cache => cache.addAll(HOME_URL))
      .then(self.skipWaiting())
  );
  event.waitUntil(
    caches.open(TINY_MCE)
    .then(cache => cache.addAll(TINY_MCE_URL))
      .then(self.skipWaiting())
  );
});

async function deleteCacheEntriesMatching(cacheName, regexp) {
  const cache = await caches.open(cacheName);
  const cachedRequests = await cache.keys();
  // request.url is a full URL, not just a path, so use an appropriate RegExp!
  const requestsToDelete = cachedRequests.filter(request => request.url.match(regexp));
  return Promise.all(requestsToDelete.map(request => cache.delete(request)));
}
// The activate handler takes care of cleaning up old caches.
self.addEventListener('activate', event => {
  const currentCaches = [PRECACHE, HOME_VERSION, RUNTIME, TINY_MCE];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return cacheNames.filter(cacheName => !currentCaches.includes(cacheName));
    }).then(cachesToDelete => {
      return Promise.all(cachesToDelete.map(cacheToDelete => {
        console.log(cacheToDelete);
        return caches.delete(cacheToDelete);
      }));
    }).then(() => self.clients.claim())
  );
  deleteCacheEntriesMatching(RUNTIME, new RegExp('\/cdn-cgi'));
});

// The fetch handler serves responses for same-origin resources from a cache.
// If no response is found, it populates the runtime cache with the response
// from the network before returning it to the page.

function isSameOrigin(url) {
  var website = self.location.hostname;
  website = website.replace("www.", "");
  var internalLinkRegex = new RegExp(
    '^('
      +'(((http:\\/\\/|https:\\/\\/)(www\\.)?)?(' + website + '|(localhost.*)))' //starts with host
      +'|'  // or
      +'(localhost.*)' //starts with localhost
      +'|' // or
      +'((\\/|#|\\?|javascript:).*))'  //starts with / # ? javascript:
      +'((\\/|\\?|\#).*'  //ends with / # $
    +')?$'
    , '');
    return internalLinkRegex.test(url);
}

self.addEventListener('fetch', event => {
  if (event.request.method =='POST') {
    return false;
  }
  
  // Skip wp-admin and stuff
  if ( event.request.url.match( /(wp-admin)|(wp-login)|(phpmyadmin)|(cdn-cgi)/i) ) {
    return false;
  }


  // Skip cross-origin requests
  if (isSameOrigin(event.request.url)) {
    event.respondWith(
      // caches.match(event.request, {ignoreSearch: true}).then(cachedResponse => {
      caches.match(event.request).then(cachedResponse => {
        return caches.open(RUNTIME).then(cache => {
          // return new Response('no network', {status: 200, statusText: "OK"});
          if (cachedResponse && event.request.url.match( /(\.jpg|\.gif|\.png|\.jpeg|\.mov|\.mp4|\.woff)$/i) ) {
          // if (cachedResponse) {
            return cachedResponse;
          }
          if (!navigator.onLine){
            if (cachedResponse) {
              return cachedResponse;
            }
            else {
              // return new Response('no network', {status: 200, statusText: "OK"});
              return caches.match(event.request, {ignoreSearch: true}).then(cachedResponse => {
                  if (cachedResponse) {
                      return cachedResponse;
                  }
                  else {
                      return new Response('No network!', {status: 408, statusText: "Service Worker: No Network & no cache."});
                  }
              });
            }
          }
          
          return fetch(event.request).then(response => {
            if (!response && cachedResponse) {
              return cachedResponse;
            }
            // Put a copy of the response in the runtime cache.
            if (response.status == 200) {
              return cache.delete(event.request, {ignoreSearch: true}).then(() => {
                return cache.put(event.request, response.clone()).then(() => {
                  return response;
                });
              });
            }
            else {
              return response;
            }
          });
        });
      })
    );
  }
});

self.addEventListener('message', function (evt) {
    console.log('postMessage received', evt.data);
    evt.source.postMessage("Hi client");
    // evt.ports[0].postMessage({'hello': 'world'});
    //alert('b');
  })