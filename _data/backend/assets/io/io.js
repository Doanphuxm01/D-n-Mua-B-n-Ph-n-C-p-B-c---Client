/**
 * Created by sakura on 20/03/2016.
 */

function _convertToAlias(Text) {
    return Text
        .toLowerCase()
        .replace(/[^\w ]+/g, '')
        .replace(/ +/g, '-')
        ;
}
function _AUTOCOMPLETE_INIT(inputElement, sourceRemote) {
    $(inputElement).autocomplete({
            minLength: 3,
            source: sourceRemote,
            focus: function (event, ui) {
                $(inputElement).val(ui.item.label);
                return false;
            },
            select: function (event, ui) {
                $(inputElement).val(ui.item.label);
                return false;
            }
        })
        .autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>").append("<a>" + item.label + "</a>").appendTo(ul);

    };
}
/***
 * @public
 * @name: _SHOW_FORM_REMOTE
 * @note: Hiển thị form modal popup lấy template từ server về
 */
var _EDITOR = false;
function _EDITOR_INIT(element) {
    tinymce.init({
        selector: element,
        //toolbar1: 'addMediaAdvance | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image  preview media forecolor backcolor code fullscreen',
         toolbar1: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image  preview media forecolor backcolor code ',
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
            editor.addButton('addMediaAdvance', {
                text: 'Thêm media',
                icon: false,
                onclick: function () {
                    return MNG_MEDIA.openUploadForm('insertImageToEditor', '');
                    //editor.insertContent('&nbsp;<b>It\'s my button!</b>&nbsp;');
                }
            });
            _EDITOR = editor;
        },
        language: "vi_VN",
        entity_encoding: "raw",
        menubar: false,//không hiển thị menu bar,
        fix_list_elements: true,
        force_p_newlines: true,
        allow_conditional_comments: false,//Không chấp nhận comment html
        height: 300,
        plugins: [
            'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'template paste textcolor colorpicker textpattern imagetools '
        ],
        image_caption: true,
        image_advtab: true,
        image_description: true,
        image_title: true
    });
}

function _GET_SCRIPT(link) {
    var resource = document.createElement('script');
    resource.src = link;
    var script = document.getElementsByTagName('script')[0];
    script.parentNode.insertBefore(resource, script);
}
function _SHOW_FORM_REMOTE(remote_link, target, multiform) {
    if (target === undefined || target == '') {
        target = 'myModal';
    }
    if (multiform != undefined) {
        target = target + remote_link.replace(/[^\w\s]/gi, '');
    } else {
        jQuery('.modal-backdrop').remove();
    }
    jQuery('#' + target).remove();
    jQuery('body').append('<div class="modal fade" id="' + target + '" tabindex="-1" role="dialog" ' +
        'aria-labelledby="' + target + 'Label" aria-hidden="true">' +
        '<div class="mmbd"></div></div>');
    var modal = jQuery('#' + target), modalBody = jQuery('#' + target + ' .mmbd');
    modal.on('show.bs.modal', function () {
        modalBody.load(remote_link);
    }).modal({backdrop: 'static'});
    return false;
}

var STATUS_JSON_DONE = 1;
var STATUS_JSON_RELOGIN = -2;
var ALL_POST_RESULT = [];
/***
 *
 * @param url
 * @param data
 * @param callback
 * @param cache
 * @param type
 * @returns {*}
 * @public
 */
function _POST(url, data, callback, cache, type) {
    if (cache != undefined) {
        if (ALL_POST_RESULT[cache] != undefined) {
            return callback(ALL_POST_RESULT[cache])
        }
    }
    var _token = jQuery('meta[name=_token]').attr("content");
    if (_token) {
        var _data = {'name': '_token', 'value': _token};
        data.push(_data);
    }
    if (type == undefined) {
        type = 'json';
    }
    jQuery.ajax({
        url: url,
        type: "POST",
        data: data,
        dataType: type,
        success: function (data) {
            if (cache != undefined && cache != null) {
                ALL_POST_RESULT[cache] = data;
            }
            return eval(callback(data));
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError);
        }
    });
    return true;
}
var App = {
    DOMAIN: document.location.origin,
    API: document.location.origin + '/api'
};
var MNG_SEO = {
    settings: {
        URL_ACTION: App.DOMAIN + '/admin/seo/'
    },
    MNG_LANDING_PAGE: {
        save: function (formElementId, script) {
            var formdata = jQuery(formElementId).serializeArray();
            var callBack = function (json) {
                if (json.status != 1) {
                    alert(json.msg);
                    if (json.key != undefined) {
                        jQuery(json.key).focus();
                    }
                } else {
                    if (json.data != undefined) {
                        if (script == undefined && json.data.link_edit != undefined) {
                            window.location.href = json.data.link_edit;
                        } else {
                            alert(json.msg);
                            window.location.href = json.data.link_add;
                        }
                    }
                }
            };
            _POST(MNG_SEO.settings.URL_ACTION + 'landing-page/save', formdata, callBack);
            return false;
        },
        InputForm_show: function (id, page) {
            if (id === undefined) {
                id = 0;
            }
            var linkremoteform = MNG_SEO.settings.URL_ACTION + 'landing-page/' + page + '?id=' + id;
            return _SHOW_FORM_REMOTE(linkremoteform);
        },
        InputForm_save: function (formElementId) {
            //Không dùng nữa
            var formdata = jQuery('#' + formElementId).serializeArray();
            var txtLink = jQuery('.txtLink').val();
            var callBack = function (json) {
                if (json.status == 0) {
                    if (json.msg) {
                        jQuery.each(json.msg, function (key, value) {
                            show_notify('Lỗi', 'warning', 'icon-warning2', value);
                        });
                    }
                } else {
                    show_notify(json.msg, 'success', 'icon-checkmark3', '');
                    if (json.id) {
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    } else {
                        if (!confirm('Ban có muốn tiếp tục thực hiện thao tác?')) {
                            window.location.reload();
                        } else {
                            jQuery('#' + formElementId)[0].reset();
                        }
                    }
                }
            };
            if (txtLink !== undefined && !isUrlValid(txtLink)) {
                if (!confirm('Link của bạn không đúng định dạng bạn vẫn muốn tiếp tục?')) {
                    return false;
                }
            }
            return _POST(MNG_SEO.settings.URL_ACTION + 'landing-page/save', formdata, callBack);
        },
        deleteHandle: function (formElementId) {
            var formdata = jQuery('#' + formElementId).serializeArray();
            var callBack = function (json) {
                if (json.status == 1) {
                    alert(json.msg);
                    window.location.reload();
                } else {
                    alert(json.msg);
                }
            };
            return _POST(MNG_SEO.settings.URL_ACTION + 'landing-page/deleteHandle', formdata, callBack);
        },
        multiRemove: function (flag) {
            var ids = getCheckBoxVal('landingPageTbl', 'landingPageCbx');
            if (ids == '') {
                alert('Bạn chưa chọn landing page nào!');
                return false;
            }
            var msg = "Bạn có chắc chắn muốn xóa";
            if (flag) {
                msg += " hoàn toàn (dữ liệu sẽ không thể khôi phục)"
            }
            if (!confirm(msg + ' ?')) {
                return false;
            }
            var data = [{'name': 'ids', 'value': ids}, {'name': 'flag', 'value': flag}];
            var callBack = function (json) {
                if (json.status == 1) {
                    alert(json.msg);
                    window.location.reload();
                } else {
                    alert(json.msg);
                }
            };
            return _POST(MNG_SEO.settings.URL_ACTION + 'landing-page/multiRemove', data, callBack);
        }
    },
    MNG_DOMAIN: {
        InputForm_show: function (id, page) {
            if (id === undefined) {
                id = 0;
            }
            var linkremoteform = MNG_SEO.settings.URL_ACTION + 'domain/' + page + '?id=' + id;
            return _SHOW_FORM_REMOTE(linkremoteform);
        },
        InputForm_save: function (formElementId) {
            var formdata = jQuery('#' + formElementId).serializeArray();
            var txtLink = jQuery('.txtLink').val();
            var callBack = function (json) {
                if (json.status == 0) {
                    if (json.msg) {
                        var first = true;
                        jQuery.each(json.msg, function (key, value) {
                            show_notify('Lỗi', 'warning', 'icon-warning2', value);
                            if (first) {
                                validateInput(key, true);
                            } else {
                                validateInput(key, false);
                            }
                        });
                    }
                } else {
                    if (json.msg) {
                        show_notify(json.msg, 'success', 'icon-checkmark3', '');
                        if (json.id) {
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        } else {
                            if (!confirm('Ban có muốn tiếp tục thực hiện thao tác?')) {
                                window.location.reload();
                            } else {
                                jQuery('#' + formElementId)[0].reset();
                            }
                        }
                    }
                }
            };
            if (txtLink !== undefined && !isUrlValid(txtLink) && txtLink != '') {
                if (!confirm('Link của bạn không đúng định dạng bạn vẫn muốn tiếp tục?')) {
                    return false;
                }
            }
            return _POST(MNG_SEO.settings.URL_ACTION + 'domain/save', formdata, callBack);
        },
        deleteHandle: function (formElementId) {
            var formdata = jQuery('#' + formElementId).serializeArray();
            var callBack = function (json) {
                if (json.status == 1) {
                    alert(json.msg);
                    window.location.reload();
                } else {
                    alert(json.msg);
                }
            };
            return _POST(MNG_SEO.settings.URL_ACTION + 'domain/deleteHandle', formdata, callBack);
        },
        //deleteConfirm : function (table) {
        //    var ids = getCheckBoxVal(table, 'domainCbx');
        //    if (ids == '') {
        //        alert('Bạn chưa chọn domain nào!');
        //        return false;
        //    }
        //    var linkremoteform = MNG_SEO.settings.URL_ACTION + 'domain/deleteConfirm';
        //    return _SHOW_FORM_REMOTE(linkremoteform);
        //},
        multiRemove: function (flag) {
            var ids = getCheckBoxVal('domainTbl', 'domainCbx');
            if (ids == '') {
                alert('Bạn chưa chọn domain nào!');
                return false;
            }
            var msg = "Bạn có chắc chắn muốn xóa";
            if (flag) {
                msg += " hoàn toàn (dữ liệu sẽ không thể khôi phục)"
            }
            if (!confirm(msg + ' ?')) {
                return false;
            }
            var data = [{'name': 'ids', 'value': ids}, {'name': 'flag', 'value': flag}];
            var callBack = function (json) {
                if (json.status == 1) {
                    alert(json.msg);
                    window.location.reload();
                } else {
                    alert(json.msg);
                }
            };
            return _POST(MNG_SEO.settings.URL_ACTION + 'domain/multiRemove', data, callBack);
        }
    },

    MNG_BACKLINK: {
        InputForm_show: function (id) {
            if (id === undefined) {
                id = 0;
            }
            var linkremoteform = MNG_SEO.settings.URL_ACTION + 'backlink/input?id=' + id;
            return _SHOW_FORM_REMOTE(linkremoteform);
        },
        DeleteForm_show: function (id) {
            if (id !== undefined && id > 0) {
                var linkremoteform = MNG_SEO.settings.URL_ACTION + 'backlink/show?id=' + id;
                return _SHOW_FORM_REMOTE(linkremoteform);
            }
        },
        InputForm_save: function (formElementId) {
            jQuery('#btn-submit').attr('disabled', true);
            var formdata = jQuery(formElementId).serializeArray();
            var callBack = function (json) {
                jQuery('#btn-submit').attr('disabled', false);
                if (json.status == 1) {
                    jQuery.each(json.msg, function (k, v) {
                        show_notify('Lỗi', 'warning', 'icon-warning2', v);
                    });
                } else {
                    show_notify('Đã lưu', 'success', 'icon-checkmark3', '');
                    if (confirm(json.msg)) {
                        window.location.reload();
                    }
                    if (json.data.id == 0) {
                        jQuery(formElementId)[0].reset();
                    }
                }
            };
            return _POST(MNG_SEO.settings.URL_ACTION + 'backlink/save', formdata, callBack);
        },
        syncToRemote: function (postId) {

            var callBack = function (json) {
                alert(json.msg);
                if(json.status==STATUS_JSON_DONE){
                    window.location.reload();
                }
            };
            return _POST(MNG_SEO.settings.URL_ACTION + 'backlink/post_remote?id='+postId, [], callBack);
        },
        DeleteForm_save: function (formElementId) {
            jQuery('#btn-submit').attr('disabled', true);
            var formdata = jQuery(formElementId).serializeArray();
            var callBack = function (json) {
                jQuery('#btn-submit').attr('disabled', false);
                if (json.status == 1) {
                    jQuery.each(json.msg, function (k, v) {
                        show_notify('Lỗi', 'warning', 'icon-warning2', v);
                    });
                } else {
                    show_notify('Đã xóa', 'success', 'icon-checkmark3', '');
                    window.location.reload();
                }
            };
            return _POST(MNG_SEO.settings.URL_ACTION + 'backlink/delete', formdata, callBack);
        },
        multiRemove: function (flag) {
            var ids = getCheckBoxVal('backLinkTbl', 'backLinkCbx');
            if (ids == '') {
                alert('Bạn chưa chọn back link nào!');
                return false;
            }
            var msg = "Bạn có chắc chắn muốn xóa";
            if (flag) {
                msg += " hoàn toàn (dữ liệu sẽ không thể khôi phục)"
            }
            if (!confirm(msg + ' ?')) {
                return false;
            }
            var data = [{'name': 'ids', 'value': ids}, {'name': 'flag', 'value': flag}];
            var callBack = function (json) {
                if (json.status == 1) {
                    alert(json.msg);
                    window.location.reload();
                } else {
                    alert(json.msg);
                }
            };
            return _POST(MNG_SEO.settings.URL_ACTION + 'backlink/multiRemove', data, callBack);
        }
    },
    POST_BACKLINK: {
        save: function (formElementId, script) {
            var $btn = jQuery('.js-post-btn');
            $btn.hide();
            var formdata = jQuery(formElementId).serializeArray();
            var callBack = function (json) {
                if (json.status != 1) {
                    $btn.show();
                    alert(json.msg);
                    if (json.key != undefined) {
                        jQuery(json.key).focus();
                    }
                } else {
                    if (json.data != undefined) {
                        if (script == undefined && json.data.link_edit != undefined) {
                            window.location.href = json.data.link_edit;
                        } else {
                            alert(json.msg);
                            window.location.href = json.data.link_add;
                        }
                    }
                }
            };
            _POST(MNG_SEO.settings.URL_ACTION + 'backlink/post-input-save', formdata, callBack);
            return false;
        },
    },

    MNG_KEYWORD: {
        InputForm_show: function (id) {
            if (id === undefined) {
                id = 0;
            }
            var linkremoteform = MNG_SEO.settings.URL_ACTION + 'keyword/input?id=' + id;
            return _SHOW_FORM_REMOTE(linkremoteform);
        },
        DeleteForm_show: function (id) {
            if (id !== undefined && id > 0) {
                var linkremoteform = MNG_SEO.settings.URL_ACTION + 'keyword/show?id=' + id;
                return _SHOW_FORM_REMOTE(linkremoteform);
            }
        },
        InputForm_save: function (formElementId) {
            jQuery('#btn-submit').attr('disabled', true);
            var formdata = jQuery(formElementId).serializeArray();
            var callBack = function (json) {
                jQuery('#btn-submit').attr('disabled', false);
                if (json.status == 1) {
                    jQuery.each(json.msg, function (k, v) {
                        show_notify('Lỗi', 'warning', 'icon-warning2', v);
                    });
                } else {
                    show_notify('Đã lưu', 'success', 'icon-checkmark3', '');
                    if (confirm(json.msg)) {
                        window.location.reload();
                    }
                    if (json.data.id == 0) {
                        jQuery(formElementId)[0].reset();
                    }
                }
            };
            return _POST(MNG_SEO.settings.URL_ACTION + 'keyword/save', formdata, callBack);
        },
        DeleteForm_save: function (formElementId) {
            jQuery('#btn-submit').attr('disabled', true);
            var formdata = jQuery(formElementId).serializeArray();
            var callBack = function (json) {
                jQuery('#btn-submit').attr('disabled', false);
                if (json.status == 1) {
                    jQuery.each(json.msg, function (k, v) {
                        show_notify('Lỗi', 'warning', 'icon-warning2', v);
                    });
                } else {
                    show_notify('Xóa thành công', 'success', 'icon-checkmark3', '');
                    window.location.reload();
                }
            };
            return _POST(MNG_SEO.settings.URL_ACTION + 'keyword/delete', formdata, callBack);
        },
        multiRemove: function (flag) {
            var ids = getCheckBoxVal('keywordTbl', 'keywordCbx');
            if (ids == '') {
                alert('Bạn chưa chọn từ khóa nào!');
                return false;
            }
            var msg = "Bạn có chắc chắn muốn xóa";
            if (flag) {
                msg += " hoàn toàn (dữ liệu sẽ không thể khôi phục)"
            }
            if (!confirm(msg + ' ?')) {
                return false;
            }
            var data = [{'name': 'ids', 'value': ids}, {'name': 'flag', 'value': flag}];
            var callBack = function (json) {
                if (json.status == 1) {
                    alert(json.msg);
                    window.location.reload();
                } else {
                    alert(json.msg);
                }
            };
            return _POST(MNG_SEO.settings.URL_ACTION + 'keyword/multiRemove', data, callBack);
        }
    }

};

/***
 * @public
 * @name: show_notify
 * @note: Hiển thị notify từ thư viện pnotify.min.js
 */
function show_notify($title, $type, $icon, $text) {
    new PNotify({
        title: $title,
        text: $text,
        icon: $icon,
        type: $type
    });
}

/***
 * @public
 * @name: isUrlValid
 * @note: validate url link
 */
function isUrlValid(url) {
    return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
}

/***
 * @param table
 * @name: checkAll
 * @note: checked check box in table
 */
function checkAll(table) {
    jQuery("#checkAll").change(function () {
        jQuery("#" + table + " input:checkbox").prop('checked', $(this).prop("checked"));
        $(".styled.js-bl-ck").uniform({
            radioClass: 'choice'
        });
    });
}

/***
 * @param table
 * @param cbName
 * @name: getCheckBoxVal
 * @note: get checked value of checkbox in table
 */
function getCheckBoxVal(table, cbName) {
    var ids = '';
    var first = true;
    jQuery("#" + table + " tbody input[name=" + cbName + "]:checked").each(function () {
        if (first) {
            ids += jQuery(this).val();
            first = false;
        } else {
            ids += ',' + jQuery(this).val();
        }
    });
    return ids;
}

/***
 * @param name
 * @name: triggerInput
 * @note: trigger click function of id
 */
function triggerInput(name) {
    jQuery('#' + name).trigger('click');
}

/***
 * @param obj
 * @name: validate_link
 * @note: validate link format
 */
function validate_link(obj) {
    var val = obj.val();
    var parent = obj.parent();
    var html = '';
    if (val === undefined || val == '') {
        return false;
    }
    parent.removeClass('has-warning has-success has-error');
    parent.find('.form-control-feedback').remove();
    if (!isUrlValid(val)) {
        show_notify('Lưu ý', 'warning', 'icon-warning2', 'Link của bạn chưa đúng định dạng');
        parent.addClass('has-warning ');
        html = '<div class="form-control-feedback right10"><i class="icon-notification2"></i></div>';
    } else {
        parent.addClass('has-success');
        html = '<div class="form-control-feedback right10"><i class="icon-checkmark-circle"></i></div>';
    }
    obj.after(html);
}

/***
 * @param inputName
 * @param focus
 * @name: validateInput
 * @note: validate input
 */
function validateInput(inputName, focus) {
    var input = jQuery('input[name=' + inputName + ']');
    if (input === undefined) {
        return false;
    }
    var parent = input.parent(); //div parent
    parent.removeClass('has-warning has-success has-error').addClass('has-error');
    parent.find('.form-control-feedback').remove();
    input.after('<div class="form-control-feedback right10"><i class="icon-cancel-circle2"></i></div>');
    if (focus) {
        input.focus();
    }
}

/***
 * @param form
 * @name: validateInput
 * @note: validate input
 */
function removeError(form) {
    jQuery('#' + form + ' input').change(function () {
        jQuery(this).parent().removeClass('has-warning has-success has-error').find('.form-control-feedback').remove();
    });
}
jQuery.fn.selectText = function () {
    var doc = document;
    var element = this[0];
    console.log(this, element);
    if (doc.body.createTextRange) {
        var range = document.body.createTextRange();
        range.moveToElementText(element);
        range.select();
    } else if (window.getSelection) {
        var selection = window.getSelection();
        var range = document.createRange();
        range.selectNodeContents(element);
        selection.removeAllRanges();
        selection.addRange(range);
    }
};


var MNG_CATE = {
    URL_ACTION: '/admin/cate/',
    save: function (formElementId, addnew) {
        var $form = jQuery(formElementId);
        //$form.find("button[type='submit']").attr('disabled', true);
        var formdata = $form.serializeArray();
        var callBack = function (json) {
            // $form.find("button[type='submit']").attr('disabled', true);
            if (json.status != 1) {
                alert(json.msg);
                if (json.key != undefined) {
                    jQuery(json.key).focus();
                }
            } else {
                if (json.data != undefined) {
                    if (addnew == undefined && json.data.link != undefined) {
                        window.location.href = json.data.link;
                    } else {
                        alert(json.msg);
                        window.location.href = MNG_CATE.URL_ACTION + 'input';
                    }
                }
            }
        };
        _POST(this.URL_ACTION + '_save', formdata, callBack);
        return false;
    },
    getCateSelectOptionByObject: function () {
        var objectId = jQuery('#obj-object').val();
        var $cateElement = jQuery('#obj-parent');
        var callBack = function (json) {
            if (json.data.html != undefined) {
                $cateElement.html(json.data.html).select2();
            }
            $cateElement.attr('disabled', false);
        };
        _POST(this.URL_ACTION + '_getCateSelectOptionByObject?object=' + objectId, [], callBack);
        return false;
    }
};

var MNG_MENU = {
    URL_ACTION: '/admin/menu/',
    saveMenu: function (formElementId) {
        var $form = jQuery(formElementId);
        var formdata = $form.serializeArray();
        var callBack = function (json) {
            alert(json.msg);
        };
        _POST(this.URL_ACTION + '_save', formdata, callBack);
        return false;
    },
};

var MNG_MEDIA = {
    URL_ACTION: '/admin/media/',
    BUTTON_ACTION_NAME: '',
    SELECTED: [],
    TARGET_OBJECT: false,
    SELECTED_MULTI: false,
    setting: {
        //setting for form upload
        MULTI_SELECT: false,
        BUTTON_ACTION: false,
        CURENT_IMAGE: '',//đường dẫn ảnh hiện tại: khi mở form và edit ảnh      cũng có thể là cả object
    },
    /***
     * @note: kịch bản mở form upload
     * -- trong form có các thành phần sau: nút upload hình ảnh
     * -- Danh sách hình ảnh đã được upload trước đó có phân trang
     * @param action_name = Tên action khi click vào nút chọn ảnh
     * @param curent = Link của ảnh hiện tại: support cả link tương đối và link tuyệt đối
     */
    openUploadForm: function (action_name, curent, object, object_type) {
        this.BUTTON_ACTION_NAME = action_name;
        this.SELECTED = [];
        this.TARGET_OBJECT = object;
        var linkremoteform = this.URL_ACTION + '_showFormUpload?action_name=' + action_name + '&curent=' + curent;
        return _SHOW_FORM_REMOTE(linkremoteform);
    },
    openUploadFormWithConfig: function (setting) {
        console.log(setting);
        this.setting = setting;
        this.SELECTED = [];
        var linkremoteform = this.URL_ACTION + '_showFormUpload?action_name=' + this.setting.BUTTON_ACTION + '&curent=' + this.setting.CURENT_IMAGE;
        return _SHOW_FORM_REMOTE(linkremoteform);
    },
    save: function (formElementId, script) {
        var $form = jQuery(formElementId);
        //$form.find("button[type='submit']").attr('disabled', true);
        var formdata = $form.serializeArray();
        var callBack = function (json) {
            // $form.find("button[type='submit']").attr('disabled', true);
            if (json.status != 1) {
                alert(json.msg);
                if (json.key != undefined) {
                    jQuery(json.key).focus();
                }
            } else {
                if (json.data != undefined) {
                    if (addnew == undefined && json.data.link != undefined) {
                        window.location.href = json.data.link;
                    } else {
                        alert(json.msg);
                        window.location.href = MNG_CATE.URL_ACTION + 'input';
                    }
                }
            }
        };
        _POST(this.URL_ACTION + '_save', formdata, callBack);
        return false;
    },
};
var MNG_POST = {
    URL_ACTION: '/admin/blog/',
    /***
     * @note: Thêm 1 tag vào trong inline hidden của html để post lên sơ vơ
     * @param sourceElement: id của input tag : nơi nhập từ khóa cách nhau bởi dấu phẩy
     * @param targetElement: id của html hiển thị tag nhập  và các thẻ hidden khác
     */
    addTag: function (sourceElement, targetElement) {
        var $targetElement = jQuery(targetElement);
        var $sourceElement = jQuery(sourceElement);
        var sourceContent = $sourceElement.val().trim();
        if (sourceContent == '') {
            return false;
        }
        sourceContent = sourceContent.split(',');
        for (var i in sourceContent) {

            if (sourceContent[i].trim() != '') {
                var id = 'TAG-' + _convertToAlias(sourceContent[i].trim());
                if (jQuery('#' + id + '').length == 0) {
                    var tag = '<li id="' + id + '"><input type="hidden" name="TAG[]" value="' + sourceContent[i].trim() + '"/>';
                    tag += '<i onclick="jQuery(\'#' + id + '\').remove();" class="icon-diff-removed"></i> ' + sourceContent[i].trim() + '</li>';
                    $targetElement.prepend(tag);
                }
            }
        }
        $sourceElement.val('');
        return false;
    },
    inputTagPress: function (event) {
        if (event.which == 13) {
            MNG_POST.addTag('#post-input-tag', '#post-list-tag');
            return false;
        }
    },
    save: function (formElementId, script) {
        var $form = jQuery(formElementId);
        //$form.find("button[type='submit']").attr('disabled', true);
        var formdata = $form.serializeArray();
        console.log(formElementId);
        var callBack = function (json) {
            // $form.find("button[type='submit']").attr('disabled', true);
            if (json.status != 1) {
                alert(json.msg);
                if (json.key != undefined) {
                    jQuery(json.key).focus();
                }
            } else {
                if (json.data != undefined) {
                    if (script == undefined && json.data.link_edit != undefined) {
                        window.location.href = json.data.link_edit;
                    } else {
                        alert(json.msg);
                        window.location.href = MNG_POST.URL_ACTION + 'input';
                    }
                }
            }
        };
        _POST(this.URL_ACTION + '_save', formdata, callBack);
        return false;
    },
};
var MNG_MEMBER = {
    URL_ACTION: '/admin/member/',

    save: function (formElementId, script) {
        var $form = jQuery(formElementId);
        var formdata = $form.serializeArray();
        var callBack = function (json) {
            // $form.find("button[type='submit']").attr('disabled', true);
            alert(json.msg);
            if (json.status != 1) {
                if (json.key != undefined) {
                    jQuery(json.key).focus();
                }
            } else {
                if (json.data != undefined) {
                    if (script == undefined && json.data.link_edit != undefined) {
                        window.location.href = json.data.link_edit;
                    } else {
                        window.location.href = MNG_MEMBER.URL_ACTION + 'input';
                    }
                }
            }
        };
        _POST(this.URL_ACTION + '_save', formdata, callBack);
        return false;
    },
};
