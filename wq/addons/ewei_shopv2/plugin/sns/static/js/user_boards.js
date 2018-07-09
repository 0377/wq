define(['core', 'tpl'], function (core, tpl) {
    var modal = {page: 1, id: ''};

    modal.firstF = function(){
      modal.first = true;
      modal.page = 1;
    };
    modal.init = function (params) {
        modal.firstF();
        modal.id = params.id;
        $('.fui-content').infinite({
            onLoading: function () {
                modal.getList()
                modal.first = false;
            }
        });
        
        if (modal.page == 1) {

            if ($(".fui-list").length <= 0 || modal.first) {
                
                modal.getList()
                modal.first = false;
            } else {
                modal.page++
            }
        }
    };
    modal.getList = function () {
        core.json('sns/user/get_boards', {page: modal.page, id: modal.id}, function (ret) {
            var result = ret.result;
            if (result.total <= 0) {
                $('#user-boards-list').hide();
                $('.empty').show();
                $('.fui-content').infinite('stop')
            } else {
                $('#user-boards-list').show();
                $('.empty').hide();
                $('.fui-content').infinite('init');
                if (result.list.length <= 0 || result.list.length < result.pagesize) {
                    $('.fui-content').infinite('stop')
                }
            }
            modal.page++;
            core.tpl('#user-boards-list', 'tpl_user_board_list', result, modal.page > 1)
        })
    };
    return modal
});