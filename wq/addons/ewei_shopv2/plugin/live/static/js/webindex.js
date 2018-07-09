define([], function () {
    var modal = {wsConnected: false, wsConfig: {}};
    modal.init = function (params) {
        modal.wsConfig = params.wsConfig;
        modal.type = params.type;
        modal.initWs();
        $('.btn-reconnect').click(function () {
            if (modal.wsConnected) {
                return
            }
            $('.btn-status').removeClass('btn-warning').removeClass('btn-danger').addClass('btn-default').text('重新连接中...');
            modal.initWs()
        });
        $('#btn-reload').click(function () {
            if (!modal.wsConfig || !modal.wsConfig.address || !modal.wsConnected) {
                tip.msgbox.err('与通信服务器连接失败');
                return
            }
            tip.confirm('确定要平滑重启？', function () {
                tip.msgbox.suc('提交重启请求，请稍后');
                modal.wsClient.send(JSON.stringify({scene: 'reload'}));
                modal.initWs()
            })
        })
    };
    modal.initWs = function () {
        if (!modal.wsConfig || !modal.wsConfig.address) {
            $('.btn-status').removeClass('btn-default').addClass('btn-warning').text('通讯服务器配置错误')
        }
        ;
        var wsClient = new WebSocket(modal.wsConfig.address);
        wsClient.onopen = function () {
            $('.btn-status').removeClass('btn-default').addClass('btn-primary').text(modal.type == 1 ? '点击平滑启动' : '与通信服务器连接成功');
            $('.alert-danger').hide();
            modal.wsConnected = true
        };
        wsClient.onmessage = function (evt) {
            console.log(evt)
        };
        wsClient.onclose = function () {
            if (!modal.wsConnected) {
                return
            }
            $('.btn-status').removeClass('btn-default').addClass('btn-warning').text('与通信服务器断开连接');
            modal.wsConnected = false
        };
        wsClient.onerror = function (evt) {
            $('.btn-status').removeClass('btn-default').addClass('btn-danger').text('与通信服务器连接失败');
            $('.alert-danger').show();
            modal.wsConnected = false
        };
        modal.wsClient = wsClient
    };
    return modal
});