define(['core', 'tpl'], function (core, tpl) {
    var modal = {page: 1, cate: ''};
    modal.init = function (params) {
        $('#container').empty();
        modal.page = 1;
        $("#cateTab a").click(function () {
            var cate = $(this).data('cate');
            modal.cate = cate;
            modal.page = 1;
            $(this).addClass('active').siblings().removeClass('active');
            FoxUI.loader.show('mini');
            $("#container").html('');
            modal.getList(modal.cate);
        });
        $('.fui-content').infinite({
            onLoading: function () {
                modal.getList(modal.cate);
            }
        });
        if (modal.page == 1) {
            modal.getList(modal.cate);
        }
    };
    modal.getList = function (cateid) {
        core.json('verifygoods/getlist', {page: modal.page, cate: cateid}, function (ret) {

            $('.infinite-loading').hide();
            var result = ret.result;

            if (result.total <= 0) {
                $('.content-empty').show();
                $('.fui-content').infinite('stop');
            } else {
                $('.content-empty').hide();
                $('.fui-content').infinite('init');
                if (result.list.length <= 0 || result.list.length < result.pagesize) {
                    $('.fui-content').infinite('stop');
                }
            }
            modal.page++;
            FoxUI.loader.hide();
            core.tpl('#container', 'tpl_list_verifygood_my', result, modal.page > 1);
        })
    };
    return modal
});