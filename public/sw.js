const CACHE_NAME = 'gfi-emergency-v10';

const EMERGENCY_URL = '/attendance/emergency';

self.addEventListener('install', event => {

    console.log('GFI Emergency SW: INSTALL');

    event.waitUntil(

        caches.open(CACHE_NAME)
            .then(cache => {

                console.log('GFI Emergency SW: caching emergency page');

                return cache.add(
                    EMERGENCY_URL
                );

            })
            .catch(error => {

                console.error(
                    'GFI Emergency SW cache error:',
                    error
                );

            })

            .then(() => {

                return self.skipWaiting();

            })

    );

});


self.addEventListener('activate', event => {

    console.log('GFI Emergency SW: ACTIVATE');

    event.waitUntil(

        caches.keys()
            .then(cacheNames => {

                return Promise.all(

                    cacheNames.map(cacheName => {

                        if (
                            cacheName !== CACHE_NAME
                        ) {

                            return caches.delete(
                                cacheName
                            );

                        }

                    })

                );

            })

            .then(() => {

                return self.clients.claim();

            })

    );

});


self.addEventListener('fetch', event => {

    const request =
        event.request;

    const url =
        new URL(request.url);


    /*
     * Only handle the Emergency Roll Call URL.
     */

    if (
        url.pathname !== EMERGENCY_URL
    ) {

        return;

    }


    if (
        request.method !== 'GET'
    ) {

        return;

    }


    event.respondWith(

        fetch(request)

            .then(response => {

                /*
                 * Server is available.
                 *
                 * Save the newest Emergency page.
                 */

                if (
                    response
                    &&
                    response.ok
                ) {

                    const responseClone =
                        response.clone();

                    caches.open(
                        CACHE_NAME
                    )
                        .then(cache => {

                            cache.put(
                                EMERGENCY_URL,
                                responseClone
                            );

                        });

                }


                return response;

            })

            .catch(error => {

                console.log(
                    'GFI Emergency SW: OFFLINE',
                    error
                );


                /*
                 * Server unavailable.
                 *
                 * Return the last saved Emergency page.
                 */

                return caches.match(
                    EMERGENCY_URL
                )
                    .then(cachedResponse => {

                        if (cachedResponse) {

                            return cachedResponse;

                        }


                        return new Response(

                            `
                            <!DOCTYPE html>

                            <html>

                            <head>

                                <meta
                                    name="viewport"
                                    content="width=device-width, initial-scale=1.0"
                                >

                                <title>
                                    Emergency Roll Call
                                </title>

                                <style>

                                    body {
                                        margin: 0;
                                        padding: 30px;
                                        font-family: Arial, sans-serif;
                                        background: #7f1d1d;
                                        color: white;
                                        text-align: center;
                                    }

                                    .box {
                                        max-width: 600px;
                                        margin: 60px auto;
                                        padding: 35px;
                                        background: white;
                                        color: #111827;
                                        border-radius: 15px;
                                    }

                                    h1 {
                                        color: #991b1b;
                                    }

                                </style>

                            </head>

                            <body>

                                <div class="box">

                                    <h1>
                                        🚨 EMERGENCY ROLL CALL
                                    </h1>

                                    <p>
                                        The device is offline.
                                    </p>

                                    <p>
                                        No Emergency Roll Call
                                        information has been cached
                                        on this device yet.
                                    </p>

                                    <p>
                                        Please connect to the
                                        Attendance System once and
                                        open this page before using
                                        offline mode.
                                    </p>

                                </div>

                            </body>

                            </html>
                            `,

                            {
                                status: 503,

                                headers: {
                                    'Content-Type':
                                        'text/html; charset=UTF-8'
                                }

                            }

                        );

                    });

            })

    );

});