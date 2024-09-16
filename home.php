 <!-- Header-->
 <header class="bg-dark py-5" id="main-header">
     <div class="container px-4 px-lg-5 my-5">
         <div class="text-center text-white">
             <h1 class="display-4 fw-bolder">Welcome to <?php echo $_settings->info('name') ?></h1>

         </div>
     </div>
 </header>
 <!-- Section-->
 <?php
    $sched_arr = array();
    $max = 0;
    ?>
 <section class="py-5">
     <div class="container d-flex justify-content-center">
         <div class="card col-md-6 p-0" id="login-card">
             <div class="card-header">
                 <div class="card-title text-center w-100">Login</div>
             </div>

             <div class="card-body">
                 <form action="" id="login-client">
                     <input type="hidden" id="faceId" name="faceId">
                     <div class="alert alert-danger d-none" id="faceid-error" role="alert">
                         Face ID get failed.
                     </div>
                     <div class="alert alert-success d-none" id="faceid-success" role="alert">
                         Face ID get successfully!
                     </div>
                     <div class="form-group">
                         <label for="email" class='control-label'>Email</label>
                         <input type="text" class="form-control" placeholder="Enter Email Here" name="email" required>
                     </div>
                     <div class="form-group">
                         <label for="password" class='control-label'>Password</label>
                         <input type="password" class="form-control" placeholder="Enter Password Here" name="password" required>
                     </div>
                     <div class="form-group" id="faceid-card">
                         <label class="control-label">Face ID</label>
                         <div class="card-body">
                             <video class="border" id="faceid-video" width="100%" height="auto" autoplay></video>
                             <div class="form-group d-flex justify-content-start mt-3">
                                 <button id="faceid-toggle" type="button" class="btn btn-sm btn-success mr-2">Start Camera</button>
                                 <button id="faceid-capture" type="button" class="btn btn-sm btn-warning">Get Face</button>
                             </div>
                             <p id="faceid-status" class="text-center mt-3"></p>
                         </div>
                     </div>


                     <div class="form-group d-flex justify-content-end mt-3">
                         <!-- <button id="login-faceid" class="btn btn-sm btn-secondary btn-flat me-2">Login with Face ID</button> -->
                         <button type="submit" class="btn btn-sm btn-primary">Login</button>
                     </div>
                 </form>
             </div>
         </div>
         <!-- <div class="card col-md-6 p-0 d-none" id="faceid-card">
            <div class="card-header">
                <div class="card-title text-center w-100">Face ID Login</div>
            </div>
            <div class="card-body">
                <video id="faceid-video" width="100%" height="auto" autoplay></video>
                <div class="form-group d-flex justify-content-end mt-3">
                <button id="faceid-login" class="btn btn-sm btn-primary btn-flat me-2">Login</button>
                    <button id="faceid-capture" class="btn btn-sm btn-primary btn-flat me-2">Capture Face</button>
                    <button id="faceid-back" class="btn btn-sm btn-secondary btn-flat">Back</button>
                </div>
                <p id="faceid-status" class="text-center mt-3"></p>
            </div>
        </div> -->
     </div>
 </section>
 <script>
     document.addEventListener("DOMContentLoaded", function() {
         const video = document.getElementById('faceid-video');
         const captureButton = document.getElementById('faceid-capture');
         const status = document.getElementById('faceid-status');
         const toggleButton = document.getElementById('faceid-toggle');
         let stream = null;
         let cameraOn = false;
         if (video.srcObject != null) {
             toggleButton.textContent = 'Camera is off'
         }

         toggleButton.addEventListener('click', function() {
             if (video.srcObject != null) {
                 video.srcObject = null;
                 toggleButton.textContent = 'Start Camera';
                 return;
             }
             toggleButton.textContent = 'Camera is off'
             navigator.mediaDevices.getUserMedia({
                     video: true
                 })
                 .then(function(stream) {
                     video.srcObject = stream;
                 })
                 .catch(function(error) {
                     console.error("Error accessing the camera: ", error);
                     status.textContent = "Error accessing the camera.";
                 });
         })

         captureButton.addEventListener('click', async function() {
             const canvas = document.createElement('canvas');
             canvas.width = video.videoWidth;
             canvas.height = video.videoHeight;
             const context = canvas.getContext('2d');
             context.drawImage(video, 0, 0, canvas.width, canvas.height);

             // Convert the canvas image to a Blob
             canvas.toBlob(async function(blob) {
                 const formData = new FormData();
                 formData.append('file', blob, 'face.jpg');

                 try {
                     const response = await fetch('http://127.0.0.1:8000/authenticate/', { // Replace with your API endpoint
                         method: 'POST',
                         body: formData
                     });

                     if (!response.ok) {
                         throw new Error('Network response was not ok');
                     }

                     const result = await response.json();
                     console.log(result)
                     var faceIdInput = document.getElementById('faceId');
                     if (result.authenticated_as) {
                         faceIdInput.value = result.authenticated_as;
                         // Show success alert
                         document.getElementById('faceid-success').classList.remove('d-none');
                         document.getElementById('faceid-error').classList.add('d-none');
                     } else {
                         // Show error alert if authentication failed
                         document.getElementById('faceid-error').classList.remove('d-none');
                         document.getElementById('faceid-success').classList.add('d-none');
                     }
                 } catch (error) {
                     console.error('Error:', error);
                     document.getElementById('faceid-status').textContent = 'Failed to send image.';
                     document.getElementById('faceid-status').style.color = "#ff0000"
                     document.getElementById('faceid-error').classList.remove('d-none');
                 }
             }, 'image/jpeg');
         });

     });
 </script>