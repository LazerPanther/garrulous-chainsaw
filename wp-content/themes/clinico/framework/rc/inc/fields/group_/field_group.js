/* global redux_change */
(function($){
    "use strict";

    $.redux = $.redux || {};

    $(document).ready(function () {
        //Group functionality
        $.redux.group();
    });
    
    $.redux.group = function(){

        $(".redux-groups-accordion")
        .accordion({
            header: "> div > h3",
            collapsible: true,
            animate: {
                duration: 200
            },
            active: false,
            heightStyle: "content",
            icons: {
                "header": "ui-icon-plus",
                "activeHeader": "ui-icon-minus"
            }
        })
        .sortable({
            axis: "y",
            handle: "h3",
            stop: function (event, ui) {
                // IE doesn't register the blur when sorting
                // so trigger focusout handlers to remove .ui-state-focus
                ui.item.children("h3").triggerHandler("focusout");
                fix_group_input_names(ui.item.closest(".redux-groups-accordion"));
            }
        });

        // find errors on this group
        if(redux.errors !== undefined){
            for(var err in redux.errors.errors){
                for(var deep_err in redux.errors.errors[err].errors){
                    var err_field_id = redux.args.opt_name + "-" + redux.errors.errors[err].errors[deep_err].id;
                    console.log( err_field_id);
                    console.log($("#" + err_field_id).parents(".redux-groups-accordion").size());
                    if($("#" + err_field_id).parents(".redux-groups-accordion").size() > 0){
                        $("#" + err_field_id).closest(".redux-groups-accordion-group").find("h3").first().addClass("group-state-error");
                    }
                }
            }
        }

        $('.redux-groups-accordion-group input[data-title="true"]').on('keyup',function(event) {
            $(this).closest('.redux-groups-accordion-group').find('.redux-groups-header').text(event.target.value);
            $(this).closest('.redux-groups-accordion-group').find('.slide-title').val(event.target.value);
        });

        $('.redux-groups-remove').live('click', function () {
            var slideCounter = $(this).closest('.redux-groups-accordion').find('.redux-group-field-count');
            // Count # of slides
            var slideCount = slideCounter.val();
            // Update the slideCounter
            slideCounter.val(parseInt(slideCount)-1 );
            var parent_el = $(this).closest('.redux-groups-accordion');
            $(this).closest('.redux-groups-accordion-group').remove();
            fix_group_input_names(parent_el);
            redux_change($(this));
        });

        $('.redux-groups-clear').on('click', function () {
            console.log("clicked");
            var slideCounter = $(this).closest('.redux-container-group').find('.redux-group-field-count');
            // Count # of slides
            var slideCount = slideCounter.val();
            // Update the slideCounter
            slideCounter.val(0);
            $(this).closest('.redux-container-group').find(".redux-groups-accordion-group").remove();
        });

        $('.redux-groups-add').click(function () {
            var addbutton = $(this);
            var group_container = addbutton.closest('.redux-container-group').find(".redux-groups-accordion");
            var newSlide = addbutton.closest('.redux-container-group').find('.redux-groups-dummy-group').clone(true, true).show();
            var slideCounter = addbutton.closest('.redux-container-group').find('.redux-group-field-count');
            // Count # of slides
            var slideCount = slideCounter.val();
            // Update the slideCounter
            var new_group_index = find_available_id_for_new_group(group_container);
            slideCounter.val(parseInt(slideCount)+1 );
            // Append Clone
            addbutton.closest('.redux-container-group').find(".redux-groups-accordion").append(newSlide);

            // Remove dummy classes from newSlide
            $(newSlide).removeClass("redux-groups-dummy-group").addClass("redux-groups-accordion-group").removeAttr("style").attr("data-group-index",new_group_index);

            // fix dynamic attributes
            $(newSlide).find('*').each(function(){
                // check and fix strings inside these attributes
                var check_attr = ["name","id","data-id","data-check-id","data-check-field","rel","class","for","data-group-id","data-presets","data-editor"];
                // buffer the object
                var $this = $(this);
                // loop through attributes
                for(var attr_index = 0; attr_index < check_attr.length ; attr_index++){
                    var attr_obj = $this.attr(check_attr[attr_index]);
                    // if attribute exists, replace them.
                    if (typeof attr_obj !== 'undefined' && attr_obj !== false) {
                        if(check_attr[attr_index] === "rel") $this.data("old-rel",attr_obj);
                        if(check_attr[attr_index] === "id") $this.attr("data-old-id",attr_obj);
                        $this.attr(check_attr[attr_index], attr_obj.replace(/dummy-field-id/g, new_group_index) );
                    }
                }
            });

            {   // field reinitializers

                // fix sliders
                $(newSlide).find("div.redux-slider-container").each(function(index,element){
                    $.redux.field_slider(element);
                });

                // fix background fields
                $(newSlide).find(".redux-container-background").each(function(index,element){
                    $.reduxBackground.init(element);
                });

                // fix color rgba inputs
                $(newSlide).find(".redux-color_rgba-init").each(function(index,element){
                    $.redux.field_color_rgba(element);
                });

                // fix border inputs
                $(newSlide).find(".redux-container-border").each(function(index,element){
                    $.redux.field_border(element);
                });

                // fix color inputs
                $(newSlide).find(".redux-color-init").each(function(index,element){
                    $.redux.field_color(element);
                });

                // fix image_select
                $(newSlide).find(".redux-container-image_select").each(function(index,element){
                    $(this).find('.redux-image-select-selected').find("input[type='radio']").attr("checked", true);
                });

                // fix spinners (needs new redux.spinner data)
                $(newSlide).find(".redux_spinner").each(function(index,element){
                    var spinner_options = JSON.parse(JSON.stringify(redux.spinner[$(element).data("old-rel")]));
                    spinner_options.id = $(element).attr("rel");
                    redux.spinner[spinner_options.id] = spinner_options;
                    $.redux.field_spinner(element);
                });

                // fix sorters (needs new redux.sorter data)
                $(newSlide).find(".redux-sorter").each(function(index,element){
                    var dummy_sorter_id = $(element).data("old-id");
                    var sorter = redux.sorter[element.id] = redux.sorter[dummy_sorter_id];
                    $.redux.field_sorter(element);
                });

                // fix select2
                $(newSlide).find(".redux-select-item").each(function(index,element){
                    $.redux.field_select(element);
                });

                // fix ace editors
                $(newSlide).find(".ace-editor").each(function (index,element) {
                    $.redux.field_ace_editor(element);
                });

                // fix tinyMCE
                $(newSlide).find(".wp-editor-area").each(function(index,element){
                    $.redux.field_editor(element);
                });

                // fix sortables
                $(newSlide).find(".redux-sortable").each(function(index,element){
                    $.redux.field_sortable(element);
                });

                // fix datetime
                $(newSlide).find(".redux-datepicker").each(function(index,element){
                    $.redux.field_date(element);
                });

                // fix button set
                $(newSlide).find(".buttonset").each(function(index,element){
                    $.redux.field_button_set(element);
                });

                // fix dimensions
                $(newSlide).find(".redux-container-dimensions").each(function(index,element){
                    $.redux.field_dimensions(element);
                });

                // fix typography fields
                $(newSlide).find(".redux-typography-container").each(function(index,element){
                    $.redux.field_typography(element);
                });

                $(newSlide).find(".redux_codemirror").each(function(index,element){
                    var dummy_codemirror_id = $(element).data("old-id");
                    var codemirror = redux.codemirror[element.id] = redux.codemirror[dummy_codemirror_id];
                    codemirror.id = element.id;
                    $.redux.field_codemirror(element);
                });

                fix_group_input_names($(newSlide).closest(".redux-groups-accordion"));
            }
        });

        function fix_group_input_names(group_container){
            var dummy_field_names = [];
            // buffer radio button and checkbox values because they lose their states on DOM change
            $(group_container).find("input:checked").attr("data-checked",true);
            // buffer dummy field indexes into a temp array
            $(group_container).find('.redux-groups-dummy-group').each(function(groupindex){
                $(this).find('*[name]').each(function(fieldindex,field){
                    dummy_field_names.push($(field).attr("name"));
                });
            });
            // change every name attribute with it's new index
            $(group_container).find('.redux-groups-accordion-group').each(function(groupindex){
                $(this).find('*[name]').each(function(fieldindex,field){
                    $(field).attr("name",dummy_field_names[fieldindex].replace("[dummy-field-id]","[" + groupindex + "]"));
                });
            });
            //Restore radio button values
            $(group_container).find("input[data-checked]").attr("checked","checked").removeAttr("data-checked");
        }

        function find_available_id_for_new_group(group_container){
            var list = [];
            group_container.find(".redux-groups-accordion-group").each(function(index,elem){
                list.push($(elem).attr("data-group-index"));
            });
            if(list.length === 0){
                return 0;
            }else{
                return Math.max.apply(Math, list) + 1;
            }
        }
    };

    $("span.group_field_edit_icon").on("click",function(e){
        e.stopPropagation();
        e.preventDefault();
        var answer = prompt ("Enter the new title:",$(this).parent().find("span.group_field_name_span").text());
        if(answer !== null){
            $(this).parent().find("span.group_field_name_span").text(answer);
            $(this).parent().find("input.group_field_name_input").val(answer);
        }
    });

})(jQuery);
