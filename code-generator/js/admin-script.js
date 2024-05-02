jQuery(document).ready(function ($) {
    'use strict';
    'esversion: 6';
    $('#code-generator-form').submit(function (e) {
        e.preventDefault();

        var form = $(this).serialize(),
            block_answer = $('.code-generator-block-answer'),
            error_block = $('.code-generator__error-message'),
            answer = $('.code-generator-answer');

        error_block.fadeOut();

        if (!form) return false;

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: form,
            beforeSend: function( xhr ) {
                $('.code-generator-form__button').attr('disabled', 'disabled');
            },
            success : function( json ) {
                    if (json.success) {
                        json.data.codes.forEach(function(item, i, arr) {
                            answer.append('<p>'+ item +'</p>');
                        });
                        block_answer.fadeIn();
                        $('.code-generator-form__button').removeAttr('disabled');
                    } else {
                        error_block.html(json.data.message).fadeIn();
                        $('.code-generator-form__button').removeAttr('disabled');
                    }
            },
            error : function(error) {
                alert(error);
            }
        });
    });


    //Форма активации
    $('#code-activator-form').submit(function (e) {
        e.preventDefault();

        var form = $(this).serialize(),
            button = $('.code-activator-button'),
            message_block = $('.message'),
            result_block = $('.code-generator-wrap-activator__result');

        $('.active-info-table, .code-generator-wrap-activator__result').fadeOut();

        if (!form) return false;

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: form,
            beforeSend: function( xhr ) {
                button.attr('disabled', 'disabled');
            },
            success : function( json ) {
                    if (json.success) {
                        $('.activate_status').text(json.data.status);
                        $('.activate_type').text(json.data.type);
                        $('.activate_count_active').text(json.data.count_activated);
                        $('.activate_date').text(json.data.date);
                        $('.activate_author').text(json.data.author);
                        $('.activate_actirovalchik').text(json.data.actirovalchik);
                        button.removeAttr('disabled');
                        $('.active-info-table, .code-generator-wrap-activator__result').fadeIn();
                    } else {
                        message_block.html(json.data.message).addClass('error').removeClass('success');//.fadeIn();
                        $('.code-generator-wrap-activator__result').fadeIn();
                        button.removeAttr('disabled');
                    }
            },
            error : function(error) {
                alert(error);
            }
        });
    });

});
