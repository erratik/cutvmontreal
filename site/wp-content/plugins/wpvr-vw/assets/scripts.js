function wpvr_show_widget_settings_dialog(token) {
    jQuery(document).ready(function ($) {
        $.getScript(wpvr_globals.functions_js).done(function () {
            var wrap = $('#widgets-right .wpvr_widget_form#' + token);
            var btn = $('.wpvr_widget_button', wrap);
            var url = wrap.attr('url');
            var data_wrap = $('.wpvr_widget_encoded_data', wrap);
            var wp_form = wrap.parent().parent();
            var save_widget_button = $('input[name=savewidget]', wp_form);

            var spinner = wpvr_add_loading_spinner(btn, 'pull-right');

            //e.preventDefault();

            var encoded_data = data_wrap.val();

            var widgetDataBox = wpvr_show_loading({
                title: 'WPVR Video Widgets',
                text: '<div class="wpvr_widget_form_content" > ' + wpvr_localize.loadingCenter + ' </div>',
                isModal: false,
                width: '70%',
                height: '85%',
                boxClass: 'widgetDataBox',
                pauseButton: '<i class="fa fa-check" ></i> Save Settings',
                cancelButton: '<i class="fa fa-close" ></i> Cancel',
            });


            jQuery('.wpvr_loading_msg', widgetDataBox).center();
            //$('.wpvr_aform_result').html('Please Wait ...');
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    wpvr_wpload: 1,
                    render_form: 1,
                    encoded_data: encoded_data,
                },
                success: function (data) {
                    wpvr_remove_loading_spinner(spinner);

                    $('.wpvr_widget_form_content').html(data);
                    $('.wpvr_widget_form_content .wpvr_nav_tab#content').trigger('click');

                    $('.wpvr_widget_form_content .wpvr_field_selectize').each(function () {
                        var select = $(this);
                        if (select.attr('maxItems') != '') {
                            var selected_items = new Array();
                            $('option', select).each(function () {
                                if ($(this).attr('c') == '1')
                                    selected_items.push($(this).attr('value'));
                            });
                            var $select = select.selectize({
                                maxItems: select.attr('maxItems'),
                                items: selected_items,
                            });
                        } else {
                            select.selectize();
                        }
                    });

                    $('.wpvr_widget_form_content .wpvr_selectize_list').each(function () {
                        var field = $(this);
                        var maxItems = field.attr('maxItems');
                        var verifyEmail = field.hasClass('verifyEmail');
                        var selectized = field;
                        var $selectized = selectized.selectize({
                            delimiter: ',',
                            maxItems: maxItems,
                            persist: false,
                            create: function (input) {
                                if (verifyEmail) {
                                    if (wpvr_validate_email(input)) return {value: input, text: input};
                                    else {
                                        var boxError = wpvr_show_loading({
                                            title: wpvr_localize.wp_video_robot,
                                            text: 'Please enter a valid email address.',
                                            pauseButton: wpvr_localize.ok_button,
                                            isModal: false,
                                        });
                                        boxError.doPause(function () {
                                            boxError.remove();
                                        });
                                        return {};
                                    }

                                } else {
                                    return {value: input, text: input};
                                }
                            }
                        });
                    });

                    $('.wpvr_widget_form_content .wpvr_cmb_selectize').each(function () {
                        var field = $(this);
                        var maxItems = field.attr('maxItems');
                        var selectized = $('.cmb_select', field);
                        var $string = $('#wpvr_source_postCats').attr('value');
                        if ($string != undefined && $string != '') var selected_items = JSON.parse($string);
                        else selected_items = false;

                        //console.log( selected_items );

                        if (maxItems != '') {
                            var $selectized = selectized.selectize({
                                maxItems: maxItems,
                                items: selected_items,
                            });
                        } else {
                            if (selected_items != false) {
                                $selectized = selectized.selectize({
                                    maxItems: null,
                                    items: selected_items,
                                });
                            } else {
                                $selectized = selectized.selectize({
                                    maxItems: null,
                                    items: [],
                                });
                            }
                        }


                        function selectize_handler(obj) {
                            var selectedItems = new Array();
                            $('option', obj.currentTarget).each(function () {
                                selectedItems.push($(this).attr('value'));
                            });
                            str = JSON.stringify(selectedItems, null, 2)
                            $('#wpvr_source_postCats').attr('value', str);
                        }

                        $selectized.on('change', selectize_handler);
                        $selectized.on('initialize', selectize_handler);
                    });


                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                },
            });

            widgetDataBox.doCancel(function () {
                widgetDataBox.remove();
            });

            widgetDataBox.doPause(function () {
                var form = $('.wpvr_widget_form_data', widgetDataBox);
                var data = JSON.stringify(form.serializeArray());

                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        wpvr_wpload: 1,
                        save_form: 1,
                        encoded_data: data,
                    },
                    success: function (encoded_string) {
                        data_wrap.val(encoded_string);
                        widgetDataBox.remove();
                        save_widget_button.trigger('click');
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                    },
                });
            });

        });
    });
    return false;
}