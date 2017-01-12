jQuery(".quiz-step").on('click',function(){
    var quizStep = {
        'action': 'quizStep',
        'sendStep': jQuery(this).data("step")
    };
    jQuery.ajax({
        method: "POST",
        url: ajaxurl,
        cache: true,
        dataType: "text",
        data:  quizStep,
        success: function(){
            window.location.reload();
        }
    });
});
