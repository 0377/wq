    define(['core', 'tpl'], function (core, tpl, op) {
        var modal = {
            params: {}
        };
        modal.init = function () {
                  $(".btn-search").click(function(){
                      if($('#verifycode').isEmpty()){
                          FoxUI.toast.show('请填写核销商品码');
                          return;
                      }

                      core.json('verify/verifygoods/search',{verifycode: $('#verifycode').val() },function(ret){
                            if(ret.status==0){
                                FoxUI.toast.show( ret.result.message);
                                return;
                            }
                            $.router.load( core.getUrl('verify/verifygoods/detail',{id: ret.result.verifygoodid,verifycode:$('#verifycode').val()}),true);
                      },true,true);
                  })
        };
        modal.verify = function(btn){
        };
        return modal;
    });