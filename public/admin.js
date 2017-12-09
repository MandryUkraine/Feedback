console.log('admin.js loaded');
const LOGIN=document.getElementById('root').hasAttribute('data-login');
console.log('login: '+LOGIN);
const DEBUG=document.getElementById('root').hasAttribute('data-debug');
console.log('debug: '+DEBUG);
$(document).ready(function(){
    if(LOGIN){
        $('main.list div.messages div.message div.header')
        .on('click','div.control a.edit',function(){
            console.log('message.edit');
            var form=$('main.list div.form');
            var message=$(this).parents('div.message');
            var header=message.find('div.header');
            form.find('div.name input').val(header.find('div.name').text());
            form.find('div.email input').val(header.find('div.email').text().replace(' # ', '@'));
            form.find('div.site input').val(header.find('div.site').text());
            form.find('div.text textarea').val(message.find('div.text').text());
            form.find('div.buttons input[name="id"]').val(header.data('id'));
            form.show();
            $('html,body').animate({scrollTop:form.offset().top},1000);
            return false;
        })
        .on('click','div.control a.delete',function(){
            console.log('message.delete');
            if(!confirm('Ви впевненні що хочете видалити повідомлення?')) return false;
            var message=$(this).parents('div.message');
            var id=message.find('div.header').data('id');
            $request = new Request();
            $request.addListener('success',function(){message.remove();});
            $request.do('/видалити/'+id);
            return false;
        });
    }
});
