jQuery(document).ready(function($) { 
    // عملکرد اکاردئون 
    $(".accordion").click(function() { 
        $(this).next(".accordion-content").slideToggle(); 
    }); 

    $(".btn-not-faqs").click(function() { 
        $(".main_content_form").slideToggle(); 
    }); 
    $(".byn_send_reply").click(function() { 
        $(".tkm_item_send_form").slideToggle(); 
    }); 

    // تغییر دپارتمان والد 
    $("#tkm-parent-depaertment").change(function(e) { 
        e.preventDefault(); 
        let selectedValue = $(this).val(); 

        // ریست کردن نوع تیکت (دپارتمان فرزند) 
        $('#tkm-child-department').prop('selectedIndex', 0); // ریست به اولین گزینه 
        $('.tkm-child-department option').hide(); 
        $('.alert-department').hide(); 

        if (selectedValue === '') { 
            $('.nf-deparment').show(); 
            return false; 
        } 

        $('.child-department-' + selectedValue).show(); 
        $('.nf-deparment').hide(); 
    }); 

    // تغییر دپارتمان فرزند 
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

    // ارسال تیکت 
    $('#tkm-submit-ticket').submit(function(e) { 
        e.preventDefault(); 

        let $this = $(this); 
        let submit = $this.find('.tkm-submit-ticket'); 
        let loader = $this.find('.loader-submit'); 
        submit.prop('disabled', true); 
        loader.show(); 

        let form_data = new FormData(); 
        form_data.append('action', 'tkm-submit-ticket'); 
        form_data.append('nonce', TKM_DATA_AJAX.nonce); 
        form_data.append('parent_department', $('#tkm-parent-depaertment').val()); 
        form_data.append('child-department', $('#tkm-child-department').val()); 
        form_data.append('title_ticket', $('#title-ticket').val()); 
        form_data.append('priority', $('#importance').val()); 
        form_data.append('content', $('#ticket-content').val()); 
        form_data.append('file', $('#file-upload').prop('files')[0]);
        form_data.append('audioData', $('#audioData').val()); // ارسال داده ویس
        form_data.append('user_purchased_products', $('#products').val()); 




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

                    window.location.href = response.result;


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
    
        // غیرفعال کردن دکمه ارسال
        submit.prop('disabled', true);
        loader.show();
    
        let form_data = new FormData();
        form_data.append('action', 'tkm-submit-reply');
        form_data.append('nonce', TKM_DATA_AJAX.nonce);
        form_data.append('status', $('#status').is(':checked') ? $('#status').val() : '');
        form_data.append('ticket_id', $('#ticket_id').val());
        form_data.append('body', $('#body').val() || ''); // متن
        let file = $('#file-upload').prop('files')[0];
        form_data.append('file', file ? file : null); // فایل فقط اگر وجود داشت
        form_data.append('audioData', $('#audioData').val() || ''); // ویس فقط اگر وجود داشت
    
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

  // ضبط و ارسال ویس
  let mediaRecorder;
  let audioChunks = [];
  let audioStream;
  let timerInterval;
  let seconds = 0;
  let isRecording = false;
  
  // توابع تایمر
  
  // شروع تایمر
  function startTimer() {
      seconds = 0;
      clearInterval(timerInterval);
      $("#timerContainer").show(); // نمایش تایمر
      timerInterval = setInterval(() => {
          seconds++;
          let minutes = Math.floor(seconds / 60);
          let secs = seconds % 60;
  
          // فرمت سازی دقیقه و ثانیه برای نمایش دو رقمی
          let formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
          let formattedSecs = secs < 10 ? '0' + secs : secs;
  
          $("#timer").text(formattedMinutes + ":" + formattedSecs);
      }, 1000);
  }
  
  // توقف تایمر
  function stopTimer() {
      clearInterval(timerInterval);
      $("#timerContainer").hide(); // مخفی کردن تایمر
  }
  
  // بازنشانی تایمر
  function resetTimer() {
      clearInterval(timerInterval);
      seconds = 0;
      $("#timer").text("00:00");
      $("#timerContainer").hide(); // مخفی کردن تایمر
  }
  
  // باز کردن پاپ‌آپ
  $("#openPopup").on("click", function() {
      $("#popup").fadeIn();
  });
  
  // بستن پاپ‌آپ
  $("#closePopup").on("click", function() {
      $("#popup").fadeOut();
      if (mediaRecorder && mediaRecorder.state === "recording") {
          mediaRecorder.stop();
          audioStream.getTracks().forEach(track => track.stop());
      }
      stopTimer();
  });
  
  // شروع ضبط صدا
  $("#startRecording").on("click", async function() {
      if (!isRecording) {
          // شروع ضبط
          audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
          mediaRecorder = new MediaRecorder(audioStream, { mimeType: "audio/webm" });
          mediaRecorder.start();
          audioChunks = [];
          startTimer(); // شروع تایمر
          $("#startRecording").prop("disabled", true); // غیرفعال کردن دکمه شروع
          $("#stopRecording").show(); // نمایش دکمه پایان ضبط
          $("#deleteRecording").hide(); // مخفی کردن دکمه حذف
  
          mediaRecorder.ondataavailable = function(event) {
              audioChunks.push(event.data);
          };
  
          mediaRecorder.onstop = function() {
              stopTimer(); // توقف تایمر
              const audioBlob = new Blob(audioChunks, { type: "audio/webm" });
              const reader = new FileReader();
  
              reader.readAsDataURL(audioBlob);
              reader.onloadend = function() {
                  const base64Audio = reader.result;
                  $("#audioData").val(base64Audio);
              };
  
              const audioUrl = URL.createObjectURL(audioBlob);
              $("#audioPlayback").attr("src", audioUrl);
              $("#deleteRecording").show(); // نمایش دکمه حذف
          };
  
          isRecording = true;
      }
  });
  
  // توقف ضبط صدا
  $("#stopRecording").on("click", function() {
      if (mediaRecorder && isRecording) {
          mediaRecorder.stop();
          audioStream.getTracks().forEach(track => track.stop());
          $("#startRecording").prop("disabled", false); // فعال کردن دکمه شروع
          $("#stopRecording").hide(); // مخفی کردن دکمه پایان ضبط
          isRecording = false;
      }
  });
  
  // حذف ضبط
  $("#deleteRecording").on("click", function() {
      $("#audioPlayback").attr("src", "");
      $("#audioData").val("");
      $("#deleteRecording").hide();
      $("#startRecording").prop("disabled", false); // فعال کردن دکمه شروع
      $("#stopRecording").hide(); // مخفی کردن دکمه پایان ضبط
      resetTimer(); // بازنشانی تایمر
      isRecording = false;
  });

      // گرفتن مقدار کوئری از URL
      function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    let selectedRating = 0;

    // ستاره ها را فعال یا غیرفعال می‌کند
    $('.tkm-stars .star').on('click', function () {
        selectedRating = $(this).data('value');
        $('.tkm-stars .star').removeClass('active');
        $(this).addClass('active');
        $(this).prevAll('.star').addClass('active');
    });

    // ثبت امتیاز
    $('#tkm-submit-rating').on('click', function () {
        if (selectedRating === 0) {
            Swal.fire({ 
                title: "امتیاز تیکت   ", 
                text: "لطفا امتیاز تیکت خود را وارد کنید", 
                icon: "info" 
            });            return;
        }

        const ticketID = getQueryParam('ticket-id'); // از URL مقدار را بگیر

        $.ajax({
            url: TKM_DATA_AJAX.ajax_url, // URL وردپرس
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
        // دریافت امتیاز از data-rating
        var rating = parseInt($('#rating-container').data('rating'), 10);
    
        // پر کردن ستاره‌ها
        $('#rating-container .star').each(function () {
            var starValue = parseInt($(this).data('value'), 10);
            if (starValue <= rating) {
                $(this).addClass('filled'); // اضافه کردن کلاس برای ستاره‌های پرشده
            }
        });
    

})