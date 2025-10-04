jQuery(document).ready(function ($) {

    let answerabel = $('#department-answerabel');

    answerabel.select2({
        ajax: {
            url: TKM_DATA.ajax_url,
            dataType: 'json',
            delay: 250,
            type:'post',
            timeout:20000,
            data: function (params) {
              return {
                term: params.term, // search term
                action: 'tkm_search_user',
              };
            },
            processResults: function (data) {

                var items = [];

                if(data){

                  $.each(data, function (index, user ) { 

                    items.push({ id: user[0] , text :user[1] })
                         
                    });

                }

                return{
                    results: items,
                }

            
         
            },
            cache: true
          },
    })

    let user_id = $('#ticket-user-id');

    user_id.select2({
        ajax: {
            url: TKM_DATA.ajax_url,
            dataType: 'json',
            delay: 250,
            type:'post',
            timeout:20000,
            data: function (params) {
              return {
                term: params.term, // search term
                action: 'tkm_search_user',
              };
            },
            processResults: function (data) {

                var items = [];

                if(data){

                  $.each(data, function (index, user ) { 

                    items.push({ id: user[0] , text :user[1] })
                         
                    });

                }

                return{
                    results: items,
                }

            
         
            },
            cache: true
          },
    })


    let creator_id = $('#ticket-creator');
    

    creator_id.select2({
        ajax: {
            url: TKM_DATA.ajax_url,
            dataType: 'json',
            delay: 250,
            type:'post',
            timeout:20000,
            data: function (params) {
              return {
                term: params.term, // search term
                action: 'tkm_search_user',
              };
            },
            processResults: function (data) {

                var items = [];

                if(data){

                  $.each(data, function (index, user ) { 

                    items.push({ id: user[0] , text :user[1] })
                         
                    });

                }

                return{
                    results: items,
                }

            
         
            },
            cache: true
          },
          
    })

    $('.ticket-file').click(function(e){

      e.preventDefault();
      var $this = $(this);
    var file = wp.media({
        multiole: false,
      }).open().on('select' , function(){
      var uploadedFILE =  file.state().get('selection').first();
      var fileURL = uploadedFILE.toJSON().url;

      $this.val(fileURL);
      });



    });

    $('.edit-date').click(function (e) {  
      e.preventDefault();
      
      var $this = $(this);
      $this.next('input').toggle();


    });

    $('.edit_btn_reply').click(function (e) {
      e.preventDefault();
      var $this = $(this);
      $this.next('.box-editor').slideToggle();
    })

   
   
      $('#save_select').on('change', function() {
          // گرفتن مقدار value آپشن انتخاب شده
          var responseText = $(this).val();
  
          // اگر ویرایشگر tinyMCE فعال است (ویرایشگر WYSIWYG)
          if (typeof tinyMCE !== "undefined" && tinyMCE.get('reply_content') !== null) {
              tinyMCE.get('reply_content').setContent(responseText); // 'reply_content' شناسه ویرایشگر است
          } 
          // اگر ویرایشگر در حالت متنی است (textarea)
          else if ($('#reply_content').length) {
              $('#reply_content').val(responseText); // مقداردهی به textarea
          }
      
  });

    
    
    
});