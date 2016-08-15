$(function(){

    function deleteErrors(){
        errors = $('.error_place');
        if(errors){
            errors.remove();
        }
    }


    function update_capcha(){
        var src = '/components/Captcha.php?num=' + Math.random();
        $('#captcha').replaceWith('<img src="'+ src +'" id="captcha">');
    }

    function clear_form(){
        $('.comment-form form input, .comment-form form textarea').each(function(){
            if($(this).val() != 'Добавить комментарий')
                $(this).val('');
        });
    }

    function hide_form(){
        $('.comment-form').fadeOut(0);
    }

    function show_errors(errors){
        deleteErrors();
        var form = $('.comment-form');
        form.prepend(errors);
    }

    function show_comment(comment){
        var form = $('.comment-form');
        var next = form.next();
        if(next.attr('id') == 'reply_comments'){
            next.append(comment);
        } else if(form.parent().attr('class') == 'comment') {
            $('#comments').append(comment);
        } else {
            form.after('<ul id="reply_comments">' + comment + '</ul>');
        }
    }

    var setData = {};

    $('#add-comment, .reply').click(function(){
        deleteErrors();
        var comment_form = $('.comment-form');

        if($(this).attr('id') == 'add-comment'){
            $(this).after(comment_form);
            comment_form.fadeOut(0);
            comment_form.fadeIn(400);
        } else {
            comment_form.fadeOut(0);
            setData.parent_id = $(this).parent().parent().parent().attr('id');
            $(this).parent().parent().after(comment_form);
            comment_form.fadeIn(400);
        }
    });

    var form = $('.comment-form form');

    form.submit(function(e){
        $('.comment-form form [name = parentId]').remove();

        var parentId = $(this).parent().parent().attr('id');
        if(typeof parentId == 'undefined'){
            parentId = 0;
        }
        $(this).prepend('<input type="hidden" name="parentId" value="' + parentId + '">');
        form = new FormData($(this)[0]);
        $.ajax({
            type: 'post',
            processData: false,
            contentType: false,
            url: '/site/ajax',
            dataType: 'json',
            data: form,
            success: function (data) {
                update_capcha();
                if(data.errors){
                    show_errors(data.errors);
                } else {
                    deleteErrors();
                    clear_form();
                    //update_capcha();
                    if($('.comment-form').parent().attr('class') != 'comment') hide_form();
                    show_comment(data.comment);
                }
            },
            error: function (error, abc) {
                alert(abc);
            }
        });

        e.preventDefault();
    });


    form.click(function (e) {
        var target = e.target;
        var control = target.closest('[data-action]');
        if(control) {
            var input = $('textarea')[0];
            if ('selectionStart' in input) {
                var start = input.selectionStart;
                var end = input.selectionEnd;

                var text = input.value;
                var textBefore = text.slice(0, start);
                var textAfter = text.slice(end);
                var selectionWord = text.substring(start, end);

                if (control.dataset.action === 'strong') {
                    selectionWord = '<strong>' + selectionWord + '</strong>';
                } else if (control.dataset.action === 'cursive') {
                    selectionWord = '<i>' + selectionWord + '</i>';
                } else if (control.dataset.action === 'link') {
                    var link = prompt('', 'http://' + selectionWord) || selectionWord;
                    selectionWord = '<a href="' + link + '">' + selectionWord + '</a>';
                } else if (control.dataset.action === 'code') {
                    selectionWord = '<code>' + selectionWord + '</code>';
                }

                text = textBefore + selectionWord + textAfter;

                input.value = text;
                input.focus();
            }
        }

    });

});



