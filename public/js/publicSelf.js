function tabAddPublic(title,url,id) {
    if(!id) id = new Date().getTime() ;
    window.parent.tab.tabAdd({
        title: title
        ,url: url //支持传入html
        ,id:id
        ,icon: '&#xe642;',
    });
}