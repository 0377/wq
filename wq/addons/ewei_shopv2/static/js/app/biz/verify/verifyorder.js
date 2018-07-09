define(['core'], function (core) {
    var modal = {page: 1, type: 0, offset: 0, keywords: '', isverifygoods_tab:0};
    modal.initList = function (params) {
        // modal.type = params.type;
        modal.keywords = params.keywords;
        modal.initClick();
        if (window.orderid) {
            var elm = $(document).find(".fui-list-group[data-order='" + window.orderid + "']");
            if (window.remarksaler == 1) {
                elm.find(".icon-pin").show()
            } else if (window.remarksaler == 2) {
                elm.find(".icon-pin").hide();
            }
        }
        var leng = $.trim($('.container').html());
        if (leng == '') {
            modal.page = 1;
            modal.getList();
        }
        $('.fui-content').infinite({
            onLoading: function () {
                modal.getList();
            }
        })
    };
    modal.initClick = function () {
      /*  $("#tab a").unbind('click').click(function () {

            var type = $(this).data("type");
            // if (modal.type == type) {
            //     return
            // }
            // modal.type = type;
            $(this).addClass("active").siblings().removeClass("active");
            modal.page = 1;
            $(".container").empty();
            modal.getList()
        });*/
        $("#tab a").unbind('click').click(function () {
            var tab = $(this).data("tab");
            modal.isverifygoods_tab = 0;

            if(tab=='verifygoods' || tab=='all'){
                $(".container").empty();
                $('#searchfieid').hide();
                $('#searchfieid_verify').hide();
            }

            if(tab=='verifygoods'){
                tab = "all";
                modal.isverifygoods_tab = 1;
                $('#searchfieid_verify').show();
            }else if(tab=='all'){
                $('#searchfieid').show();
            }
            $(this).addClass("active").siblings().removeClass("active");
            $(".tab-content").hide();
            $("#tab_" + tab).show();


            if( tab!='count'){
                modal.page = 1;
                modal.keywords ='';
                modal.offset = 0;
                modal.getList();
            }
        });
        $(".fui-search-btn").unbind('click').click(function () {
            var keywords = $.trim($("#keywords").val());
            if (keywords == '') {
                FoxUI.toast.show("请输入搜索关键字");
                return
            }

            if(!$('#content-empty').is(":hidden")){
                FoxUI.toast.show("您暂时没有核销记录");
                return
            }

            modal.keywords = keywords;
            modal.page = 1;
            $(".container").empty();
            modal.getList();
        });
        $("#keywords").bind('input propertychange', function () {
            var keywords = $.trim($(this).val());
            if (keywords == '') {
                modal.keywords = '';
                modal.page = 1;
                modal.offset = 0;
                $(".container").empty();
                modal.getList();
            }
        })
    };
    modal.getList = function () {
        // var obj = {page: modal.page, type: modal.type, keyword: modal.keywords, offset: modal.offset};
        var obj = {page: modal.page, keyword: modal.keywords, offset: modal.offset ,isverifygoods:modal.isverifygoods_tab};
        if (obj.keyword != '') {
            if(modal.isverifygoods_tab==1) {
                obj.searchfield = modal.selectVal('searchfieid_verify', true);
            }
            else{
                obj.searchfield = modal.selectVal('searchfieid', true);
            }
        }

        var url  = 'verify/verifyorder/orderData';
        if(modal.isverifygoods_tab==1){
            url = 'verify/verifyorder/orderDataVerify';
        }
        core.json(url, obj, function (json) {

            if (json.status != 1) {
                return
            }
            var result = json.result;
            if (result.total < 1) {
                $('#content-empty').show();
                $('#content-nomore').hide();
                $('#content-more').hide();
                $('.fui-content').infinite('stop');
            } else {
                $('#content-empty').hide();
                $('.fui-content').infinite('init');
                if (result.list.length <= 0 || result.list.length < result.pagesize) {
                    $('#content-more').hide();
                    $("#content-nomore").show();
                    $("#content-empty").hide();
                    $('.fui-content').infinite('stop');
                } else {
                    $("#content-nomore").hide();
                }
            }
            modal.page++;
            // result.type = modal.type;
            core.tpl('.container', 'tpl_order', result, modal.page > 1);
            FoxUI.loader.hide();
        }, false, true);
    };
    modal.selectVal = function (elm, isVal) {
        if (isVal) {
            return $("#" + elm).find('option:selected').val();
        }
        return $("#" + elm).find('option:selected').text();
    };
    return modal
});