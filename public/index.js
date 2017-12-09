console.log('index.js loaded');
$(document).ready(function(){
    console.log('document ready');
    $urlParams=new URLParams();
    var id='';
    var formBlock=$('main.list div.form');
    var form=formBlock.find('form');
    var buttons=form.find('div.buttons');
    buttons.on('click','input[name="submit"]',function(){
        console.log('form.submit');
        id=buttons.find('input[name="id"]').val();
        $request=new Request();
        $request.addListener('success',function(){
            var message=$request.getData();
            if(typeof id!=='undefined'){
                $('main.list div.messages div.message').each(function(){
                    headerNode=$(this).find('div.header');
                    var idCurrent=headerNode.data('id');
                    if(idCurrent===parseInt(id)){
                         headerNode.find('div.name').text(message.name);
                        headerNode.find('div.email').text(message.email.replace('@',' # '));
                        headerNode.find('div.site').text(message.site);
                        $(this).find('div.text').text(message.text);
                        $('html,body').animate({scrollTop:$(this).offset().top},1000);
                    }
                });
                form.get(0).reset();
                formBlock.hide();
            }else{location.reload();}
        });
        var url='/зберегти';
        url=(typeof id==='undefined')?url:url+'/'+id;
        $request.do(url,form.serializeArray());
        if(typeof grecaptcha!=='undefined')grecaptcha.reset();
        return false;
    });
    $('main div.top div.order select').change(function(){
        console.log('order.change');
        $urlParams.set('сортування',$(this).val());
        var redirect=location.protocol+'//'+location.hostname;
        if(location.pathname.length>0)redirect+=location.pathname;
        redirect+='?'+$urlParams.getQuery();
        window.location.replace(redirect);
    });
});

function Request(){
    var parent=this;
    var properties={data:null,error:null,callbacks:{success:null,error:null,always:null}};
    this.setError=function(error){
        properties.error=error;
        if(properties.callbacks.error!=null)properties.callbacks.error();
        alert('Помилка: '+error);
    };
    this.getData=function(){
        return properties.data;
    };
    this.addListener=function(handler,callback){
        if (typeof properties.callbacks[handler]==='undefined'){
            this.setError('Невідомий метод зворотнього виклику '+handler);
            return false;
        }
        properties.callbacks[handler]=callback;
    };

    this.do=function(url,data){
        var params={method:'GET',url:url,text:'text'};
        if(typeof data!=='undefined'){
            params.method='POST';
            params.data=data;
        }
        $.ajax(params)
            .done(function(responce){
                console.log('request.done');
                //console.log(responce);
                if(responce.length===0){
                    parent.setError('Відсутня відповідь сервера зображень');return false;
                }
                try {
                    responce=JSON.parse(responce);
                }catch(e){
                    parent.setError('Помилка перетворення відповіді сервера');return false;
                }
                if(typeof responce!=='object'){
                    parent.setError('Відповідь сервера неправильного типу ('+typeof responce+')');return false;
                }
                if(responce.status!==1){
                    parent.setError(responce.error);return false;
                }
                properties.data=responce.data;
                if(properties.callbacks.success!=null)properties.callbacks.success();
            })
            .fail(function(responce) {
                parent.setError('Помилка запиту до сервера: '+responce);
                if(properties.callbacks.fail!=null)properties.callbacks.fail();
            })
            .always(function() {
                if(properties.callbacks.always!=null)properties.callbacks.always();
            });
    };
}

function URLParams(){
    var params=decodeURI(location.search.substring(1));
    this.set=function(title,value){params[title]=value;};
    this.get=function(title){return params[title];};
    this.getQuery=function(){
        query=[];
        for (var property in params)
            if(params.hasOwnProperty(property))
                query.push(property+'='+params[property]);
        return query.join('&');
    };
    if(params.length>0)
        params=JSON.parse('{"'+params.replace(/&/g,'","').replace(/=/g,'":"')+'"}');
    else params={};
}