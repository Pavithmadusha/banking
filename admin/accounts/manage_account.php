<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * from `accounts` where id = '{$_GET['id']}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}
?>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><?php echo isset($_GET['id']) ? 'Update Account' : "Create New Account"; ?></h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <form id="account-form">
                <input type="hidden" name="id" value='<?php echo isset($id) ? $id : '' ?>'>
                <div class="form-group">
                    <label class="control-label">Account Number</label>
                    <input type="text" class="form-control col-sm-6" name="account_number" value="<?php echo isset($account_number) ? $account_number : '' ?>" required>
                </div>
                <hr>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="control-label">First Name</label>
                        <input type="text" class="form-control" name="firstname" value="<?php echo isset($firstname) ? $firstname : '' ?>" required>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="control-label">Middle Name</label>
                        <input type="text" class="form-control" name="middlename" value="<?php echo isset($middlename) ? $middlename : '' ?>" placeholder="(optional)" required>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="control-label">Last Name</label>
                        <input type="text" class="form-control" name="lastname" value="<?php echo isset($lastname) ? $lastname : '' ?>" required>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label class="control-label">Email</label>
                    <input type="text" class="form-control col-sm-6" name="email" value="<?php echo isset($email) ? $email : '' ?>" required>
                </div>

                <div class="form-group">
                    <label class="control-label">Password</label>
                    <div class="input-group m-0 p-0  col-sm-6">
                        <input type="text" class="form-control" name="generated_password" value="<?php echo isset($generated_password) ? $generated_password : '' ?>" <?php echo (!isset($id)) ? "required" : '' ?>>
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary" type="button" id="generate_pass">Generate</button>
                        </div>
                    </div>
                </div>
                <?php
                if (isset($_GET['id'])) {
                ?>
                    <div class="form-group" id="faceid-card">
                        <label class="control-label">Face ID</label>
                        <div class="card-body">
                            <video class="border" id="faceid-video" width="80%" height="auto" autoplay></video>
                            <div class="form-group d-flex justify-content-start mt-3">
                                <button id="faceid-toggle" type="button" class="btn btn-sm btn-success mr-2">Start Camera</button>
                                <button id="faceid-capture" type="button" class="btn btn-sm btn-warning">Register Face</button>
                            </div>
                            <p id="faceid-status" class="text-center mt-3"></p>
                        </div>
                    </div>
                <?php
                }
                ?>

                <?php if (!isset($id)): ?>
                    <div class="form-group">
                        <label class="control-label">PIN</label>
                        <input type="text" class="form-control col-sm-6" name="pin" value="<?php echo isset($pin) ? $pin : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Beginning Balance</label>
                        <input type="number" step='any' min="0" class="form-control col-sm-6 text-right" name="balance" value="0" required>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-flex w-100">
            <button form="account-form" class="btn btn-primary mr-2">Save</button>
            <a href="./?page=accounts" class="btn btn-default">Cancel</a>
        </div>
    </div>
</div>
<script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     // Access the camera when the page loads
    //     navigator.mediaDevices.getUserMedia({
    //             video: true
    //         })
    //         .then(function(stream) {
    //             document.getElementById('faceid-video').srcObject = stream;
    //         })
    //         .catch(function(error) {
    //             console.error('Error accessing camera:', error);
    //             document.getElementById('faceid-status').textContent = 'Unable to access camera.';
    //         });
    // });

    document.addEventListener("DOMContentLoaded", function() {
        const video = document.getElementById('faceid-video');
        const captureButton = document.getElementById('faceid-capture');
        const status = document.getElementById('faceid-status');
        const toggleButton = document.getElementById('faceid-toggle');
        let stream = null;
        let cameraOn = false;

        // Access the device camera and stream video to the <video> element
        // navigator.mediaDevices.getUserMedia({
        //         video: true
        //     })
        //     .then(function(stream) {
        //         video.srcObject = stream;
        //     })
        //     .catch(function(error) {
        //         console.error("Error accessing the camera: ", error);
        //         status.textContent = "Error accessing the camera.";
        //     });
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

        const usereId = <?php echo json_encode($_GET['id'] ?? ''); ?>;
        if (usereId) {
        captureButton.addEventListener('click', async function() {
            
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Convert the canvas image to a Blob
            canvas.toBlob(async function(blob) {
                // Create a FormData object and append the image Blob and user ID
                const formData = new FormData();
                formData.append('file', blob, 'face.jpg');
                formData.append('userId', usereId); // Replace with actual user ID

                try {
                    const response = await fetch('http://127.0.0.1:8000/register_face/', { // Replace with your API endpoint
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const result = await response.json();
                    console.log('Success:', result);
                    status.textContent = 'Image captured and sent successfully!';
                    status.style.color = "#00ff00"
                } catch (error) {
                    console.error('Error:', error);
                    status.textContent = 'Failed to send image.';
                    status.style.color = "#ff0000"
                }
            }, 'image/jpeg');
        });
    }
    });




    // document.getElementById('faceid-capture').addEventListener('click', function() {
    //     // Handle face capture and authentication logic here.
    //     alert('To be implemented.');
    //     const canvas =document.createElement('canvas');
    //     canvas.width =video.videoWidth;

    // });
    $(function() {
        $('#generate_pass').click(function() {
            var randomstring = Math.random().toString(36).slice(-8);
            $('[name="generated_password"]').val(randomstring)
        })
        $('[name="account_number"]').on('input', function() {
            if ($('._checks').length > 0)
                $('._checks').remove()
            $('button[form="account-form"]').attr('disabled', true)
            $(this).removeClass('border-danger')
            $(this).removeClass('border-success')
            var checks = $('<small class="_checks">')
            checks.text("Checking availablity")
            $('[name="account_number"]').after(checks)
            $.ajax({
                url: _base_url_ + 'classes/Master.php?f=check_account',
                method: 'POST',
                data: {
                    id: $('[name="id"]').val(),
                    account_number: $(this).val()
                },
                dataType: 'json',
                error: err => {
                    console.log(err)
                    alert_toast("An error occured", "error")
                    end_loader()
                },
                success: function(resp) {
                    if (resp.status == 'available') {
                        checks.addClass('text-success')
                        checks.text('Available')
                        $('[name="account_number"]').addClass('border-success')
                        $('button[form="account-form"]').attr('disabled', false)
                    } else if (resp.status == 'taken') {
                        checks.addClass('text-danger')
                        checks.text('Account already exist')
                        $('[name="account_number"]').addClass('border-danger')
                        $('button[form="account-form"]').attr('disabled', true)
                    } else {
                        alert_toast('An error occured', "error")
                        $('[name="account_number"]').addClass('border-danger')
                        console.log(resp)
                    }
                    end_loader()
                }
            })
        })
        $('#account-form').submit(function(e) {
            e.preventDefault()
            start_loader()
            if ($('.err_msg').length > 0)
                $('.err_msg').remove()
            $.ajax({
                url: _base_url_ + 'classes/Master.php?f=save_account',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                error: err => {
                    console.log(err)
                    alert_toast("An error occured", "error")
                    end_loader()
                },
                success: function(resp) {
                    if (resp.status == 'success') {
                        location.href = "./?page=accounts"
                    } else if (!!resp.msg) {
                        var msg = $('<div class="err_msg"><div class="alert alert-danger">' + resp.msg + '</div></div>')
                        $('#account-form').prepend(msg)
                        msg.show('slow')
                    } else {
                        alert_toast('An error occured', "error")
                        console.log(resp)
                    }
                    end_loader()
                }
            })
        })
    })
</script>