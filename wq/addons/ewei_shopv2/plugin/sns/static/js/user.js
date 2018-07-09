define(['core', 'tpl'], function (core, tpl) {

    var modal  = {};
    modal.init = function (params) {

        $('#btnSend').click(function(){

            if($(this).attr('stop')){
                return;
            }
            
            var content =$.trim($('#content').val() );

                $(this).attr('stop',1);
                core.json('sns/user/submit_sign', {
                    'sign': content
                }, function (ret) {
                    if (ret.status == 0) {
                        $('#btnSend').removeAttr('stop');
                        FoxUI.toast.show(ret.result.message);
                        return
                    }
                    if(content!=''){
                        $(".sign-content").html(content);
                    } else{
                        $(".sign-content").html("这个家伙什么也没留下~~");
                    }
                    FoxUI.toast.show('修改成功!');
                        $('#btnSend').removeAttr('stop');
                        $('#edit-sign').hide();

                }, true, true);


        })
    };
    return modal;
});   
