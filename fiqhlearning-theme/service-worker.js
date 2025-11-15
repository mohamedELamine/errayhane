/**
 * Service Worker for FiqhLearning Theme
 * Provides offline functionality and caching
 */

const CACHE_VERSION = 'fiqh-v1.0.0';
const CACHE_NAME = `fiqhlearning-${CACHE_VERSION}`;

// الملفات الأساسية التي يجب تخزينها مؤقتاً
const STATIC_CACHE_URLS = [
  '/',
  '/wp-content/themes/fiqhlearning-theme/style.css',
  '/wp-content/themes/fiqhlearning-theme/assets/css/main.css',
  '/wp-content/themes/fiqhlearning-theme/assets/css/components.css',
  '/wp-content/themes/fiqhlearning-theme/assets/js/main.js',
  '/wp-content/themes/fiqhlearning-theme/manifest.json'
];

// تثبيت Service Worker
self.addEventListener('install', (event) => {
  console.log('Service Worker: Installing...');

  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Service Worker: Caching static assets');
        return cache.addAll(STATIC_CACHE_URLS.map(url => new Request(url, {cache: 'reload'})));
      })
      .then(() => self.skipWaiting())
      .catch((error) => {
        console.error('Service Worker: Cache failed', error);
      })
  );
});

// تفعيل Service Worker
self.addEventListener('activate', (event) => {
  console.log('Service Worker: Activating...');

  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== CACHE_NAME) {
              console.log('Service Worker: Deleting old cache', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => self.clients.claim())
  );
});

// استراتيجية التخزين المؤقت
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // تجاهل طلبات admin و login
  if (url.pathname.includes('/wp-admin') || url.pathname.includes('/wp-login')) {
    return;
  }

  // تجاهل طلبات POST
  if (request.method !== 'GET') {
    return;
  }

  event.respondWith(
    caches.match(request)
      .then((cachedResponse) => {
        // إذا كان موجود في Cache، أرجعه
        if (cachedResponse) {
          // تحديث Cache في الخلفية (stale-while-revalidate)
          fetch(request)
            .then((response) => {
              if (response && response.status === 200) {
                caches.open(CACHE_NAME)
                  .then((cache) => {
                    cache.put(request, response);
                  });
              }
            })
            .catch(() => {
              // تجاهل الأخطاء في التحديث الخلفي
            });

          return cachedResponse;
        }

        // إذا لم يكن موجود، اجلبه من الشبكة
        return fetch(request)
          .then((response) => {
            // تأكد من صحة الاستجابة
            if (!response || response.status !== 200 || response.type === 'error') {
              return response;
            }

            // استنسخ الاستجابة
            const responseToCache = response.clone();

            // تخزين الصفحات والموارد الثابتة
            if (
              request.destination === 'document' ||
              request.destination === 'style' ||
              request.destination === 'script' ||
              request.destination === 'image' ||
              request.destination === 'font'
            ) {
              caches.open(CACHE_NAME)
                .then((cache) => {
                  cache.put(request, responseToCache);
                });
            }

            return response;
          })
          .catch(() => {
            // في حالة عدم توفر الشبكة، أرجع صفحة offline إذا كانت متوفرة
            if (request.destination === 'document') {
              return caches.match('/offline.html')
                .then((offlineResponse) => {
                  if (offlineResponse) {
                    return offlineResponse;
                  }
                  // صفحة offline بسيطة
                  return new Response(
                    `<!DOCTYPE html>
                    <html lang="ar" dir="rtl">
                    <head>
                      <meta charset="UTF-8">
                      <meta name="viewport" content="width=device-width, initial-scale=1">
                      <title>غير متصل</title>
                      <style>
                        body {
                          font-family: 'Cairo', sans-serif;
                          text-align: center;
                          padding: 50px;
                          direction: rtl;
                        }
                        h1 { color: #2C5F2D; }
                      </style>
                    </head>
                    <body>
                      <h1>غير متصل بالإنترنت</h1>
                      <p>يبدو أنك غير متصل بالإنترنت. يرجى التحقق من اتصالك والمحاولة مرة أخرى.</p>
                    </body>
                    </html>`,
                    {
                      headers: { 'Content-Type': 'text/html; charset=utf-8' }
                    }
                  );
                });
            }

            return new Response('', { status: 408, statusText: 'Network request failed' });
          });
      })
  );
});

// التعامل مع الرسائل
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }

  if (event.data && event.data.type === 'CLEAR_CACHE') {
    event.waitUntil(
      caches.keys().then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => caches.delete(cacheName))
        );
      })
    );
  }
});
