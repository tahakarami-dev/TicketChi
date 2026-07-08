jQuery(document).ready(function($) { 
    $(".accordion").click(function() { 
        $(this).next(".accordion-content").slideToggle(); 
    }); 

    $(".btn-not-faqs").click(function() { 
        $(".main_content_form").slideToggle(); 
    }); 
    $(".byn_send_reply").click(function() { 
        $(".tkm_item_send_form").slideToggle(); 
    }); 

    function tkmUploadConfig() {
        return (typeof TKM_DATA_AJAX !== 'undefined' && TKM_DATA_AJAX.upload) ? TKM_DATA_AJAX.upload : {
            multiple: false, max_files: 5, max_size: 5242880, allowed_ext: []
        };
    }

    // اعتبارسنجی سمت کاربر و افزودن فایل‌ها به FormData. در صورت خطا false برمی‌گرداند.
    function tkmAppendFiles(inputSelector, formData) {
        var cfg = tkmUploadConfig();
        var files = $(inputSelector).prop('files');

        if (!files || !files.length) {
            return true; // فایل اختیاری است
        }

        var maxFiles = cfg.multiple ? cfg.max_files : 1;
        if (files.length > maxFiles) {
            Swal.fire({
                title: "تعداد فایل زیاد است",
                text: "حداکثر " + maxFiles + " فایل می‌توانید آپلود کنید.",
                icon: "warning"
            });
            return false;
        }

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var ext = file.name.split('.').pop().toLowerCase();

            if (cfg.allowed_ext && cfg.allowed_ext.length && cfg.allowed_ext.indexOf(ext) === -1) {
                Swal.fire({
                    title: "پسوند غیرمجاز",
                    text: "پسوند فایل «" + ext + "» مجاز نیست.",
                    icon: "warning"
                });
                return false;
            }

            if (file.size > cfg.max_size) {
                Swal.fire({
                    title: "حجم فایل زیاد است",
                    text: "حجم فایل «" + file.name + "» بیشتر از حد مجاز است.",
                    icon: "warning"
                });
                return false;
            }

            formData.append('file[]', file);
        }

        return true;
    }

    // نمایش نام فایل‌های انتخاب‌شده زیر دکمه آپلود
    $(document).on('change', '#file-upload', function() {
        var container = $(this).closest('form').find('.tkm-selected-files');
        var files = this.files;
        container.empty();
        if (files && files.length) {
            for (var i = 0; i < files.length; i++) {
                container.append($('<span class="tkm-selected-file"></span>').text(files[i].name));
            }
        }
    });

    $("#tkm-parent-depaertment").change(function(e) {
        e.preventDefault(); 
        let selectedValue = $(this).val(); 

        $('#tkm-child-department').prop('selectedIndex', 0); 
        $('.tkm-child-department option').hide(); 
        $('.alert-department').hide(); 

        if (selectedValue === '') { 
            $('.nf-deparment').show(); 
            return false; 
        } 

        $('.child-department-' + selectedValue).show(); 
        $('.nf-deparment').hide(); 
    }); 

    $('#tkm-child-department').change(function(e) { 
        e.preventDefault(); 
        let selectedValue = $(this).val(); 
        $('.alert-department').hide(); 

        if (selectedValue !== '') { 
            let alertElement = $('.alert-department-' + selectedValue); 
            if (alertElement.length > 0) { 
                alertElement.slideDown(); 
            } 
        } 
    }); 

    $('#tkm-submit-ticket').submit(function(e) { 
        e.preventDefault(); 

        let $this = $(this);
        let submit = $this.find('.tkm-submit-ticket');
        let loader = $this.find('.loader-submit');

        let form_data = new FormData();
        form_data.append('action', 'tkm-submit-ticket');
        form_data.append('nonce', TKM_DATA_AJAX.nonce);
        form_data.append('parent_department', $('#tkm-parent-depaertment').val());
        form_data.append('child-department', $('#tkm-child-department').val());
        form_data.append('title_ticket', $('#title-ticket').val());
        form_data.append('priority', $('#importance').val());
        form_data.append('content', $('#ticket-content').val());
        if (!tkmAppendFiles('#file-upload', form_data)) { return; }
        form_data.append('audioData', $('#audioData').val());

        submit.prop('disabled', true);
        loader.show();
        form_data.append('user_purchased_products', $('#products').val()); 
        form_data.append('edd_purchased_products', $('#edd-products').val()); 

        $.ajax({ 
            type: "post", 
            url: TKM_DATA_AJAX.ajax_url, 
            data: form_data, 
            contentType: false, 
            processData: false, 

            success: function(response) { 
                if (response.success) { 
                    Swal.fire({ 
                        title: "تیکت ارسال شد", 
                        text: "تیکت شما با موفقیت ارسال شد", 
                        icon: "success" 
                    }); 

                    location.reload();


                } else { 
                    Swal.fire({ 
                        title: "خطا", 
                        text: response.result.toString().replace(',', '<br>'), 
                        icon: "error" 
                    }); 
                } 
            }, 
            error: function(error) { 
                Swal.fire({ 
                    title: "خطا", 
                    text: "خطا در ارسال درخواست. لطفاً دوباره تلاش کنید.", 
                    icon: "error" 
                }); 
            }, 
            complete: function() { 
                submit.prop('disabled', false); 
                loader.hide(); 

            }, 
        }); 
    }); 
    $('#reply-submit').submit(function (e) {
        e.preventDefault();
    
        let $this = $(this);
        let submit = $this.find('.submit-reply');
        let loader = $this.find('.loader');
    
        submit.prop('disabled', true);
        loader.show();

           let eddProductValue = $('#edd-products').val();
    if (eddProductValue === '') {
        Swal.fire({
            title: "انتخاب محصول",
            text: "لطفاً یک محصول دیجیتال را از لیست انتخاب کنید.",
            icon: "warning"
        });
        submit.prop('disabled', false);
        loader.hide();
        return; 
    }
    
        let form_data = new FormData();
        form_data.append('action', 'tkm-submit-reply');
        form_data.append('nonce', TKM_DATA_AJAX.nonce);
        form_data.append('status', $('#status').is(':checked') ? $('#status').val() : '');
        form_data.append('ticket_id', $('#ticket_id').val());
        form_data.append('body', $('#body').val() || '');
        if (!tkmAppendFiles('#file-upload', form_data)) {
            submit.prop('disabled', false);
            loader.hide();
            return;
        }
        form_data.append('audioData', $('#audioData').val() || '');
    
        $.ajax({
            type: "post",
            url: TKM_DATA_AJAX.ajax_url,
            data: form_data,
            contentType: false,
            processData: false,
    
            success: function (response) {
                if (response.success) {
                    $('.response-item').html(response.replies_html);
                    $('.status-ticket .name-status').hide();
                    $('.status-ticket .name-status').html(response.status_update);
                    Swal.fire({ 
                        title: "پاسخ ارسال شد", 
                        text: "پاسخ شما با موفقیت ارسال شد", 
                        icon: "success" 
                    }); 
                    location.reload();
                } else {
                    Swal.fire({
                        title: "خطا",
                        text: response.result,
                        icon: "error"
                    });
                }
            },
    
            error: function () {
                Swal.fire({
                    title: "خطا",
                    text: "خطا در ارسال درخواست. لطفاً دوباره تلاش کنید.",
                    icon: "error"
                });
            },
    
            complete: function () {
                submit.prop('disabled', false);
                loader.hide();
            },
        });
    });

  let mediaRecorder;
  let audioChunks = [];
  let audioStream;
  let timerInterval;
  let seconds = 0;
  let isRecording = false;
  
  function startTimer() {
      seconds = 0;
      clearInterval(timerInterval);
      $("#timerContainer").show(); 
      timerInterval = setInterval(() => {
          seconds++;
          let minutes = Math.floor(seconds / 60);
          let secs = seconds % 60;
  
          let formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
          let formattedSecs = secs < 10 ? '0' + secs : secs;
  
          $("#timer").text(formattedMinutes + ":" + formattedSecs);
      }, 1000);
  }
  
  function stopTimer() {
      clearInterval(timerInterval);
      $("#timerContainer").hide(); 
  }
  
  function resetTimer() {
      clearInterval(timerInterval);
      seconds = 0;
      $("#timer").text("00:00");
      $("#timerContainer").hide(); 
  }
  
  $("#openPopup").on("click", function() {
      $("#popup").fadeIn();
  });
  
  $("#closePopup").on("click", function() {
      $("#popup").fadeOut();
      if (mediaRecorder && mediaRecorder.state === "recording") {
          mediaRecorder.stop();
          audioStream.getTracks().forEach(track => track.stop());
      }
      stopTimer();
  });
  
  $("#startRecording").on("click", async function() {
      if (!isRecording) {
          audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
          mediaRecorder = new MediaRecorder(audioStream, { mimeType: "audio/webm" });
          mediaRecorder.start();
          audioChunks = [];
          startTimer(); 
          $("#startRecording").prop("disabled", true); 
          $("#stopRecording").show(); 
          $("#deleteRecording").hide(); 
  
          mediaRecorder.ondataavailable = function(event) {
              audioChunks.push(event.data);
          };
  
          mediaRecorder.onstop = function() {
              stopTimer(); 
              const audioBlob = new Blob(audioChunks, { type: "audio/webm" });
              const reader = new FileReader();
  
              reader.readAsDataURL(audioBlob);
              reader.onloadend = function() {
                  const base64Audio = reader.result;
                  $("#audioData").val(base64Audio);
              };
  
              const audioUrl = URL.createObjectURL(audioBlob);
              $("#audioPlayback").attr("src", audioUrl);
              $("#deleteRecording").show();
          };
  
          isRecording = true;
      }
  });
  
  $("#stopRecording").on("click", function() {
      if (mediaRecorder && isRecording) {
          mediaRecorder.stop();
          audioStream.getTracks().forEach(track => track.stop());
          $("#startRecording").prop("disabled", false); 
          $("#stopRecording").hide(); 
          isRecording = false;
      }
  });
  
  $("#deleteRecording").on("click", function() {
      $("#audioPlayback").attr("src", "");
      $("#audioData").val("");
      $("#deleteRecording").hide();
      $("#startRecording").prop("disabled", false); 
      $("#stopRecording").hide();
      resetTimer(); 
      isRecording = false;
  });

      function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    let selectedRating = 0;

    $('.tkm-stars .star').on('click', function () {
        selectedRating = $(this).data('value');
        $('.tkm-stars .star').removeClass('active');
        $(this).addClass('active');
        $(this).prevAll('.star').addClass('active');
    });

    $('#tkm-submit-rating').on('click', function () {
        if (selectedRating === 0) {
            Swal.fire({ 
                title: "امتیاز تیکت   ", 
                text: "لطفا امتیاز تیکت خود را وارد کنید", 
                icon: "info" 
            });            return;
        }

        const ticketID = getQueryParam('ticket-id'); 

        $.ajax({
            url: TKM_DATA_AJAX.ajax_url,
            type: 'POST',
            data: {
                action: 'tkm_submit_rating',
                rating: selectedRating,
                ticket_id: ticketID
            },
            success: function (response) {
                if (response.success) {
                    Swal.fire({ 
                        title: " امتیاز ثبت شد", 
                        text: "امتیاز شما ثبت شد. سپاس از همراهی شما", 
                        icon: "success" 
                    });
                    location.reload();
            
                } else {
                    Swal.fire({ 
                        title: "  ثبت امتیاز با خطا مواجه شد", 
                        text: "مشکلی در ثبت امتیاز رخ داد", 
                        icon: "error" 
                    });
                }
            },
            error: function () {
                Swal.fire({ 
                    title: "خطا", 
                    text: "مشکلی رخ داد", 
                    icon: "error" 
                });            }
        });
    });
        var rating = parseInt($('#rating-container').data('rating'), 10);
    
        $('#rating-container .star').each(function () {
            var starValue = parseInt($(this).data('value'), 10);
            if (starValue <= rating) {
                $(this).addClass('filled'); 
            }
        });
})