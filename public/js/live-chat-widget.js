var LHC_API = LHC_API||{};
LHC_API = {
  "args": {
    "mode": "widget",
    "lhc_base_url": "//livechat.sobkichubazar.com.bd/index.php/",
    "wheight": 450,
    "wwidth": 350,
    "pheight": 520,
    "pwidth": 500,
    "department": [],
    "leaveamessage": true,
    "check_messages": false,
    "theme": 2
  }
};

(function() {
    var po = document.createElement('script'); 
    po.type = 'text/javascript'; 
    po.async = true;
    var date = new Date();
    po.src = 'https://livechat.sobkichubazar.com.bd/design/defaulttheme/js/widgetv2/index.js?' + (date.getFullYear() + date.getMonth() + date.getDate());
    var s = document.getElementsByTagName('script')[0]; 
    s.parentNode.insertBefore(po, s);
})();




