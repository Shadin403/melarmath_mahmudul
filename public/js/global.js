


window.addEventListener('load',function(){
    const hideHeaderOnMobile = document.getElementById('hideHeaderOnMobile')
    const rh_footerMenu = document.getElementById('rh_footerMenu')
    console.log(hideHeaderOnMobile)
    if(window.innerWidth >= 991){
        hideHeaderOnMobile.classList.remove('sticky-bar')
    }
    
    window.addEventListener('resize',function(){
        if(window.innerWidth <= 991){
            hideHeaderOnMobile.classList.add('sticky-bar')
        }
    })
    let lastScrollTop = 0;
    window.addEventListener('scroll',function(e){
        let currentScroll = window.pageYOffset || document.documentElement.scrollTop;
        if(window.pageYOffset > 230){
            if(currentScroll < lastScrollTop){
                hideHeaderOnMobile.style.marginTop = '-80px';
            }else{
                hideHeaderOnMobile.style.marginTop = '0';
            }
        }else{
            hideHeaderOnMobile.style.marginTop = '0'
        }
        if(currentScroll > lastScrollTop){
            rh_footerMenu.style.marginBottom = '-80px';
        }else{
            rh_footerMenu.style.marginBottom = '0';
        }
        
        
        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    })
    
    const chatting_popup = document.getElementById('chatting_popup')
    const buttonContactSr = document.getElementById('button-contact-vr')
    const defaultButtonContactHtml = buttonContactSr.innerHTML
    buttonContactSr.style.display = 'block !important'
    buttonContactSr.innerHTML = ''
    
    chatting_popup.addEventListener('click',function(){
        console.log(buttonContactSr.innerHTML)
        if(buttonContactSr.innerHTML == ''){
            buttonContactSr.innerHTML = defaultButtonContactHtml
        const allInOne = document.getElementById('gom-all-in-one')
        allInOne.style.display = 'block';
        }else{
            buttonContactSr.innerHTML = ''
        }
        
    })
    
    const insideOfDhaka = document.getElementById('insideOfDhaka')
    const outSideOfDhaka = document.getElementById('outSideOfDhaka')
    console.log(insideOfDhaka, 'dhakar vitore')
    console.log(outSideOfDhaka, 'dhakar bahire')
    
})
