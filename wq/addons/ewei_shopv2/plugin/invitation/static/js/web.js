define(['./sortable.js'], function (Sortable) {
    var modal = {
        default: {
            title: '未命名邀请卡',
            type: 0,
            status: 0,
            qrcode: 0,
            templist: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'],
            upload: {},
            selected: ['1', '2']
        }, data: {}, attachurl: '', id: 0
    };
    modal.init = function (params) {
        params = params || {};
        modal.id = params.id;
        modal.data = params.data ? params.data : modal.default;
        modal.attachurl = params.attachurl ? params.attachurl : '';
        if (!modal.data.upload) {
            modal.data.upload = {}
        }
        if (!modal.data.templist) {
            modal.data.templist = modal.default.templist
        }
        modal.initTpl();
        modal.initShow();
        modal.initEditor();
        modal.initClick()
    };
    modal.initSort = function () {
        new Sortable(selectedList, {
            draggable: '.item', onUpdate: function (event) {
                var newList = [];
                $('.temp-selected .item').each(function () {
                    var tempid = $(this).data('tempid');
                    if (tempid != '') {
                        newList.push($.trim(tempid))
                    }
                });
                modal.data.selected = newList
            }
        })
    };
    modal.initClick = function () {
        $(document).on('click', '.temp-upload .item:not(.add), .temp-default .item', function () {
            var upload = $(this).closest('.temp-list').hasClass('temp-upload');
            var selected = $(this).hasClass('selected');
            var selectedLen = modal.length(modal.data.selected);
            var tempid = $(this).data('tempid');
            if (selected) {
                if (selectedLen == 1) {
                    tip.msgbox.err('至少保留1个');
                    return
                }
                modal.deleteSelected(tempid)
            } else {
                if (selectedLen >= 10) {
                    tip.msgbox.err('最多选择10个');
                    return
                }
                modal.data.selected.push(tempid + '')
            }
            modal.initEditor()
        });
        $(document).on('click', '.temp-upload .item.add', function () {
            $("#addModal").modal()
        });
        $(document).on('click', '.temp-selected .item', function () {
            var tempid = $(this).closest('.item ').data('tempid');
            modal.preview(tempid)
        });
        $(document).on('click', '.temp-list .item .icon-close', function (e) {
            e.stopPropagation();
            var upload = $(this).closest('.temp-list').hasClass('temp-upload');
            var selected = $(this).closest('.item').hasClass('selected');
            var tempid = $(this).closest('.item ').data('tempid');
            var selectedLen = modal.length(modal.data.selected);
            if (upload) {
                if (selectedLen == 1 && $.inArray(tempid, modal.data.selected) > -1) {
                    tip.msgbox.err('至少保留1个');
                    return
                }
                var tips = '确认删除自定义模板？';
                if (selected) {
                    tips += '<br>当前模板已选择，将一并移除'
                }
                tip.confirm(tips, function () {
                    modal.deleteUpload(tempid);
                    if (selected) {
                        modal.deleteSelected(tempid)
                    }
                    modal.initEditor()
                });
                return
            }
            if (selectedLen == 1) {
                tip.msgbox.err('至少保留1个');
                return
            }
            if (tempid) {
                modal.deleteSelected(tempid)
            }
            modal.initEditor()
        });
        $('#addModal .close').click(function () {
            modal.closeModal()
        });
        $('#btn-add').click(function () {
            var nail = $('#image-nail').val();
            var bg = $('#image-bg').val();
            var itemid = modal.getId();
            if (nail == '') {
                tip.msgbox.err('请选择缩略图');
                return
            }
            if (bg == '') {
                tip.msgbox.err('请选择背景图');
                return
            }
            modal.data.upload[itemid] = {bg: $.trim(bg), nail: $.trim(nail)};
            modal.addBtn();
            modal.initEditor();
            modal.closeModal()
        });
        $(document).on('click', '[data-toggle="selectImage"]', function () {
            var _input = $(this).data('input');
            var _img = $(this).data('img');
            require(['jquery', 'util'], function ($, util) {
                util.image('', function (data) {
                    var imgurl = data.attachment;
                    if (_input) {
                        $(_input).val(imgurl).trigger('change')
                    }
                    if (_img) {
                        $(_img).attr('src', data.url).show().closest('.image').addClass('selected')
                    }
                })
            })
        });
        $(document).on('click', '#btn-save', function () {
            var _this = $(this);
            if (_this.attr('stop')) {
                tip.msgbox.err('保存中...请稍后...');
                return
            }
            if (modal.data.selected.length < 1) {
                tip.msgbox.err('请至少选择一1个模板');
                return
            }
            if (modal.data.title == '') {
                tip.msgbox.err('请填写邀请卡名称');
                return
            }
            _this.attr('stop', 1).text('保存中...');
            $.post(biz.url('invitation/edit'), {id: modal.id, data: modal.data}, function (ret) {
                if (ret.status == 0) {
                    tip.msgbox.err(ret.result.message);
                    _this.removeAttr('stop').text('保存');
                    return
                }
                tip.msgbox.suc('保存成功');
                if (ret.result.id != modal.id) {
                    location.href = biz.url('invitation/edit', {id: ret.result.id})
                }
                ;
                _this.removeAttr('stop').text('保存')
            }, 'json')
        })
    };
    modal.preview = function (tempid) {
        if (tempid == '') {
            return
        }
        if (isNaN(tempid)) {
            if ($.isEmptyObject(modal.data.upload)) {
                return
            }
            var uploadItem = modal.data.upload[tempid];
            if (!uploadItem || uploadItem.bg == '') {
                return
            }
            var bg = modal.imgsrc(uploadItem.bg)
        } else {
            var bg = '../addons/ewei_shopv2/plugin/invitation/static/templist/image_' + tempid + '_bg.jpg'
        }
        $('.card-bg').attr('src', bg);
        $('.temp-selected .item[data-tempid="' + tempid + '"]').addClass('selected').siblings().removeClass('selected')
    };
    modal.deleteSelected = function (tempid) {
        $.each(modal.data.selected, function (index, id) {
            if (id == tempid) {
                modal.data.selected.splice(index, 1)
            }
        })
    };
    modal.deleteUpload = function (tempid) {
        delete modal.data.upload[tempid]
    };
    modal.initTpl = function () {
        tpl.helper("inArray", function (str, tag) {
            if (!str || !tag) {
                return false
            }
            if (typeof(str) == 'string') {
                var arr = str.split(",");
                if ($.inArray(tag, arr) > -1) {
                    return true
                }
            } else {
                return $.inArray(tag, str) > -1
            }
            return false
        });
        tpl.helper("count", function (data) {
            return modal.length(data)
        });
        tpl.helper("getNail", function (str) {
            var isNum = isNaN(str) ? false : true;
            if (isNum) {
                return '../addons/ewei_shopv2/plugin/invitation/static/templist/image_' + str + '_nail.jpg'
            } else {
                var item = modal.data.upload[str];
                if (item) {
                    return modal.imgsrc(item.nail)
                }
            }
        });
        tpl.helper("imgsrc", function (src) {
            return modal.imgsrc(src)
        })
    };
    modal.imgsrc = function (src) {
        if (typeof src != 'string') {
            return ''
        }
        if (src.indexOf('http://') == 0 || src.indexOf('https://') == 0 || src.indexOf('../addons/ewei_shopv2/') == 0) {
            return src
        } else if (src.indexOf('images/') == 0 || src.indexOf('audios/') == 0) {
            return modal.attachurl + src
        }
    };
    modal.length = function (json) {
        if (typeof(json) === 'undefined') {
            return 0
        }
        var jsonlen = 0;
        for (var item in json) {
            jsonlen++
        }
        return jsonlen
    };
    modal.getId = function () {
        var date = +new Date();
        return 'U' + date
    };
    modal.initShow = function () {
        var html = tpl("tpl_show_live", modal.data);
        $("#phone").html(html)
    };
    modal.initEditor = function () {
        var html = tpl("tpl_editor", modal.data);
        $("#editor").html(html);
        $("#editor").find(".diy-bind").bind('input propertychange change', function () {
            var _this = $(this);
            var bind = _this.data("bind");
            var tag = this.tagName;
            var value = '';
            if (tag == 'INPUT') {
                value = _this.val()
            }
            value = $.trim(value);
            modal.data[bind] = value
        });
        modal.initSort();
        modal.addBtn();
        modal.preview(modal.data.selected[0]);
        $('.diy-editor').show()
    };
    modal.addBtn = function () {
        var uploadLen = modal.length(modal.data.upload);
        if (uploadLen >= 10) {
            $('.temp-upload .item.add').hide()
        } else {
            $('.temp-upload .item.add').show()
        }
    };
    modal.closeModal = function () {
        $('#image-nail-show').attr('src', '').hide();
        $('#image-nail').val('');
        $('.image-nail').removeClass('selected');
        $('#image-bg-show').attr('src', '').hide();
        $('#image-bg').val('');
        $('.image-bg').removeClass('selected');
        $('#addModal').modal('hide')
    };
    return modal
});