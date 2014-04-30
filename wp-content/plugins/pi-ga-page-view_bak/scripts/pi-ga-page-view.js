(function($) {
    $(document).ready(function() {
        function showErrorMessage(message, view) {
            showMessage(message, view, 'error');
        }

        function showSuccessMessage(message, view) {
            showMessage(message, view, 'success');
        }

        function showMessage(message, view, cls) {
            var c = $(view).children('.message:first');
            if (!c.size()) {
                cls += ' message';
                $(view).append('<div class="' + cls + '">' + message + '</div>');
            } else {
                c[0].className = 'message ' + cls;
                c[0].innerText = message;
            }
        }

        $('a.deleteCategoryFromList').live('click',function(e) {
            $(this).prev().prev().remove().end().remove();
            $(this).remove();
            return false;
        });
        $('button.addMoreCategories').live('click',function(e) {
            e.preventDefault();
            e.stopPropagation();

            var field = $(this).parent().find('input:last').clone();
            field[0].value = '';
            $(this).before('<a class="deleteCategoryFromList" href="javascript:void(0);">Delete</a><br />'+field[0].outerHTML);
            return false;
        });

        $('select.ajaxDataSelectLoad').live('change',function(e) {
            if($(this).val() < 1 || $(this).val().length < 1) {
                $(this).parents('table').find('div.message').remove();
                var tmpOption = $('.gaWebPropertyIdSelect option:first');
                $('.gaWebPropertyIdSelect').html('').append(tmpOption);
                tmpOption = $('.gaProfileIdSelect option:first');
                $('.gaProfileIdSelect').html('').append(tmpOption);
                return;
            }
            $('.gaProfileIdSelect').val('');

            var view = $(this).parents('td');
            var select = $(this).parents('tr').next('tr').find('select');

            var data = "action=ga_get_html_for_select_by_type&"+$(this).parents('tr').find('input').serialize();
            if($('.gaIdSelect').val() > 0) {
                data += '&gaId='+$('.gaIdSelect').val();
            }
            if($('.gaWebPropertyIdSelect').val().length > 2) {
                data += '&webpropertyId='+$('.gaWebPropertyIdSelect').val();
            }
            $(view).append('<div class="loadImage"><img src="'+pi_ga_pageview.loadImagePath+'"/></div>');
            var message = 'Error. Problem with load data. Try to reload page and select options again.';
            $.post(ajaxurl, data, function(data) {
                if ('status' in data) {
                    if ('OK' == data.status) {
                        if('html' in data && data.html.length > 2) {
                            showSuccessMessage('Select data in next field',view);
                            select.html(data.html);
                        }
                    } else {
                        if ('message' in data && data.message.length > 1) {
                            message = data.message;
                        }
                        showErrorMessage(message, view);
                    }
                } else {
                    showErrorMessage("Server problem. Please reload page and try again", view);
                }
            $('.loadImage').remove();
            }, 'json');

        });
    });
})(jQuery);
