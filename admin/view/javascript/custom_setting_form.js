
$(document).on("focus mousedown mouseup click", ".btn-group .btn.disabled", function (e) {
    e.preventDefault();
    e.stopPropagation();
});

$(document).on('change', '#input-type', function (e) {
    valuesBox($(this).val());
});

$(document).on('change', '#input-setting-values-options-values-route', function (e) {
    e.preventDefault();
    var type = $(this).find(':selected').attr('data-type');
    $('.tab-route').hide();
    var value = $(this).val();
    if (value == '0') {
        $('.not-required-values-route').show();
    } else {
        $('.required-values-route').show();
        if (type == 'block') {
            $('.required-values-route-field').show();
            var setting_id = $(this).val();
            $.ajax({
                url: 'index.php?route=custom/setting/settingsFields',
                dataType: 'json',
                type: 'GET',
                data: {
                    user_token: settings.user_token,
                    setting_id: value,
                },
                beforeSend: function (json) {
                    $('#input-setting-values-options-values-route-field').prop('disabled', true);
                },
                complete: function (json) {
                    $('#input-setting-values-options-values-route-field').prop('disabled', false);
                },
                success: function (json) {

                    $('#input-setting-values-options-values-route-field').find(':not([value="0"])').remove();
                    $.each(json, function (index, data) {
                        if (settings.values_options.values_route_field == data['setting_id']) {
                            var option = '<option value="' + data['setting_id'] + '" selected>' + data['title'] + '</option>';
                        } else {
                            var option = '<option value="' + data['setting_id'] + '">' + data['title'] + '</option>';
                        }
                        if (data.hasOwnProperty("group")) {

                            var group = data.group;

                            if ($('#input-setting-values-options-values-route-field').find("optgroup[label=\"" + group + "\"]").length === 0) {
                                $('#input-setting-values-options-values-route-field').append("<optgroup label=\"" + group + "\" />");
                            }

                            $('#input-setting-values-options-values-route-field').find("optgroup[label=\"" + group + "\"]").append(option);
                        } else {
                            $('#input-setting-values-options-values-route-field').append(option);
                        }

                    });

                },
            });
        }
        if (type == 'block' || type == 'multiple_autocomplete' || type == 'array' || type == 'radio' || type == 'old_radio' || type == 'checkbox' || type == 'select' || type == 'autocomplete') {

            $('.required-values-route-block').show();

            var setting_id = $(this).val();
            $.ajax({
                url: 'index.php?route=custom/setting/settingsPages',
                dataType: 'json',
                type: 'GET',
                data: {
                    user_token: settings.user_token,
                    setting_id: setting_id,
                },
                beforeSend: function (json) {
                    $('#input-setting-values-options-values-route-page').prop('disabled', true);
                },
                complete: function (json) {
                    $('#input-setting-values-options-values-route-page').prop('disabled', false);
                },
                success: function (json) {

                    $('#input-setting-values-options-values-route-page').find(':not([value="0"])').remove();
                    var option = '';
                    $('#input-setting-values-options-values-route-field').find(':not([value="0"])').remove();
                    $.each(json, function (index, group) {
                        option += '<optgroup label="' + group['heading_title'] + '">';
                        $.each(group['modules'], function (index, value) {
                            if (settings.values_options.values_route_page == value['id']) {
                                option += '<option value="' + value['id'] + '" selected>' + value['name'] + '</option>';
                            } else {
                                option += '<option value="' + value['id'] + '">' + value['name'] + '</option>';
                            }
                        });
                        option += '</optgroup>';
                    });
                    $('#input-setting-values-options-values-route-page').append(option);

                },
            });
        }
    }
});

$(document).on('change', '#input-setting-values-options-model-route', function (e) {
    e.preventDefault();
    $('.model-route').hide();

    if ($(this).val() == '0') {
        $('.not-required-model-route').show();
    } else if ($(this).val() == 'other') {
        $('.required-model-route').show();
        $('.required-model-route-other').show();
    } else {
        $('.required-model-route').show();

    }
});

$(document).on('change', '#input-section', function (e) {
    e.preventDefault();
    var $values_options = $('[name="values_options[values_route]"]');
    var value = $(this).val();
    settings.section_id = value;
    $.ajax({
        url: 'index.php?route=custom/setting/autocomplete',
        dataType: 'json',
        type: 'GET',
        data: {
            user_token: settings.user_token,
            // filter_section_id: value,
            filter_types: [
                'select',
                'radio',
                'radio_old',
                'checkbox',
                'array',
                'multiple_autocomplete',
                'block',
            ],
        },
        beforeSend: function (json) {
            $values_options.prop('disabled', true);
        },
        complete: function (json) {
            $values_options.prop('disabled', false);
        },
        success: function (json) {
            settings.settings = json;
            $values_options.find(':not([value="0"])').remove();

            $.each(settings.settings, function (index, data) {

                var option = '<option value="' + data['setting_id'] + '">' + data['title'] + '</option>';

                if (data.hasOwnProperty("type")) {

                    var group = settings.types.filter(function (value, index) {
                        return value['value'] === data.type;
                    })[0]['title'];

                    if ($values_options.find("optgroup[label=\"" + group + "\"]").length === 0) {
                        $values_options.append("<optgroup label=\"" + group + "\" />");
                    }

                    $values_options.find("optgroup[label=\"" + group + "\"]").append(option);
                } else {
                    $values_options.append(option);
                }

            });
            $('[name="values_options[values_route]"]').trigger('change');
        },
    });

});

$(document).on("focus mousedown mouseup click", "select[readonly],:checkbox[readonly]", function (e) {
    e.preventDefault();
    e.stopPropagation();
});

$(document).on('click', '#form-apply', function () {
    $('#alert-success, #alert-warning').remove();
    var form = $('#form-setting'),
        action = form.attr('action'),
        redirect = action + '&redirect=true';

    form.attr('action', redirect).submit().attr('action', action);

    return;
});

var fields = {
    placeholder: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += '  <label class="col-sm-2 control-label">' + settings.text.entry_placeholder + '</label>';
        field += '  <div class="col-sm-10">';

        $.each(settings['languages'], function (index, language) {
            field += '  <div class="input-group">';
            field += '      <span class="input-group-addon"><img src="language/' + language.code + '/' + language.code + '.png" title="' + language.name + '" /></span>';
            field += '      <input placeholder="' + settings.text.entry_placeholder + '" type="text" id="input-setting-values-options-' + language.language_id + '-placeholder" name="values_options[placeholder][' + language.language_id + ']" class="form-control"/>';
            field += '  </div>';
        });

        field += '  </div>';
        field += '</div>';
        return field;
    },
    inline: function (type) {
        field = '<div class="form-group setting-select-type-prop">';
        field += '   <label class="col-sm-2 control-label">';
        field += '<span title="' + settings.text.help_Inline + '" data-toggle="tooltip">' + settings.text.entry_Inline + '</span>';
        field += '</label>';
        field += '   <div class="col-sm-10">';
        field += '       <div class="btn-group" data-toggle="buttons">';
        // Да
        field += '           <label class="btn btn-default btn-toggle-on">';
        field += '               <input type="radio" name="values_options[inline]" value="1"/>';
        field += '               <span class="on-off">' + settings.text.text_yes + '</span>';
        field += '           </label>';
        // Нет
        field += '           <label class="btn btn-default active btn-toggle-off">';
        field += '               <input type="radio" name="values_options[inline]" value="0" checked=""/>';
        field += '               <span class="on-off">' + settings.text.text_no + '</span>';
        field += '           </label>';
        //
        field += '       </div>';
        field += '   </div>';
        field += '</div>';
        return field;
    },
    regex: function (type, value) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += '   <label class="col-sm-2 control-label" for="input-setting-regex">';
        field += '       <span data-toggle="tooltip" title="' + settings.text.help_regex + '">' + settings.text.entry_regex + '</span> ';
        field += '   </label>';
        field += '   <div class="col-sm-10">';
        field += '       <input placeholder="' + settings.text.entry_regex + '" name="regex" id="input-setting-regex" value="' + (
            value ? value : ''
        ) + '" class="form-control " type="text"/>';
        field += '   </div>';
        field += '</div>';
        return field;
    },
    values_route: function (type) {
        var field = '<div class="form-group setting-select-type-prop model-route required-model-route-other required" style="display:none">';
        field += ' <label class="col-sm-2 control-label" for="input-setting-values-route">';
        field += '<span title="' + settings.text.help_route + '" data-toggle="tooltip">' + settings.text.entry_route + '</span>';
        field += ' </label>';
        field += ' <div class="col-sm-10">';
        field += '     <input placeholder="demo_values"  name="values_route" id="input-setting-values-route" class="form-control " value="" type="text"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += ' </div>';
        field += '</div>';
        return field;
    },
    controller: function (type) {
        var field = '<div class="form-group setting-select-type-prop required">';
        field += ' <label class="col-sm-2 control-label" for="input-setting-values-options-controller">';
        field += '<span title="' + settings.text.help_route + '" data-toggle="tooltip">' + settings.text.entry_route + '</span>';
        field += ' </label>';
        field += ' <div class="col-sm-10">';
        field += '     <input placeholder="demo_field"  name="values_options[controller]" id="input-setting-values-options-controller" class="form-control " value="" type="text"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += ' </div>';
        field += '</div>';
        return field;
    },
    placeholder_size: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += '<label class="col-sm-2 control-label">' + settings.text.entry_placeholder_size + '';
        field += '</label>';
        field += '<div class="col-sm-10 form-inline">';
        field += '<div id="input-setting-values-options-placeholder-size">';
        // width
        field += '<input type="number" name="values_options[placeholder_size][width]" value="80" placeholder="' + settings.text.entry_placeholder_size_width + '" id="input-setting-values-options-placeholder-size-width" class="form-control"/>';
        // x
        field += '<span class="hidden-xs"> x </span> ';
        // Height
        field += '<input type="number" name="values_options[placeholder_size][height]" value="80" placeholder="' + settings.text.entry_placeholder_size_height + '" id="input-setting-values-options-placeholder-size-height" class="form-control"/>';
        // px
        field += '<span class="hidden-xs"> px</span>';
        //
        field += '</div>';
        field += '</div>';
        field += '</div>';
        return field;
    },
    mask: function (type) {

        var field = '<div class="form-group setting-select-type-prop">';
        field += ' <label class="col-sm-2 control-label" for="input-setting-values-options-mask">';
        field += '<span  data-toggle="tooltip" title="' + settings.text.help_mask + '">' + settings.text.entry_mask + '</span> ';
        field += ' </label>';
        field += ' <div class="col-sm-10">';
        field += '     <input placeholder="' + settings.text.entry_mask + '"  name="values_options[mask]" id="input-setting-values-options-mask" class="form-control " value="" type="text"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += ' </div>';
        field += '</div>';
        return field;
    },
    custom_validate: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += ' <label class="col-sm-2 control-label" for="input-setting-values-options-custom-validate">';
        field += '<span  data-toggle="tooltip" title="' + settings.text.help_custom_validate + '">' + settings.text.entry_custom_validate + '</span> ';
        field += ' </label>';
        field += ' <div class="col-sm-10">';
        field += '     <input placeholder="' + settings.text.entry_custom_validate + '" name="values_options[custom_validate]" id="input-setting-values-options-custom-validate" class="form-control " value="" type="text"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += ' </div>';
        field += '</div>';
        return field;
    },
    shown_tab: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += '    <label class="col-sm-2 control-label">' + settings.text.entry_show_tab + '';
        field += '    </label>';
        field += '    <div class="col-sm-10">';
        field += '        <div class="btn-group" data-toggle="buttons">';
        // Да
        field += '            <label class="btn btn-default active btn-toggle-on">';
        field += '                <input type="radio" name="values_options[shown_tab]" value="1" checked=""/>';
        field += '                <span class="on-off">' + settings.text.text_yes + '</span>';
        field += '            </label>';
        // Нет
        field += '            <label class="btn btn-default btn-toggle-off">';
        field += '                <input type="radio" name="values_options[shown_tab]" value="0"/>';
        field += '                <span class="on-off">' + settings.text.text_no + '</span>';
        field += '            </label>';
        //
        field += '        </div>';
        field += '    </div>';
        field += '</div>';
        return field;

    },
    accordion: function (type, show = false) {

        var field = '<div class="form-group setting-select-type-prop required">';

        field += '    <label class="col-sm-2 control-label">';
        field += settings.text.entry_accordion;
        field += '</label>';
        field += '    <div class="col-sm-10">';
        field += '        <div class="table-responsive setting-select-type-prop" id="input-setting-block-accordion">';
        field += '            <table id="setting-block-accordion" class="table table-striped table-bordered table-hover">';

        field += '                <tbody>';
        field += '                </tbody> ';

        if (!settings.integrated) {
            field += '            <tfoot> ';
            field += '                <tr>';
            field += '                    <td colspan="1"></td>';
            field += '                    <td class="text-left" style="width:57px"> <button type="button" onclick=" Accordion.addRow(\'' + type + '\');" data-toggle="tooltip" class="btn btn-primary" title="' + settings.text.button_add + '"> <i class="fa fa-plus-circle"></i> </button> </td> ';

            field += '                </tr>';
            field += '            </tfoot> ';
        }

        field += '            </table>';
        field += '        </div>';
        field += '    </div>';
        field += '</div>';
        return field;
    },
    filter_values: function (type, show = false) {
        if (show) {
            var field = '<div class="form-group setting-select-type-prop">';
        } else {
            var field = '<div class="form-group setting-select-type-prop model-route required-model-route" style="display:none">';
        }
        field += '    <label class="col-sm-2 control-label">';
        field += '    <span title="' + settings.text.help_filter + '" data-toggle="tooltip">' + settings.text.entry_filter + '</span>';
        field += '</label>';
        field += '    <div class="col-sm-10">';
        field += '        <div class="table-responsive setting-select-type-prop" >';
        field += '            <table id="setting-model-filter-values" class="table table-striped table-bordered table-hover">';
        field += '                <thead> ';
        field += '                    <tr>';
        field += '                        <td class="text-left required">' + settings.text.entry_row_key + '</td>';
        field += '                        <td class="text-left">' + settings.text.entry_row_value + '</td>';

        if (!settings.integrated) {
            field += '                    <td style="width: 60px;"></td>';
        }
        field += '                    </tr>';
        field += '                </thead>';
        field += '                <tbody>';
        field += '                </tbody> ';

        if (!settings.integrated) {
            field += '            <tfoot> ';
            field += '                <tr>';
            field += '                    <td colspan="2"></td>';
            field += '                    <td class="text-right"> <button type="button" onclick=" FilterValues.addRow(\'' + type + '\');" data-toggle="tooltip" class="btn btn-primary" title="' + settings.text.button_add + '"> <i class="fa fa-plus-circle"></i> </button> </td> ';
            field += '                </tr>';
            field += '            </tfoot> ';
        }

        field += '            </table>';
        field += '        </div>';
        field += '    </div>';
        field += '</div>';
        return field;
    },
    collation: function (type) {

        field = '<div class="form-group setting-select-type-prop model-route required-model-route-other required" style="display:none">';
        field += '<label class="col-sm-2 control-label">';
        field += '<span title="' + settings.text.help_collation + '" data-toggle="tooltip">' + settings.text.entry_collation + '</span>';
        field += '</label>';
        field += '<div class="col-sm-10">';
        field += '<div class="table-responsive setting-select-type-prop">';
        field += '<table id="input-setting-values-row" class="table table-striped table-bordered table-hover">';
        field += '<thead>';
        field += '<tr><td class="text-left required">' + settings.text.entry_row_title + '</td>';
        field += '<td class="text-left required">' + settings.text.entry_row_value + '</td> </tr>';
        field += '</thead>';
        field += '<tbody>';
        field += '<tr>';
        // title
        field += '<td class="text-left"><div class="input-group"><input type="text" id="input-setting-values-row-title" name="values_options[title_row]" value="title" size="80" class="form-control" placeholder="' + settings.text.entry_row_title + '"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/></div></td>';
        // value
        field += '<td class="text-left"><div class="input-group"><input type="text" id="input-setting-values-row-value" name="values_options[value_row]" value="value" size="80" class="form-control" placeholder="' + settings.text.entry_row_value + '"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/></div></td>';
        //
        field += '</tr>';
        field += '</tbody>';
        field += '  </table>';
        field += '</div>';
        field += '</div>';
        field += '</div>';

        return field;
    },

    option_values: function (type) {

        var field = '<div class="form-group setting-select-type-prop model-route not-required-model-route required">';
        field += '<label class="col-sm-2 control-label">';
        field += settings.text.entry_values;
        field += '</label>';
        field += '<div class="col-sm-10">';
        field += '<div class="table-responsive " id="input-setting-values-options-values">';
        field += '<table id="setting-values-options-values" class="table table-striped table-bordered table-hover">';
        field += '<thead> ';
        field += '<tr>';
        field += '<td class="text-left required">' + settings.text.entry_row_title + '</td>';
        field += '<td class="text-left required">' + settings.text.entry_row_value + '</td>';

        if (!settings.integrated) {
            field += '<td style="width: 60px;"></td>';
        }

        field += ' </tr>';
        field += ' </thead>';
        field += ' <tbody>';
        field += '</tbody> ';

        if (!settings.integrated) {
            field += '<tfoot> ';
            field += '<tr>';
            field += ' <td colspan="2"></td>';
            field += ' <td class="text-right"> <button type="button" onclick="ValuesTable.addRow(\'' + type + '\');" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="' + settings.text.button_add + '"> <i class="fa fa-plus-circle"></i> </button> </td> ';
            field += '</tr>';
            field += '</tfoot>';
        }

        field += '</table> </div>';
        field += '</div>';
        field += '</div>';
        return field;
    },
    block_width: function (type) {
        var field = '<div class="form-group setting-select-type-prop ">';
        field += '<label class="col-sm-2 control-label">' + settings.text.entry_block_width + '';
        field += '</label>';
        field += '<div class="col-sm-10">';
        field += ' <div class="btn-group" data-toggle="buttons">';
        // Да
        field += '<label class="btn btn-default btn-toggle-on">';
        field += '   <input type="radio" name="values_options[block_width]" value="1" />';
        field += '     <span class="on-off">' + settings.text.text_yes + '</span>';
        field += ' </label>';
        // Нет
        field += '  <label class="btn btn-default active btn-toggle-off">';
        field += '     <input type="radio" name="values_options[block_width]" value="0" checked=""/>';
        field += '     <span class="on-off">' + settings.text.text_no + '</span>';
        field += '   </label>';
        //
        field += '   </div>';
        field += '   </div>';
        field += '   </div>';
        return field;
    },
    range_type: function (type) {
        var field = '<div class="form-group setting-select-type-prop ">';
        field += '<label class="col-sm-2 control-label">' + settings.text.entry_range_type + '';
        field += '</label>';
        field += '<div class="col-sm-10">';
        field += ' <div class="btn-group" data-toggle="buttons">';
        // Нет
        field += '  <label class="btn btn-default active btn-toggle-off">';
        field += '     <input type="radio" name="values_options[range_type]" value="single" checked=""/>';
        field += '     <span class="on-off">' + settings.text.text_single + '</span>';
        field += '   </label>';

        // Да
        field += '<label class="btn btn-default btn-toggle-on">';
        field += '   <input type="radio" name="values_options[range_type]" value="double" />';
        field += '     <span class="on-off">' + settings.text.text_double + '</span>';
        field += ' </label>';

        //
        field += '   </div>';
        field += '   </div>';
        field += '   </div>';
        return field;
    },
    step: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += ' <label class="col-sm-2 control-label" for="input-setting-values-options-step">';
        field += settings.text.entry_step;
        field += ' </label>';
        field += ' <div class="col-sm-10">';
        field += '     <input placeholder="' + settings.text.entry_step + '"  name="values_options[step]" id="input-setting-values-options-step" class="form-control " value="1" type="number" min="0"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += ' </div>';
        field += '</div>';
        return field;
    },
    empty: function (type) {
        var field = '<div class="form-group setting-select-type-prop ">';
        field += '<label class="col-sm-2 control-label">';
        field += '<span title="' + settings.text.help_text_none + '" data-toggle="tooltip">' + settings.text.entry_text_none + '</span>';
        field += '</label>';
        field += '<div class="col-sm-10">';
        field += ' <div class="btn-group" data-toggle="buttons">';
        // Да
        field += '<label class="btn btn-default active btn-toggle-on' + (
            settings.integrated ? ' disabled' : ''
        ) + '">';
        field += '   <input type="radio" name="values_options[empty]" value="1" checked=""' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '     <span class="on-off">' + settings.text.text_yes + '</span>';
        field += ' </label>';
        // Нет
        field += '  <label class="btn btn-default btn-toggle-off' + (
            settings.integrated ? ' disabled' : ''
        ) + '">';
        field += '     <input type="radio" name="values_options[empty]" value="0"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '     <span class="on-off">' + settings.text.text_no + '</span>';
        field += '   </label>';
        //
        field += '   </div>';
        field += '   </div>';
        field += '   </div>';
        return field;
    },
    rows: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += '<label class="col-sm-2 control-label" for="input-setting-values-options-textarea-rows">';
        field += '<span  data-toggle="tooltip" title="' + settings.text.help_rows + '">' + settings.text.entry_rows + '</span> ';
        field += '</label>';
        field += '<div class="col-sm-10 form-inline">';
        field += '<input placeholder="' + settings.text.entry_rows + '" min="0" name="values_options[rows]" id="input-setting-values-options-textarea-rows" class="form-control " value="6" type="number"/>';
        field += '</div>';
        field += '</div>';
        return field;
    },
    height: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += '<label class="col-sm-2 control-label" for="input-setting-values-options-height">';
        field += settings.text.entry_height_map;
        field += '</label>';
        field += '<div class="col-sm-10 form-inline">';
        field += '<input placeholder="' + settings.text.entry_height_map + '" min="100" value="250" name="values_options[height]" id="input-setting-values-options-height" class="form-control " value="6" type="number"/>';
        field += '</div>';
        field += '</div>';
        return field;
    },
    style: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += '<label class="col-sm-2 control-label" for="input-setting-values-options-alert-type">';
        field += '' + settings.text.entry_style + '';
        field += '</label>';
        field += '<div class="col-sm-10 form-inline">';
        field += '<select name="values_options[alert][style]" id="input-setting-values-options-alert-type" class="form-control "' + (
            settings.integrated ? ' readonly' : ''
        ) + '>';
        field += '<option value="success">Success</option>';
        field += '<option value="info">Info</option>';
        field += '<option value="warning">Warning</option>';
        field += '<option value="danger">Danger</option>';
        field += '</select>';
        field += '</div>';
        field += '</div>';
        return field;
    },
    text: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += '<label class="col-sm-2 control-label">';
        field += '' + settings.text.entry_text + '';
        field += '</label>';
        field += '<div class="col-sm-10">';
        $.each(settings['languages'], function (index, language) {
            field += ' <div class="input-group">';
            field += '<span class="input-group-addon"><img src="language/' + language.code + '/' + language.code + '.png" title="' + language.name + '" /></span>';
            field += '<textarea id="input-setting-values-options-' + language.language_id + '-text" name="values_options[text][' + language.language_id + ']" class="form-control"></textarea>';
            field += '</div>';
        });
        field += '</div>';
        field += '</div>';

        return field;
    },
    html: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += '<label class="col-sm-2 control-label">';
        field += '' + settings.text.entry_html + '';
        field += '</label>';
        field += '<div class="col-sm-10">';
        field += '<ul class="nav nav-tabs">';
        $.each(settings['languages'], function (index, language) {
            field += '<li>';
            field += '<a href="#tab-setting-values-options-' + language.language_id + '-html" data-toggle="tab" aria-expanded="false"><img src="language/' + language.code + '/' + language.code + '.png" title="' + language.name + '" /> ';
            field += language.name;
            field += '</a>';
            field += '</li>';
        });
        field += '</ul>';
        field += '<div class="tab-content">';
        $.each(settings['languages'], function (index, language) {
            field += '<div class="tab-pane" id="tab-setting-values-options-' + language.language_id + '-html">';
            field += '<textarea name="values_options[html][' + language.language_id + ']"  id="input-setting-values-options-' + language.language_id + '-html"  class="form-control texteditor"></textarea>';

            field += '</div>';
        });
        field += '</div>';


        field += '</div>';
        field += '</div>';
        return field;
    },

    model_route: function (type, empty) {

        var field = '<div class="form-group form-inline setting-select-type-prop">';
        field += '<label class="col-sm-2 control-label" for="input-setting-values-options-model-route">';
        field += '       <span data-toggle="tooltip" title="' + settings.text.help_controller + '">' + settings.text.entry_model + '</span> ';
        field += '</label>';
        field += '<div class="col-sm-10 form-inline">';
        field += ' <select name="values_options[model_route]" id="input-setting-values-options-model-route" class="form-control"' + (
            settings.integrated ? ' readonly' : ''
        ) + '>';
        if (empty) {
            field += ' <option value="0">' + settings.text.text_none + '</option>';
        }
        var select_controller = $('<select/>');
        $.each(settings.controllers, function (index, data) {
            var option = '<option value="' + data['value'] + '">' + data['title'] + '</option>';

            if (data.hasOwnProperty("group")) {
                var group = data.group;

                if (select_controller.find("optgroup[label=\"" + group + "\"]").length === 0) {
                    select_controller.append("<optgroup label=\"" + group + "\" />");
                }
                select_controller.find("optgroup[label=\"" + group + "\"]").append(option);
            } else {
                select_controller.append(option);
            }
        });
        field += select_controller.html();
        field += ' </select>';
        field += ' </div>';
        field += ' </div>';
        return field;
    },
    code: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += '<label class="col-sm-2 control-label" for="input-setting-values-options-code">';
        field += '' + settings.text.entry_code + '';
        field += '</label>';
        field += '<div class="col-sm-10 form-inline">';
        field += '<input name="values_options[code]" id="input-setting-values-options-code" class="form-control " value="" type="text" placeholder="' + settings.text.entry_code + '"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '</div>';
        field += '</div>';
        return field;
    },

    date_format: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += '<label class="col-sm-2 control-label" for="input-setting-values-options-datetimepicker-date-format">';
        field += settings.text.entry_format;
        field += '</label>';
        field += '<div class="col-sm-10 form-inline">';
        field += '<input placeholder="DD/MM/YYYY HH:mm:ss" name="values_options[date_format]" id="input-setting-values-options-datetimepicker-date-format" class="form-control " value="' + settings.text.datetime_format + '" type="text"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '</div>';
        field += '</div>';
        return field;
    },
    color_format: function (type) {
        var field = '<div class="form-group setting-select-type-prop ">';
        field += '<label class="col-sm-2 control-label">' + settings.text.entry_format + '';
        field += '</label>';
        field += '<div class="col-sm-10">';
        field += ' <div class="btn-group" data-toggle="buttons">';
        field += '<label class="btn btn-default active btn-toggle-on' + (
            settings.integrated ? ' disabled' : ''
        ) + '">';
        field += '   <input type="radio" name="values_options[format]" value="auto" checked=""' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '     <span class="on-off">Auto</span>';
        field += ' </label>';

        field += '  <label class="btn btn-default btn-toggle-on' + (
            settings.integrated ? ' disabled' : ''
        ) + '">';
        field += '     <input type="radio" name="values_options[format]" value="hex"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '     <span class="on-off">hex</span>';
        field += '   </label>';

        field += '  <label class="btn btn-default btn-toggle-on' + (
            settings.integrated ? ' disabled' : ''
        ) + '">';
        field += '     <input type="radio" name="values_options[format]" value="rgb"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '     <span class="on-off">rgb</span>';
        field += '   </label>';

        field += '  <label class="btn btn-default btn-toggle-on' + (
            settings.integrated ? ' disabled' : ''
        ) + '">';
        field += '     <input type="radio" name="values_options[format]" value="rgba"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '     <span class="on-off">rgba</span>';
        field += '   </label>';

        field += '   </div>';
        field += '   </div>';
        field += '   </div>';
        return field;

    },
    map_type: function (type) {

        var field = '<div class="form-group setting-select-type-prop ">';
        field += '<label class="col-sm-2 control-label">' + settings.text.entry_map_type + '';
        field += '</label>';
        field += '<div class="col-sm-10">';
        field += ' <div class="btn-group" data-toggle="buttons">';

        field += '  <label class="btn btn-default active btn-toggle-on' + (
            settings.integrated ? ' disabled' : ''
        ) + '">';
        field += '     <input type="radio" name="values_options[map_type]" value="google" checked=""' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '     <span class="on-off">' + settings.text.text_google_map + '</span>';
        field += '   </label>';

        field += '<label class="btn btn-default btn-toggle-on' + (
            settings.integrated ? ' disabled' : ''
        ) + '">';
        field += '   <input type="radio" name="values_options[map_type]" value="yandex"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '     <span class="on-off">' + settings.text.text_yandex_map + '</span>';
        field += ' </label>';

        field += '<label class="btn btn-default btn-toggle-on' + (
            settings.integrated ? ' disabled' : ''
        ) + '">';
        field += '   <input type="radio" name="values_options[map_type]" value="2gis"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '     <span class="on-off">' + settings.text.text_2gis_map + '</span>';
        field += ' </label>';

        field += '   </div>';
        field += '   </div>';
        field += '   </div>';
        return field;

    },
    pick: function (type) {

        var field = '<div class="form-group setting-select-type-prop ">';
        field += '<label class="col-sm-2 control-label">' + settings.text.entry_pick + '';
        field += '</label>';
        field += '<div class="col-sm-10">';
        field += ' <div class="btn-group" data-toggle="buttons">';
        field += '<label class="btn btn-default active btn-toggle-on' + (
            settings.integrated ? ' disabled' : ''
        ) + '">';
        field += '   <input type="radio" name="values_options[pick]" value="all" checked=""' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '     <span class="on-off">' + settings.text.text_all + '</span>';
        field += ' </label>';

        field += '  <label class="btn btn-default btn-toggle-on' + (
            settings.integrated ? ' disabled' : ''
        ) + '">';
        field += '     <input type="radio" name="values_options[pick]" value="date"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '     <span class="on-off">' + settings.text.text_date + '</span>';
        field += '   </label>';

        field += '  <label class="btn btn-default btn-toggle-on' + (
            settings.integrated ? ' disabled' : ''
        ) + '">';
        field += '     <input type="radio" name="values_options[pick]" value="time"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '     <span class="on-off">' + settings.text.text_time + '</span>';
        field += '   </label>';
        field += '   </div>';
        field += '   </div>';
        field += '   </div>';
        return field;

    },
    select_values_route: function (type, empty) {
        var field = '<div class="form-group form-inline setting-select-type-prop">';
        if (settings.integrated && (settings.values_options.integrate_values == '0' && !settings.developer_status)) {
            field += '<div class="col-sm-12"> ';
            field += '<div class="alert alert-info alert-dismissible"> ';
            field += ' <i class="fa fa-info-circle"></i> ' + settings.text.text_developer_block_value_integrated + ' ';
            field += '  </div> ';
            field += '  </div> ';
        }
        field += '<label class="col-sm-2 control-label" for="input-setting-values-options-values-route">';
        field += '       <span data-toggle="tooltip" title="' + settings.text.help_value_from + '">' + settings.text.entry_value_from + '</span> ';
        field += '</label>';
        field += '<div class="col-sm-10 form-inline">';

        field += ' <select name="values_options[values_route]" id="input-setting-values-options-values-route" class="form-control"' + (
            !(!settings.integrated || (settings.values_options.integrate_values == '0' && settings.developer_status)) ? ' readonly' : ''
        ) + '>';
        if (empty) {
            field += ' <option value="0">' + settings.text.text_none + '</option>';
        }
        var select_setting = $('<select/>');
        $.each(settings.settings, function (index, data) {
            console.log(data);
            var option = '<option data-type="' + data['type'] + '" value="' + data['setting_id'] + '">' + data['title'] + '</option>';

            if (data.hasOwnProperty("type")) {
                var group = settings.types.filter(function (value, index) {
                    return value['value'] === data.type;
                })[0]['title'];

                if (select_setting.find("optgroup[label=\"" + group + "\"]").length === 0) {
                    select_setting.append("<optgroup label=\"" + group + "\" />");
                }
                select_setting.find("optgroup[label=\"" + group + "\"]").append(option);
            } else {
                select_setting.append(option);
            }
        });

        field += select_setting.html();
        field += ' </select>';
        field += ' </div>';
        field += ' </div>';

        field += '<div class="form-group setting-select-type-prop required-values-route-block tab-route">';
        field += '<label class="col-sm-2 control-label" for="input-setting-values-options-values-route-page">';
        field += '' + settings.text.text_pages + '';
        field += '</label>';
        field += '<div class="col-sm-10 form-inline">';
        field += '<select name="values_options[values_route_page]" id="input-setting-values-options-values-route-page" class="form-control "' + (
            settings.integrated ? ' readonly' : ''
        ) + '>';
        field += ' <option value="0">' + settings.text.text_none + '</option>';
        field += '</select>';
        field += '</div>';
        field += '</div>';

        field += '<div class="form-group setting-select-type-prop required-values-route-block tab-route">';
        field += '<label class="col-sm-2 control-label" for="input-setting-values-options-route-page-id">';
        field += 'Id';
        field += '</label>';
        field += '<div class="col-sm-10 form-inline">';
        field += '<input placeholder="" name="values_options[values_route_page_id]" id="input-setting-values-options-values-route-page-id" class="form-control " value="" type="text"/>';
        field += '</div>';
        field += '</div>';

        field += '<div class="form-group setting-select-type-prop required-values-route-field tab-route required">';
        field += '<label class="col-sm-2 control-label" for="input-setting-values-options-values-route-field">';
        field += 'Поле';
        field += '</label>';
        field += '<div class="col-sm-10 form-inline">';
        field += '<select name="values_options[values_route_field]" id="input-setting-values-options-values-route-field" class="form-control "' + (
            settings.integrated ? ' readonly' : ''
        ) + '>';

        field += '</select>';
        field += '</div>';
        field += '</div>';


        return field;
    },
    min: function (type, required = false) {
        var field = '<div class="form-group setting-select-type-prop' + (required ? ' required' : '') + '">';
        field += '<label class="col-sm-2 control-label" for="input-setting-values-options-min">';
        field += settings.text.text_min;
        field += '</label>';
        field += '<div class="col-sm-10 form-inline">';
        field += '<input name="values_options[min]" id="input-setting-values-options-min" class="form-control " value="" type="number"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '</div>';
        field += '</div>';
        return field;
    },
    max: function (type, required = false) {
        var field = '<div class="form-group setting-select-type-prop' + (required ? ' required' : '') + '">';
        field += '<label class="col-sm-2 control-label" for="input-setting-values-options-max">';
        field += settings.text.text_max;
        field += '</label>';
        field += '<div class="col-sm-10 form-inline">';
        field += '<input name="values_options[max]" id="input-setting-values-options-max" class="form-control " value="" type="number"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '</div>';
        field += '</div>';
        return field;
    },

    integrate_values: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += '   <label class="col-sm-2 control-label">';
        field += '       <span data-toggle="tooltip" title="' + settings.text.help_integrate + '">' + settings.text.entry_integrate_values + '</span> ';
        field += '   </label>';
        field += '   <div class="col-sm-10">';
        field += '       <div class="btn-group" data-toggle="buttons">';
        // Да
        field += '           <label class="btn btn-default btn-toggle-on' + (
            settings.integrated ? ' disabled' : ''
        ) + '">';
        field += '               <input type="radio" name="values_options[integrate_values]" value="1"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '               <span class="on-off">' + settings.text.text_yes + '</span>';
        field += '           </label>';
        // Нет
        field += '           <label class="btn btn-default active btn-toggle-off' + (
            settings.integrated ? ' disabled' : ''
        ) + '">';
        field += '               <input type="radio" name="values_options[integrate_values]" value="0" checked=""' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '               <span class="on-off">' + settings.text.text_no + '</span>';
        field += '           </label>';
        //
        field += '       </div>';
        field += '   </div>';
        field += '</div>';
        return field;
    },
    vertical_tab: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += '   <label class="col-sm-2 control-label">' + settings.text.entry_vertical_tab + '';
        field += '   </label>';
        field += '   <div class="col-sm-10">';
        field += '       <div class="btn-group" data-toggle="buttons">';
        // Да
        field += '           <label class="btn btn-default btn-toggle-on">';
        field += '               <input type="radio" name="values_options[vertical_tab]" value="1"/>';
        field += '               <span class="on-off">' + settings.text.text_yes + '</span>';
        field += '           </label>';
        // Нет
        field += '           <label class="btn btn-default active btn-toggle-off">';
        field += '               <input type="radio" name="values_options[vertical_tab]" value="0" checked=""/>';
        field += '               <span class="on-off">' + settings.text.text_no + '</span>';
        field += '           </label>';
        //
        field += '       </div>';
        field += '   </div>';
        field += '</div>';
        return field;

    },
    range_grid: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += '   <label class="col-sm-2 control-label">' + settings.text.entry_range_grid + '';
        field += '   </label>';
        field += '   <div class="col-sm-10">';
        field += '       <div class="btn-group" data-toggle="buttons">';
        // Да
        field += '           <label class="btn btn-default btn-toggle-on">';
        field += '               <input type="radio" name="values_options[range_grid]" value="1"/>';
        field += '               <span class="on-off">' + settings.text.text_yes + '</span>';
        field += '           </label>';
        // Нет
        field += '           <label class="btn btn-default active btn-toggle-off">';
        field += '               <input type="radio" name="values_options[range_grid]" value="0" checked=""/>';
        field += '               <span class="on-off">' + settings.text.text_no + '</span>';
        field += '           </label>';
        //
        field += '       </div>';
        field += '   </div>';
        field += '</div>';
        return field;

    },
    pre_rows: function (type) {
        var field = '<div class="form-group setting-select-type-prop">';
        field += '<label class="col-sm-2 control-label" for="input-setting-values-options-block-pre-rows">';
        field += settings.text.entry_pre_rows;
        field += '</label>';
        field += '<div class="col-sm-10 form-inline">';
        field += '<input placeholder="' + settings.text.entry_pre_rows + '" name="values_options[pre_rows]" id="input-setting-values-options-block-pre-rows" class="form-control " value="" type="number"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '</div>';
        field += '</div>';
        return field;
    },
    rows_limit: function (type) {

        var field = '<div class="form-group setting-select-type-prop">';
        field += '<label class="col-sm-2 control-label" for="input-setting-values-options-block-rows-limit">';
        field += settings.text.entry_rows_limit;
        field += '</label>';
        field += '<div class="col-sm-10 form-inline">';
        field += '<input placeholder="' + settings.text.entry_rows_limit + '" name="values_options[rows_limit]" id="input-setting-values-options-block-rows-limit" class="form-control " value="" type="number"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        field += '</div>';
        field += '</div>';
        return field;
    },
    block_values: function (type) {
        var field = '<div class="form-group setting-select-type-prop required" >';
        field += '<label class="col-sm-2 control-label">';
        field += settings.text.entry_settings;
        field += '</label>';
        field += '<div class="col-sm-10">';
        field += '<div class="setting-select-type-prop" id="input-setting-block-values">';
        if (settings.integrated) {
            field += '<div class="alert alert-info alert-dismissible"> ';
            field += ' <i class="fa fa-info-circle"></i> ' + settings.text.text_developer_block_value_integrated + ' ';
            field += '  </div> ';
        }
        field += '<table id="setting-block-values" class="table table-striped table-bordered table-hover">';
        field += '<thead> ';
        field += '<tr>';
        field += '<td class="text-left required">' + settings.text.entry_row_column + '</td>';
        field += '<td class="text-left required">' + settings.text.entry_row_setting + '</td>';
        field += '<td class="text-left">' + settings.text.entry_row_width + '</td>';
        if (!settings.integrated) {
            field += '<td style="width: 60px;"></td>';
        }
        field += ' </tr>';
        field += ' </thead>';
        field += ' <tbody>';
        field += '</tbody> ';
        if (!settings.integrated) {
            field += '<tfoot> ';
            field += '<tr>';
            field += ' <td colspan="3"></td>';
            field += ' <td class="text-right"> <button type="button" onclick="Setting.addRow(\'' + type + '\');" data-toggle="tooltip" class="btn btn-primary" title="' + settings.text.button_add + '"> <i class="fa fa-plus-circle"></i> </button> </td> ';
            field += '</tr>';
            field += '</tfoot> ';
        }
        field += '</table>';
        field += ' </div>';
        field += ' </div>';
        field += ' </div>';
        return field;
    },
    childrens: function (type) {
        var field = '<div class="form-group setting-select-type-prop not-required-values-route tab-route required">';

        field += '<label class="col-sm-2 control-label">';
        field += '       <span data-toggle="tooltip" title="' + settings.text.help_values + '">' + settings.text.entry_values + '</span> ';
        field += '</label>';
        field += '<div class="col-sm-10">';
        field += '<div class="table-responsive " id="input-setting-values-options-childrens">';
        field += '<table id="setting-values-options-childrens" class="table table-striped table-bordered table-hover">';
        field += '<thead> ';
        field += '<tr>';
        field += '<td class="text-left required">' + settings.text.entry_row_title + '</td>';
        field += '<td class="text-left"><span data-toggle="tooltip" title="' + settings.text.help_row_key + '"> ' + settings.text.entry_row_key + '</span></td>';


        if ((!settings.integrated || settings.values_options.integrate_values == '0') || (settings.values_options.integrate_values == '0' && settings.developer_status)) {
            field += '<td style=" width: 60px; "></td>';
        }
        field += ' </tr>';
        field += ' </thead>';
        field += ' <tbody>';
        field += '</tbody> ';
        if ((!settings.integrated || settings.values_options.integrate_values == '0') || (settings.values_options.integrate_values == '0' && settings.developer_status)) {
            field += '<tfoot> ';
            field += '<tr>';
            field += ' <td colspan="2"></td>';
            field += ' <td class="text-right"> <button type="button" onclick="Children.addRow(\'' + type + '\');" data-toggle="tooltip" class="btn btn-primary" title="' + settings.text.button_add + '"> <i class="fa fa-plus-circle"></i> </button> </td> ';
            field += '</tr>';
            field += '</tfoot>';
        }
        field += '</table> </div>';
        field += '</div>';
        field += '</div>';
        return field;
    },
    children_settings: function (type) {

        var field = '<div class="form-group setting-select-type-prop required" >';
        if ((settings.integrated && settings.values_options.integrate_values == '0' && !settings.developer_status)) {
            field += '<div class="col-sm-12"> ';
            field += '<div class="alert alert-info alert-dismissible"> ';
            field += ' <i class="fa fa-info-circle"></i> ' + settings.text.text_developer_block_value_integrated + ' ';
            field += '  </div> ';
            field += '  </div> ';
        }
        field += '<label class="col-sm-2 control-label">';
        field += '       <span data-toggle="tooltip" title="' + settings.text.help_settings + '">' + settings.text.entry_settings + '</span> ';
        field += '</label>';
        field += '<div class="col-sm-10">';
        field += '<div class="setting-select-type-prop" id="input-setting-children-values">';
        field += '<table id="setting-children-values" class="table table-striped table-bordered table-hover">';
        field += '<thead> ';
        field += '<tr>';
        field += '<td class="text-left required">' + settings.text.entry_row_setting + '</td>';
        if (type == 'juxtapose') {
            field += '<td class="text-left" style=" width: 271px; ">' + settings.text.entry_row_width + '</td>';
        }
        if (!settings.integrated || (settings.values_options.integrate_values == '0' && settings.developer_status)) {
            field += '<td style=" width: 60px; "></td>';
        }
        field += ' </tr>';
        field += ' </thead>';
        field += ' <tbody>';
        field += '</tbody> ';
        if (!settings.integrated || (settings.values_options.integrate_values == '0' && settings.developer_status)) {
            field += '<tfoot> ';
            field += '<tr>';
            field += ' <td colspan="' + (
                type == 'juxtapose' ? 2 : 1
            ) + '"></td>';
            field += ' <td class="text-right"> <button type="button" onclick="SettingValues.addRow(\'' + type + '\');" data-toggle="tooltip" class="btn btn-primary" title="' + settings.text.button_add + '"> <i class="fa fa-plus-circle"></i> </button> </td> ';
            field += '</tr>';
            field += '</tfoot> ';
        }
        field += '</table>';
        field += ' </div>';
        field += ' </div>';
        field += ' </div>';
        return field;

    },
};

function error(i, json) {
    var element = $('#input-' + i.replace(/_/g, '-')),
        alert = '<div class="text-danger">' + json[i] + '</div>';
    if (element.length === 0) {
        var element = $('[name="' + i + '"]');
    }
    element.prop('readonly', false);
    if (element.length) {
        if (element.parent().hasClass('input-group')) {
            element.parent().after(alert);
            element.parent().addClass('has-error');
        } else if (element.closest('.form-group').length) {
            element.after(alert);
            element.closest('.form-group').addClass('has-error');
        } else {
            element.after(alert);
            element.parent().addClass('has-error');
        }

    }
    element.addClass('is-invalid');
};

function convert_object_keys(obj, prefix = '', res = {}) {
    for (let key in obj) {
        if (!obj.hasOwnProperty(key)) {
            continue;
        }
        if (typeof obj[key] === 'object' && obj[key] !== null) {
            this.convert_object_keys(obj[key], prefix + '[' + key + ']', res);
        } else {
            res[prefix + '[' + key + ']'] = obj[key];
        }
    }
    return res;
};

var Accordion = {
    'addValues': function (type, values, length) {
        var values = values || [];
        setting_block_accordion_row = 0;
        if (values && Object.keys(values).length) {
            $.each(values, function (index, data) {
                Accordion.addRow(type, setting_block_accordion_row);
            });
        }
        $('[data-toggle="tooltip"]').tooltip('hide');
    },
    'addRow': function (type, index) {
        if (!index) {
            var index = setting_block_accordion_row;
        }
        var html = '';

        html += ' <tr data-id="' + index + '" id="setting-value-accordion-' + index + '">';

        html += '<td class="text-left">';
        html += '<ul class="nav nav-tabs" style="margin-bottom:5px;">';
        $.each(settings['languages'], function (language_id, language) {
            html += '<li>';
            html += '<a href="#tab-setting-values-options-accordion-' + index + '-' + language.language_id + '" data-toggle="tab" aria-expanded="false"><img src="language/' + language.code + '/' + language.code + '.png" title="' + language.name + '" /> ' + language.name;
            html += '</a>';
            html += '</li>';
        });
        html += '</ul>';
        html += '<div class="tab-content">';
        $.each(settings['languages'], function (language_id, language) {
            html += '<div class="tab-pane" id="tab-setting-values-options-accordion-' + index + '-' + language.language_id + '">';
            html += '<input style="margin-bottom:5px;" placeholder="' + settings.text.entry_title + '" name="values_options[accordion][' + index + '][descriptions][' + language.language_id + '][title]"  id="input-setting-block-accordion-' + index + '-title-' + language.language_id + '"  class="form-control"/>';
            html += '<div  id="input-setting-block-accordion-' + index + '-description-' + language.language_id + '">';
            html += '<textarea name="values_options[accordion][' + index + '][descriptions][' + language.language_id + '][description]"  id="input-setting-block-accordion-' + index + '-description-' + language.language_id + '"  class="form-control texteditor"></textarea>';
            html += '</div>';
            html += '</div>';
        });
        html += '</div>';

        html += '</td> ';
        if (!settings.integrated) {
            html += '<td class="text-left" style=" width: 57px; "><button type="button"  onclick="confirm(settings[\'text\'][\'text_confirm\'] ) ? ($(this).tooltip(\'destroy\'), $(\'#setting-value-accordion-' + index + '\').remove(),setting_block_accordion_row--) : false;" data-toggle="tooltip" class="btn btn-danger" title="' + settings.text.button_remove + '"><i class="fa fa-minus-circle"></i></button></td>';
        }

        html += '</tr>';

        var html = $(html);
        html.find('.texteditor').summernote({ height: 150 });
        $('#setting-block-accordion tbody').append(html);
        html.find('[data-toggle="tooltip"]').tooltip();

        html.find("a[data-toggle=tab]:first").tab("show");


        $('[data-toggle="tooltip"]').tooltip('hide');
        setting_block_accordion_row = setting_block_accordion_row + 1;

    },
};

var Setting = {
    'addValues': function (type, values, length) {
        var values = values || [];
        setting_block_value_row = 0;
        if (values && Object.keys(values).length) {
            $.each(values, function (index, data) {
                Setting.addRow(type, setting_block_value_row);
            });
        }
        $('[data-toggle="tooltip"]').tooltip('hide');
    },
    'addRow': function (type, index) {
        if (!index) {
            var index = setting_block_value_row;
        }
        var html = '';

        html += ' <tr data-id="' + index + '" id="setting-value-block-' + index + '">';
        html += '<td class="text-left">';
        $.each(settings['languages'], function (language_id, language) {
            html += '<div class="input-group"><span class="input-group-addon"><img src="language/' + language.code + '/' + language.code + '.png" title="' + language.name + '" /></span><input id="input-setting-block-options-values-row-' + index + '-description-' + language.language_id + '-title"  type="text" name="values_options[settings][' + index + '][description][' + language.language_id + '][title]" placeholder="' + settings.text.entry_row_title + '" value="" class="form-control" size="80"/></div>';
        });

        html += '</td> ';
        html += '<td class="text-left"><div class="input-group">';
        html += '<input type="text" id="input-setting-block-' + index + '-value" name="values_options[settings][' + index + '][setting][title]" value="" size="80" class="form-control" placeholder="' + settings.text.entry_row_setting + '"' + (
            settings.integrated && !settings.developer_status ? ' readonly' : ''
        ) + '/>';
        html += '<input type="hidden"name="values_options[settings][' + index + '][setting][setting_id]"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/>';
        html += '</div></td> ';
        html += '<td class="text-left"><div class="input-group"><input id="input-setting-block-options-values-row-' + index + '-width"  type="text" name="values_options[settings][' + index + '][width]" placeholder="' + settings.text.entry_row_width + '" value="" class="form-control" size="30"/></div></td>';
        if (!settings.integrated) {
            html += '<td class="text-right"><button type="button"  onclick="confirm(settings[\'text\'][\'text_confirm\'] ) ? ($(this).tooltip(\'destroy\'), $(\'#setting-value-block-' + index + '\').remove(),setting_block_value_row--) : false;" data-toggle="tooltip" class="btn btn-danger" title="' + settings.text.button_remove + '"><i class="fa fa-minus-circle"></i></button></td>';
        }
        html += '</tr>';

        var html = $(html);

        $('#setting-block-values tbody').append(html);


        html.find('[data-toggle="tooltip"]').tooltip();
        html.find('[name="values_options[settings][' + index + '][setting][title]"]').autocomplete({
            'source': function (request, response) {
                if (!html.find('[name="values_options[settings][' + index + '][setting][title]"]').prop('readonly')) {
                    $.ajax({
                        url: 'index.php?route=custom/setting/autocomplete&user_token=' + settings.user_token + '&filter_section_id=' + settings.section_id + '&filter_title=' + encodeURIComponent(request),
                        dataType: 'json',
                        success: function (json) {
                            response($.map(json, function (item) {
                                return { label: item['title'], value: item['setting_id'], };
                            }));
                        },
                    });
                }
            },
            'select': function (item) {
                html.find('[name="values_options[settings][' + index + '][setting][title]"]').val(item['label']);
                html.find('[name="values_options[settings][' + index + '][setting][setting_id]"]').val(item['value']);
            },
        });

        $('[data-toggle="tooltip"]').tooltip('hide');
        setting_block_value_row = setting_block_value_row + 1;

    },
};

var SettingValues = {
    'addSettings': function (type, values, length) {
        var values = values || [];
        setting_children_value_row = 0;
        if (values && Object.keys(values).length) {
            $.each(values, function (index, data) {
                SettingValues.addRow(type, setting_children_value_row);
            });
        }
        $('[data-toggle="tooltip"]').tooltip('hide');
    },
    'addValues': function () { },
    'addRow': function (type, index) {
        if (!index) {
            var index = setting_children_value_row;
        }
        var html = '';

        html += ' <tr data-id="' + index + '" id="setting-value-children-' + index + '">';

        html += '<td class="text-left"><div class="input-group btn-block">';
        html += '<input type="text" id="input-setting-children-' + index + '-value" name="values_options[settings][' + index + '][setting][title]" value="" class="form-control" placeholder="' + settings.text.entry_row_setting + '"' + (
            !settings.integrated || (settings.values_options.integrate_values == '0' && settings.developer_status) ? '' : ' readonly'
        ) + '/>';
        html += '<input type="hidden"name="values_options[settings][' + index + '][setting][setting_id]"' + (
            !settings.integrated || (settings.values_options.integrate_values == '0' && settings.developer_status) ? '' : ' readonly'
        ) + '/>';
        html += '</div></td> ';
        if (type == 'juxtapose') {
            html += '<td class="text-left"><div class="input-group"><input id="input-setting-block-options-values-row-' + index + '-width"  type="text" name="values_options[settings][' + index + '][width]" placeholder="' + settings.text.entry_row_width + '" value="" class="form-control" size="30"/></div></td>';

        }
        if (!settings.integrated || (settings.values_options.integrate_values == '0' && settings.developer_status)) {
            html += '<td class="text-right"><button type="button"  onclick="confirm(settings[\'text\'][\'text_confirm\'] ) ? ($(this).tooltip(\'destroy\'), $(\'#setting-value-children-' + index + '\').remove(),setting_children_value_row--) : false;" data-toggle="tooltip" class="btn btn-danger" title="' + settings.text.button_remove + '"><i class="fa fa-minus-circle"></i></button></td>';
        }
        html += '</tr>';
        var html = $(html);
        $('#setting-children-values tbody').append(html);
        html.find('[data-toggle="tooltip"]').tooltip();
        html.find('[name="values_options[settings][' + index + '][setting][title]"]').autocomplete({
            'source': function (request, response) {
                if (!html.find('[name="values_options[settings][' + index + '][setting][title]"]').prop('readonly')) {
                    $.ajax({
                        url: 'index.php?route=custom/setting/autocomplete&user_token=' + settings.user_token + '&filter_section_id=' + settings.section_id + '&filter_title=' + encodeURIComponent(request),
                        dataType: 'json',
                        success: function (json) {
                            response($.map(json, function (item) {
                                return { label: item['title'], value: item['setting_id'], };
                            }));
                        },
                    });
                }
            },
            'select': function (item) {
                html.find('[name="values_options[settings][' + index + '][setting][title]"]').val(item['label']);
                html.find('[name="values_options[settings][' + index + '][setting][setting_id]"]').val(item['value']);
            },
        });
        $('[data-toggle="tooltip"]').tooltip('hide');
        setting_children_value_row = setting_children_value_row + 1;
    },
};

var FilterValues = {
    'addRow': function (type, index) {
        if (!index) {
            var index = setting_value_filter_row;
        }
        var html = '';
        html += ' <tr data-id="' + index + '" id="setting-value-model-filter-' + index + '">';
        html += '<td class="text-left"><div class="input-group"><input type="text" id="input-setting-model-filter-' + index + '-key" name="values_options[model_filter][' + index + '][key]" value="" size="80" class="form-control" placeholder="' + settings.text.entry_row_key + '"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/></div></td> ';
        html += '<td class="text-left"><div class="input-group"><input type="text" id="input-setting-model-filter-' + index + '-value" name="values_options[model_filter][' + index + '][value]" value="" size="80" class="form-control" placeholder="' + settings.text.entry_row_value + '"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/></div></td> ';
        if (!settings.integrated) {
            html += '<td class="text-right"><button type="button"  onclick="confirm(settings[\'text\'][\'text_confirm\'] ) ? ($(this).tooltip(\'destroy\'), $(\'#setting-value-model-filter-' + index + '\').remove(),setting_value_filter_row--) : false;" data-toggle="tooltip" class="btn btn-danger" title="' + settings.text.button_remove + '"><i class="fa fa-minus-circle"></i></button></td>';
        }
        html += '</tr>';
        var html = $(html);
        $('#setting-model-filter-values tbody').append(html);
        html.find('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="tooltip"]').tooltip('hide');
        setting_value_filter_row = setting_value_filter_row + 1;
    },
    'addValues': function (type, values, length) {
        var values = values || [];
        setting_value_filter_row = 0;
        if (values && Object.keys(values).length) {
            $.each(values, function (index, data) {
                FilterValues.addRow(type, setting_value_filter_row);
            });
        }
        $('[data-toggle="tooltip"]').tooltip('hide');
    },
};

var Children = {
    'addRow': function (type, index) {
        if (!index) {
            var index = setting_value_children_row;
        }
        switch (type) {
            case 'tab':
                var value = 'tabs';
                break;
            case 'juxtapose':
                var value = 'rows';
                break;
        }
        var html = '';
        html += ' <tr data-id="' + index + '" id="setting-values-options-childrens-row-' + index + '">';

        html += '<td class="text-left">';

        $.each(settings['languages'], function (language_id, language) {
            html += '<div class="input-group"><span class="input-group-addon"><img src="language/' + language.code + '/' + language.code + '.png" title="' + language.name + '" /></span><input id="input-setting-values-options-childrens-row-' + index + '-description-' + language.language_id + '-title"  type="text" name="values_options[' + value + '][' + index + '][description][' + language.language_id + '][title]" placeholder="' + settings.text.entry_row_title + '" value="" class="form-control" size="80"/></div>';
        });
        html += '</td>';
        html += '<td class="text-left"><div class="input-group"><input type="text" id="input-setting-values-options-childrens-row-' + index + '-value" name="values_options[' + value + '][' + index + '][key]" value="" size="73" class="form-control" placeholder="' + settings.text.entry_row_key + '"' + (
            (!settings.integrated || settings.values_options.integrate_values == '0') || (settings.values_options.integrate_values == '0' && settings.developer_status) ? '' : ' readonly'
        ) + '/></div></td> ';
        if ((!settings.integrated || settings.values_options.integrate_values == '0') || (settings.values_options.integrate_values == '0' && settings.developer_status)) {
            html += '<td class="text-right"><div class="input-group"><button type="button"  onclick="confirm(settings[\'text\'][\'text_confirm\'] ) ? ($(this).tooltip(\'destroy\'), $(\'#setting-values-options-childrens-row-' + index + '\').remove(),setting_value_children_row--) : false;" data-toggle="tooltip" class="btn btn-danger" title="' + settings.text.button_remove + '"><i class="fa fa-minus-circle"></i></button></td>';
        }
        html += '</tr>';
        var html = $(html);
        setting_value_children_row = setting_value_children_row + 1;

        $('#setting-values-options-childrens tbody').append(html);
        html.find('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="tooltip"]').tooltip('hide');
    },
    'addValues': function (type, values, length) {
        var values = values || [];
        setting_value_children_row = 0;
        if (values && Object.keys(values).length) {
            $.each(values, function (index, data) {
                Children.addRow(type, setting_value_children_row);
            });
        }
        $('[data-toggle="tooltip"]').tooltip('hide');
    },
};

var ValuesTable = {
    'addValues': function (type, values, length) {
        var values = values || [];

        if (!index) {
            var index = setting_value_row;
        }
        if (values && Object.keys(values).length) {
            $.each(values, function (index, data) {
                ValuesTable.addRow(type, setting_value_row);
            });
        }
        $('[data-toggle="tooltip"]').tooltip('hide');
    },
    'addRow': function (type, index) {
        if (!index) {
            var index = setting_value_row;
        }

        var html = '';
        html += ' <tr data-id="' + index + '" id="setting-values-options-values-row-' + index + '">';
        html += '<td class="text-left">';

        $.each(settings['languages'], function (language_id, language) {
            html += '<div class="input-group"><span class="input-group-addon"><img src="language/' + language.code + '/' + language.code + '.png" title="' + language.name + '" /></span><input id="input-setting-values-options-values-row-' + index + '-description-' + language.language_id + '-title"  type="text" name="values_options[values][' + index + '][description][' + language.language_id + '][title]" placeholder="' + settings.text.entry_row_title + '" value="" class="form-control" size="80"/></div>';
        });

        html += '</td>';
        html += '<td class="text-left"><div class="input-group"><input type="text" id="input-setting-values-options-values-row-' + index + '-value" name="values_options[values][' + index + '][value]" value="" size="73" class="form-control" placeholder="' + settings.text.entry_row_value + '"' + (
            settings.integrated ? ' readonly' : ''
        ) + '/></div></td> ';

        if (!settings.integrated) {
            html += '<td class="text-right"><div class="input-group"><button type="button"  onclick="confirm(settings[\'text\'][\'text_confirm\'] ) ? ($(this).tooltip(\'destroy\'), $(\'#setting-values-options-values-row-' + index + '\').remove(),setting_value_row--) : false;" data-toggle="tooltip" class="btn btn-danger" title="' + settings.text.button_remove + '"><i class="fa fa-minus-circle"></i></button></td>';
        }

        html += '</tr>';
        var html = $(html);
        setting_value_row = setting_value_row + 1;

        $('#setting-values-options-values tbody').append(html);
        html.find('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="tooltip"]').tooltip('hide');
    },
};


function valuesBox(type) {
    var html = '';
    $('.setting-select-type-prop').remove();
    setting_value_row = 0;
    setting_value_filter_row = 0;
    setting_block_value_row = 0;
    setting_block_accordion_row = 0;
    switch (type) {
        case 'text':
        case 'password':
        case 'iconpicker':
            //
            html += fields.placeholder(type);
            html += fields.inline(type);
            html += fields.regex(type);
            html += fields.custom_validate(type);
            html += fields.mask(type);
            break;
        case 'colorpicker':
            //
            html += fields.color_format(type);
            html += fields.inline(type);
            html += fields.regex(type);
            html += fields.custom_validate(type);
            html += fields.mask(type);
            break;
        case 'html':
            //

            html += fields.html(type);
            html += fields.block_width(type);
            break;
        case 'alert':
            //
            html += fields.style(type);
            html += fields.text(type);
            break;
        case 'block':
            //
            html += fields.block_values(type);
            html += fields.pre_rows(type);
            html += fields.rows_limit(type);
            html += fields.block_width(type);
            html += fields.custom_validate(type);
            break;
        case 'number':
            //
            html += fields.min(type);
            html += fields.max(type);
            html += fields.placeholder(type);
            html += fields.inline(type);
            html += fields.regex(type, '/^[0-9]*$/');
            html += fields.custom_validate(type);
            html += fields.mask(type);
            break;
        case 'text_language':
            //
            html += fields.shown_tab(type);
            html += fields.placeholder(type);
            html += fields.inline(type);
            html += fields.regex(type);
            html += fields.custom_validate(type);
            html += fields.mask(type);
            break;
        case 'datetimepicker':
            //
            html += fields.date_format(type);
            html += fields.pick(type);
            html += fields.placeholder(type);
            html += fields.inline(type);
            html += fields.regex(type);
            html += fields.custom_validate(type);
            break;
        case 'geocode':
            //
            html += fields.map_type(type);
            html += fields.height(type);
            break;
        case 'textarea':
            //
           html += fields.rows(type);
            html += fields.placeholder(type);
            html += fields.inline(type);
            html += fields.regex(type);
            html += fields.custom_validate(type);
            html += fields.mask(type);

            break;
        case 'image':
            //
            html += fields.placeholder_size(type);
            html += fields.regex(type);
            html += fields.custom_validate(type);
            break;

        case 'image_language':
            //
            html += fields.placeholder_size(type);
            html += fields.shown_tab(type);
            html += fields.regex(type);
            html += fields.custom_validate(type);
            break;
        case 'texteditor_language':
        case 'texteditor':
            //
            html += fields.regex(type);;
            html += fields.custom_validate(type);
            break;
        case 'textarea_language':
            //
            html += fields.rows(type);
            html += fields.shown_tab(type);
            html += fields.placeholder(type);
            html += fields.inline(type);
            html += fields.regex(type);
            html += fields.custom_validate(type);
            html += fields.mask(type);
            break;
        case 'codemirror':
            //
            html += fields.code(type);
            html += fields.regex(type);
            html += fields.custom_validate(type);
            break;
        case 'select':
            //
            html += fields.model_route(type, 1);
            html += fields.values_route(type);
            html += fields.filter_values(type);
            html += fields.collation(type);
            html += fields.option_values(type);
            html += fields.empty(type);
            html += fields.inline(type);
            html += fields.custom_validate(type);
            break;
        case 'radio':
        case 'old_radio':
        case 'checkbox':
        case 'array':
            //
            html += fields.model_route(type, 1);
            html += fields.values_route(type);
            html += fields.filter_values(type);
            html += fields.collation(type);
            html += fields.option_values(type);
            html += fields.custom_validate(type);
            break;
        case 'autocomplete':
            //
            html += fields.model_route(type, 0);
            html += fields.values_route(type);
            html += fields.filter_values(type);
            html += fields.collation(type);
            html += fields.empty(type);
            html += fields.placeholder(type);
            html += fields.inline(type);
            html += fields.custom_validate(type);
            break;
        case 'multiple_autocomplete':
            //
            html += fields.model_route(type, 0);
            html += fields.values_route(type);
            html += fields.filter_values(type);
            html += fields.collation(type);
            html += fields.placeholder(type);
            html += fields.inline(type);
            html += fields.custom_validate(type);
            break;
        case 'juxtapose':
            //
            html += fields.select_values_route(type, 1);
            html += fields.childrens(type);
            html += fields.children_settings(type);
            html += fields.block_width(type);
            html += fields.integrate_values(type);
            html += fields.custom_validate(type);
            break;
        case 'tab':
            //
            html += fields.select_values_route(type, 1);
            html += fields.childrens(type);
            html += fields.children_settings(type);
            html += fields.vertical_tab(type);
            html += fields.block_width(type);
            html += fields.integrate_values(type);
            html += fields.custom_validate(type);
            break;
        case 'add_tab':
            //
            html += fields.children_settings(type);
            html += fields.model_route(type, 1);
            html += fields.values_route(type);
            html += fields.filter_values(type);
            html += fields.collation(type);
            html += fields.vertical_tab(type);
            html += fields.block_width(type);

            html += fields.custom_validate(type);
            break;
        case 'select_route':
            //
            html += fields.select_values_route(type, 1);
            html += fields.empty(type);
            html += fields.inline(type);
            html += fields.block_width(type);

            html += fields.custom_validate(type);
            break;

        case 'range':
            //
            html += fields.min(type, true);
            html += fields.max(type, true);
            html += fields.range_type(type);
            html += fields.range_grid(type);
            html += fields.step(type);
            html += fields.block_width(type);
            html += fields.custom_validate(type);
            break;
        case 'controller':
            //
            html += fields.controller(type);
            html += fields.filter_values(type, 1);
            html += fields.placeholder(type);
            html += fields.inline(type);
            html += fields.block_width(type);
            html += fields.mask(type);
            html += fields.custom_validate(type);
            break;
        case 'accordion':
            //
            html += fields.accordion(type);
            html += fields.block_width(type);
            break;
    }
    var html = $(html);

    html.find('[data-toggle="tooltip"]').tooltip();

    $('#setting-select-type').after(html);

    $.each($('.nav-tabs'), function (name, value) {
        $(this).find("a[data-toggle=tab]:first").tab("show");
    });

    html.find('.texteditor').summernote({ height: 150 });
    $(html).find('[name="values_options[values_route]"]').trigger('change');
}


$(document).ready(function () {
    valuesBox(settings.type);
    $('#input-type').trigger('change');
    $('[name="values_route"]').val(settings.values_route);

    if (settings.values_options) {
        Setting.addValues(settings.type, settings.values_options.settings);
        Accordion.addValues(settings.type, settings.values_options.accordion);
        Children.addValues(settings.type, settings.values_options.tabs);
        Children.addValues(settings.type, settings.values_options.rows);
        SettingValues.addSettings(settings.type, settings.values_options.settings);

        ValuesTable.addValues(settings.type, settings.values_options.values);
        FilterValues.addValues(settings.type, settings.values_options.model_filter);
        $.each(convert_object_keys(settings.values_options), function (name, value) {
            $('[name="values_options' + name + '"]:not([type="radio"])').val(value).trigger('change');
            if ($('[data-toggle="buttons"] [name="values_options' + name + '"]').length) {
                $('[data-toggle="buttons"] [name="values_options' + name + '"][value="' + value + '"]').click();
            }
        });

    }
    $('[name="regex"]').val(settings.regex);
    $.each($('.texteditor'), function (name, value) {
        $(this).summernote('code', $(this).val());
    });

    for (i in settings.errors) {
        error(i, settings.errors);
    }
    $.each($('.nav-tabs'), function (name, value) {
        $(this).find("a[data-toggle=tab]:first").tab("show");
    });
    if (settings.integrated && typeof (settings.values_options.model_filter) == 'undefined') {
        $('#setting-model-filter-values').closest('.form-group').hide();
    }
    if ($('[name="values_options[values_route]"]').find('option:selected:not([value="0"])').length == 0) { // $('[name="values_options[values_route]"]').removeAttr('readonly');
    }

});