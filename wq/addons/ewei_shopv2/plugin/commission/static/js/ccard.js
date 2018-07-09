define(['core', 'tpl'], function (core, tpl) {
    var modal = {page: 1, orderPage: 1, status: ''};
    modal.init = function () {

        $('.fui-content').infinite({
            onLoading: function () {
                modal.getList()
            }
        });
        if (modal.page == 1) {
            modal.getList()
        }
    };
    modal.getList = function () {
        core.json('commission/ccard/get_list', {page: modal.page, status: modal.status}, function (ret) {
            var result = ret.result;
            $('#total').html(result.total);
            //$('#commissioncount').html(result.commissioncount);
            if (result.total <= 0) {
                $('.content-empty').show();
                $('#container').hide();
                $('.fui-content').infinite('stop')
            } else {
                $('#container').show();
                $('.content-empty').hide();
                $('.fui-content').infinite('init');
                if (result.list.length <= 0 || result.list.length < result.pagesize) {
                    $('.fui-content').infinite('stop')
                }
            }
            modal.page++;
            core.tpl('#container', 'tpl_commission_log_list', result, modal.page > 1)
        })
    };
    modal.initDetail = function (id) {
        $('.fui-content').infinite({
            onLoading: function () {
                modal.getDetaiList()
            }
        });
        modal.applyid = id;
        if (modal.orderPage == 1) {
            modal.getDetaiList()
        }
    };
    return modal
});