define(['core', 'tpl'], function (core, tpl, op) {

    var modal = {
        params: {}
    };
    modal.init = function () {

        $('.order-verify').click(function () {
            modal.verify($(this));
        });

        $(".fui-number").numbers({
            minToast: "最少核销{min}次",
            maxToast: "最多核销{max}次"
        });


    };

    modal.verify = function(btn){

        var verifygoodid= btn.data('verifygoodid') ;

        var times = parseInt( $('.shownum').val() );
        var verifycode =  btn.data('verifycode');
        var remarks = $('#remarks').val();

        FoxUI.confirm( "确认核销吗?" ,function(){
            core.json('verify/verifygoods/complete',{id:verifygoodid,times:times,verifycode:verifycode,remarks:remarks},function(ret){
                if(ret.status==0){
                    FoxUI.toast.show( ret.result.message );
                    return;
                }

                location.href = core.getUrl('verify/verifygoods/success');
            });
        });
    };
    return modal;
});