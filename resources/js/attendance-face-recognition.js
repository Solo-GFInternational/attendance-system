// Uses the one face-api copy the page already loads (public/js/face-api),
// so the tablet downloads it once and registration shares the same models.
const faceapi = window.faceapi;

const MODEL_URL = '/models';

// Smaller detector input = much faster on tablets. The face must fill at
// least 18% of the frame width anyway, so 320 finds it easily. The face
// descriptor itself is still taken from the full-size camera image.
const DETECTOR_OPTIONS = faceapi
    ? new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.6 })
    : null;

let modelsPromise = null;

let stream = null;
let recognitionRunning = false;
let modelsLoaded = false;
let recognitionInProgress = false;
let recognitionTimer = null;

// Accuracy: identify from several consecutive good frames, not one.
// The server checks that every frame points to the same employee.
const REQUIRED_SAMPLES = 3;
let samples = [];

const startButton =
    document.getElementById('startRecognitionButton');

const video =
    document.getElementById('recognitionVideo') ||
    document.getElementById('cameraPreview');

const statusElement =
    document.getElementById('recognitionStatus');

const frame =
    document.getElementById('recognitionFrame');


// ============================================================
// CHECK REQUIRED ELEMENTS
// ============================================================

if (!startButton || !video) {

    console.log(
        'Face recognition elements not found on this page.'
    );

} else {


    // ========================================================
    // LOAD FACE RECOGNITION MODELS
    // ========================================================

    // Loads the three models at the same time and warms them up once,
    // so the first scan after a tap does not pay the start-up cost.
    // Safe to call many times: everyone shares one promise.
    function loadModels() {

        if (modelsPromise) {
            return modelsPromise;
        }

        if (!faceapi) {
            return Promise.reject(
                new Error('Face recognition library is not available.')
            );
        }

        const started = performance.now();

        modelsPromise = (async () => {

            // Lets the graphics engine reuse compiled code across image
            // sizes: warm-up measured about twice as fast with this on.
            try {
                faceapi.tf.env().set('WEBGL_USE_SHAPES_UNIFORMS', true);
            } catch (error) {
                // Older engines without the flag still work.
            }

            await faceapi.tf.ready();

            await Promise.all([
                faceapi.nets.tinyFaceDetector.isLoaded || faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.isLoaded || faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.isLoaded || faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
            ]);

            await warmUp();

            modelsLoaded = true;

            console.log(
                'Face recognition ready in ' +
                Math.round(performance.now() - started) + ' ms (' +
                faceapi.tf.getBackend() + ').'
            );

            return true;

        })().catch(error => {

            console.error('Failed to load face recognition models:', error);

            modelsPromise = null;

            modelsLoaded = false;

            throw error;
        });

        return modelsPromise;
    }


    // The first run of each model compiles its graphics code, which can take
    // a few seconds on a tablet. Do it now on a blank image, not on a person.
    async function warmUp() {

        try {

            const canvas = document.createElement('canvas');
            canvas.width = 320;
            canvas.height = 320;
            canvas.getContext('2d').fillRect(0, 0, 320, 320);

            await faceapi.detectSingleFace(canvas, DETECTOR_OPTIONS);
            await faceapi.detectFaceLandmarks(canvas);
            await faceapi.computeFaceDescriptor(canvas);

        } catch (error) {

            console.warn('Face model warm-up skipped:', error);
        }
    }


    // ========================================================
    // START RECOGNITION
    // ========================================================

    async function startRecognition() {

        if (recognitionRunning) {
            return;
        }

        try {

            clearRecognitionTimer();

            startButton.disabled = true;

            // Models are normally already loaded (started when the page
            // opened). Start the camera at the same time, not after.
            const modelsReady = loadModels();

            modelsReady.catch(() => {}); // reported below when awaited

            if (!modelsLoaded && statusElement) {
                statusElement.textContent =
                    'Starting face recognition...';
            }


            // ------------------------------------------------
            // REQUEST CAMERA
            // ------------------------------------------------

            stream =
                await navigator.mediaDevices.getUserMedia({

                    video: {

                        facingMode: 'user',

                        width: {
                            ideal: 1280
                        },

                        height: {
                            ideal: 720
                        }
                    },

                    audio: false
                });


            // ------------------------------------------------
            // CONNECT CAMERA TO VIDEO
            // ------------------------------------------------

            video.srcObject = stream;

            // Size and position come from the page CSS (.face-recognition-frame).
            video.style.display = 'block';

            if (frame) {
                frame.classList.add('is-active');

                // Landscape tablets: bring the whole camera and the button
                // into view instead of leaving them below the screen edge.
                const box = frame.closest('.face-recognition-box') || frame;

                requestAnimationFrame(() => {
                    box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });
            }


            await video.play();

            await modelsReady;


            // ------------------------------------------------
            // START RECOGNITION
            // ------------------------------------------------

            recognitionRunning = true;

            recognitionInProgress = false;


            if (statusElement) {

                statusElement.textContent =
                    'Look at the camera and fit your face in the oval.';
            }


            recognizeLoop();


        } catch (error) {

            console.error(
                'Unable to start face recognition:',
                error
            );

            recognitionRunning = false;

            recognitionInProgress = false;

            clearRecognitionTimer();

            stopCamera();

            startButton.disabled = false;


            if (statusElement) {

                statusElement.textContent =
                    modelsLoaded
                        ? 'Unable to start the camera.'
                        : 'Unable to start face recognition. Please reload the page.';
            }
        }
    }


    // ========================================================
    // FACE RECOGNITION LOOP
    // ========================================================

    async function recognizeLoop() {

        if (!recognitionRunning) {
            return;
        }


        if (recognitionInProgress) {

            scheduleNextRecognition(100);

            return;
        }


        try {

            if (
                video.readyState <
                HTMLMediaElement.HAVE_CURRENT_DATA
            ) {

                scheduleNextRecognition(100);

                return;
            }


            if (
                video.videoWidth <= 0 ||
                video.videoHeight <= 0
            ) {

                scheduleNextRecognition(100);

                return;
            }


            recognitionInProgress = true;


            // ------------------------------------------------
            // DETECT FACE
            // ------------------------------------------------

            const detection =
                await faceapi
                    .detectSingleFace(
                        video,
                        DETECTOR_OPTIONS
                    )
                    .withFaceLandmarks()
                    .withFaceDescriptor();


            // ------------------------------------------------
            // FACE FOUND
            // ------------------------------------------------

            if (detection) {

                const box =
                    detection.detection.box;


                const faceWidthRatio =
                    box.width /
                    video.videoWidth;


                const faceCenterX =
                    box.x +
                    (box.width / 2);


                const videoCenterX =
                    video.videoWidth / 2;


                const centerDifference =
                    Math.abs(
                        faceCenterX -
                        videoCenterX
                    ) /
                    video.videoWidth;


                // ------------------------------------------------
                // CHECK FACE POSITION
                // Large enough, centred, and facing the camera
                // (side-on faces give unreliable descriptors).
                // ------------------------------------------------

                const positionOk =
                    faceWidthRatio >= 0.18 &&
                    centerDifference <= 0.20;

                const facingCamera =
                    positionOk &&
                    isFacingCamera(detection.landmarks);

                if (positionOk && facingCamera) {

                    samples.push(
                        Array.from(detection.descriptor)
                    );


                    if (samples.length < REQUIRED_SAMPLES) {

                        if (statusElement) {
                            statusElement.textContent =
                                'Face detected. Hold still…';
                        }

                        recognitionInProgress = false;

                        // Next sample almost straight away: just long enough
                        // for the camera to give a new frame.
                        scheduleNextRecognition(40);

                        return;
                    }


                    if (statusElement) {

                        statusElement.textContent =
                            'Identifying...';
                    }


                    const toSend = samples;

                    samples = [];


                    const matched =
                        await sendDescriptors(
                            toSend
                        );


                    recognitionInProgress = false;


                    if (matched) {

                        return;
                    }


                    scheduleNextRecognition(400);

                    return;
                }


                if (statusElement && positionOk && !facingCamera) {

                    statusElement.textContent =
                        'Please look straight at the camera.';
                }
            }


            // No usable face in this frame: start the sample set again
            // so frames from different people are never mixed.
            samples = [];

            recognitionInProgress = false;

            scheduleNextRecognition(120);


        } catch (error) {

            console.error(
                'Face recognition error:',
                error
            );

            recognitionInProgress = false;

            scheduleNextRecognition(300);
        }
    }


    // ========================================================
    // SINGLE RECOGNITION SCHEDULER
    // ========================================================

    function scheduleNextRecognition(delay = 500) {

        if (!recognitionRunning) {
            return;
        }

        clearRecognitionTimer();


        recognitionTimer =
            setTimeout(
                () => {

                    recognitionTimer = null;

                    recognizeLoop();

                },
                delay
            );
    }


    // ========================================================
    // CLEAR RECOGNITION TIMER
    // ========================================================

    function clearRecognitionTimer() {

        if (recognitionTimer !== null) {

            clearTimeout(
                recognitionTimer
            );

            recognitionTimer = null;
        }
    }


    // ========================================================
    // IS THE FACE TURNED TOWARDS THE CAMERA?
    // Nose tip roughly between the eyes, eyes roughly level.
    // ========================================================

    function isFacingCamera(landmarks) {

        const centre = points => ({
            x: points.reduce((s, p) => s + p.x, 0) / points.length,
            y: points.reduce((s, p) => s + p.y, 0) / points.length,
        });

        const leftEye = centre(landmarks.getLeftEye());
        const rightEye = centre(landmarks.getRightEye());
        const noseTip = landmarks.getNose()[3];

        const eyeDistance = Math.hypot(
            rightEye.x - leftEye.x,
            rightEye.y - leftEye.y
        );

        if (eyeDistance <= 0) {
            return false;
        }

        const turn =
            Math.abs(noseTip.x - (leftEye.x + rightEye.x) / 2) / eyeDistance;

        const tilt =
            Math.abs(rightEye.y - leftEye.y) / eyeDistance;

        return turn <= 0.22 && tilt <= 0.25;
    }


    // ========================================================
    // SEND FACE DESCRIPTORS TO LARAVEL
    // ========================================================

    async function sendDescriptors(descriptors) {

        try {

            const csrfTokenElement =
                document.querySelector(
                    'meta[name="csrf-token"]'
                );


            if (!csrfTokenElement) {

                throw new Error(
                    'CSRF token not found.'
                );
            }


            const csrfToken =
                csrfTokenElement.getAttribute(
                    'content'
                );


            const response =
                await fetch(
                    '/attendance/recognize',
                    {

                        method: 'POST',

                        headers: {

                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json',

                            'X-CSRF-TOKEN':
                                csrfToken
                        },

                        body:
                            JSON.stringify({

                                face_descriptors:
                                    descriptors
                            })
                    }
                );


            const data =
                await response.json();


            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'Recognition request failed.'
                );
            }


            // ------------------------------------------------
            // EMPLOYEE MATCHED
            // ------------------------------------------------

            if (data.matched) {

                recognitionRunning = false;

                recognitionInProgress = false;

                clearRecognitionTimer();


                if (statusElement) {

                    statusElement.textContent =
                        'Employee recognized — ready to sign in.';
                }


                displayEmployee(
                    data.employee
                );


                return true;
            }


            // ------------------------------------------------
            // NO MATCH
            // ------------------------------------------------

            if (statusElement) {

                statusElement.textContent =
                    data.message ||
                    'Face not recognized. Please try again.';
            }


            return false;


        } catch (error) {

            console.error(
                'Face recognition request error:',
                error
            );


            if (statusElement) {

                statusElement.textContent =
                    error.message ||
                    'Recognition failed.';
            }


            return false;
        }
    }


    // ========================================================
    // DISPLAY RECOGNIZED EMPLOYEE
    // ========================================================

    function displayEmployee(employee) {

        const name =
            document.getElementById(
                'employeeName'
            );


        const code =
            document.getElementById(
                'employeeCode'
            );


        const employeeStatus =
            document.getElementById(
                'employeeStatus'
            );


        const employeeSelect =
            document.getElementById(
                'employeeSelect'
            );


        const employeeSearch =
            document.getElementById(
                'employeeSearch'
            );


        // ----------------------------------------------------
        // UPDATE NAME
        // ----------------------------------------------------

        if (name) {

            name.textContent =
                employee.name;
        }


        // ----------------------------------------------------
        // UPDATE EMPLOYEE CODE
        // ----------------------------------------------------

        if (code) {

            code.textContent =
                employee.employee_code ||
                '';
        }


        // ----------------------------------------------------
        // UPDATE EMPLOYEE STATUS
        // ----------------------------------------------------

        if (employeeStatus) {

            employeeStatus.dataset.employeeId =
                employee.id;
        }


        // ----------------------------------------------------
        // SELECT RECOGNIZED EMPLOYEE
        // ----------------------------------------------------

        if (employeeSelect) {

            // The list only holds this site's employees; someone from
            // another site can still be recognised, so add them if missing.
            let option = Array.from(employeeSelect.options)
                .find(o => o.value === String(employee.id));

            if (!option) {

                option = document.createElement('option');
                option.value = String(employee.id);
                option.textContent = employee.name;
                option.dataset.name = employee.name;
                option.dataset.code = employee.employee_code || '';
                employeeSelect.appendChild(option);
            }

            // Recognised by face, so the face is registered.
            option.dataset.faceRegistered = '1';

            employeeSelect.value =
                String(employee.id);


            employeeSelect.dispatchEvent(
                new Event(
                    'change',
                    {
                        bubbles: true
                    }
                )
            );
        }


        // ----------------------------------------------------
        // UPDATE SEARCH BOX
        // ----------------------------------------------------

        if (employeeSearch) {

            employeeSearch.value =
                employee.name +
                (
                    employee.employee_code
                        ? ' (' +
                          employee.employee_code +
                          ')'
                        : ''
                );
        }
    }


    // ========================================================
    // STOP CAMERA
    // ========================================================

    function stopCamera() {

        samples = [];

        recognitionRunning = false;

        recognitionInProgress = false;

        clearRecognitionTimer();


        if (stream) {

            stream
                .getTracks()
                .forEach(
                    track => {

                        try {

                            track.stop();

                        } catch (error) {

                            console.error(
                                'Error stopping camera track:',
                                error
                            );
                        }
                    }
                );

            stream = null;
        }


        if (video) {

            try {

                video.pause();

            } catch (error) {

                console.error(error);
            }

            video.srcObject = null;
        }


        if (frame) {

            frame.classList.remove('is-active');
        }


        if (startButton) {

            startButton.disabled = false;
        }
    }


    // ========================================================
    // EXPOSE RECOGNITION CAMERA
    // ========================================================

    window.attendanceFaceRecognition = {

        loadModels: loadModels,


        getVideo: function () {

            return video;
        },


        getStream: function () {

            return stream;
        },


        isCameraActive: function () {

            return !!(
                stream &&
                stream.active &&
                video &&
                video.srcObject &&
                video.readyState >=
                    HTMLMediaElement.HAVE_CURRENT_DATA &&
                video.videoWidth > 0 &&
                video.videoHeight > 0
            );
        },


        stopCamera: function () {

            stopCamera();
        }
    };


    // ========================================================
    // START BUTTON
    // ========================================================

    startButton.addEventListener(
        'click',
        startRecognition
    );


    // ========================================================
    // CLEAN UP WHEN LEAVING PAGE
    // ========================================================

    window.addEventListener(
        'beforeunload',
        stopCamera
    );


    // ========================================================
    // GET READY AS SOON AS THE PAGE OPENS
    // The page reloads after every sign-in, so without this each
    // person would wait for the models to load after tapping Start.
    // ========================================================

    const preload = () => loadModels().catch(() => {
        // Tapping Start tries again and shows the message.
    });

    if (document.readyState === 'complete') {
        setTimeout(preload, 0);
    } else {
        window.addEventListener('load', () => setTimeout(preload, 0), { once: true });
    }
}

